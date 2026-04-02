<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BudgetStatusChanged;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BudgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index(Request $request)
    {
        $query = Budget::with('requester');

        // Search (title + requester name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('requester', function ($q2) use ($search) {
                      $q2->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status (including draft)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // "My Budgets" filter
        if ($request->boolean('my')) {
            $query->where('requested_by', Auth::id());
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        if (in_array($sortField, ['title', 'amount', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $budgets = $query->paginate(15)->appends($request->except('page'));

        // Statistics (including draft)
        $statusCounts = [
            'draft'     => Budget::draft()->count(),
            'pending'   => Budget::pending()->count(),
            'reviewed'  => Budget::reviewed()->count(),
            'approved'  => Budget::approved()->count(),
            'rejected'  => Budget::rejected()->count(),
            'disbursed' => Budget::disbursed()->count(),
        ];
        $totalApproved = Budget::approved()->sum('amount');
        $categories = BudgetCategory::all();

        return view('budgets.index', compact('budgets', 'statusCounts', 'totalApproved', 'categories'));
    }

    public function create()
    {
        $user = auth()->user();
        $categories = BudgetCategory::all();

        // Get categories ordered by usage frequency for this user
        $userCategories = Budget::where('requested_by', $user->id)
            ->select('category')
            ->groupBy('category')
            ->orderByRaw('COUNT(*) DESC')
            ->pluck('category')
            ->toArray();
        $sortedCategories = $categories->sortBy(function ($cat) use ($userCategories) {
            $index = array_search($cat->name, $userCategories);
            return $index !== false ? $index : PHP_INT_MAX;
        })->values();

        // Previous budgets (for copy)
        $previousBudgets = Budget::where('requested_by', $user->id)
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title']);

        // Estimated remaining budget (current fiscal year)
        $currentYear = now()->year;
        $totalApproved = Budget::where('status', 'approved')
            ->whereYear('created_at', $currentYear)
            ->sum('amount');
        $annualBudget = 1000000; // Set your annual budget limit
        $remainingBudget = max(0, $annualBudget - $totalApproved);

        return view('budgets.create', compact('sortedCategories', 'previousBudgets', 'remainingBudget', 'totalApproved'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'category'       => 'required|string|max:100',
            'attachment'     => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'items'          => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'save_as_draft'  => 'boolean',
        ]);

        $status = $request->boolean('save_as_draft') ? 'draft' : 'pending';

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('budget_attachments', 'public');
        }

        // Calculate total amount from items
        $total = 0;
        foreach ($validated['items'] as $item) {
            $total += $item['quantity'] * $item['unit_price'];
        }

        DB::beginTransaction();
        try {
            $budget = Budget::create([
                'title'          => $validated['title'],
                'description'    => $validated['description'],
                'start_date'     => $validated['start_date'] ?? null,
                'end_date'       => $validated['end_date'] ?? null,
                'amount'         => $total,
                'category'       => $validated['category'],
                'status'         => $status,
                'requested_by'   => auth()->id(),
                'attachment_path'=> $attachmentPath,
            ]);

            foreach ($validated['items'] as $item) {
                $budget->items()->create([
                    'description' => $item['description'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                ]);
            }

            DB::commit();

            $message = $status === 'draft' ? 'Budget saved as draft.' : 'Budget request submitted.';
            return redirect()->route('budgets.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save budget: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Budget $budget)
    {
        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        $user = auth()->user();
        // Allow System Administrator and Supreme Admin
        if (in_array($user->role->name, ['System Administrator', 'Supreme Admin'])) {
            // proceed
        } elseif ($budget->status === 'pending' && $budget->requested_by === $user->id) {
            // allow owner only if pending
        } else {
            abort(403, 'You are not authorized to edit this budget.');
        }
        
        $categories = BudgetCategory::all();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        $user = auth()->user();
        // Same authorization logic
        if (in_array($user->role->name, ['System Administrator', 'Supreme Admin'])) {
            // allowed
        } elseif ($budget->status === 'pending' && $budget->requested_by === $user->id) {
            // allowed
        } else {
            abort(403, 'You are not authorized to update this budget.');
        }
        
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount'      => 'required|numeric|min:0',
            'category'    => 'required|string|max:100',
            'attachment'  => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        $attachmentPath = $budget->attachment_path;
        if ($request->hasFile('attachment')) {
            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = $request->file('attachment')->store('budget_attachments', 'public');
        }

        $budget->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'amount'      => $validated['amount'],
            'category'    => $validated['category'],
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->route('budgets.show', $budget)->with('success', 'Budget updated.');
    }

    public function review(Budget $budget)
    {
        if (!in_array($budget->status, ['pending', 'reviewed'])) {
            return back()->with('error', 'This budget cannot be reviewed.');
        }
        return view('budgets.review', compact('budget'));
    }

    public function approve(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'approval_remarks' => 'nullable|string',
        ]);
        $budget->status = 'approved';
        $budget->approved_by = Auth::id();
        $budget->approved_at = now();
        $budget->approval_remarks = $validated['approval_remarks'];
        $budget->save();

        if ($budget->requester) {
            $budget->requester->notify(new BudgetStatusChanged($budget, 'approved'));
        }

        return redirect()->route('budgets.index')->with('success', 'Budget approved.');
    }

    public function reject(Request $request, Budget $budget)
    {
        $validated = $request->validate([
            'review_remarks' => 'required|string',
        ]);
        $budget->status = 'rejected';
        $budget->reviewed_by = Auth::id();
        $budget->reviewed_at = now();
        $budget->review_remarks = $validated['review_remarks'];
        $budget->save();

        if ($budget->requester) {
            $budget->requester->notify(new BudgetStatusChanged($budget, 'rejected'));
        }

        return redirect()->route('budgets.index')->with('success', 'Budget rejected.');
    }

    public function disburse(Request $request, Budget $budget)
    {
        if ($budget->status !== 'approved') {
            return back()->with('error', 'Only approved budgets can be marked as disbursed.');
        }
        $budget->status = 'disbursed';
        $budget->disbursed_at = now();
        $budget->save();

        if ($budget->requester) {
            $budget->requester->notify(new BudgetStatusChanged($budget, 'disbursed'));
        }

        return redirect()->route('budgets.index')->with('success', 'Budget marked as disbursed.');
    }

    public function copy(Budget $budget)
    {
        if ($budget->requested_by !== auth()->id() && !in_array(auth()->user()->role->name, ['System Administrator', 'Supreme Admin'])) {
            abort(403, 'Unauthorized to copy this budget.');
        }

        $newBudget = $budget->replicate();
        $newBudget->title = $budget->title . ' (Copy)';
        $newBudget->status = 'pending';
        $newBudget->requested_by = auth()->id();
        $newBudget->approved_by = null;
        $newBudget->reviewed_by = null;
        $newBudget->approved_at = null;
        $newBudget->reviewed_at = null;
        $newBudget->disbursed_at = null;
        $newBudget->approval_remarks = null;
        $newBudget->review_remarks = null;
        $newBudget->copied_from_id = $budget->id;
        $newBudget->save();

        foreach ($budget->items as $item) {
            $newBudget->items()->create([
                'description' => $item->description,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
            ]);
        }

        return redirect()->route('budgets.edit', $newBudget)->with('success', 'Budget copied. You can now edit it.');
    }

    public function copyData(Budget $budget)
    {
        if ($budget->requested_by !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = [
            'title'       => $budget->title . ' (Copy)',
            'description' => $budget->description,
            'start_date'  => $budget->start_date?->format('Y-m-d'),
            'end_date'    => $budget->end_date?->format('Y-m-d'),
            'category'    => $budget->category,
            'items'       => $budget->items->map(function ($item) {
                return [
                    'description' => $item->description,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unit_price,
                ];
            }),
        ];
        return response()->json($data);
    }

    public function export(Request $request)
    {
        $query = Budget::with('requester');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('requester', fn($q2) => $q2->where('full_name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->boolean('my')) $query->where('requested_by', Auth::id());
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $budgets = $query->get();

        $fileName = 'budgets_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];
        $callback = function () use ($budgets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Amount', 'Category', 'Status', 'Requester', 'Created At', 'Approved At', 'Remarks']);
            foreach ($budgets as $b) {
                fputcsv($file, [
                    $b->id,
                    $b->title,
                    $b->amount,
                    $b->category,
                    $b->status,
                    $b->requester->full_name ?? 'N/A',
                    $b->created_at,
                    $b->approved_at,
                    $b->approval_remarks ?? $b->review_remarks,
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}