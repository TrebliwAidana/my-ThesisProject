<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        // Only the user with role=Member (user_id 4) gets a members table record
        // But Officers and Admin can also be linked as members if needed
        DB::table('members')->insert([
            [
                'user_id'    => 2, // Bob Santos — Officer also serving as a member
                'position'   => 'Vice President',
                'term_start' => '2024-01-01',
                'term_end'   => '2024-12-31',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'    => 4, // Dan Mercado — role=Member
                'position'   => 'General Member',
                'term_start' => '2024-01-01',
                'term_end'   => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
