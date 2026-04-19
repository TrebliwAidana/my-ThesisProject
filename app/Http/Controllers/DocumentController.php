<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogger;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // -------------------------------------------------------------------------
    // Guest restriction helper (redirects with flash message)
    // -------------------------------------------------------------------------

    /**
     * Redirect guest users back with a friendly message.
     *
     * @param string $action Description of the blocked action.
     * @return \Illuminate\Http\RedirectResponse|null
     */
    private function blockGuest(string $action = 'perform this action')
    {
        if (Auth::user()->email === 'guest@gmail.com') {
            return redirect()->route('dashboard')
                ->with('error', "Guest accounts cannot {$action}.");
        }
        return null;
    }

    // -------------------------------------------------------------------------
    // Index – guests can view (policy restricts to public)
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasPermission('documents.view') && $user->role->level !== 1) {
            abort(403, 'You are not authorized to view documents.');
        }

        $categories = DocumentCategory::active()->pluck('name');
        $query = Document::with('owner');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('visibility')) {
            $query->where('is_public', $request->visibility === 'public');
        }

        $documents = $query->latest()->paginate(15)->appends($request->query());

        $publicCount  = Document::where('is_public', true)->count();
        $privateCount = Document::where('is_public', false)->count();

        return view('documents.index', compact('documents', 'categories', 'publicCount', 'privateCount'));
    }

    // -------------------------------------------------------------------------
    // Create / Store – blocked for guests with redirect + flash
    // -------------------------------------------------------------------------

    public function create()
    {
        if ($redirect = $this->blockGuest('upload documents')) {
            return $redirect;
        }

        $user = Auth::user();
        if ($user->role->level !== 1 && !$user->hasPermission('documents.upload')) {
            abort(403, 'You are not allowed to upload documents.');
        }

        $categories = DocumentCategory::active()->pluck('name');
        return view('documents.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->blockGuest('upload documents')) {
            return $redirect;
        }

        $user = Auth::user();
        if ($user->role->level !== 1 && !$user->hasPermission('documents.upload')) {
            abort(403);
        }

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'category_final'=> 'nullable|string|max:100',
            'is_public'     => 'boolean',
            'file'          => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip',
        ]);

        $validated['category'] = $validated['category_final'] ?? null;
        unset($validated['category_final']);

        DB::transaction(function () use ($validated, $request, $5_user) {
            $document = Document::create([
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'category'    => $validated['category'] ?? null,
                'is_public'   => $validated['is_public'] ?? false,
                'owner_id'    => $user->id,
            ]);

            $document->addVersion($request->file('file'), 'Initial upload');

            AuditLogger::log('created', $document, "Document: {$document->title}", [], $document->toArray());
        });

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    // -------------------------------------------------------------------------
    // Show – policy handles access; guests redirected on private docs
    // -------------------------------------------------------------------------

    public function show(Document $document)
    {
        try {
            $this->authorize('view', $document);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if (Auth::user()->email === 'guest@gmail.com') {
                return redirect()->route('dashboard')
                    ->with('error', 'This document is private. Guest accounts can only view public documents.');
            }
            throw $e;
        }

        return view('documents.show', compact('document'));
    }

    // -------------------------------------------------------------------------
    // Edit / Update – blocked for guests
    // -------------------------------------------------------------------------

    public function edit(Document $document)
    {
        if ($redirect = $this->blockGuest('edit documents')) {
            return $redirect;
        }

        $user = Auth::user();
        if ($user->role->level !== 1 && !$user->hasPermission('documents.edit')) {
            abort(403);
        }
        if ($user->role->level !== 1 && $document->owner_id !== $user->id) {
            abort(403, 'You can only edit your own documents.');
        }

        $categories = DocumentCategory::active()->pluck('name');
        return view('documents.edit', compact('document', 'categories'));
    }

    public function update(Request $request, Document $document)
    {
        if ($redirect = $this->blockGuest('edit documents')) {
            return $redirect;
        }

        $user = Auth::user();
        if ($user->role->level !== 1 && !$user->hasPermission('documents.edit')) {
            abort(403);
        }
        if ($user->role->level !== 1 && $document->owner_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'category_final'=> 'nullable|string|max:100',
            'is_public'     => 'boolean',
        ]);

        $validated['category'] = $validated['category_final'] ?? null;
        unset($validated['category_final']);

        $oldData = $document->getOriginal();
        $document->update($validated);

        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip',
            ]);
            $document->addVersion($request->file('file'), $request->input('change_notes', 'Updated document'));
        }

        AuditLogger::log('updated', $document, "Document: {$document->title}", $oldData, $document->getChanges());

        return redirect()->route('documents.index')
            ->with('success', 'Document updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Destroy – blocked for guests
    // -------------------------------------------------------------------------

    public function destroy(Document $document)
    {
        if ($redirect = $this->blockGuest('delete documents')) {
            return $redirect;
        }

        $user = Auth::user();
        if ($user->role->level !== 1 && !$user->hasPermission('documents.delete')) {
            abort(403);
        }
        if ($user->role->level !== 1 && $document->owner_id !== $user->id) {
            abort(403);
        }

        AuditLogger::log('deleted', $document, "Document: {$document->title}", $document->toArray(), []);

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // Download / Preview – policy handles access; guests redirected
    // -------------------------------------------------------------------------

    public function download(Document $document)
    {
        try {
            $this->authorize('view', $document);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if (Auth::user()->email === 'guest@gmail.com') {
                return redirect()->route('dashboard')
                    ->with('error', 'You do not have permission to download this document.');
            }
            throw $e;
        }

        if (!$document->currentVersion) {
            return redirect()->back()->with('error', 'No file available for this document.');
        }

        $version = $document->currentVersion;
        $path = $version->file_path;

        if (!Storage::disk('private')->exists($path)) {
            return redirect()->back()->with('error', 'File not found on server. Please contact administrator.');
        }

        return Storage::disk('private')->download($path, $version->file_name);
    }

    public function preview(Document $document)
    {
        try {
            $this->authorize('view', $document);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            if (Auth::user()->email === 'guest@gmail.com') {
                return redirect()->route('dashboard')
                    ->with('error', 'You do not have permission to preview this document.');
            }
            throw $e;
        }

        if (!$document->currentVersion) {
            abort(404);
        }

        $version = $document->currentVersion;
        $path = $version->file_path;

        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('private')->path($path), [
            'Content-Type' => $version->mime_type,
        ]);
    }

    // -------------------------------------------------------------------------
    // Trash / Restore / Force Delete – blocked for guests
    // -------------------------------------------------------------------------

    public function trash()
    {
        if ($redirect = $this->blockGuest('access trash')) {
            return $redirect;
        }

        $this->authorize('viewAny', Document::class);
        $documents = Document::onlyTrashed()->with('owner')->latest()->paginate(15);
        return view('documents.trash', compact('documents'));
    }

    public function restore($id)
    {
        if ($redirect = $this->blockGuest('restore documents')) {
            return $redirect;
        }

        $document = Document::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $document);
        $document->restore();

        AuditLogger::log('restored', $document, "Document: {$document->title}");

        return redirect()->route('documents.trash')
            ->with('success', 'Document restored successfully.');
    }

    public function forceDelete($id)
    {
        if ($redirect = $this->blockGuest('permanently delete documents')) {
            return $redirect;
        }

        $document = Document::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $document);

        AuditLogger::log('force_deleted', $document, "Document: {$document->title}", $document->toArray(), []);

        $document->forceDelete();

        return redirect()->route('documents.trash')
            ->with('success', 'Document permanently deleted.');
    }
}