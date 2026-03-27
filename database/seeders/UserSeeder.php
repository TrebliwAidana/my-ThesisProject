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
                'email'      => 'admin@vsulhs-sslg.com',
                'password'   => Hash::make('Admin@1234'),
                'role_id'    => 1, // Admin
                'theme'      => 'navy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name'  => 'Bob Santos',
                'email'      => 'officer@vsulhs-sslg.com',
                'password'   => Hash::make('Officer@1234'),
                'role_id'    => 2, // Officer
                'theme'      => 'navy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name'  => 'Clara Dela Cruz',
                'email'      => 'auditor@vsulhs-sslg.com',
                'password'   => Hash::make('Auditor@1234'),
                'role_id'    => 3, // Auditor
                'theme'      => 'navy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'full_name'  => 'Dan Mercado',
                'email'      => 'member@vsulhs-sslg.com',
                'password'   => Hash::make('Member@1234'),
                'role_id'    => 4, // Member
                'theme'      => 'navy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}