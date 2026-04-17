<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed order (respects foreign key dependencies):
     *  1. RoleSeeder            — roles table
     *  2. PermissionMatrixSeeder — permissions + role_permissions tables
     *  3. UserSeeder            — users table (needs roles)
     *  4. MemberSeeder          — members table (needs users)
     *  5. DocumentSeeder        — documents table
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionMatrixSeeder::class,  // ✅ replaced PermissionSeeder
            UserSeeder::class,
            MemberSeeder::class,
            DocumentSeeder::class,
        ]);
    }
}