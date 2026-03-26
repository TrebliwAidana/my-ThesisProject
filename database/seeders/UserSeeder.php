<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'full_name'  => 'Alice Reyes',
                'email'      => 'admin@example.com',
                'password'   => Hash::make('password'),
                'role_id'    => 1, // Admin
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name'  => 'Bob Santos',
                'email'      => 'officer@example.com',
                'password'   => Hash::make('password'),
                'role_id'    => 2, // Officer
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name'  => 'Clara Dela Cruz',
                'email'      => 'auditor@example.com',
                'password'   => Hash::make('password'),
                'role_id'    => 3, // Auditor
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name'  => 'Dan Mercado',
                'email'      => 'member@example.com',
                'password'   => Hash::make('password'),
                'role_id'    => 4, // Member
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
