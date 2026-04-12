<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = Auth::user();

        // Level 1 (SysAdmin) sees all orgs
        // Others only see their own org
        $query = Organization::with('adviser')->withCount('users', 'documents', 'budgets');

        if ($user->role->level !== 1) {
            $query->where('id', $user->organization_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('abbreviation', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $organizations = $query->orderBy('name')->paginate(15)->appends($request->query());

        $totalOrgs    = Organization::count();
        $activeOrgs   = Organization::where('is_active', true)->count();
        $totalMembers = User::whereNotNull('organization_id')->count();
        $types        = Organization::types();

        return view('organizations.index', compact(
            'organizations', 'totalOrgs', 'activeOrgs', 'totalMembers', 'types'
        ));
    }

    // ── Show ──────────────────────────────────────────────────────────────────

    public function show(Organization $organization)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && $user->organization_id !== $organization->id) {
            abort(403, 'You can only view your own organization.');
        }

        $organization->load(['adviser', 'users.role']);

        $membersByRole = $organization->users->groupBy(fn($u) => $u->role->name ?? 'Unknown');

        $recentDocs    = \App\Models\Document::where('organization_id', $organization->id)
                            ->latest()->take(5)->get();
        $recentBudgets = \App\Models\Budget::where('organization_id', $organization->id)
                            ->latest()->take(5)->get();

        $documentCount = \App\Models\Document::where('organization_id', $organization->id)->count();

        $budgetSummary = [
            'total'    => \App\Models\Budget::where('organization_id', $organization->id)->count(),
            'approved' => \App\Models\Budget::where('organization_id', $organization->id)->where('status', 'approved')->sum('amount'),
            'pending'  => \App\Models\Budget::where('organization_id', $organization->id)->where('status', 'pending')->count(),
        ];

        return view('organizations.show', compact(
            'organization', 'membersByRole', 'recentDocs', 'recentBudgets', 'budgetSummary','documentCount',
        ));
    }

    // ── Create / Store ────────────────────────────────────────────────────────

    public function create()
    {
        $this->requireLevel1();

        $advisers = User::whereHas('role', fn($q) => $q->whereIn('abbreviation', ['CA', 'SA', 'SysAdmin']))
            ->orderBy('full_name')->get();

        $types = Organization::types();

        return view('organizations.create', compact('advisers', 'types'));
    }

    public function store(Request $request)
    {
        $this->requireLevel1();

        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:organizations,name',
            'abbreviation'  => 'nullable|string|max:20',
            'description'   => 'nullable|string',
            'type'          => 'required|string|in:' . implode(',', array_keys(Organization::types())),
            'academic_year' => 'nullable|string|max:20',
            'adviser_id'    => 'nullable|exists:users,id',
            'is_active'     => 'boolean',
            'logo'          => 'nullable|image|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('org-logos', 'public');
        }

        $org = Organization::create(array_merge($validated, ['logo' => $logoPath]));

        return redirect()->route('admin.organizations.show', $org)
            ->with('success', "✅ Organization '{$org->name}' created successfully.");
    }

    // ── Edit / Update ─────────────────────────────────────────────────────────

    public function edit(Organization $organization)
    {
        $user = Auth::user();

        // Level 1 can edit any org; Org Admin can edit their own
        if ($user->role->level !== 1 && $user->organization_id !== $organization->id) {
            abort(403);
        }
        if (!in_array($user->role->abbreviation, ['SysAdmin', 'SA', 'OA'])) {
            abort(403);
        }

        $advisers = User::whereHas('role', fn($q) => $q->whereIn('abbreviation', ['CA', 'SA', 'SysAdmin']))
            ->orderBy('full_name')->get();

        $types = Organization::types();

        return view('organizations.edit', compact('organization', 'advisers', 'types'));
    }

    public function update(Request $request, Organization $organization)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && $user->organization_id !== $organization->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:organizations,name,' . $organization->id,
            'abbreviation'  => 'nullable|string|max:20',
            'description'   => 'nullable|string',
            'type'          => 'required|string|in:' . implode(',', array_keys(Organization::types())),
            'academic_year' => 'nullable|string|max:20',
            'adviser_id'    => 'nullable|exists:users,id',
            'is_active'     => 'boolean',
            'logo'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($organization->logo) {
                Storage::disk('public')->delete($organization->logo);
            }
            $validated['logo'] = $request->file('logo')->store('org-logos', 'public');
        }

        $organization->update($validated);

        return redirect()->route('admin.organizations.show', $organization)
            ->with('success', "✅ Organization updated successfully.");
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Organization $organization)
    {
        $this->requireLevel1();

        if ($organization->users()->count() > 0) {
            return back()->with('error', "❌ Cannot delete '{$organization->name}' — it still has members. Remove or reassign them first.");
        }

        $organization->delete();

        return redirect()->route('admin.organizations.index')
            ->with('success', "✅ Organization deleted successfully.");
    }

    // ── Toggle Active ─────────────────────────────────────────────────────────

    public function toggleActive(Organization $organization)
    {
        $this->requireLevel1();

        $organization->update(['is_active' => !$organization->is_active]);

        $status = $organization->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success'   => true,
            'is_active' => $organization->is_active,
            'message'   => "Organization {$status} successfully.",
        ]);
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    private function requireLevel1(): void
    {
        if (Auth::user()->role->level !== 1) {
            abort(403, 'Only System Administrators can perform this action.');
        }
    }
        public function myOrganization()
    {
        $user = Auth::user();

        // Guard: user must belong to an organization
        if (! $user->organization_id) {
            return redirect()->route('dashboard')
                ->with('info', 'ℹ️ You are not assigned to any organization yet.');
        }

        $organization = Organization::with(['adviser', 'users.role'])
            ->withCount(['users', 'documents', 'budgets'])
            ->findOrFail($user->organization_id);

        $membersByRole = $organization->users->groupBy(fn($u) => $u->role->name ?? 'Unknown');

        $recentDocs = \App\Models\Document::where('organization_id', $organization->id)
            ->latest()->take(5)->get();

        $recentBudgets = \App\Models\Budget::where('organization_id', $organization->id)
            ->latest()->take(5)->get();

        $documentCount = \App\Models\Document::where('organization_id', $organization->id)->count();

        $budgetSummary = [
            'total'    => \App\Models\Budget::where('organization_id', $organization->id)->count(),
            'approved' => \App\Models\Budget::where('organization_id', $organization->id)->where('status', 'approved')->sum('amount'),
            'pending'  => \App\Models\Budget::where('organization_id', $organization->id)->where('status', 'pending')->count(),
        ];

        // Reuses the same organizations.show blade
        return view('organizations.show', compact(
            'organization', 'membersByRole', 'recentDocs',
            'recentBudgets', 'budgetSummary', 'documentCount'
        ));
    }
}