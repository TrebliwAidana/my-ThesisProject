<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialCategory;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // -------------------------------------------------------------------------
    // Gate
    // -------------------------------------------------------------------------

    private function authorizeAccess(): void
    {
        $user = Auth::user();

        if ($user->email === 'guest@gmail.com') {
            abort(403, 'Guest accounts cannot manage financial categories.');
        }

        if ($user->role->level !== 1 && !$user->hasPermission('financial_categories.manage')) {
            abort(403, 'You do not have permission to manage financial categories.');
        }
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = FinancialCategory::with('creator')
            ->withTrashed()
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at')->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->whereNull('deleted_at')->where('is_active', false);
            } elseif ($request->status === 'deleted') {
                $query->onlyTrashed();
            }
        }

        $categories = $query->paginate(20)->appends($request->except('page'));

        return view('admin.financial-categories.index', compact('categories'));
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    public function create()
    {
        $this->authorizeAccess();
        return view('admin.financial-categories.create');
    }

    // -------------------------------------------------------------------------
    // Store
    // -------------------------------------------------------------------------

    public function store(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:financial_categories,name'],
            'type'        => ['required', 'in:income,expense,receivable,both'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_active']  = true;

        $category = FinancialCategory::create($validated);

        AuditLogger::log('created', $category, "Financial category created: {$category->name}");

        return redirect()->route('admin.financial-categories.index')
            ->with('success', "Category \"{$category->name}\" created successfully.");
    }

    // -------------------------------------------------------------------------
    // Edit
    // -------------------------------------------------------------------------

    public function edit(FinancialCategory $financialCategory)
    {
        $this->authorizeAccess();
        return view('admin.financial-categories.edit', compact('financialCategory'));
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    public function update(Request $request, FinancialCategory $financialCategory)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', "unique:financial_categories,name,{$financialCategory->id}"],
            'type'        => ['required', 'in:income,expense,receivable,both'],  // ← receivable added
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $oldData = $financialCategory->getOriginal();
        $financialCategory->update($validated);

        AuditLogger::log('updated', $financialCategory, "Financial category updated: {$financialCategory->name}", $oldData, $financialCategory->getChanges());

        return redirect()->route('admin.financial-categories.index')
            ->with('success', "Category \"{$financialCategory->name}\" updated successfully.");
    }

    // -------------------------------------------------------------------------
    // Toggle Active
    // -------------------------------------------------------------------------

    public function toggleActive(FinancialCategory $financialCategory)
    {
        $this->authorizeAccess();

        $financialCategory->update(['is_active' => !$financialCategory->is_active]);

        $state = $financialCategory->is_active ? 'activated' : 'deactivated';
        AuditLogger::log('updated', $financialCategory, "Financial category {$state}: {$financialCategory->name}");

        return back()->with('success', "Category \"{$financialCategory->name}\" {$state}.");
    }

    // -------------------------------------------------------------------------
    // Soft Delete
    // -------------------------------------------------------------------------

    public function destroy(FinancialCategory $financialCategory)
    {
        $this->authorizeAccess();

        AuditLogger::log('deleted', $financialCategory, "Financial category soft-deleted: {$financialCategory->name}", $financialCategory->toArray(), []);

        $financialCategory->delete();

        return back()->with('success', "Category \"{$financialCategory->name}\" deleted.");
    }

    // -------------------------------------------------------------------------
    // Restore
    // -------------------------------------------------------------------------

    public function restore(int $id)
    {
        $this->authorizeAccess();

        $category = FinancialCategory::onlyTrashed()->findOrFail($id);
        $category->restore();

        AuditLogger::log('restored', $category, "Financial category restored: {$category->name}");

        return back()->with('success', "Category \"{$category->name}\" restored.");
    }

    // -------------------------------------------------------------------------
    // Force Delete
    // -------------------------------------------------------------------------

    public function forceDelete(int $id)
    {
        $this->authorizeAccess();

        $category = FinancialCategory::onlyTrashed()->findOrFail($id);

        AuditLogger::log('force_deleted', $category, "Financial category permanently deleted: {$category->name}", $category->toArray(), []);

        $category->forceDelete();

        return back()->with('success', "Category permanently deleted.");
    }

    // -------------------------------------------------------------------------
    // API endpoint — used by transaction create/edit form dropdowns
    // -------------------------------------------------------------------------

    public function apiList(Request $request)
    {
        $type = $request->query('type');

        $categories = FinancialCategory::active()
            ->when($type, fn($q) => $q->forType($type))
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        return response()->json($categories);
    }
}