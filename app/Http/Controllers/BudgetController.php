<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Member;
use Illuminate\Http\Request;

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

    public function index()
    {
        $budgets = Budget::with('reviewer.user')->latest()->paginate(10);
        return view('budgets.index', compact('budgets'));
    }

    public function create()
    {
        $members = Member::with('user')->get();
        return view('budgets.create', compact('members'));
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
        $members = Member::with('user')->get();
        return view('budgets.edit', compact('budget', 'members'));
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
