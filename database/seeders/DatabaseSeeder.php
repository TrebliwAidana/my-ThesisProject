<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed order (respects foreign key dependencies):
     *  1. RoleSeeder        — roles table
     *  2. PermissionSeeder  — permissions + role_permissions tables
     *  3. UserSeeder        — users table (needs roles)
     *  4. MemberSeeder      — members table (needs users)
     *  5. DocumentSeeder    — documents table
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            MemberSeeder::class,
            DocumentSeeder::class,
        ]);
    }
}