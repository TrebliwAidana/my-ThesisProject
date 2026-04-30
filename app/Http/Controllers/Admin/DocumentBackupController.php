<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentVersion;
use App\Models\RestoredBackup;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DocumentBackupController extends Controller
{
    private string $backupDisk   = 'private';
    private string $backupFolder = 'backups/documents';

    private array $fileTypeGroups = [
        'all'        => [],
        'pdf'        => ['application/pdf'],
        'word'       => ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'excel'      => ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'powerpoint' => ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'images'     => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'zip'        => ['application/zip', 'application/x-zip-compressed'],
    ];

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------
    public function index()
    {
        $this->authorizeAccess();

        // FIX: Flysystem v3 (Laravel 10+) returns a DirectoryListing of
        // League\Flysystem\FileAttributes objects, NOT plain arrays.
        // We must call ->toArray() on the listing and then access object
        // properties via methods, OR just use Storage::files() + individual
        // stat calls. Using files() + stat is the safest cross-version approach.
        $files = Storage::disk($this->backupDisk)->files($this->backupFolder);

        $backups = [];
        foreach ($files as $file) {
            if (!str_ends_with($file, '.zip')) {
                continue;
            }

            $basename  = basename($file, '.zip');
            $sizeBytes = Storage::disk($this->backupDisk)->size($file);
            $modified  = Storage::disk($this->backupDisk)->lastModified($file);

            [$categorySlug, $filetypeSlug] = $this->parseBackupSlug($basename);

            $backups[] = [
                'filename'      => basename($file),
                'path'          => $file,
                'size'          => $this->formatBytes($sizeBytes),
                'size_bytes'    => $sizeBytes,
                'category_slug' => $categorySlug,
                'filetype_slug' => $filetypeSlug,
                'created_at'    => Carbon::createFromTimestamp($modified)->format('M d, Y h:i A'),
                'created_ts'    => $modified,
            ];
        }

        // Sort newest first.
        usort($backups, fn ($a, $b) => $b['created_ts'] - $a['created_ts']);

        $totalBytes = array_sum(array_column($backups, 'size_bytes'));
        $stats = [
            'count'      => count($backups),
            'total_size' => $this->formatBytes($totalBytes),
            'latest'     => $backups[0]['created_at'] ?? null,
            'oldest'     => !empty($backups) ? end($backups)['created_at'] : null,
        ];

        $categories     = DocumentCategory::withCount('documents')->orderBy('name')->get();
        $fileTypeGroups = array_keys($this->fileTypeGroups);

        return view('admin.document-backups.index', compact('backups', 'categories', 'fileTypeGroups', 'stats'));
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------
    public function create(Request $request)
    {
        $this->authorizeAccess();

        // Always respond with JSON when called via AJAX so the blade
        // JS never receives an HTML error page that breaks res.json().
        $isAjax = $request->ajax() || $request->wantsJson();

        try {
            $request->validate([
                'category_ids'   => 'nullable|array',
                'category_ids.*' => 'exists:document_categories,id',
                'file_type'      => 'nullable|in:' . implode(',', array_keys($this->fileTypeGroups)),
            ]);
        } catch (\Throwable $e) {
            return $isAjax
                ? response()->json(['success' => false, 'error' => $e->getMessage()], 422)
                : back()->with('error', $e->getMessage());
        }

        $startTime = microtime(true);

        // ------------------------------------------------------------------
        // Resolve filters
        // ------------------------------------------------------------------
        $categoryIds  = array_values(array_filter((array) $request->input('category_ids', [])));
        $fileTypeKey  = $request->input('file_type', 'all');
        $allowedMimes = $this->fileTypeGroups[$fileTypeKey] ?? [];

        $categorySlug  = 'all';
        $categoryLabel = 'All Categories';

        if (!empty($categoryIds)) {
            $cats = DocumentCategory::whereIn('id', $categoryIds)
                                    ->orderBy('name')
                                    ->get(['id', 'name']);

            $categorySlug  = $cats->map(fn ($c) => Str::slug($c->name, '_'))->join('--');
            $categoryLabel = $cats->pluck('name')->join(', ');
        }

        // ------------------------------------------------------------------
        // Build tmp ZIP
        // ------------------------------------------------------------------
        $timestamp = now()->format('Y-m-d_His');
        $zipName   = "doc_backup__{$categorySlug}__{$fileTypeKey}__{$timestamp}.zip";
        $tmpPath   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

        // FIX: wrap the entire archive build in try/catch so any failure
        // returns a proper JSON error instead of a 500 HTML page.
        try {
            $zip = new ZipArchive;
            if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Failed to create ZIP archive. Check server temp directory permissions.');
            }

            // 1. Categories JSON
            $allCategories = DocumentCategory::all(['id', 'name', 'description', 'is_active'])->toArray();
            $zip->addFromString('categories.json', json_encode($allCategories, JSON_PRETTY_PRINT));

            // 2. Documents + versions (2 queries total via eager load)
            $documents = Document::withTrashed()
                ->with(['versions' => fn ($q) => $q->select([
                    'id', 'document_id', 'version_number', 'file_path',
                    'file_name', 'mime_type', 'file_size', 'change_notes',
                    'uploaded_by', 'created_at',
                ])])
                ->when(!empty($categoryIds), fn ($q) => $q->whereIn('document_category_id', $categoryIds))
                ->get([
                    'id', 'owner_id', 'current_version_id', 'title', 'description',
                    'document_category_id', 'uploaded_at', 'created_at', 'updated_at', 'deleted_at',
                ])
                ->map(fn ($doc) => [
                    'id'                   => $doc->id,
                    'owner_id'             => $doc->owner_id,
                    'current_version_id'   => $doc->current_version_id,
                    'title'                => $doc->title,
                    'description'          => $doc->description,
                    'document_category_id' => $doc->document_category_id,
                    'uploaded_at'          => $doc->uploaded_at,
                    'created_at'           => $doc->created_at,
                    'updated_at'           => $doc->updated_at,
                    'deleted_at'           => $doc->deleted_at,
                    'versions'             => $doc->versions
                        ->when(
                            !empty($allowedMimes),
                            fn ($col) => $col->filter(fn ($v) => in_array($v->mime_type, $allowedMimes))
                        )
                        ->values()
                        ->toArray(),
                ])
                ->all();

            // 3. Manifest JSON
            $zip->addFromString('manifest.json', json_encode([
                'backup_version' => '2.0',
                'created_at'     => now()->toISOString(),
                'created_by'     => Auth::user()->full_name ?? Auth::user()->email,
                'scope'          => [
                    'category_ids'   => $categoryIds,
                    'category_label' => $categoryLabel,
                    'file_type'      => $fileTypeKey,
                    'allowed_mimes'  => $allowedMimes,
                ],
                'document_count' => count($documents),
                'documents'      => $documents,
            ], JSON_PRETTY_PRINT));

            // 4. Physical files
            // FIX: initialize $tempFiles before the loop so it always exists.
            $tempFiles = [];
            $fileCount = 0;

            foreach ($documents as $doc) {
                foreach ($doc['versions'] as $version) {
                    $filePath = $version['file_path'];

                    if (!Storage::disk($this->backupDisk)->exists($filePath)) {
                        continue;
                    }

                    // FIX: use readStream + temp file instead of get() to avoid
                    // loading the entire file into a PHP string (OOM on large files).
                    $readStream = Storage::disk($this->backupDisk)->readStream($filePath);
                    if (!is_resource($readStream)) {
                        continue;
                    }

                    $tmpFile  = tempnam(sys_get_temp_dir(), 'bkf_');
                    $dest     = fopen($tmpFile, 'wb');

                    if ($dest === false) {
                        fclose($readStream);
                        continue; // skip this file, don't abort the whole backup
                    }

                    stream_copy_to_stream($readStream, $dest);
                    fclose($dest);
                    fclose($readStream);

                    $zip->addFile($tmpFile, "files/{$filePath}");
                    $tempFiles[] = $tmpFile;
                    $fileCount++;
                }
            }

            // Seal the ZIP before streaming it to storage.
            $zip->close();

            // Cleanup per-file temp files after ZIP is sealed.
            foreach ($tempFiles as $tmp) {
                @unlink($tmp);
            }

            // 5. Stream sealed ZIP to permanent storage
            $destination = "{$this->backupFolder}/{$zipName}";
            $writeStream = @fopen($tmpPath, 'rb');

            if ($writeStream === false) {
                throw new \RuntimeException('Failed to open sealed ZIP for streaming to storage.');
            }

            Storage::disk($this->backupDisk)->writeStream($destination, $writeStream);

            if (is_resource($writeStream)) {
                fclose($writeStream);
            }

            @unlink($tmpPath);

        } catch (\Throwable $e) {
            // Clean up any open temp files on failure.
            foreach ($tempFiles ?? [] as $tmp) {
                @unlink($tmp);
            }
            @unlink($tmpPath);

            \Log::error('Backup creation failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            $error = 'Backup failed: ' . $e->getMessage();
            return $isAjax
                ? response()->json(['success' => false, 'error' => $error], 500)
                : back()->with('error', $error);
        }

        $elapsed = round(microtime(true) - $startTime, 2);

        AuditLogger::log(
            'backup_created',
            null,
            "Document backup created: {$zipName} (category: {$categoryLabel}, type: {$fileTypeKey}, {$fileCount} files, " . count($documents) . ' documents)',
            [],
            [
                'filename'        => $zipName,
                'category_ids'    => $categoryIds,
                'category_label'  => $categoryLabel,
                'file_type'       => $fileTypeKey,
                'elapsed_seconds' => $elapsed,
            ]
        );

        if ($isAjax) {
            return response()->json([
                'success'  => true,
                'filename' => $zipName,
                'elapsed'  => $elapsed,
                'message'  => "Backup created in {$elapsed}s — {$fileCount} files from \"{$categoryLabel}\" ({$fileTypeKey}).",
            ]);
        }

        return redirect()->route('admin.document-backups.index')
            ->with('success', "Backup created: {$zipName} ({$fileCount} files · category: {$categoryLabel} · type: {$fileTypeKey})");
    }

    // -------------------------------------------------------------------------
    // Download
    // -------------------------------------------------------------------------
    public function download(string $filename)
    {
        $this->authorizeAccess();

        $filename = basename($filename);
        $path     = "{$this->backupFolder}/{$filename}";

        if (!Storage::disk($this->backupDisk)->exists($path)) {
            abort(404, 'Backup file not found.');
        }

        AuditLogger::log('backup_downloaded', null, "Backup downloaded: {$filename}");

        return Storage::disk($this->backupDisk)->download($path, $filename);
    }

    // -------------------------------------------------------------------------
    // Restore
    // -------------------------------------------------------------------------
    public function restore(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'backup_file'   => 'required|file|mimes:zip|max:512000',
            'mode'          => 'required|in:merge,skip',
            'force_restore' => 'sometimes|boolean',
        ]);

        $uploadedFile = $request->file('backup_file');
        $filename     = $uploadedFile->getClientOriginalName();
        $tmpPath      = $uploadedFile->getPathname();
        $backupHash   = hash_file('sha256', $tmpPath);

        if (!$request->boolean('force_restore')) {
            $existing = RestoredBackup::where('backup_filename', $filename)->first();
            if ($existing) {
                $restorer = $existing->restoredBy?->full_name ?? 'User #' . $existing->restored_by;
                return back()->with('error', sprintf(
                    'Backup "%s" was already restored on %s by %s. Tick "Force restore" to override.',
                    $filename,
                    $existing->restored_at->format('Y-m-d H:i:s'),
                    $restorer
                ));
            }
        }

        $zip = new ZipArchive;
        if ($zip->open($tmpPath) !== true) {
            return back()->with('error', 'Invalid or corrupted ZIP file.');
        }

        try {
            $manifestJson = $zip->getFromName('manifest.json');
            if ($manifestJson === false) {
                throw new \RuntimeException('Invalid backup: manifest.json not found.');
            }

            $manifest = json_decode($manifestJson, true, 512, JSON_THROW_ON_ERROR);

            if (empty($manifest['documents'])) {
                throw new \RuntimeException('Backup manifest is empty or contains no documents.');
            }

            $categoriesJson = $zip->getFromName('categories.json');
            $categories     = $categoriesJson
                ? json_decode($categoriesJson, true, 512, JSON_THROW_ON_ERROR)
                : [];
        } catch (\Throwable $e) {
            $zip->close();
            return back()->with('error', $e->getMessage());
        }

        $scope     = $manifest['scope'] ?? ['category_label' => 'All', 'file_type' => 'all'];
        $documents = $manifest['documents'];
        $stats     = ['categories' => 0, 'documents' => 0, 'versions' => 0, 'files' => 0, 'skipped' => 0];

        DB::beginTransaction();
        try {
            // Step 1 – Categories (single query)
            $catNames     = array_filter(array_column($categories, 'name'));
            $existingCats = DocumentCategory::whereIn('name', $catNames)
                                            ->get(['id', 'name'])
                                            ->keyBy('name');

            $categoryIdMap = [];

            foreach ($categories as $cat) {
                if (empty($cat['name'])) {
                    continue;
                }
                if ($existingCats->has($cat['name'])) {
                    $categoryIdMap[$cat['id']] = $existingCats[$cat['name']]->id;
                } else {
                    $newCat = DocumentCategory::create([
                        'name'        => $cat['name'],
                        'description' => $cat['description'] ?? null,
                        'is_active'   => $cat['is_active'] ?? true,
                    ]);
                    $categoryIdMap[$cat['id']] = $newCat->id;
                    $existingCats->put($cat['name'], $newCat);
                    $stats['categories']++;
                }
            }

            // Step 2 – Bulk-check documents (single query)
            $backupDocIds = array_column($documents, 'id');
            $existingDocs = Document::withTrashed()
                                    ->whereIn('id', $backupDocIds)
                                    ->get(['id', 'title', 'owner_id', 'current_version_id'])
                                    ->keyBy('id');

            // Step 3 – Bulk-check versions (single query)
            $allVersionIds = [];
            foreach ($documents as $doc) {
                foreach ($doc['versions'] as $v) {
                    $allVersionIds[] = $v['id'];
                }
            }

            $existingVersionIds = DocumentVersion::whereIn('id', $allVersionIds)
                                                 ->pluck('id')
                                                 ->flip()
                                                 ->all();

            // Step 4 – Restore
            foreach ($documents as $docData) {
                $alreadyExists = $existingDocs->has($docData['id']);

                if ($alreadyExists && $request->input('mode') === 'skip') {
                    $stats['skipped']++;
                    continue;
                }

                $newCategoryId = $categoryIdMap[$docData['document_category_id']] ?? null;

                if ($alreadyExists) {
                    $doc = $existingDocs[$docData['id']];
                    $doc->update([
                        'owner_id'             => $docData['owner_id'],
                        'title'                => $docData['title'],
                        'description'          => $docData['description'],
                        'document_category_id' => $newCategoryId,
                        'uploaded_at'          => $docData['uploaded_at'],
                        'deleted_at'           => $docData['deleted_at'],
                    ]);
                } else {
                    $doc = Document::withTrashed()->forceCreate([
                        'id'                   => $docData['id'],
                        'owner_id'             => $docData['owner_id'],
                        'title'                => $docData['title'],
                        'description'          => $docData['description'],
                        'document_category_id' => $newCategoryId,
                        'uploaded_at'          => $docData['uploaded_at'],
                        'deleted_at'           => $docData['deleted_at'],
                    ]);
                }

                $stats['documents']++;
                $resolvedCurrentVersionId = null;

                foreach ($docData['versions'] as $versionData) {
                    $versionExists = isset($existingVersionIds[$versionData['id']]);

                    if ($versionExists && $request->input('mode') === 'skip') {
                        if ($versionData['id'] == $docData['current_version_id']) {
                            $resolvedCurrentVersionId = $versionData['id'];
                        }
                        continue;
                    }

                    if ($versionExists) {
                        DocumentVersion::where('id', $versionData['id'])->update([
                            'document_id'    => $doc->id,
                            'version_number' => $versionData['version_number'],
                            'file_path'      => $versionData['file_path'],
                            'file_name'      => $versionData['file_name'],
                            'mime_type'      => $versionData['mime_type'],
                            'file_size'      => $versionData['file_size'],
                            'change_notes'   => $versionData['change_notes'],
                            'uploaded_by'    => $versionData['uploaded_by'],
                        ]);
                    } else {
                        DocumentVersion::forceCreate([
                            'id'             => $versionData['id'],
                            'document_id'    => $doc->id,
                            'version_number' => $versionData['version_number'],
                            'file_path'      => $versionData['file_path'],
                            'file_name'      => $versionData['file_name'],
                            'mime_type'      => $versionData['mime_type'],
                            'file_size'      => $versionData['file_size'],
                            'change_notes'   => $versionData['change_notes'],
                            'uploaded_by'    => $versionData['uploaded_by'],
                        ]);
                    }

                    $stats['versions']++;

                    // Stream from ZIP — no full-file string in memory.
                    $stream = $zip->getStream("files/{$versionData['file_path']}");
                    if ($stream !== false) {
                        Storage::disk($this->backupDisk)->writeStream(
                            $versionData['file_path'],
                            $stream
                        );
                        fclose($stream);
                        $stats['files']++;
                    }

                    if ($versionData['id'] == $docData['current_version_id']) {
                        $resolvedCurrentVersionId = $versionData['id'];
                    }
                }

                if ($resolvedCurrentVersionId !== null) {
                    $doc->update(['current_version_id' => $resolvedCurrentVersionId]);
                }
            }

            RestoredBackup::updateOrCreate(
                ['backup_filename' => $filename],
                [
                    'backup_hash' => $backupHash,
                    'restored_by' => Auth::id(),
                    'restored_at' => now(),
                ]
            );

            DB::commit();
            $zip->close();

        } catch (\Throwable $e) {
            DB::rollBack();
            $zip->close();
            \Log::error('Backup restore failed', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }

        AuditLogger::log(
            'backup_restored',
            null,
            "Backup restored: {$stats['documents']} documents, {$stats['files']} files, {$stats['skipped']} skipped",
            [],
            array_merge($stats, ['scope' => $scope])
        );

        $parts = array_filter([
            'Restore complete!',
            "{$stats['categories']} categories,",
            "{$stats['documents']} documents,",
            "{$stats['versions']} versions,",
            "{$stats['files']} files restored.",
            $stats['skipped'] ? "{$stats['skipped']} skipped (already exist)." : '',
        ]);

        return redirect()->route('admin.document-backups.index')
            ->with('success', implode(' ', $parts));
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------
    public function destroy(string $filename)
    {
        $this->authorizeAccess();

        $filename = basename($filename);
        $path     = "{$this->backupFolder}/{$filename}";

        if (!Storage::disk($this->backupDisk)->exists($path)) {
            return back()->with('error', 'Backup not found.');
        }

        Storage::disk($this->backupDisk)->delete($path);
        AuditLogger::log('backup_deleted', null, "Backup deleted: {$filename}");

        return back()->with('success', "Backup {$filename} deleted.");
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function parseBackupSlug(string $basename): array
    {
        // v2 format: doc_backup__{categorySlug}__{filetypeSlug}__{date}__{time}
        if (str_contains($basename, '__')) {
            $parts = explode('__', $basename);
            return [$parts[1] ?? 'all', $parts[2] ?? 'all'];
        }

        // v1 legacy fallback
        $parts = explode('_', $basename, 6);
        return [$parts[2] ?? 'all', $parts[3] ?? 'all'];
    }

    private function authorizeAccess(): void
    {
        $user = Auth::user();

        if (
            $user->role->level !== 1
            && !in_array($user->role->name ?? '', ['System Administrator'], true)
            && !$user->hasPermission('documents.manage')
        ) {
            abort(403);
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i     = (int) floor(log($bytes, 1024));
        $i     = min($i, count($units) - 1);

        return round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }
}