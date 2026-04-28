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

    /**
     * Allowed file-type filter groups.
     * 'all' means no mime filtering.
     */
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
    // Index – list all saved backups
    //
    // O(N) over backup files.
    // Single listContents() call replaces 2N separate stat calls (size + lastModified).
    // -------------------------------------------------------------------------
    public function index()
    {
        $this->authorizeAccess();

        // ONE directory listing call → returns size + timestamp per entry.
        // Avoids the previous 2N filesystem round-trips (one size(), one lastModified() per file).
        $contents = collect(
            Storage::disk($this->backupDisk)->listContents($this->backupFolder)
        )->filter(fn ($item) => $item['type'] === 'file' && str_ends_with($item['path'], '.zip'));

        $backups = $contents->map(function ($item) {
            $basename = basename($item['path'], '.zip');

            // Filename format: doc_backup__{categorySlug}__{filetypeSlug}__{date}__{time}
            // Double-underscore delimiter avoids the single-underscore ambiguity when
            // category slugs themselves contain underscores (e.g. human_resources).
            [$categorySlug, $filetypeSlug] = $this->parseBackupSlug($basename);

            return [
                'filename'      => basename($item['path']),
                'path'          => $item['path'],
                'size'          => $this->formatBytes($item['file_size'] ?? $item['size'] ?? 0),
                'size_bytes'    => $item['file_size'] ?? $item['size'] ?? 0,
                'category_slug' => $categorySlug,
                'filetype_slug' => $filetypeSlug,
                'created_at'    => Carbon::createFromTimestamp($item['last_modified'] ?? $item['timestamp'] ?? now()->timestamp)
                                         ->format('M d, Y h:i A'),
                'created_ts'    => $item['last_modified'] ?? $item['timestamp'] ?? 0,
            ];
        })->sortByDesc('created_ts')->values()->all();

        // Stats for the summary bar – O(N) single pass, no extra queries.
        $totalBytes = array_sum(array_column($backups, 'size_bytes'));
        $stats = [
            'count'      => count($backups),
            'total_size' => $this->formatBytes($totalBytes),
            'latest'     => $backups[0]['created_at']  ?? null,
            'oldest'     => $backups ? end($backups)['created_at'] : null,
        ];

        // withCount avoids N+1 when rendering the doc-count badge per category.
        $categories     = DocumentCategory::withCount('documents')->orderBy('name')->get();
        $fileTypeGroups = array_keys($this->fileTypeGroups);

        return view('admin.document-backups.index', compact('backups', 'categories', 'fileTypeGroups', 'stats'));
    }

    // -------------------------------------------------------------------------
    // Create – generate a scoped backup ZIP
    //
    // CATEGORY FILTER : pass `category_ids[]` (array) to back up only those
    //   categories. Omit for all categories.
    //
    // FILE-TYPE FILTER: pass `file_type` slug; applies at the version level.
    //
    // Filename uses double-underscore delimiter:
    //   doc_backup__{categorySlug}__{filetypeSlug}__{timestamp}.zip
    // -------------------------------------------------------------------------
    public function create(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'category_ids'   => 'nullable|array',
            'category_ids.*' => 'exists:document_categories,id',
            'file_type'      => 'nullable|in:' . implode(',', array_keys($this->fileTypeGroups)),
        ]);

        $isAjax    = $request->ajax() || $request->wantsJson();
        $startTime = microtime(true);

        // ------------------------------------------------------------------
        // Resolve filters
        // ------------------------------------------------------------------
        $categoryIds  = array_filter((array) $request->input('category_ids', []));
        $fileTypeKey  = $request->input('file_type', 'all');
        $allowedMimes = $this->fileTypeGroups[$fileTypeKey] ?? [];

        // Build slug + label from selected categories.
        // O(K) where K = selected categories — single whereIn query.
        $categorySlug  = 'all';
        $categoryLabel = 'All Categories';

        if (!empty($categoryIds)) {
            // Single query, keyed by id for O(1) lookups later if needed.
            $cats = DocumentCategory::whereIn('id', $categoryIds)
                                    ->orderBy('name')
                                    ->get(['id', 'name']);

            $categorySlug  = $cats->map(fn ($c) => Str::slug($c->name, '_'))->join('--');
            $categoryLabel = $cats->pluck('name')->join(', ');
        }

        // ------------------------------------------------------------------
        // Build ZIP filename with double-underscore delimiters so multi-word
        // category slugs (human_resources) never break parsing.
        // ------------------------------------------------------------------
        $timestamp = now()->format('Y-m-d_His');
        $zipName   = "doc_backup__{$categorySlug}__{$fileTypeKey}__{$timestamp}.zip";
        $tmpPath   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

        $zip = new ZipArchive;
        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $error = 'Failed to create backup archive. Check server permissions.';
            return $isAjax
                ? response()->json(['success' => false, 'error' => $error], 500)
                : back()->with('error', $error);
        }

        // ------------------------------------------------------------------
        // 1. Categories JSON – always dump ALL categories so restore can
        //    re-map IDs even when the backup is category-scoped.
        //    Single query, no N+1.
        // ------------------------------------------------------------------
        $allCategories = DocumentCategory::all(['id', 'name', 'description', 'is_active'])->toArray();
        $zip->addFromString('categories.json', json_encode($allCategories, JSON_PRETTY_PRINT));

        // ------------------------------------------------------------------
        // 2. Documents + versions – eager-load everything in ONE query set.
        //
        //    withTrashed + with(['versions']) = 2 queries total regardless
        //    of document count (Eloquent batch-loads versions via whereIn).
        //    Avoids the previous implicit N+1 on version relationships.
        //
        //    Mime filter applied in-memory on the already-loaded collection —
        //    no extra queries per document.
        // ------------------------------------------------------------------
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

        // ------------------------------------------------------------------
        // 3. Manifest JSON
        // ------------------------------------------------------------------
        $zip->addFromString('manifest.json', json_encode([
            'backup_version' => '2.0',
            'created_at'     => now()->toISOString(),
            'created_by'     => Auth::user()->full_name ?? Auth::user()->email,
            'scope'          => [
                'category_ids'   => array_values($categoryIds),
                'category_label' => $categoryLabel,
                'file_type'      => $fileTypeKey,
                'allowed_mimes'  => $allowedMimes,
            ],
            'document_count' => count($documents),
            'documents'      => $documents,
        ], JSON_PRETTY_PRINT));

        // ------------------------------------------------------------------
        // 4. Physical files – streamed directly into the ZIP, no full-file
        //    string in memory. ZipArchive::addFromString() loads the entire
        //    file; instead we use a temp-file bridge so the ZIP deflate
        //    stream can consume the storage stream incrementally.
        // ------------------------------------------------------------------
        $fileCount = 0;
        foreach ($documents as $doc) {
            foreach ($doc['versions'] as $version) {
                $filePath = $version['file_path'];
                if (!Storage::disk($this->backupDisk)->exists($filePath)) {
                    continue;
                }

                // Retrieve a stream resource from the storage driver.
                $stream = Storage::disk($this->backupDisk)->readStream($filePath);
                if (!is_resource($stream)) {
                    continue;
                }

                // Write stream to a per-file temp path then add to ZIP,
                // then unlink immediately — keeps peak RAM to one file at a time.
                $tmpFile = tempnam(sys_get_temp_dir(), 'bkf_');
                $dest    = fopen($tmpFile, 'wb');
                stream_copy_to_stream($stream, $dest);
                fclose($dest);
                fclose($stream);

                $zip->addFile($tmpFile, "files/{$filePath}");
                // Store temp path for cleanup after zip->close()
                $tempFiles[] = $tmpFile;
                $fileCount++;
            }
        }

        $zip->close();

        // Cleanup per-file temp files now that the ZIP is sealed.
        foreach ($tempFiles ?? [] as $tmp) {
            @unlink($tmp);
        }

        // ------------------------------------------------------------------
        // 5. Stream ZIP to permanent storage – single stream, no full read.
        // ------------------------------------------------------------------
        $destination = "{$this->backupFolder}/{$zipName}";
        try {
            $stream = @fopen($tmpPath, 'rb');
            if ($stream === false) {
                throw new \RuntimeException('Failed to open backup file for streaming.');
            }
            Storage::disk($this->backupDisk)->writeStream($destination, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        } catch (\Throwable $e) {
            @unlink($tmpPath);
            $error = 'Failed to save backup: ' . $e->getMessage();
            return $isAjax
                ? response()->json(['success' => false, 'error' => $error], 500)
                : back()->with('error', $error);
        }

        @unlink($tmpPath);

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
    // Download – stream a backup ZIP to the browser
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
    // Restore – upload a backup ZIP and restore documents
    //
    // Key optimizations:
    //  • categories restored/mapped in a single pass with a keyed lookup array
    //  • documents bulk-checked with a single whereIn instead of per-doc queries
    //  • versions bulk-checked the same way
    //  • files streamed via ZipArchive::getStream() — no full-file string in memory
    //  • current_version_id always set unconditionally (fixes silent-skip bug)
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

        // Duplicate-restore guard.
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

        // ------------------------------------------------------------------
        // Parse manifest + categories upfront before opening a transaction.
        // ------------------------------------------------------------------
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
            // ------------------------------------------------------------------
            // Step 1 – Restore categories
            //
            // O(C) — single query to fetch all existing categories by name,
            // then one INSERT per genuinely new category. No per-category query.
            // ------------------------------------------------------------------
            $catNames    = array_filter(array_column($categories, 'name'));
            $existingCats = DocumentCategory::whereIn('name', $catNames)
                                            ->get(['id', 'name'])
                                            ->keyBy('name'); // O(1) lookups

            $categoryIdMap = []; // old_id → new_id

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
                    // Add to the keyed collection so subsequent duplicates within
                    // the same backup don't trigger a second INSERT.
                    $existingCats->put($cat['name'], $newCat);
                    $stats['categories']++;
                }
            }

            // ------------------------------------------------------------------
            // Step 2 – Bulk-check which documents already exist
            //
            // O(D) — single query with compound (title, owner_id) pairs via
            // a whereIn on doc IDs from the backup. Avoids one query per document.
            // ------------------------------------------------------------------
            $backupDocIds = array_column($documents, 'id');

            // Fetch existing docs by their original backup IDs (updateOrCreate
            // preserves IDs, so the IDs are stable across restores).
            $existingDocs = Document::withTrashed()
                                    ->whereIn('id', $backupDocIds)
                                    ->get(['id', 'title', 'owner_id', 'current_version_id'])
                                    ->keyBy('id'); // O(1) lookups

            // ------------------------------------------------------------------
            // Step 3 – Bulk-check which versions already exist
            //
            // O(V) — single query for all version IDs in the backup.
            // ------------------------------------------------------------------
            $allVersionIds = [];
            foreach ($documents as $doc) {
                foreach ($doc['versions'] as $v) {
                    $allVersionIds[] = $v['id'];
                }
            }

            $existingVersionIds = DocumentVersion::whereIn('id', $allVersionIds)
                                                 ->pluck('id')
                                                 ->flip() // flip to Set for O(1) has()
                                                 ->all();

            // ------------------------------------------------------------------
            // Step 4 – Restore documents + versions
            // ------------------------------------------------------------------
            foreach ($documents as $docData) {
                $alreadyExists = $existingDocs->has($docData['id']);

                if ($alreadyExists && $request->input('mode') === 'skip') {
                    $stats['skipped']++;
                    continue;
                }

                $newCategoryId = $categoryIdMap[$docData['document_category_id']] ?? null;

                if ($alreadyExists) {
                    // Merge mode — update in place.
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
                    // New document — insert with the original ID preserved.
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

                // Track which version should become current.
                $resolvedCurrentVersionId = null;

                foreach ($docData['versions'] as $versionData) {
                    $versionExists = isset($existingVersionIds[$versionData['id']]);

                    if ($versionExists && $request->input('mode') === 'skip') {
                        // Still need to check if this is the current version.
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

                    // Stream file from ZIP — no full-file string in memory.
                    // Fixes the OOM bug from getFromName() on large files.
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

                // Always set current_version_id if resolved — fixes the silent-skip bug
                // where the old guard ($doc->current_version_id != $new) could prevent
                // the update when IDs matched the pre-existing stale value.
                if ($resolvedCurrentVersionId !== null) {
                    $doc->update(['current_version_id' => $resolvedCurrentVersionId]);
                }
            }

            // Record restore — inside transaction so it rolls back on failure.
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
    // Destroy – delete a backup from server
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

    /**
     * Parse category + filetype slugs from a backup filename.
     *
     * New format (v2): doc_backup__{categorySlug}__{filetypeSlug}__{date}__{time}
     * Legacy format (v1): doc_backup_{categorySlug}_{filetypeSlug}_{date}_{time}
     *
     * The double-underscore delimiter allows category slugs that themselves
     * contain underscores (e.g. human_resources) to parse correctly.
     */
    private function parseBackupSlug(string $basename): array
    {
        // v2 format
        if (str_contains($basename, '__')) {
            $parts        = explode('__', $basename);
            $categorySlug = $parts[1] ?? 'all';
            $filetypeSlug = $parts[2] ?? 'all';
            return [$categorySlug, $filetypeSlug];
        }

        // v1 legacy fallback — best-effort, limit=6 as before
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