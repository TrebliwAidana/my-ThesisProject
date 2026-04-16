<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Treasurer',       'abbreviation' => 'TR', 'level' => 3, 'is_visible' => true],
            ['name' => 'Auditor',         'abbreviation' => 'AU', 'level' => 4, 'is_visible' => true],
            ['name' => 'Member',          'abbreviation' => 'MB', 'level' => 5, 'is_visible' => true],
            ['name' => 'Admin/Adviser',   'abbreviation' => 'AA', 'level' => 2, 'is_visible' => true],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}