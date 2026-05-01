<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialCategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $categories = [

            // ─────────────────────────────────────────────────────────────────
            // INCOME
            // ─────────────────────────────────────────────────────────────────
            [
                'name'        => 'Membership & Dues',
                'type'        => 'income',
                'description' => 'Fees collected for active membership, including annual dues, semester fees, and one-time induction fees.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Fundraising Events',
                'type'        => 'income',
                'description' => 'Proceeds from bake sales, car washes, raffles, auctions, fun runs, walk-a-thons, and seasonal sales.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Activity & Service Income',
                'type'        => 'income',
                'description' => 'Revenue from events, merchandise sales, lock-ins, and tutoring.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Sponsorships & Donations',
                'type'        => 'income',
                'description' => 'Cash or in-kind support from businesses, parents, alumni, and grants.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Other Income',
                'type'        => 'income',
                'description' => 'Bank interest, forfeited deposits, and recycling proceeds.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],

            // ─────────────────────────────────────────────────────────────────
            // EXPENSE
            // ─────────────────────────────────────────────────────────────────
            [
                'name'        => 'Events & Activities',
                'type'        => 'expense',
                'description' => 'Decorations, entertainment, food, prizes, photography, rentals.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Promotional & Marketing',
                'type'        => 'expense',
                'description' => 'Flyers, posters, ads, banners, signage.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Supplies & Materials',
                'type'        => 'expense',
                'description' => 'Office and craft supplies, tags, lanyards, batteries.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Merchandise Production',
                'type'        => 'expense',
                'description' => 'Production of shirts, hoodies, stickers, etc.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Travel & Competitions',
                'type'        => 'expense',
                'description' => 'Registration, transport, lodging, meals.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Service & Community Projects',
                'type'        => 'expense',
                'description' => 'Donations, project materials, appreciation gifts.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Administrative',
                'type'        => 'expense',
                'description' => 'Bank fees, hosting, printing, storage.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],

            // ─────────────────────────────────────────────────────────────────
            // RECEIVABLE
            // ─────────────────────────────────────────────────────────────────
            [
                'name'        => 'Member Related Receivables',
                'type'        => 'receivable',
                'description' => 'Unpaid dues, ticket balances, deposits, fines.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Event Related Receivables',
                'type'        => 'receivable',
                'description' => 'Uncollected sponsor pledges and grants.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Fundraising Receivable',
                'type'        => 'receivable',
                'description' => 'Unreturned raffle funds and unpaid auction bids.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'name'        => 'Staff / Advisor Related',
                'type'        => 'receivable',
                'description' => 'Cash advances pending receipts and fines.',
                'is_active'   => true,
                'created_by'  => 1,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        // Use upsert to avoid duplicate entries on re-seed
        DB::table('financial_categories')->upsert(
            $categories,
            ['name', 'type'],       // unique keys to match on
            ['description', 'is_active', 'updated_at']  // columns to update if exists
        );
    }
}