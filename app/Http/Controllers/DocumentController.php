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
    // Index – all documents are public, no visibility filtering
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasPermission('documents.view') && $user->role->level !== 1) {
            abort(403);
        }

        $query = Document::with(['owner', 'currentVersion', 'category']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter (foreign key)
        if ($request->filled('category')) {
            $query->where('document_category_id', $request->category);
        }

        $documents = $query->latest()->paginate(15)->appends($request->query());

        $categories = DocumentCategory::active()->orderBy('name')->pluck('name', 'id');

        return view('documents.index', compact('documents', 'categories'));
    }

    // -------------------------------------------------------------------------
    // Show – policy handles access (no private check needed)
    // -------------------------------------------------------------------------

    public function show(Document $document)
    {
        $this->authorize('view', $document);
        return view('documents.show', compact('document'));
    }

    // -------------------------------------------------------------------------
    // Edit / Update – using document_category_id
    // -------------------------------------------------------------------------

    public function edit(Document $document)
    {
        $user = Auth::user();
        if ($user->role->level !== 1 && !$user->hasPermission('documents.edit')) {
            abort(403);
        }
        if ($user->role->level !== 1 && $document->owner_id !== $user->id) {
            abort(403, 'You can only edit your own documents.');
        }

        $categories = DocumentCategory::active()->orderBy('name')->pluck('name', 'id');
        return view('documents.edit', compact('document', 'categories'));
    }

    public function update(Request $request, Document $document)
    {
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
            'document_category_id' => 'nullable|exists:document_categories,id',
        ]);

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
    // Destroy – permission based
    // -------------------------------------------------------------------------

    public function destroy(Document $document)
    {
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
    // Download / Preview – policy handles access (no visibility restrictions)
    // -------------------------------------------------------------------------

    public function download(Document $document)
    {
        $this->authorize('view', $document);

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
        $this->authorize('view', $document);

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
    // Trash / Restore / Force Delete – permission based
    // -------------------------------------------------------------------------

   public function trash()
    {
        if (!Auth::user()->hasPermission('documents.manage') && Auth::user()->role->level !== 1) {
            abort(403);
        }
 
        // ✅ FIXED: added 'currentVersion' and 'category' to eager loads
        $documents = Document::onlyTrashed()
            ->with(['owner', 'currentVersion', 'category'])
            ->latest('deleted_at')
            ->paginate(15);
 
        return view('documents.trash', compact('documents'));
    }
 

    public function restore($id)
    {
        if (!Auth::user()->hasPermission('documents.manage') && Auth::user()->role->level !== 1) {
            abort(403);
        }

        $document = Document::onlyTrashed()->findOrFail($id);
        $document->restore();

        AuditLogger::log('restored', $document, "Document: {$document->title}");

        return redirect()->route('documents.trash')
            ->with('success', 'Document restored successfully.');
    }

    public function forceDelete($id)
    {
        if (!Auth::user()->hasPermission('documents.manage') && Auth::user()->role->level !== 1) {
            abort(403);
        }

        $document = Document::onlyTrashed()->findOrFail($id);

        AuditLogger::log('force_deleted', $document, "Document: {$document->title}", $document->toArray(), []);

        $document->forceDelete();

        return redirect()->route('documents.trash')
            ->with('success', 'Document permanently deleted.');
    }
}