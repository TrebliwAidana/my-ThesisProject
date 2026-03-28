<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * BudgetController
 * - Admin, Officer : full CRUD
 * - Auditor        : index + show only (read-only)
 * - Member         : index + show only (read-only)
 */
class BudgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');

        // Auditors and Members cannot create, edit, or delete
        $this->middleware('role:Admin,Officer')
             ->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

   public function index(Request $request)
{
    $user = Auth::user();
    
    // Check permissions
    if (!in_array($user->role->name, ['Adviser', 'Officer', 'Auditor'])) {
        abort(403, 'Unauthorized. Only Advisers, Officers, and Auditors can view budgets.');
    }
    
    $query = Budget::with(['requester', 'reviewer', 'approver'])->latest();
    
    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    
    // Filter by category
    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }
    
    // Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
    
    $budgets = $query->paginate(15);
    $categories = BudgetCategory::where('is_active', true)->get();
    $statusCounts = [
        'pending' => Budget::where('status', 'pending')->count(),
        'reviewed' => Budget::where('status', 'reviewed')->count(),
        'approved' => Budget::where('status', 'approved')->count(),
        'rejected' => Budget::where('status', 'rejected')->count(),
        'disbursed' => Budget::where('status', 'disbursed')->count(),
    ];
    
    return view('budgets.index', compact('budgets', 'categories', 'statusCounts'));
}

    public function create()
    {
        $user = Auth::user();
    
    // Only Adviser and Officer can create budgets
    if (!in_array($user->role->name, ['Adviser', 'Officer'])) {
        abort(403, 'Unauthorized. Only Advisers and Officers can create budgets.');
    }
    
    $categories = BudgetCategory::where('is_active', true)->get();
    return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount'      => ['required', 'numeric', 'min:0'],
            'desc'        => ['nullable', 'string'],
            'reviewed_by' => ['nullable', 'exists:members,id'],
            'status'      => ['required', 'in:pending,approved,rejected'],
        ]);

        Budget::create($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget entry created successfully.');
    }

    public function show(Budget $budget)
    {
        $budget->load('reviewer.user');
        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        $user = Auth::user();
    
    // Only pending budgets can be edited, and only by creator
    if ($budget->status !== 'pending' || $budget->requested_by !== $user->id) {
        abort(403, 'Cannot edit this budget request.');
    }
    
    $categories = BudgetCategory::where('is_active', true)->get();
    return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'amount'      => ['required', 'numeric', 'min:0'],
            'desc'        => ['nullable', 'string'],
            'reviewed_by' => ['nullable', 'exists:members,id'],
            'status'      => ['required', 'in:pending,approved,rejected'],
        ]);

        $budget->update($validated);

        return redirect()->route('budgets.index')
            ->with('success', 'Budget entry updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return redirect()->route('budgets.index')
            ->with('success', 'Budget entry deleted successfully.');
    }
}
