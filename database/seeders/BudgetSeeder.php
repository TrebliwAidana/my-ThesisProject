<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BudgetSeeder extends Seeder
{
    public function run(): void
    {
        // member id 1 = Bob Santos (Vice President)
        // member id 2 = Dan Mercado (General Member)
        DB::table('budgets')->insert([
            [
                'amount'      => 50000.00,
                'desc'        => 'Q1 operational budget for office supplies and utilities.',
                'reviewed_by' => 1, // Bob Santos
                'status'      => 'approved',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'amount'      => 15000.00,
                'desc'        => 'Budget for annual team event.',
                'reviewed_by' => 1, // Bob Santos
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'amount'      => 8500.00,
                'desc'        => 'Equipment maintenance and repair fund.',
                'reviewed_by' => null, // not yet reviewed
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}