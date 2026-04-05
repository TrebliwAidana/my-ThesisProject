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
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // FIX: was Gate::allows('budgets.view') — Gates were never registered
        // so this always returned false, blocking every non-admin user.
        // Now consistently uses hasPermission() like DocumentController does.
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.view')) {
            abort(403, 'You are not authorized to view budgets.');
        }

        $query = Budget::with('requester');

        if ($user->role->level !== 1 && \Illuminate\Support\Facades\Schema::hasColumn('budgets', 'organization_id')) {
            $query->where('organization_id', $user->organization_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('requester', function ($q2) use ($search) {
                      $q2->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->boolean('my'))      $query->where('requested_by', Auth::id());
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        if (in_array($sortField, ['title', 'amount', 'status', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $budgets = $query->paginate(15)->appends($request->except('page'));

        $counts = Budget::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusCounts = [
            'draft'     => $counts->get('draft', 0),
            'pending'   => $counts->get('pending', 0),
            'reviewed'  => $counts->get('reviewed', 0),
            'approved'  => $counts->get('approved', 0),
            'rejected'  => $counts->get('rejected', 0),
            'disbursed' => $counts->get('disbursed', 0),
        ];

        $totalApproved = Budget::where('status', 'approved')->sum('amount');
        $categories    = BudgetCategory::all();

        return view('budgets.index', compact('budgets', 'statusCounts', 'totalApproved', 'categories'));
    }

    public function create()
    {
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.submit')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.submit')) {
            abort(403, 'You are not authorized to submit budgets.');
        }

        $categories = BudgetCategory::all();

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

        $previousBudgets = Budget::where('requested_by', $user->id)
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title']);

        $currentYear     = now()->year;
        $totalApproved   = Budget::where('status', 'approved')->whereYear('created_at', $currentYear)->sum('amount');
        $annualBudget    = 1000000;
        $remainingBudget = max(0, $annualBudget - $totalApproved);

        return view('budgets.create', compact('sortedCategories', 'previousBudgets', 'remainingBudget', 'totalApproved'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.submit')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.submit')) {
            abort(403);
        }

        if ($request->has('items')) {
            $request->merge(['items' => json_decode($request->items, true)]);
        }

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'start_date'          => 'nullable|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'category'            => 'required|string|max:100',
            'attachment'          => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'required|numeric|min:0',
            'save_as_draft'       => 'boolean',
        ]);

        $status = $request->boolean('save_as_draft') ? 'draft' : 'pending';

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('budget_attachments', 'public');
        }

        $total = 0;
        foreach ($validated['items'] as $item) {
            $total += $item['quantity'] * $item['unit_price'];
        }

        DB::beginTransaction();
        try {
            $budgetData = [
                'title'           => $validated['title'],
                'description'     => $validated['description'],
                'start_date'      => $validated['start_date'] ?? null,
                'end_date'        => $validated['end_date'] ?? null,
                'amount'          => $total,
                'category'        => $validated['category'],
                'status'          => $status,
                'requested_by'    => $user->id,
                'attachment_path' => $attachmentPath,
            ];

            if (\Illuminate\Support\Facades\Schema::hasColumn('budgets', 'organization_id')) {
                $budgetData['organization_id'] = $user->organization_id;
            }

            $budget = Budget::create($budgetData);

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
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.view')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.view')) {
            abort(403);
        }

        if (
            \Illuminate\Support\Facades\Schema::hasColumn('budgets', 'organization_id') &&
            $budget->organization_id !== $user->organization_id &&
            $user->role->level !== 1
        ) {
            abort(403, 'You are not authorized to view this budget.');
        }

        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.submit')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.submit')) {
            abort(403);
        }

        if (!in_array($budget->status, ['pending', 'draft'])) {
            abort(403, 'This budget cannot be edited.');
        }

        if ($budget->requested_by !== $user->id && $user->role->level !== 1) {
            abort(403, 'You are not authorized to edit this budget.');
        }

        $categories = BudgetCategory::all();
        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.submit')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.submit')) {
            abort(403);
        }

        if (!in_array($budget->status, ['pending', 'draft'])) {
            abort(403, 'This budget cannot be updated.');
        }

        if ($budget->requested_by !== $user->id && $user->role->level !== 1) {
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
            if ($attachmentPath) Storage::disk('public')->delete($attachmentPath);
            $attachmentPath = $request->file('attachment')->store('budget_attachments', 'public');
        }

        $budget->update([
            'title'           => $validated['title'],
            'description'     => $validated['description'],
            'amount'          => $validated['amount'],
            'category'        => $validated['category'],
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->route('budgets.show', $budget)->with('success', 'Budget updated.');
    }

    public function review(Budget $budget)
    {
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.approve')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.approve')) {
            abort(403);
        }

        if (!in_array($budget->status, ['pending', 'reviewed'])) {
            return back()->with('error', 'This budget cannot be reviewed.');
        }

        return view('budgets.review', compact('budget'));
    }

    public function approve(Request $request, Budget $budget)
    {
        $user = auth()->user();

        if ($user->role->level === 2 && $user->position === 'SSLG President') {
            abort(403, 'SSLG President is not allowed to approve budgets.');
        }

        // FIX: was Gate::allows('budgets.approve')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.approve')) {
            abort(403);
        }

        if (
            \Illuminate\Support\Facades\Schema::hasColumn('budgets', 'organization_id') &&
            $budget->organization_id !== $user->organization_id &&
            $user->role->level !== 1
        ) {
            abort(403, 'You cannot approve budgets from another organisation.');
        }

        $validated = $request->validate([
            'approval_remarks' => 'nullable|string',
        ]);

        $budget->status           = 'approved';
        $budget->approved_by      = Auth::id();
        $budget->approved_at      = Carbon::now();
        $budget->approval_remarks = $validated['approval_remarks'] ?? null;
        $budget->save();

        if ($budget->requester) {
            $budget->requester->notify(new BudgetStatusChanged($budget, 'approved'));
        }

        return redirect()->route('budgets.index')->with('success', 'Budget approved.');
    }

    public function reject(Request $request, Budget $budget)
    {
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.approve')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.approve')) {
            abort(403);
        }

        if (
            \Illuminate\Support\Facades\Schema::hasColumn('budgets', 'organization_id') &&
            $budget->organization_id !== $user->organization_id &&
            $user->role->level !== 1
        ) {
            abort(403, 'You cannot reject budgets from another organisation.');
        }

        $validated = $request->validate([
            'review_remarks' => 'required|string',
        ]);

        $budget->status         = 'rejected';
        $budget->reviewed_by    = Auth::id();
        $budget->reviewed_at    = Carbon::now();
        $budget->review_remarks = $validated['review_remarks'] ?? null;
        $budget->save();

        if ($budget->requester) {
            $budget->requester->notify(new BudgetStatusChanged($budget, 'rejected'));
        }

        return redirect()->route('budgets.index')->with('success', 'Budget rejected.');
    }

    public function disburse(Request $request, Budget $budget)
    {
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.disburse')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.disburse')) {
            abort(403);
        }

        if ($budget->status !== 'approved') {
            return back()->with('error', 'Only approved budgets can be marked as disbursed.');
        }

        $budget->status       = 'disbursed';
        $budget->disbursed_at = Carbon::now();
        $budget->save();

        if ($budget->requester) {
            $budget->requester->notify(new BudgetStatusChanged($budget, 'disbursed'));
        }

        return redirect()->route('budgets.index')->with('success', 'Budget marked as disbursed.');
    }

    public function copy(Budget $budget)
    {
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.submit')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.submit')) {
            abort(403);
        }

        if ($budget->requested_by !== $user->id && $user->role->level !== 1) {
            abort(403, 'Unauthorized to copy this budget.');
        }

        $newBudget                   = $budget->replicate();
        $newBudget->title            = $budget->title . ' (Copy)';
        $newBudget->status           = 'pending';
        $newBudget->requested_by     = $user->id;
        $newBudget->approved_by      = null;
        $newBudget->reviewed_by      = null;
        $newBudget->approved_at      = null;
        $newBudget->reviewed_at      = null;
        $newBudget->disbursed_at     = null;
        $newBudget->approval_remarks = null;
        $newBudget->review_remarks   = null;
        $newBudget->copied_from_id   = $budget->id;

        if (\Illuminate\Support\Facades\Schema::hasColumn('budgets', 'organization_id')) {
            $newBudget->organization_id = $user->organization_id;
        }

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
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.submit')
        if ($budget->requested_by !== $user->id && $user->role->level !== 1 && !$user->hasPermission('budgets.submit')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = [
            'title'       => $budget->title . ' (Copy)',
            'description' => $budget->description,
            'start_date'  => $budget->start_date?->format('Y-m-d'),
            'end_date'    => $budget->end_date?->format('Y-m-d'),
            'category'    => $budget->category,
            'items'       => $budget->items->map(fn($item) => [
                'description' => $item->description,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
            ]),
        ];

        return response()->json($data);
    }

    public function export(Request $request)
    {
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.view')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.view')) {
            abort(403);
        }

        $query = Budget::with('requester');

        if ($user->role->level !== 1 && \Illuminate\Support\Facades\Schema::hasColumn('budgets', 'organization_id')) {
            $query->where('organization_id', $user->organization_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('requester', fn($q2) => $q2->where('full_name', 'like', "%{$search}%"));
            });
        }
        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('category'))  $query->where('category', $request->category);
        if ($request->boolean('my'))       $query->where('requested_by', Auth::id());
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);

        $budgets  = $query->get();
        $fileName = 'budgets_' . date('Y-m-d_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($budgets) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['ID','Title','Description','Amount (₱)','Category','Status','Requester','Requester Email','Start Date','End Date','Created At','Approved At','Remarks','Attachment']);

            foreach ($budgets as $b) {
                fputcsv($file, [
                    $b->id, $b->title, $b->description,
                    number_format($b->amount, 2), $b->category, ucfirst($b->status),
                    $b->requester->full_name ?? 'N/A', $b->requester->email ?? 'N/A',
                    optional($b->start_date)->format('Y-m-d'),
                    optional($b->end_date)->format('Y-m-d'),
                    optional($b->created_at)->format('Y-m-d H:i:s'),
                    optional($b->approved_at)->format('Y-m-d H:i:s'),
                    $b->approval_remarks ?? $b->review_remarks ?? '',
                    $b->attachment_path ? 'Yes' : 'No',
                ]);
            }

            $totalAmount = $budgets->sum('amount');
            fputcsv($file, []);
            fputcsv($file, ['TOTAL','','',number_format($totalAmount, 2),'','','','','','','','','','']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy(Budget $budget)
    {
        $user = auth()->user();

        // FIX: was Gate::allows('budgets.submit')
        if ($user->role->level !== 1 && !$user->hasPermission('budgets.submit')) {
            abort(403);
        }

        if (!in_array($budget->status, ['pending', 'draft'])) {
            return back()->with('error', 'Only pending or draft budgets can be deleted.');
        }

        if ($budget->requested_by !== $user->id && $user->role->level !== 1) {
            abort(403, 'Unauthorized to delete this budget.');
        }

        $budget->items()->delete();
        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
    }
}