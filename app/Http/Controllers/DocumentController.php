<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * DocumentController
 * - System Admin, Supreme Admin, Adviser, Org Admin, Org Officer : full CRUD
 * - Supreme Officer, Auditor, Org Member : index + show only (read-only)
 * - Guest : no access
 */
class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');

        // Full CRUD access for these roles
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $allowedRoles = ['System Administrator', 'Supreme Admin', 'Adviser', 'Org Admin', 'Org Officer'];
            $allowedAbbreviations = ['SysAdmin', 'SA', 'AD', 'OA', 'OO'];
            
            if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
                abort(403, 'Unauthorized. Only System Administrators, Supreme Admins, Advisers, Org Admins, and Org Officers can modify documents.');
            }
            
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $user = Auth::user();
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Supreme Officer', 'Adviser', 'Org Admin', 'Org Officer', 'Auditor', 'Org Member'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'SO', 'AD', 'OA', 'OO', 'OM'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. You do not have permission to view documents.');
        }
        
        $query = Document::with('uploader')->latest('uploaded_at');
        
        // If user is Org Officer or Org Member, only show their organization's documents
        if (in_array($user->role->abbreviation, ['OO', 'OM']) || in_array($user->role->name, ['Org Officer', 'Org Member'])) {
            $query->where('organization_id', $user->organization_id ?? 0);
        }
        
        $documents = $query->paginate(10);
        
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Adviser', 'Org Admin', 'Org Officer'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'AD', 'OA', 'OO'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. Only authorized roles can upload documents.');
        }
        
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file'  => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg'],
            'organization_id' => ['nullable', 'exists:organizations,id'],
        ]);

        $path = $request->file('file')->store('documents', 'public');

        Document::create([
            'title' => $request->title,
            'file_path' => $path,
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
            'organization_id' => $request->organization_id ?? $user->organization_id ?? null,
        ]);

        return redirect()->route('documents.index')
            ->with('success', '✅ Document uploaded successfully.');
    }

    public function show(Document $document)
    {
        $user = Auth::user();
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Supreme Officer', 'Adviser', 'Org Admin', 'Org Officer', 'Auditor', 'Org Member'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'SO', 'AD', 'OA', 'OO', 'OM'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. You do not have permission to view this document.');
        }
        
        // Check organization access
        if (in_array($user->role->abbreviation, ['OO', 'OM']) && $document->organization_id != ($user->organization_id ?? null)) {
            abort(403, 'Unauthorized. You can only view documents from your organization.');
        }
        
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
            ->with('success', '✅ Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        $user = Auth::user();
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Adviser'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'AD'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. Only System Administrators, Supreme Admins, and Advisers can delete documents.');
        }
        
        Storage::disk('public')->delete($document->file_path);
        $documentTitle = $document->title;
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', "✅ Document '{$documentTitle}' deleted successfully.");
    }

    /**
     * Only the uploader or authorized roles can edit a document.
     * Org Officer/Admin can edit documents from their organization.
     */
    private function authorizeEdit(Document $document): void
    {
        $user = Auth::user();
        
        // System Admin, Supreme Admin, Adviser can edit anything
        if (in_array($user->role->abbreviation, ['SysAdmin', 'SA', 'AD'])) {
            return;
        }
        
        // Org Admin can edit documents from their organization
        if (in_array($user->role->abbreviation, ['OA'])) {
            if ($document->organization_id == ($user->organization_id ?? null)) {
                return;
            }
            abort(403, 'Unauthorized. You can only edit documents from your organization.');
        }
        
        // Org Officer can only edit documents they uploaded
        if (in_array($user->role->abbreviation, ['OO'])) {
            if ($document->uploaded_by === $user->id) {
                return;
            }
            abort(403, 'Unauthorized. You can only edit documents you uploaded.');
        }
        
        abort(403, 'Unauthorized to edit this document.');
    }
}