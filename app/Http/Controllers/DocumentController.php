<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // Helper: check if user can manage documents (upload/edit/delete)
    private function canManageDocuments()
    {
        $allowed = ['System Administrator', 'Supreme Admin', 'Supreme Officer', 'Org Admin', 'Org Officer', 'Club Adviser'];
        return in_array(Auth::user()->role->name, $allowed);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Document::with('uploader', 'organization');

        // Filter by user's organization + public docs
        if (!$this->canManageDocuments()) {
            $query->where(function ($q) use ($user) {
                $q->where('is_public', true)
                  ->orWhere('organization_id', $user->organization_id);
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status (if approval enabled)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $documents = $query->latest()->paginate(15)->appends($request->query());

        $categories = Document::distinct()->pluck('category')->filter();

        return view('documents.index', compact('documents', 'categories'));
    }

    public function create()
    {
        if (!$this->canManageDocuments()) {
            abort(403, 'You are not allowed to upload documents.');
        }
        return view('documents.create');
    }

    public function store(Request $request)
    {
        if (!$this->canManageDocuments()) {
            abort(403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'nullable|string|max:100',
            'is_public'   => 'boolean',
            'file'        => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip', // max 10MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mime = $file->getMimeType();
        $size = $file->getSize();

        // Store file
        $path = $file->store('documents/' . date('Y/m'), 'public');

        $document = Document::create([
            'title'           => $validated['title'],
            'description'     => $validated['description'],
            'file_path'       => $path,
            'file_name'       => $originalName,
            'mime_type'       => $mime,
            'size'            => $size,
            'category'        => $validated['category'],
            'uploaded_by'     => Auth::id(),
            'organization_id' => Auth::user()->organization_id,
            'is_public'       => $validated['is_public'] ?? false,
            'status'          => 'approved', // or 'pending' if approval needed
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $user = Auth::user();
        if (!$document->is_public && $document->organization_id != $user->organization_id && !$this->canManageDocuments()) {
            abort(403);
        }
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        if (!$this->canManageDocuments()) {
            abort(403);
        }
        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        if (!$this->canManageDocuments()) {
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
        if (!$this->canManageDocuments()) {
            abort(403);
        }

        // Delete physical file
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    public function download(Document $document)
    {
        $user = Auth::user();
        if (!$document->is_public && $document->organization_id != $user->organization_id && !$this->canManageDocuments()) {
            abort(403);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}