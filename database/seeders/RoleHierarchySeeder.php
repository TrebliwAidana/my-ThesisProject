<?php

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleHierarchySeeder extends Seeder
{
    public function run()
    {
        // First, create new roles (won't affect existing)
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'level' => 1,
            'is_system' => true,
            'permissions' => ['*'] // All permissions
        ]);
        
        $ssgAdmin = Role::create([
            'name' => 'SSG Admin',
            'parent_id' => $superAdmin->id,
            'level' => 2,
            'is_system' => true,
            'permissions' => ['manage_all', 'approve_budgets', 'manage_orgs']
        ]);
        
        $ssgOfficer = Role::create([
            'name' => 'SSG Officer',
            'parent_id' => $ssgAdmin->id,
            'level' => 3,
            'is_system' => true,
            'permissions' => ['view_budgets', 'submit_proposals']
        ]);
        
        // Map existing roles to hierarchy
        $adviser = Role::where('name', 'Adviser')->first();
        if ($adviser) {
            $adviser->update([
                'parent_id' => $ssgAdmin->id,
                'level' => 2,
                'is_system' => true
            ]);
        }
        
        $officer = Role::where('name', 'Officer')->first();
        if ($officer) {
            $officer->update([
                'parent_id' => $ssgAdmin->id,
                'level' => 3,
                'is_system' => true
            ]);
        }
    }
}