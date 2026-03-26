<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * DocumentController
 * - Admin, Officer : full CRUD
 * - Auditor        : index + show only (read-only)
 * - Member         : index + show only (read-only)
 */
class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');

        // Auditors and Members cannot create, edit, or delete
        $this->middleware('role:Admin,Officer')
             ->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $documents = Document::with('uploader')->latest('uploaded_at')->paginate(10);
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file'  => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg'],
        ]);

        $path = $request->file('file')->store('documents', 'public');

        Document::create([
            'title'       => $request->title,
            'file_path'   => $path,
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $document->load('uploader');
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $this->authorizeEdit($document);
        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $this->authorizeEdit($document);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file'  => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg'],
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $document->file_path = $request->file('file')->store('documents', 'public');
        }

        $document->title = $request->title;
        $document->save();

        return redirect()->route('documents.index')
            ->with('success', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Document deleted successfully.');
    }

    /**
     * Only the uploader or Admin can edit a document.
     * Officer can edit only documents they uploaded.
     */
    private function authorizeEdit(Document $document): void
    {
        $user = Auth::user();

        if ($user->role->name === 'Admin') {
            return; // Admin can edit anything
        }

        if ($document->uploaded_by !== $user->id) {
            abort(403, 'You can only edit documents you uploaded.');
        }
    }
}
