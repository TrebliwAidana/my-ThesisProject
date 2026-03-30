<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * BudgetController
 * - System Admin, Supreme Admin, Adviser, Org Admin, Org Officer : full CRUD
 * - Supreme Officer, Auditor, Org Member : index + show only (read-only)
 * - Guest : no access
 */
class BudgetController extends Controller
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
                abort(403, 'Unauthorized. Only System Administrators, Supreme Admins, Advisers, Org Admins, and Org Officers can modify budgets.');
            }
            
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Supreme Officer', 'Adviser', 'Org Admin', 'Org Officer', 'Auditor', 'Org Member'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'SO', 'AD', 'OA', 'OO', 'OM'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. You do not have permission to view budgets.');
        }
        
        $query = Budget::with(['requester', 'reviewer', 'approver'])->latest();
        
        // If user is Org Officer or Org Member, only show their organization's budgets
        if (in_array($user->role->abbreviation, ['OO', 'OM']) || in_array($user->role->name, ['Org Officer', 'Org Member'])) {
            $query->where('organization_id', $user->organization_id ?? 0);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
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
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Adviser', 'Org Admin', 'Org Officer'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'AD', 'OA', 'OO'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. Only System Administrators, Supreme Admins, Advisers, Org Admins, and Org Officers can create budgets.');
        }
        
        $categories = BudgetCategory::where('is_active', true)->get();
        return view('budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'category'    => ['required', 'string', 'max:100'],
            'status'      => ['required', 'in:pending,reviewed,approved,rejected,disbursed'],
        ]);
        
        Budget::create([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'status' => $validated['status'],
            'requested_by' => $user->id,
            'organization_id' => $user->organization_id ?? null,
        ]);

        return redirect()->route('budgets.index')
            ->with('success', '✅ Budget request created successfully.');
    }

    public function show(Budget $budget)
    {
        $user = Auth::user();
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Supreme Officer', 'Adviser', 'Org Admin', 'Org Officer', 'Auditor', 'Org Member'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'SO', 'AD', 'OA', 'OO', 'OM'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. You do not have permission to view this budget.');
        }
        
        // Check organization access
        if (in_array($user->role->abbreviation, ['OO', 'OM']) && $budget->organization_id != ($user->organization_id ?? null)) {
            abort(403, 'Unauthorized. You can only view budgets from your organization.');
        }
        
        $budget->load('requester', 'reviewer', 'approver');
        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        $user = Auth::user();
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Adviser', 'Org Admin', 'Org Officer'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'AD', 'OA', 'OO'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. Only authorized roles can edit budgets.');
        }
        
        if ($budget->status !== 'pending') {
            abort(403, 'Cannot edit budget that is not in pending status.');
        }
        
        if (in_array($user->role->abbreviation, ['OO', 'OA']) && $budget->requested_by !== $user->id) {
            abort(403, 'Unauthorized. You can only edit budgets you created.');
        }
        
        $categories = BudgetCategory::where('is_active', true)->get();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        $user = Auth::user();
        
        if ($budget->status !== 'pending') {
            return back()->with('error', '❌ Cannot update budget that is not in pending status.');
        }
        
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'category'    => ['required', 'string', 'max:100'],
            'status'      => ['required', 'in:pending,reviewed,approved,rejected,disbursed'],
        ]);

        $budget->update($validated);

        return redirect()->route('budgets.index')
            ->with('success', '✅ Budget updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $user = Auth::user();
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Adviser'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'AD'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. Only System Administrators, Supreme Admins, and Advisers can delete budgets.');
        }
        
        $budgetTitle = $budget->title;
        $budget->delete();
        
        return redirect()->route('budgets.index')
            ->with('success', "✅ Budget '{$budgetTitle}' deleted successfully.");
    }
    
    public function approve(Budget $budget)
    {
        $user = Auth::user();
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Adviser', 'Org Admin'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'AD', 'OA'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. Only authorized roles can approve budgets.');
        }
        
        if ($budget->status !== 'reviewed') {
            return back()->with('error', '❌ Budget must be reviewed before approval.');
        }
        
        $budget->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('budgets.index')
            ->with('success', '✅ Budget approved successfully.');
    }
    
    public function review(Budget $budget)
    {
        $user = Auth::user();
        
        $allowedRoles = ['System Administrator', 'Supreme Admin', 'Adviser', 'Org Admin', 'Auditor'];
        $allowedAbbreviations = ['SysAdmin', 'SA', 'AD', 'OA'];
        
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
            abort(403, 'Unauthorized. Only authorized roles can review budgets.');
        }
        
        if ($budget->status !== 'pending') {
            return back()->with('error', '❌ Only pending budgets can be reviewed.');
        }
        
        $budget->update([
            'status' => 'reviewed',
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);
        
        return redirect()->route('budgets.index')
            ->with('success', '✅ Budget marked as reviewed.');
    }
}