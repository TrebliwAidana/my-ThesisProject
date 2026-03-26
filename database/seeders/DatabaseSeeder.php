<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds in dependency order:
     * roles → users → members → documents → budgets
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            MemberSeeder::class,
            DocumentSeeder::class,
            BudgetSeeder::class,
        ]);
    }
}
