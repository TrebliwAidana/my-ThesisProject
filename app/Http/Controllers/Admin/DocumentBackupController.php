<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentVersion;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\RestoredBackup;
use ZipArchive;

class DocumentBackupController extends Controller
{
    private string $backupDisk   = 'private';
    private string $backupFolder = 'backups/documents';

    // -------------------------------------------------------------------------
    // Index – list all saved backups
    // -------------------------------------------------------------------------
    public function index()
    {
        $this->authorizeAccess();

        $files   = Storage::disk($this->backupDisk)->files($this->backupFolder);
        $backups = [];

        foreach ($files as $file) {
            if (!str_ends_with($file, '.zip')) continue;

            $backups[] = [
                'filename'   => basename($file),
                'path'       => $file,
                'size'       => $this->formatBytes(Storage::disk($this->backupDisk)->size($file)),
                'created_at' => \Carbon\Carbon::createFromTimestamp(
                    Storage::disk($this->backupDisk)->lastModified($file)
                )->format('M d, Y h:i A'),
            ];
        }

        // Newest first
        usort($backups, fn($a, $b) => strcmp($b['filename'], $a['filename']));

        return view('admin.document-backups.index', compact('backups'));
    }

    // -------------------------------------------------------------------------
    // Create – generate a new backup ZIP
    // -------------------------------------------------------------------------
    public function create()
    {
        $this->authorizeAccess();

        $isAjax = request()->ajax() || request()->wantsJson();
        $startTime = microtime(true);

        $timestamp  = now()->format('Y-m-d_His');
        $zipName    = "doc_backup_{$timestamp}.zip";
        $tmpPath    = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $error = 'Failed to create backup archive. Check server permissions.';
            if ($isAjax) {
                return response()->json(['success' => false, 'error' => $error], 500);
            }
            return back()->with('error', $error);
        }

        // 1. Categories JSON
        $categories = DocumentCategory::all()->toArray();
        $zip->addFromString('categories.json', json_encode($categories, JSON_PRETTY_PRINT));

        // 2. Documents + versions manifest (no is_public column)
        $documents = Document::withTrashed()
            ->with(['versions', 'category', 'owner'])
            ->get()
            ->map(fn($doc) => [
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
                'versions'             => $doc->versions->map(fn($v) => [
                    'id'             => $v->id,
                    'document_id'    => $v->document_id,
                    'version_number' => $v->version_number,
                    'file_path'      => $v->file_path,
                    'file_name'      => $v->file_name,
                    'mime_type'      => $v->mime_type,
                    'file_size'      => $v->file_size,
                    'change_notes'   => $v->change_notes,
                    'uploaded_by'    => $v->uploaded_by,
                    'created_at'     => $v->created_at,
                ])->toArray(),
            ])->toArray();

        $zip->addFromString('manifest.json', json_encode([
            'backup_version' => '1.0',
            'created_at'     => now()->toISOString(),
            'created_by'     => Auth::user()->full_name ?? Auth::user()->email,
            'document_count' => count($documents),
            'documents'      => $documents,
        ], JSON_PRETTY_PRINT));

        // 3. Physical files
        $fileCount = 0;
        foreach ($documents as $doc) {
            foreach ($doc['versions'] as $version) {
                $filePath = $version['file_path'];
                if (Storage::disk($this->backupDisk)->exists($filePath)) {
                    $fileContents = Storage::disk($this->backupDisk)->get($filePath);
                    $zip->addFromString("files/{$filePath}", $fileContents);
                    $fileCount++;
                }
            }
        }

        $zip->close();

        // 4. Move ZIP to permanent backup storage
        $destination = "{$this->backupFolder}/{$zipName}";
        Storage::disk($this->backupDisk)->put($destination, file_get_contents($tmpPath));
        @unlink($tmpPath);

        $elapsed = microtime(true) - $startTime;

        AuditLogger::log(
            'backup_created',
            null,
            "Document backup created: {$zipName} ({$fileCount} files, " . count($documents) . ' documents)',
            [],
            ['filename' => $zipName, 'elapsed_seconds' => round($elapsed, 2)]
        );

        if ($isAjax) {
            return response()->json([
                'success'  => true,
                'filename' => $zipName,
                'elapsed'  => round($elapsed, 2),
                'message'  => "Backup created successfully in " . round($elapsed, 2) . " seconds."
            ]);
        }

        return redirect()->route('admin.document-backups.index')
            ->with('success', "Backup created successfully: {$zipName} ({$fileCount} files backed up)");
    }
    // -------------------------------------------------------------------------
    // Download – stream a backup ZIP to the browser
    // -------------------------------------------------------------------------
    public function download(string $filename)
    {
        $this->authorizeAccess();

        // Sanitize filename – no path traversal
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
    // -------------------------------------------------------------------------
    public function restore(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:512000',
            'mode'        => 'required|in:merge,skip',
            'force_restore' => 'sometimes|boolean',
        ]);

        $filename = $request->file('backup_file')->getClientOriginalName();

        // Check for duplicate restore unless forced
        if (!$request->boolean('force_restore')) {
            $existing = RestoredBackup::where('backup_filename', $filename)->first();
            if ($existing) {
                $restorer = $existing->restoredBy->full_name ?? 'User #' . $existing->restored_by;
                return back()->with('error', sprintf(
                    'Backup "%s" was already restored on %s by %s. Use "Force restore" checkbox to override.',
                    $filename,
                    $existing->restored_at->format('Y-m-d H:i:s'),
                    $restorer
                ));
            }
        }

        $tmpPath = $request->file('backup_file')->getPathname();
        $zip = new ZipArchive();

        if ($zip->open($tmpPath) !== true) {
            return back()->with('error', 'Invalid or corrupted ZIP file.');
        }

        // Read manifest
        $manifestJson = $zip->getFromName('manifest.json');
        if (!$manifestJson) {
            $zip->close();
            return back()->with('error', 'Invalid backup: manifest.json not found.');
        }

        $manifest = json_decode($manifestJson, true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($manifest['documents'])) {
            $zip->close();
            return back()->with('error', 'Backup manifest is corrupted or empty.');
        }

        // Read categories
        $categoriesJson = $zip->getFromName('categories.json');
        $categories = $categoriesJson ? json_decode($categoriesJson, true) : [];

        $stats = ['categories' => 0, 'documents' => 0, 'versions' => 0, 'files' => 0, 'skipped' => 0];

        DB::beginTransaction();
        try {
            // Restore categories (by name, skip duplicates)
            $categoryIdMap = [];
            foreach ($categories as $cat) {
                $existing = DocumentCategory::where('name', $cat['name'])->first();
                if ($existing) {
                    $categoryIdMap[$cat['id']] = $existing->id;
                } else {
                    $newCat = DocumentCategory::create([
                        'name'        => $cat['name'],
                        'description' => $cat['description'] ?? null,
                        'is_active'   => $cat['is_active'] ?? true,
                    ]);
                    $categoryIdMap[$cat['id']] = $newCat->id;
                    $stats['categories']++;
                }
            }

            // Restore documents
            foreach ($manifest['documents'] as $docData) {
                $existingDoc = Document::withTrashed()
                    ->where('title', $docData['title'])
                    ->where('owner_id', $docData['owner_id'])
                    ->first();

                if ($existingDoc && $request->mode === 'skip') {
                    $stats['skipped']++;
                    continue;
                }

                $newCategoryId = null;
                if ($docData['document_category_id']) {
                    $newCategoryId = $categoryIdMap[$docData['document_category_id']]
                        ?? $docData['document_category_id'];
                }

                $doc = Document::withTrashed()->updateOrCreate(
                    ['id' => $docData['id']],
                    [
                        'owner_id'             => $docData['owner_id'],
                        'title'                => $docData['title'],
                        'description'          => $docData['description'],
                        'document_category_id' => $newCategoryId,
                        'uploaded_at'          => $docData['uploaded_at'],
                        'deleted_at'           => $docData['deleted_at'],
                    ]
                );
                $stats['documents']++;

                $newCurrentVersionId = null;
                foreach ($docData['versions'] as $versionData) {
                    $version = DocumentVersion::updateOrCreate(
                        ['id' => $versionData['id']],
                        [
                            'document_id'    => $doc->id,
                            'version_number' => $versionData['version_number'],
                            'file_path'      => $versionData['file_path'],
                            'file_name'      => $versionData['file_name'],
                            'mime_type'      => $versionData['mime_type'],
                            'file_size'      => $versionData['file_size'],
                            'change_notes'   => $versionData['change_notes'],
                            'uploaded_by'    => $versionData['uploaded_by'],
                        ]
                    );
                    $stats['versions']++;

                    $zipFilePath = "files/{$versionData['file_path']}";
                    $fileData = $zip->getFromName($zipFilePath);
                    if ($fileData !== false) {
                        Storage::disk($this->backupDisk)->put($versionData['file_path'], $fileData);
                        $stats['files']++;
                    }

                    if ($versionData['id'] == $docData['current_version_id']) {
                        $newCurrentVersionId = $version->id;
                    }
                }

                if ($newCurrentVersionId && $doc->current_version_id != $newCurrentVersionId) {
                    $doc->update(['current_version_id' => $newCurrentVersionId]);
                }
            }

            DB::commit();
            $zip->close();

            // Record this restore
            $hash = hash_file('sha256', $tmpPath);
            RestoredBackup::updateOrCreate(
                ['backup_filename' => $filename],
                [
                    'backup_hash' => $hash,
                    'restored_by' => auth()->id(),
                    'restored_at' => now(),
                ]
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            $zip->close();
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }

        AuditLogger::log(
            'backup_restored',
            null,
            "Backup restored: {$stats['documents']} documents, {$stats['files']} files, {$stats['skipped']} skipped",
            [],
            $stats
        );

        return redirect()->route('admin.document-backups.index')
            ->with('success', implode(' ', [
                "Restore complete!",
                "{$stats['categories']} categories,",
                "{$stats['documents']} documents,",
                "{$stats['versions']} versions,",
                "{$stats['files']} files restored.",
                $stats['skipped'] ? "{$stats['skipped']} documents skipped (already exist)." : '',
            ]));
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
    private function authorizeAccess(): void
    {
        $user = Auth::user();
        $isAdmin  = $user->role->level === 1 || in_array($user->role->name ?? '', ['System Administrator']);
        $canManage = $user->hasPermission('documents.manage');

        if (!$isAdmin && !$canManage) {
            abort(403);
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}