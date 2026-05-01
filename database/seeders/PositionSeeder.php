<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        // Map positions to role_id according to your roles table
        $positions = [
            1 => ['System Administrator'],
            2 => ['Club Adviser'],
            3 => ['Treasurer'],
            4 => ['Auditor'],
            5 => ['Guest'],
        ];

        foreach ($positions as $roleId => $rolePositions) {
            foreach ($rolePositions as $name) {
                DB::table('positions')->insert([
                    'name' => $name,
                    'role_id' => $roleId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}