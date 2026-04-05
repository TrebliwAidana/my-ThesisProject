<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

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

        $query = Document::with('uploader', 'organization');

        // Organisation scoping for non-level-1 users
        if ($user->role->level !== 1) {
            $query->where(function ($q) use ($user) {
                $q->where('organization_id', $user->organization_id)
                  ->orWhere('is_public', true);
            });
        }

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

        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
            'file'        => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip',
        ]);

        $file         = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mime         = $file->getMimeType();
        $size         = $file->getSize();
        $path         = $file->store('documents/' . date('Y/m'), 'public');

        Document::create([
            'title'           => $validated['title'],
            'description'     => $validated['description'],
            'file_path'       => $path,
            'file_name'       => $originalName,
            'mime_type'       => $mime,
            'size'            => $size,
            'category'        => $validated['category'],
            'uploaded_by'     => $user->id,
            'organization_id' => $user->organization_id,
            'is_public'       => $validated['is_public'] ?? false,
            'status'          => 'approved',
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.view')) {
            abort(403);
        }

        if (!$document->is_public && $document->organization_id != $user->organization_id && $user->role->level !== 1) {
            abort(403, 'You are not authorized to view this document.');
        }

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.edit')) {
            abort(403);
        }

        if ($document->organization_id !== $user->organization_id && $user->role->level !== 1) {
            abort(403, 'You cannot edit documents from another organisation.');
        }

        if ($document->uploaded_by !== $user->id && $user->role->level !== 1) {
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

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'is_public'   => 'boolean',
        ]);

        $document->update($validated);

        return redirect()->route('documents.index')
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.delete')) {
            abort(403);
        }

        if ($document->organization_id !== $user->organization_id && $user->role->level !== 1) {
            abort(403);
        }

        if ($document->uploaded_by !== $user->id && $user->role->level !== 1) {
            abort(403);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function download(Document $document)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.view')) {
            abort(403);
        }

        if (!$document->is_public && $document->organization_id != $user->organization_id && $user->role->level !== 1) {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}