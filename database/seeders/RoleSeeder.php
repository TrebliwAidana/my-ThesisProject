<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name'       => 'Admin',
                'desc'       => 'Full access to all modules including user management.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Officer',
                'desc'       => 'Can manage documents, budgets, and members but cannot manage users.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Auditor',
                'desc'       => 'Read-only access to all modules. Cannot create, edit, or delete.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Member',
                'desc'       => 'Can only view their own profile and related data.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
