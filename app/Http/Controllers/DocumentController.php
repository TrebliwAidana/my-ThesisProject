<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.view')) {
            abort(403, 'You are not authorized to view documents.');
        }

        $query = Document::with('owner'); // use owner instead of uploader

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

        $categories   = Document::distinct()->pluck('category')->filter();
        $publicCount  = Document::where('is_public', true)->count();
        $privateCount = Document::where('is_public', false)->count();

        return view('documents.index', compact('documents', 'categories', 'publicCount', 'privateCount'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.upload')) {
            abort(403, 'You are not allowed to upload documents.');
        }

        return view('documents.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.upload')) {
            abort(403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'is_public'   => 'boolean',
            'file'        => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip',
        ]);

        DB::transaction(function () use ($validated, $request, $user) {
            // Create the document record
            $document = Document::create([
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'category'    => $validated['category'] ?? null,
                'is_public'   => $validated['is_public'] ?? false,
                'owner_id'    => $user->id,
            ]);

            // Add the first version
            $document->addVersion($request->file('file'), 'Initial upload');
        });

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.view')) {
            abort(403);
        }

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.edit')) {
            abort(403);
        }

        // Non‑admin users can only edit their own documents
        if ($user->role->level !== 1 && $document->owner_id !== $user->id) {
            abort(403, 'You can only edit your own documents.');
        }

        return view('documents.edit', compact('document'));
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
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'is_public'   => 'boolean',
        ]);

        $document->update($validated);

        // Optionally handle new file version
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip',
            ]);
            $document->addVersion($request->file('file'), $request->input('change_notes', 'Updated document'));
        }

        return redirect()->route('documents.index')
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.delete')) {
            abort(403);
        }

        if ($user->role->level !== 1 && $document->owner_id !== $user->id) {
            abort(403);
        }

        // Document model's booted method will delete versions and files
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function download(Document $document)
    {
        $this->authorize('view', $document);

        if (!$document->currentVersion) {
            abort(404, 'No file available for this document.');
        }

        $version = $document->currentVersion;
        $path = $version->file_path;

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
        
        return response()->file(Storage::disk('private')->path($path), [
            'Content-Type' => $version->mime_type,
        ]);
    }

    // -------------------------------------------------------------------------
    // Trash / Restore (optional, if you want soft delete management)
    // -------------------------------------------------------------------------

    public function trash()
    {
            $this->authorize('viewAny', Document::class);
            $documents = Document::onlyTrashed()->with('owner')->latest()->paginate(15);
            return view('documents.trash', compact('documents'));
    }

    public function restore($id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $document);
        $document->restore();
        return redirect()->route('documents.trash')->with('success', 'Document restored.');
    }

    public function forceDelete($id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $document);
        $document->forceDelete();
        return redirect()->route('documents.trash')->with('success', 'Document permanently deleted.');
    }

}