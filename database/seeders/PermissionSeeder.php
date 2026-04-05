<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * All system permissions.
     * 'name' is the dot-key ("module.action") — matches your existing schema.
     * 'module', 'action', 'label' fill the new columns added by the fix migration.
     */
    private array $permissions = [
        // Members
        ['name' => 'members.view',   'module' => 'members',   'action' => 'view',   'label' => 'View Members'],
        ['name' => 'members.create', 'module' => 'members',   'action' => 'create', 'label' => 'Create Members'],
        ['name' => 'members.edit',   'module' => 'members',   'action' => 'edit',   'label' => 'Edit Members'],
        ['name' => 'members.delete', 'module' => 'members',   'action' => 'delete', 'label' => 'Delete Members'],

        // Documents
        ['name' => 'documents.view',   'module' => 'documents', 'action' => 'view',   'label' => 'View Documents'],
        ['name' => 'documents.create', 'module' => 'documents', 'action' => 'create', 'label' => 'Upload Documents'],
        ['name' => 'documents.edit',   'module' => 'documents', 'action' => 'edit',   'label' => 'Edit Documents'],
        ['name' => 'documents.delete', 'module' => 'documents', 'action' => 'delete', 'label' => 'Delete Documents'],

        // Budgets
        ['name' => 'budgets.view',     'module' => 'budgets',   'action' => 'view',     'label' => 'View Budgets'],
        ['name' => 'budgets.submit',   'module' => 'budgets',   'action' => 'submit',   'label' => 'Submit Budgets'],
        ['name' => 'budgets.approve',  'module' => 'budgets',   'action' => 'approve',  'label' => 'Approve/Reject Budgets'],
        ['name' => 'budgets.disburse', 'module' => 'budgets',   'action' => 'disburse', 'label' => 'Disburse Budgets'],
    ];

    /**
     * Default permissions per role name (case-insensitive match).
     * '*' = all actions for that module.
     */
    private array $roleDefaults = [
        'System Administrator' => ['members.*', 'documents.*', 'budgets.*'],
        'Supreme Admin'        => ['members.*', 'documents.*', 'budgets.*'],
        'Supreme Officer'      => [
            'members.view', 'members.create', 'members.edit',
            'documents.view', 'documents.create', 'documents.edit',
            'budgets.view', 'budgets.submit',
        ],
        'Club Adviser'         => [
            'members.view', 'members.create', 'members.edit',
            'documents.view', 'documents.create', 'documents.edit',
            'budgets.view', 'budgets.approve',
        ],
        'Org Admin'            => [
            'members.view', 'members.create', 'members.edit',
            'documents.view', 'documents.create',
            'budgets.view', 'budgets.submit',
        ],
        'Org Officer'          => [
            'members.view',
            'documents.view', 'documents.create',
            'budgets.view', 'budgets.submit',
        ],
        'Org Member'           => ['members.view', 'documents.view', 'budgets.view'],
        'Guest'                => ['documents.view'],
    ];

    public function run(): void
    {
        // ── 1. Upsert all permissions ─────────────────────────────────────────
        $permissionMap = []; // 'module.action' => Permission

        foreach ($this->permissions as $data) {
            $perm = Permission::updateOrCreate(
                ['name' => $data['name']],
                [
                    'module' => $data['module'],
                    'action' => $data['action'],
                    'label'  => $data['label'],
                ]
            );
            $permissionMap[$data['name']] = $perm;
        }

        // ── 2. Assign to roles ────────────────────────────────────────────────
        Role::all()->each(function (Role $role) use ($permissionMap) {
            // Match role by name (case-insensitive)
            $defaultKey = collect(array_keys($this->roleDefaults))
                ->first(fn($k) => strtolower($k) === strtolower($role->name));

            $keys = $defaultKey
                ? $this->roleDefaults[$defaultKey]
                : ['members.view', 'documents.view', 'budgets.view']; // safe fallback

            // Expand wildcards
            $ids = [];
            foreach ($keys as $key) {
                if (str_ends_with($key, '.*')) {
                    $module = str_replace('.*', '', $key);
                    foreach ($permissionMap as $dotKey => $perm) {
                        if ($perm->module === $module) {
                            $ids[] = $perm->id;
                        }
                    }
                } elseif (isset($permissionMap[$key])) {
                    $ids[] = $permissionMap[$key]->id;
                }
            }

            $role->permissions()->sync(array_unique($ids));

            // Clear any cached permissions for users with this role
            $role->users()->each(fn($u) => cache()->forget("user_perms_{$u->id}"));
        });

        $this->command->info('✅ Permissions seeded and assigned to all roles.');
    }
}