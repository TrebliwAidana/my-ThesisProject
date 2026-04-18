<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
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

    public function create()
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('documents.upload')) {
            abort(403, 'You are not allowed to upload documents.');
        }

        $categories = DocumentCategory::active()->pluck('name');
        return view('documents.create', compact('categories'));
    }

    public function store(Request $request)
    {
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

        DB::transaction(function () use ($validated, $request, $user) {
            $document = Document::create([
                'title'       => $validated['title'],
                'description' => $validated['description'] ?? null,
                'category'    => $validated['category'] ?? null,
                'is_public'   => $validated['is_public'] ?? false,
                'owner_id'    => $user->id,
            ]);

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

        if ($user->role->level !== 1 && $document->owner_id !== $user->id) {
            abort(403, 'You can only edit your own documents.');
        }

        $categories = DocumentCategory::active()->pluck('name');
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
            'category_final'=> 'nullable|string|max:100',
            'is_public'     => 'boolean',
        ]);

        $validated['category'] = $validated['category_final'] ?? null;
        unset($validated['category_final']);

        $document->update($validated);

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