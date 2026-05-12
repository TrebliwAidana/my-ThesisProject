<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Financial\FinancialHelperTrait;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\DocumentVersion;
use App\Models\FinancialTransaction;
use App\Models\RestoredBackup;
use App\Services\AuditLogger;
use App\Services\CloudinaryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;

class DocumentBackupController extends Controller
{
    use FinancialHelperTrait;

    // ✅ No more local disk — backups are JSON metadata only
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
        $this->requirePermission('backups.view');

        // ✅ Load backups from DB instead of local disk
        $backups = \App\Models\DocumentBackup::with('creator')
            ->latest()
            ->get()
            ->map(fn ($b) => [
                'id'                 => $b->id,
                'filename'           => $b->filename,
                'cloudinary_url'     => $b->cloudinary_url,
                'cloudinary_public_id' => $b->cloudinary_public_id,
                'size'               => $this->formatBytes($b->size_bytes),
                'size_bytes'         => $b->size_bytes,
                'category_slug'      => $b->category_slug,
                'filetype_slug'      => $b->file_type,
                'has_financial_data' => $b->financial_count > 0,
                'document_count'     => $b->document_count,
                'financial_count'    => $b->financial_count,
                'created_at'         => $b->created_at->format('M d, Y h:i A'),
                'created_ts'         => $b->created_at->timestamp,
                'creator'            => $b->creator?->full_name,
            ]);

        $totalBytes = $backups->sum('size_bytes');
        $stats = [
            'count'      => $backups->count(),
            'total_size' => $this->formatBytes($totalBytes),
            'latest'     => $backups->first()['created_at'] ?? null,
            'oldest'     => $backups->last()['created_at']  ?? null,
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
        $this->requirePermission('backups.create');

        $isAjax = $request->ajax() || $request->wantsJson();

        try {
            $request->validate([
                'category_ids'        => 'nullable|array',
                'category_ids.*'      => 'exists:document_categories,id',
                'file_type'           => 'nullable|in:' . implode(',', array_keys($this->fileTypeGroups)),
                'include_financials'  => 'nullable|boolean',
                'financial_status'    => 'nullable|array',
                'financial_status.*'  => 'in:pending,audited,approved,rejected,paid',
                'financial_type'      => 'nullable|array',
                'financial_type.*'    => 'in:income,expense,receivable',
                'financial_date_from' => 'nullable|date',
                'financial_date_to'   => 'nullable|date|after_or_equal:financial_date_from',
            ]);
        } catch (\Throwable $e) {
            return $isAjax
                ? response()->json(['success' => false, 'error' => $e->getMessage()], 422)
                : back()->with('error', $e->getMessage());
        }

        $startTime = microtime(true);

        $categoryIds  = array_values(array_filter((array) $request->input('category_ids', [])));
        $fileTypeKey  = $request->input('file_type', 'all');
        $allowedMimes = $this->fileTypeGroups[$fileTypeKey] ?? [];

        $categorySlug  = 'all';
        $categoryLabel = 'All Categories';

        if (! empty($categoryIds)) {
            $cats = DocumentCategory::whereIn('id', $categoryIds)
                ->orderBy('name')
                ->get(['id', 'name']);
            $categorySlug  = $cats->map(fn ($c) => Str::slug($c->name, '_'))->join('--');
            $categoryLabel = $cats->pluck('name')->join(', ');
        }

        $includeFinancials = $request->boolean('include_financials', true);
        $financialStatuses = $request->input('financial_status', []);
        $financialTypes    = $request->input('financial_type', []);
        $financialDateFrom = $request->input('financial_date_from');
        $financialDateTo   = $request->input('financial_date_to');

        $timestamp = now()->format('Y-m-d_His');
        $zipName   = "doc_backup__{$categorySlug}__{$fileTypeKey}__{$timestamp}.zip";
        $tmpPath   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $zipName;

        try {
            $zip = new ZipArchive;
            if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Failed to create ZIP archive.');
            }

            // ── Categories metadata ───────────────────────────────────────
            $allCategories = DocumentCategory::all(['id', 'name', 'description', 'is_active'])->toArray();
            $zip->addFromString('categories.json', json_encode($allCategories, JSON_PRETTY_PRINT));

            // ── Documents metadata (Cloudinary URLs stored as file_path) ──
            $documents = Document::withTrashed()
                ->with(['versions' => fn ($q) => $q->select([
                    'id', 'document_id', 'version_number', 'file_path',
                    'cloudinary_public_id', 'file_name', 'mime_type',
                    'file_size', 'change_notes', 'uploaded_by', 'created_at',
                ])])
                ->when(! empty($categoryIds), fn ($q) => $q->whereIn('document_category_id', $categoryIds))
                ->get([
                    'id', 'owner_id', 'current_version_id', 'title', 'description',
                    'document_category_id', 'tags', 'uploaded_at', 'created_at',
                    'updated_at', 'deleted_at',
                ])
                ->map(fn ($doc) => [
                    'id'                   => $doc->id,
                    'owner_id'             => $doc->owner_id,
                    'current_version_id'   => $doc->current_version_id,
                    'title'                => $doc->title,
                    'description'          => $doc->description,
                    'document_category_id' => $doc->document_category_id,
                    'tags'                 => $doc->tags,
                    'uploaded_at'          => $doc->uploaded_at,
                    'created_at'           => $doc->created_at,
                    'updated_at'           => $doc->updated_at,
                    'deleted_at'           => $doc->deleted_at,
                    'versions'             => $doc->versions
                        ->when(
                            ! empty($allowedMimes),
                            fn ($col) => $col->filter(fn ($v) => in_array($v->mime_type, $allowedMimes))
                        )
                        ->values()
                        ->map(fn ($v) => [
                            'id'                   => $v->id,
                            'document_id'          => $v->document_id,
                            'version_number'       => $v->version_number,
                            'file_path'            => $v->file_path,            // ✅ Cloudinary URL
                            'cloudinary_public_id' => $v->cloudinary_public_id, // ✅ for re-deletion if needed
                            'file_name'            => $v->file_name,
                            'mime_type'            => $v->mime_type,
                            'file_size'            => $v->file_size,
                            'change_notes'         => $v->change_notes,
                            'uploaded_by'          => $v->uploaded_by,
                            'created_at'           => $v->created_at,
                        ])
                        ->toArray(),
                ])
                ->all();

            // ── Financial transactions ────────────────────────────────────
            $financialData     = [];
            $financialCount    = 0;
            $financialSnapshot = [
                'included'     => false,
                'statuses'     => [],
                'types'        => [],
                'date_from'    => null,
                'date_to'      => null,
                'record_count' => 0,
            ];

            if ($includeFinancials) {
                $financialData = FinancialTransaction::withTrashed()
                    ->when(! empty($financialStatuses), fn ($q) => $q->whereIn('status', $financialStatuses))
                    ->when(! empty($financialTypes),    fn ($q) => $q->whereIn('type', $financialTypes))
                    ->when($financialDateFrom,          fn ($q) => $q->whereDate('transaction_date', '>=', $financialDateFrom))
                    ->when($financialDateTo,            fn ($q) => $q->whereDate('transaction_date', '<=', $financialDateTo))
                    ->get()
                    ->map(fn ($ft) => [
                        'id'               => $ft->id,
                        'type'             => $ft->type,
                        'user_id'          => $ft->user_id,
                        'status'           => $ft->status,
                        'description'      => $ft->description,
                        'amount'           => $ft->amount,
                        'category'         => $ft->category,
                        'transaction_date' => $ft->transaction_date,
                        'notes'            => $ft->notes,
                        'approved_by'      => $ft->approved_by,
                        'approved_at'      => $ft->approved_at,
                        'audited_by'       => $ft->audited_by,
                        'audited_at'       => $ft->audited_at,
                        'customer_name'    => $ft->customer_name,
                        'due_date'         => $ft->due_date,
                        'deleted_at'       => $ft->deleted_at,
                        'created_at'       => $ft->created_at,
                        'updated_at'       => $ft->updated_at,
                    ])
                    ->all();

                $financialCount    = count($financialData);
                $financialSnapshot = [
                    'included'     => true,
                    'statuses'     => $financialStatuses,
                    'types'        => $financialTypes,
                    'date_from'    => $financialDateFrom,
                    'date_to'      => $financialDateTo,
                    'record_count' => $financialCount,
                ];

                $zip->addFromString(
                    'financial_transactions.json',
                    json_encode($financialData, JSON_PRETTY_PRINT)
                );
            }

            // ── Manifest ──────────────────────────────────────────────────
            $zip->addFromString('manifest.json', json_encode([
                'backup_version' => '5.0', // ✅ Cloudinary-aware, no physical files
                'storage'        => 'cloudinary', // ✅ Flag for restore to know file_path = URL
                'created_at'     => now()->toISOString(),
                'created_by'     => Auth::user()->email,
                'scope'          => [
                    'category_ids'   => $categoryIds,
                    'category_label' => $categoryLabel,
                    'file_type'      => $fileTypeKey,
                    'allowed_mimes'  => $allowedMimes,
                ],
                'financial'      => $financialSnapshot,
                'document_count' => count($documents),
                'documents'      => $documents,
                // ✅ No 'files' section — Cloudinary URLs are permanent
            ], JSON_PRETTY_PRINT));

            $zip->close();

            // ✅ Upload ZIP to Cloudinary instead of local disk
            $cloudinary = new CloudinaryService();
            $zipFile    = new \Illuminate\Http\UploadedFile(
                $tmpPath, $zipName, 'application/zip', null, true
            );
            $uploaded = $cloudinary->upload($zipFile, 'vsulhs-sslg/backups');

            $sizeBytes = filesize($tmpPath);

            // ✅ Save backup record to DB
            \App\Models\DocumentBackup::create([
                'filename'             => $zipName,
                'cloudinary_url'       => $uploaded['url'],
                'cloudinary_public_id' => $uploaded['public_id'],
                'category_slug'        => $categorySlug,
                'category_label'       => $categoryLabel,
                'file_type'            => $fileTypeKey,
                'document_count'       => count($documents),
                'financial_count'      => $financialCount,
                'file_count'           => 0, // ✅ No physical files — Cloudinary handles storage
                'size_bytes'           => $sizeBytes,
                'created_by'           => Auth::id(),
            ]);

        } catch (\Throwable $e) {
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
        } finally {
            @unlink($tmpPath);
        }

        $elapsed = round(microtime(true) - $startTime, 2);

        AuditLogger::log(
            'backup_created',
            null,
            "Document backup created: {$zipName} (category: {$categoryLabel}, type: {$fileTypeKey}, " . count($documents) . " documents, {$financialCount} financial records)",
            [],
            [
                'filename'        => $zipName,
                'category_ids'    => $categoryIds,
                'category_label'  => $categoryLabel,
                'file_type'       => $fileTypeKey,
                'financial'       => $financialSnapshot,
                'elapsed_seconds' => $elapsed,
            ]
        );

        if ($isAjax) {
            return response()->json([
                'success'         => true,
                'filename'        => $zipName,
                'elapsed'         => $elapsed,
                'financial_count' => $financialCount,
                'message'         => "Backup created in {$elapsed}s — " . count($documents) . " documents, {$financialCount} financial records from \"{$categoryLabel}\" ({$fileTypeKey}).",
            ]);
        }

        return redirect()->route('admin.document-backups.index')
            ->with('success', "Backup created: {$zipName} (" . count($documents) . " documents · {$financialCount} financial records · category: {$categoryLabel} · type: {$fileTypeKey})");
    }

    // -------------------------------------------------------------------------
    // Download — redirect to Cloudinary URL
    // -------------------------------------------------------------------------

    public function download(string $filename)
    {
        $this->requirePermission('backups.view');

        // ✅ Find backup in DB and redirect to Cloudinary URL
        $backup = \App\Models\DocumentBackup::where('filename', basename($filename))->firstOrFail();

        AuditLogger::log('backup_downloaded', null, "Backup downloaded: {$filename}");

        return redirect($backup->cloudinary_url);
    }

    // -------------------------------------------------------------------------
    // Restore — DB records only, no file restoration needed
    // -------------------------------------------------------------------------

    // -------------------------------------------------------------------------
    // Restore — DB records only, no file restoration needed
    // -------------------------------------------------------------------------

    public function restore(Request $request)
    {
        $this->requirePermission('backups.restore');

        $request->validate([
            'backup_file'              => 'required|file|mimes:zip|max:512000',
            'mode'                     => 'required|in:merge,skip',
            'force_restore'            => 'sometimes|boolean',
            'restore_financials'       => 'sometimes|boolean',
            'financial_restore_status' => 'sometimes|in:as_is,force_pending',
        ]);

        $uploadedFile = $request->file('backup_file');

        // ✅ Fix 1: Sanitize filename — prevents '--' in filename
        // from being interpreted as a SQL comment by PostgreSQL
        $filename = trim(basename($uploadedFile->getClientOriginalName()));

        if (empty($filename)) {
            return back()->with('error', 'Invalid backup filename.');
        }

        $tmpPath    = $uploadedFile->getPathname();
        $backupHash = hash_file('sha256', $tmpPath);

        ini_set('max_execution_time', 300);

        // ✅ Fix 2: Force a clean PostgreSQL connection state
        // DB::rollBack() alone doesn't clear an aborted transaction on pgsql
        try { DB::rollBack(); } catch (\Throwable) {}
        DB::reconnect(); // ← clears any aborted transaction block

        if (! $request->boolean('force_restore')) {
            try {
                $existing = RestoredBackup::where('backup_filename', $filename)->first();
                if ($existing) {
                    $restorer = $existing->restoredBy?->email ?? 'User #' . $existing->restored_by;
                    return back()->with('error', sprintf(
                        'Backup "%s" was already restored on %s by %s. Tick "Force restore" to override.',
                        $filename,
                        $existing->restored_at->format('Y-m-d H:i:s'),
                        $restorer
                    ));
                }
            } catch (\Throwable $e) {
                return back()->with('error', 'Failed to check restore history: ' . $e->getMessage());
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

            $financialJson = $zip->getFromName('financial_transactions.json');
            $financialRows = ($financialJson && $request->boolean('restore_financials', true))
                ? json_decode($financialJson, true, 512, JSON_THROW_ON_ERROR)
                : [];

        } catch (\Throwable $e) {
            $zip->close();
            return back()->with('error', $e->getMessage());
        }

        $scope                  = $manifest['scope'] ?? ['category_label' => 'All', 'file_type' => 'all'];
        $documents              = $manifest['documents'];
        $financialRestoreStatus = $request->input('financial_restore_status', 'as_is');
        $actorUser              = Auth::user();
        $isPostgres             = DB::getDriverName() === 'pgsql';

        $stats = [
            'categories'               => 0,
            'documents'                => 0,
            'versions'                 => 0,
            'skipped'                  => 0,
            'financial'                => 0,
            'fin_skipped'              => 0,
            'financial_docs_generated' => 0,
        ];

        try {
            DB::beginTransaction();

            // ── Step 1: Restore categories ────────────────────────────────
            $catNames     = array_filter(array_column($categories, 'name'));
            $existingCats = DocumentCategory::whereIn('name', $catNames)
                ->get(['id', 'name'])
                ->keyBy('name');

            $categoryIdMap = [];

            foreach ($categories as $cat) {
                if (empty($cat['name'])) continue;

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

            // ── Step 2: Bulk-check existing documents ─────────────────────
            $backupDocIds = array_column($documents, 'id');
            $existingDocs = Document::withTrashed()
                ->whereIn('id', $backupDocIds)
                ->get(['id'])
                ->keyBy('id');

            // ── Step 3: Bulk-check existing versions ──────────────────────
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

            // ── Step 4: Restore documents and versions ────────────────────
            $documentsForUpsert = [];
            $versionsForUpsert  = [];
            $docVersionUpdates  = [];

            foreach ($documents as $docData) {
                $alreadyExists = $existingDocs->has($docData['id']);

                if ($alreadyExists && $request->input('mode') === 'skip') {
                    $stats['skipped']++;
                    continue;
                }

                $newCategoryId = $categoryIdMap[$docData['document_category_id']] ?? null;

                // ✅ Fix tags for PostgreSQL — must be valid JSON string
                $tags = $docData['tags'] ?? null;
                if (is_array($tags)) {
                    $tags = json_encode($tags);
                } elseif (is_string($tags) && ! $this->isValidJson($tags)) {
                    $tags = null;
                }

                $documentsForUpsert[] = [
                    'id'                   => $docData['id'],
                    'owner_id'             => $docData['owner_id'],
                    'title'                => $docData['title'],
                    'description'          => $docData['description'],
                    'tags'                 => $tags,
                    'document_category_id' => $newCategoryId,
                    'uploaded_at'          => $this->normalizeDatetime($docData['uploaded_at']),
                    'deleted_at'           => $this->normalizeDatetime($docData['deleted_at']),
                    'created_at'           => $this->normalizeDatetime($docData['created_at']) ?? now()->toDateTimeString(),
                    'updated_at'           => now()->toDateTimeString(),
                ];

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

                    $versionsForUpsert[] = [
                        'id'                   => $versionData['id'],
                        'document_id'          => $docData['id'],
                        'version_number'       => $versionData['version_number'],
                        'file_path'            => $versionData['file_path'],
                        'cloudinary_public_id' => $versionData['cloudinary_public_id'] ?? null,
                        'file_name'            => $versionData['file_name'],
                        'mime_type'            => $versionData['mime_type'],
                        'file_size'            => $versionData['file_size'],
                        'change_notes'         => $versionData['change_notes'],
                        'uploaded_by'          => $versionData['uploaded_by'],
                        'created_at'           => $this->normalizeDatetime($versionData['created_at']) ?? now()->toDateTimeString(),
                        'updated_at'           => now()->toDateTimeString(),
                    ];

                    $stats['versions']++;

                    if ($versionData['id'] == $docData['current_version_id']) {
                        $resolvedCurrentVersionId = $versionData['id'];
                    }
                }

                if ($resolvedCurrentVersionId !== null) {
                    $docVersionUpdates[$docData['id']] = $resolvedCurrentVersionId;
                }
            }

            $chunkSize = 200;

            if ($isPostgres) {
                foreach ($documentsForUpsert as $docRow) {
                    DB::table('documents')->upsert([$docRow], ['id'], [
                        'owner_id', 'title', 'description', 'tags',
                        'document_category_id', 'uploaded_at', 'deleted_at', 'updated_at',
                    ]);
                }
            } else {
                foreach (array_chunk($documentsForUpsert, $chunkSize) as $chunk) {
                    Document::upsert($chunk, ['id'], [
                        'owner_id', 'title', 'description', 'tags',
                        'document_category_id', 'uploaded_at', 'deleted_at', 'updated_at',
                    ]);
                }
            }

            foreach (array_chunk($versionsForUpsert, $chunkSize) as $chunk) {
                DocumentVersion::upsert($chunk, ['id'], [
                    'document_id', 'version_number', 'file_path', 'cloudinary_public_id',
                    'file_name', 'mime_type', 'file_size', 'change_notes',
                    'uploaded_by', 'updated_at',
                ]);
            }

            foreach ($docVersionUpdates as $docId => $versionId) {
                Document::where('id', $docId)->update(['current_version_id' => $versionId]);
            }

            // ── Step 5: Restore financial transactions ────────────────────
            if (! empty($financialRows)) {
                $financialsForUpsert = [];
                $backupFtIds         = array_column($financialRows, 'id');
                $existingFtIds       = FinancialTransaction::withTrashed()
                    ->whereIn('id', $backupFtIds)
                    ->pluck('id')
                    ->flip()
                    ->all();

                foreach ($financialRows as $ftData) {
                    $ftExists = isset($existingFtIds[$ftData['id']]);

                    if ($ftExists && $request->input('mode') === 'skip') {
                        $stats['fin_skipped']++;
                        continue;
                    }

                    $restoredStatus = ($financialRestoreStatus === 'force_pending')
                        ? 'pending'
                        : $ftData['status'];

                    $financialsForUpsert[] = [
                        'id'               => $ftData['id'],
                        'type'             => $ftData['type'],
                        'user_id'          => $ftData['user_id'],
                        'status'           => $restoredStatus,
                        'description'      => $ftData['description'],
                        'amount'           => $ftData['amount'],
                        'category'         => $ftData['category'],
                        'transaction_date' => $this->normalizeDatetime($ftData['transaction_date']),
                        'notes'            => $ftData['notes'],
                        'customer_name'    => $ftData['customer_name'] ?? null,
                        'due_date'         => $this->normalizeDatetime($ftData['due_date'] ?? null),
                        'deleted_at'       => $this->normalizeDatetime($ftData['deleted_at'] ?? null),
                        'approved_by'      => $restoredStatus === 'pending' ? null : ($ftData['approved_by'] ?? null),
                        'approved_at'      => $restoredStatus === 'pending' ? null : $this->normalizeDatetime($ftData['approved_at'] ?? null),
                        'audited_by'       => $restoredStatus === 'pending' ? null : ($ftData['audited_by'] ?? null),
                        'audited_at'       => $restoredStatus === 'pending' ? null : $this->normalizeDatetime($ftData['audited_at'] ?? null),
                        'created_at'       => $this->normalizeDatetime($ftData['created_at']) ?? now()->toDateTimeString(),
                        'updated_at'       => now()->toDateTimeString(),
                    ];

                    $stats['financial']++;
                }

                foreach (array_chunk($financialsForUpsert, 200) as $chunk) {
                    FinancialTransaction::upsert($chunk, ['id'], [
                        'type', 'user_id', 'status', 'description', 'amount', 'category',
                        'transaction_date', 'notes', 'customer_name', 'due_date',
                        'deleted_at', 'approved_by', 'approved_at', 'audited_by', 'audited_at',
                        'updated_at',
                    ]);
                }

                // ── Regenerate approval documents ─────────────────────────
                if ($financialRestoreStatus !== 'force_pending') {
                    $restoredFts = FinancialTransaction::whereIn('id', $backupFtIds)
                        ->whereIn('status', ['approved', 'paid'])
                        ->whereNull('deleted_at')
                        ->with(['user', 'approver', 'auditor'])
                        ->get();

                    foreach ($restoredFts as $ft) {
                        try {
                            if ($ft->type === 'receivable' && $ft->status !== 'paid') continue;

                            $alreadyHasDoc = $isPostgres
                                ? $ft->documents()->whereRaw("tags::jsonb @> '[\"auto-generated\"]'::jsonb")->exists()
                                : $ft->documents()->whereJsonContains('tags', 'auto-generated')->exists();

                            if ($alreadyHasDoc) continue;

                            $this->saveApprovedTransactionAsDocument($ft, $actorUser);
                            $stats['financial_docs_generated']++;
                        } catch (\Throwable $e) {
                            \Log::warning("Could not regenerate approval doc for transaction #{$ft->id}: " . $e->getMessage());
                        }
                    }
                }
            }

            // ── Step 6: Record restore ────────────────────────────────────
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
            try { DB::rollBack(); } catch (\Throwable) {}
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
            "Backup restored: {$stats['documents']} documents, {$stats['financial']} financial records, {$stats['financial_docs_generated']} approval docs regenerated, {$stats['skipped']} skipped",
            [],
            array_merge($stats, ['scope' => $scope])
        );

        $parts = array_filter([
            'Restore complete!',
            "{$stats['categories']} categories,",
            "{$stats['documents']} documents,",
            "{$stats['versions']} versions restored.",
            $stats['financial']                > 0 ? "{$stats['financial']} financial records restored."                     : '',
            $stats['financial_docs_generated'] > 0 ? "{$stats['financial_docs_generated']} approval documents regenerated." : '',
            $stats['fin_skipped']              > 0 ? "{$stats['fin_skipped']} financial records skipped."                   : '',
            $stats['skipped']                  > 0 ? "{$stats['skipped']} documents skipped (already exist)."               : '',
            $financialRestoreStatus === 'force_pending' && $stats['financial'] > 0
                ? '⚠️ Financial records reset to pending — please re-audit and re-approve.'
                : ($stats['financial'] > 0 ? '✅ Financial records restored with original status.' : ''),
        ]);

        return redirect()->route('admin.document-backups.index')
            ->with('success', implode(' ', $parts));
    }

    // -------------------------------------------------------------------------
    // Destroy — delete from Cloudinary and DB
    // -------------------------------------------------------------------------

    public function destroy(string $filename)
    {
        $this->requirePermission('backups.delete');

        // ✅ Find in DB and delete from Cloudinary
        $backup = \App\Models\DocumentBackup::where('filename', basename($filename))->firstOrFail();

        $cloudinary = new CloudinaryService();
        $cloudinary->delete($backup->cloudinary_public_id);

        $backup->delete();

        AuditLogger::log('backup_deleted', null, "Backup deleted: {$filename}");

        return back()->with('success', "Backup {$filename} deleted.");
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function normalizeDatetime($value): ?string
    {
        if (empty($value) || $value instanceof \DateTimeInterface) {
            return $value;
        }

        try {
            return Carbon::parse($value)->toDateTimeString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB'];
        $i     = (int) floor(log($bytes, 1024));
        $i     = min($i, count($units) - 1);
        return round($bytes / (1024 ** $i), 2) . ' ' . $units[$i];
    }
    private function isValidJson(?string $value): bool
    {
        if ($value === null) return false;
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }
}