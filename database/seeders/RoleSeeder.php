<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'System Administrator', 'abbreviation' => 'SysAdmin', 'level' => 1, 'is_visible' => true, 'is_predefined' => true], // main role
            ['name' => 'Admin/Adviser',        'abbreviation' => 'AA',       'level' => 2, 'is_visible' => true, 'is_predefined' => true], // main role
            ['name' => 'Supreme Admin',        'abbreviation' => 'SA',       'level' => 2, 'is_visible' => true, 'is_predefined' => true],
            ['name' => 'Supreme Officer',      'abbreviation' => 'SO',       'level' => 3, 'is_visible' => true, 'is_predefined' => true],
            ['name' => 'Club Adviser',         'abbreviation' => 'CA',       'level' => 4, 'is_visible' => true, 'is_predefined' => true],
            ['name' => 'Org Admin',            'abbreviation' => 'OA',       'level' => 5, 'is_visible' => true, 'is_predefined' => true],
            ['name' => 'Org Officer',          'abbreviation' => 'OO',       'level' => 6, 'is_visible' => true, 'is_predefined' => true],
            ['name' => 'Treasurer',            'abbreviation' => 'TR',       'level' => 7, 'is_visible' => true, 'is_predefined' => true], // main role
            ['name' => 'Auditor',              'abbreviation' => 'AU',       'level' => 8, 'is_visible' => true, 'is_predefined' => true],// main role
            ['name' => 'Org Member',           'abbreviation' => 'OM',       'level' => 9, 'is_visible' => true, 'is_predefined' => true],
            ['name'=> 'Member',                'abbreviation' => 'M',        'level'=>  9, 'is_visible' => true,  'is_predefined' => true], // main role
            ['name' => 'Guest',                'abbreviation' => 'G',        'level' => 10, 'is_visible' => false, 'is_predefined' => true], // main role
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['name' => $roleData['name']], $roleData);
        }

        $this->command->info('All roles seeded successfully!');
    }
}