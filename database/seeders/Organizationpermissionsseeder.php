<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class OrganizationPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Create organization permissions ────────────────────────────────
        $permissions = [
            [
                'name'   => 'View Organizations',
                'slug'   => 'organization.view',
                'module' => 'organization',
                'action' => 'view',
            ],
            [
                'name'   => 'Create Organization',
                'slug'   => 'organization.create',
                'module' => 'organization',
                'action' => 'create',
            ],
            [
                'name'   => 'Edit Organization',
                'slug'   => 'organization.edit',
                'module' => 'organization',
                'action' => 'edit',
            ],
            [
                'name'   => 'Delete Organization',
                'slug'   => 'organization.delete',
                'module' => 'organization',
                'action' => 'delete',
            ],
            [
                'name'   => 'Toggle Organization Status',
                'slug'   => 'organization.toggle',
                'module' => 'organization',
                'action' => 'manage',
            ],
            [
                'name'   => 'View Organization Analytics',
                'slug'   => 'organization.view_analytics',
                'module' => 'organization',
                'action' => 'view',
            ],
        ];

        $created = [];
        foreach ($permissions as $perm) {
            $created[$perm['slug']] = Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }

        // ── 2. Assign permissions to roles ────────────────────────────────────
        //
        // Based on OrganizationController logic:
        //   requireLevel1()  → SysAdmin only      (create, store, destroy, toggleActive)
        //   level !== 1 check → SysAdmin + own org (index, show, edit, update)
        //   abbreviation check → SysAdmin, SA, OA  (edit specifically)
        //
        $rolePermissions = [
            // System Administrator — full access
            'SysAdmin' => [
                'organization.view',
                'organization.create',
                'organization.edit',
                'organization.delete',
                'organization.toggle',
                'organization.view_analytics',
            ],
            // Supreme Admin — view + edit own, no create/delete/toggle
            'SA' => [
                'organization.view',
                'organization.edit',
                'organization.view_analytics',
            ],
            // Club Adviser — view + analytics only
            'CA' => [
                'organization.view',
                'organization.view_analytics',
            ],
            // Org Admin — view + edit own org
            'OA' => [
                'organization.view',
                'organization.edit',
                'organization.view_analytics',
            ],
            // Org Officer — view only
            'OO' => [
                'organization.view',
            ],
            // Org Member — view only
            'OM' => [
                'organization.view',
            ],
        ];

        foreach ($rolePermissions as $abbrev => $slugs) {
            $role = Role::where('abbreviation', $abbrev)->first();

            if (! $role) {
                $this->command->warn("Role '{$abbrev}' not found — skipping.");
                continue;
            }

            $permIds = collect($slugs)
                ->map(fn($slug) => $created[$slug]?->id)
                ->filter()
                ->toArray();

            // sync without detaching — keeps any existing permissions intact
            $role->permissions()->syncWithoutDetaching($permIds);

            $this->command->info("✅ Assigned " . count($permIds) . " organization permissions to '{$role->name}'.");
        }
    }
}