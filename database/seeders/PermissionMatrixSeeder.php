<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\Role;

class PermissionMatrixSeeder extends Seeder
{
    /**
     * Permission matrix.
     * Format: 'module' => ['action', ...]
     *
     * Extend this list as your app grows — the seeder is idempotent
     * (uses updateOrCreate) so it is safe to re-run at any time.
     */
    private array $matrix = [
        'users'        => ['view', 'manage'],
        'members'      => ['view', 'manage'],
        'documents'    => ['view', 'manage'],
        'budgets'      => ['view', 'manage', 'review'],
        'reports'      => ['view'],
        'roles'        => ['manage'],
        'permissions'  => ['manage'],
        'organization' => ['view'],
    ];

    /**
     * Optional human-readable label overrides.
     * Key = slug, value = label string.
     * Any slug not listed here will auto-generate a label from the slug.
     */
    private array $labels = [
        'users.view'          => 'View Users',
        'users.manage'        => 'Manage Users',
        'members.view'        => 'View Members',
        'members.manage'      => 'Manage Members',
        'documents.view'      => 'View Documents',
        'documents.manage'    => 'Manage Documents',
        'budgets.view'        => 'View Budgets',
        'budgets.manage'      => 'Manage Budgets',
        'budgets.review'      => 'Review Budgets',
        'reports.view'        => 'View Reports',
        'roles.manage'        => 'Manage Roles',
        'permissions.manage'  => 'Manage Permissions',
        'organization.view'   => 'View Organization Info',
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

        // ── 2. Assign permissions to roles ────────────────────────────────────
        // Adjust role names to match exactly what is in your roles table.
        $this->assignRolePermissions();

        $this->command->info('Role–permission matrix applied.');
    }

    private function assignRolePermissions(): void
    {
        $all = Permission::pluck('id', 'slug'); // ['members.view' => 1, ...]

        $matrix = [
            // Role name => array of slugs it should have
            'System Administrator' => $all->keys()->toArray(), // every permission
            'Supreme Admin'        => $all->keys()->toArray(),
            'Supreme Officer'      => $all->except(['roles.manage', 'permissions.manage'])->keys()->toArray(),
            'Org Admin'            => [
                'users.view', 'users.manage',
                'members.view', 'members.manage',
                'documents.view', 'documents.manage',
                'budgets.view', 'budgets.manage', 'budgets.review',
                'reports.view',
                'organization.view',
            ],
            'Org Officer'          => [
                'members.view',
                'documents.view', 'documents.manage',
                'budgets.view',
                'reports.view',
                'organization.view',
            ],
            'Club Adviser'         => [
                'members.view',
                'documents.view',
                'budgets.view', 'budgets.review',
                'reports.view',
                'organization.view',
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

            // sync() replaces the current set — safe to re-run
            $role->permissions()->sync($ids);

            $this->command->line("  {$roleName}: " . count($ids) . ' permissions assigned.');
        }
    }
}