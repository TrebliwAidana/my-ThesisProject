<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionMatrixSeeder extends Seeder
{
    /**
     * Permission matrix – fine‑grained CRUD.
     * Format: 'module' => ['action', ...]
     */
    private array $matrix = [
        // Users – fine‑grained
        'users'     => ['view', 'create', 'edit', 'delete'],

        // Members – fine‑grained
        'members'   => ['view', 'create', 'edit', 'delete'],

        // Documents – fine‑grained + trash actions
        'documents' => ['view', 'create', 'edit', 'delete', 'trash', 'restore', 'force-delete'],

        // Categories – new module for document categories
        'categories' => ['view', 'create', 'edit', 'delete'],

        // Financial – fine‑grained + audit / approve / review
        'financial' => ['view', 'create', 'edit', 'delete', 'audit', 'approve', 'review'],

        // Reports
        'reports'   => ['view', 'generate', 'public'],

        // Audit & monitoring
        'audit'     => ['view', 'remarks'],
        'activities'=> ['monitor'],

        // Roles – fine‑grained
        'roles'     => ['view', 'create', 'edit', 'delete'],

        // Permissions – view + edit (no create/delete needed)
        'permissions' => ['view', 'edit'],
    ];

    /**
     * Human‑readable labels.
     */
    private array $labels = [
        // Users
        'users.view'   => 'View Users',
        'users.create' => 'Create Users',
        'users.edit'   => 'Edit Users',
        'users.delete' => 'Delete Users',

        // Members
        'members.view'   => 'View Members',
        'members.create' => 'Create Members',
        'members.edit'   => 'Edit Members',
        'members.delete' => 'Delete Members',

        // Documents
        'documents.view'         => 'View Documents',
        'documents.create'       => 'Upload Documents',
        'documents.edit'         => 'Edit Documents',
        'documents.delete'       => 'Delete Documents',
        'documents.trash'        => 'View Trash',
        'documents.restore'      => 'Restore Documents',
        'documents.force-delete' => 'Permanently Delete',

        // Categories
        'categories.view'   => 'View Document Categories',
        'categories.create' => 'Create Document Categories',
        'categories.edit'   => 'Edit Document Categories',
        'categories.delete' => 'Delete Document Categories',

        // Financial
        'financial.view'    => 'View Financial Records',
        'financial.create'  => 'Record Income/Expense',
        'financial.edit'    => 'Edit Transactions',
        'financial.delete'  => 'Delete Transactions',
        'financial.audit'   => 'Audit Transactions',
        'financial.approve' => 'Approve Transactions',
        'financial.review'  => 'Review Transactions',

        // Reports
        'reports.view'     => 'View Reports',
        'reports.generate' => 'Generate Reports',
        'reports.public'   => 'View Public Reports',

        // Audit
        'audit.view'    => 'View Audit Logs',
        'audit.remarks' => 'Add Audit Remarks',

        // Activities
        'activities.monitor' => 'Monitor Activities',

        // Roles
        'roles.view'   => 'View Roles',
        'roles.create' => 'Create Roles',
        'roles.edit'   => 'Edit Roles',
        'roles.delete' => 'Delete Roles',

        // Permissions
        'permissions.view' => 'View Permissions',
        'permissions.edit' => 'Edit Permissions',
    ];

    public function run(): void
    {
        // 1. Upsert all permissions
        foreach ($this->matrix as $module => $actions) {
            foreach ($actions as $action) {
                $slug  = "{$module}.{$action}";
                $label = $this->labels[$slug] ?? ucwords("{$action} {$module}");

                Permission::updateOrCreate(
                    ['slug' => $slug], 
                    [
                        'name'   => $label,
                        'module' => $module,
                        'action' => $action,
                        'label'  => $label,
                    ]
                );
            }
        }

        $this->command->info('Permissions seeded: ' . Permission::count() . ' total.');

        // 2. Assign permissions to roles
        $this->assignRolePermissions();
    }

    private function assignRolePermissions(): void
    {
        $all = Permission::pluck('id', 'slug');

        $matrix = [
            'System Administrator' => $all->keys()->toArray(),

            'Supreme Admin' => $all->except([
                'roles.create', 'roles.edit', 'roles.delete',
                'permissions.edit'
            ])->keys()->toArray(),

            'Supreme Officer' => [
                'users.view',
                'members.view', 'members.create', 'members.edit', 'members.delete',
                'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
                'documents.trash', 'documents.restore', 'documents.force-delete',
                'categories.view', // allow viewing categories
                'financial.view', 'financial.create', 'financial.edit', 'financial.delete',
                'financial.audit', 'financial.approve', 'financial.review',
                'reports.view', 'reports.generate',
                'audit.view', 'audit.remarks',
                'activities.monitor',
            ],

            'Org Admin' => [
                'members.view', 'members.create', 'members.edit', 'members.delete',
                'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
                'documents.trash', 'documents.restore', 'documents.force-delete',
                'categories.view', 'categories.create', 'categories.edit', 'categories.delete', // full category management
                'financial.view', 'financial.create', 'financial.edit', 'financial.delete',
                'financial.audit', 'financial.approve', 'financial.review',
                'reports.view', 'reports.generate',
                'audit.view',
                'activities.monitor',
            ],

            'Org Officer' => [
                'members.view',
                'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
                'documents.trash', 'documents.restore',
                'categories.view', // only view, not edit/delete
                'financial.view', 'financial.create', 'financial.edit', 'financial.delete',
                'reports.view', 'reports.generate',
            ],

            'Club Adviser' => [
                'members.view',
                'documents.view', 'documents.trash', 'documents.restore',
                'categories.view', // view categories
                'financial.view', 'financial.audit', 'financial.approve', 'financial.review',
                'reports.view',
                'audit.view',
                'activities.monitor',
            ],

            'Treasurer' => [
                'financial.view', 'financial.create', 'financial.edit', 'financial.delete',
                'reports.view', 'reports.generate',
            ],

            'Auditor' => [
                'financial.view', 'financial.audit', 'financial.review',
                'reports.view',
                'audit.view', 'audit.remarks',
            ],

            'Member' => [
                'financial.view',
                'reports.view',
            ],

            'Guest' => [
                'reports.public',
            ],

            'Admin/Adviser' => [
                'financial.audit', 'financial.approve', 'financial.review',
                'reports.view',
                'activities.monitor',
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

            $role->permissions()->sync($ids);

            $this->command->line("  {$roleName}: " . count($ids) . ' permissions assigned.');
        }
    }
}