<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionMatrixSeeder extends Seeder
{
    /**
     * Permission matrix.
     * Format: 'module' => ['action', ...]
     */
    private array $matrix = [
        // Core modules
        'users'        => ['view', 'manage'],
        'members'      => ['view', 'manage'],
        'documents'    => ['view', 'manage'],
        
        // Financial module
        'financial'    => ['view', 'create', 'edit', 'delete', 'approve', 'review'],
        
        // Reports
        'reports'      => ['view', 'generate', 'public'],
        
        // Audit & monitoring
        'audit'        => ['view', 'remarks'],
        'activities'   => ['monitor'],
        
        // Admin
        'roles'        => ['manage'],
        'permissions'  => ['manage'],
        // 'organization' => ['view'],   // ❌ REMOVED
    ];

    /**
     * Human-readable label overrides.
     */
    private array $labels = [
        // Users & Members
        'users.view'          => 'View Users',
        'users.manage'        => 'Manage Users',
        'members.view'        => 'View Members',
        'members.manage'      => 'Manage Members',
        
        // Documents
        'documents.view'      => 'View Documents',
        'documents.manage'    => 'Manage Documents',
        
        // Financial
        'financial.view'      => 'View Financial Records',
        'financial.create'    => 'Record Income/Expense',
        'financial.edit'      => 'Edit Transactions',
        'financial.delete'    => 'Delete Transactions',
        'financial.approve'   => 'Approve Transactions',
        'financial.review'    => 'Review Transactions',
        
        // Reports
        'reports.view'        => 'View Reports',
        'reports.generate'    => 'Generate Reports',
        'reports.public'      => 'View Public Reports',
        
        // Audit
        'audit.view'          => 'View Audit Logs',
        'audit.remarks'       => 'Add Audit Remarks',
        
        // Activities
        'activities.monitor'  => 'Monitor Activities',
        
        // Admin
        'roles.manage'        => 'Manage Roles',
        'permissions.manage'  => 'Manage Permissions',
        // 'organization.view' => 'View Organization Info',   // ❌ REMOVED
    ];

    public function run(): void
    {
        // ── 1. Upsert all permissions ─────────────────────────────────────────
        foreach ($this->matrix as $module => $actions) {
            foreach ($actions as $action) {
                $slug  = "{$module}.{$action}";
                $label = $this->labels[$slug] ?? ucwords("{$action} {$module}");

                Permission::updateOrCreate(
                    ['slug' => $slug, 'name' => $label],
                    [
                        'module' => $module,
                        'action' => $action,
                        'label'  => $label,
                    ]
                );
            }
        }

        $this->command->info('Permissions seeded: ' . Permission::count() . ' total.');

        // ── 2. Assign permissions to roles (financial management hierarchy) ────
        $this->assignRolePermissions();
    }

    private function assignRolePermissions(): void
    {
        $all = Permission::pluck('id', 'slug'); // ['financial.view' => 1, ...]

        // Define permission sets per role (using slugs)
        $matrix = [
            // System Administrator – everything
            'System Administrator' => $all->keys()->toArray(),

            // Supreme Admin – full access except system permissions
            'Supreme Admin' => $all->except(['roles.manage', 'permissions.manage'])->keys()->toArray(),

            // Supreme Officer – similar to Org Admin but higher level
            'Supreme Officer' => [
                'users.view',
                'members.view', 'members.manage',
                'documents.view', 'documents.manage',
                'financial.view', 'financial.create', 'financial.edit', 'financial.delete', 'financial.approve', 'financial.review',
                'reports.view', 'reports.generate',
                'audit.view', 'audit.remarks',
                'activities.monitor',
                // 'organization.view',   // ❌ REMOVED
            ],

            // Org Admin – full organisation management
            'Org Admin' => [
                'members.view', 'members.manage',
                'documents.view', 'documents.manage',
                'financial.view', 'financial.create', 'financial.edit', 'financial.delete', 'financial.approve', 'financial.review',
                'reports.view', 'reports.generate',
                'audit.view',
                'activities.monitor',
                // 'organization.view',   // ❌ REMOVED
            ],

            // Org Officer – operational
            'Org Officer' => [
                'members.view',
                'documents.view', 'documents.manage',
                'financial.view', 'financial.create', 'financial.edit', 'financial.delete',
                'reports.view', 'reports.generate',
                // 'organization.view',   // ❌ REMOVED
            ],

            // Club Adviser – approval and oversight
            'Club Adviser' => [
                'members.view',
                'documents.view',
                'financial.view', 'financial.approve', 'financial.review',
                'reports.view',
                'audit.view',
                'activities.monitor',
                // 'organization.view',   // ❌ REMOVED
            ],

            // Treasurer – full transaction management + reports
            'Treasurer' => [
                'financial.view', 'financial.create', 'financial.edit', 'financial.delete',
                'reports.view', 'reports.generate',
                // 'organization.view',   // ❌ REMOVED
            ],

            // Auditor – review and audit
            'Auditor' => [
                'financial.view', 'financial.review',
                'reports.view',
                'audit.view', 'audit.remarks',
                // 'organization.view',   // ❌ REMOVED
            ],

            // Member – read‑only
            'Member' => [
                'financial.view',
                'reports.view',
                // 'organization.view',   // ❌ REMOVED
            ],

            // Guest – only public reports
            'Guest' => [
                'reports.public',
            ],

            // Additional role: Admin/Adviser (if you have it separately)
            'Admin/Adviser' => [
                'financial.approve', 'financial.review',
                'reports.view',
                'activities.monitor',
                // 'organization.view',   // ❌ REMOVED
            ],
        ];

        foreach ($matrix as $roleName => $slugs) {
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $this->command->warn("Role not found, skipping: {$roleName}");
                continue;
            }

            $ids = collect($slugs)
                ->filter(fn($slug) => $all->has($slug))
                ->map(fn($slug) => $all[$slug])
                ->values()
                ->toArray();

            // Sync (replace) permissions for this role
            $role->permissions()->sync($ids);

            $this->command->line("  {$roleName}: " . count($ids) . ' permissions assigned.');
        }
    }
}