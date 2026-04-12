<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class AddOrganizationManagePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create the permission without description
        $perm = Permission::firstOrCreate([
            'name'   => 'organization.manage',
            'slug'   => 'organization.manage',
            'action' => 'manage',
            'module' => 'organization',
        ]);


        // Assign to System Administrator and Supreme Admin roles
        $roles = \App\Models\Role::whereIn('name', ['System Administrator', 'Supreme Admin'])->get();
        foreach ($roles as $role) {
            if (!$role->permissions->contains($perm->id)) {
                $role->permissions()->attach($perm->id);
            }
        }
    }
}