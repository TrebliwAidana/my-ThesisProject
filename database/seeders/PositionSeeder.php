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
            2 => ['SSLG President', 'SSLG Adviser', 'Student Affairs Head'],
            3 => ['SSLG Secretary', 'SSLG Treasurer', 'SSLG PIO'],
            4 => ['Organization President', 'Organization Vice President'],
            5 => ['Organization Secretary', 'Organization Treasurer', 'Organization Auditor', 'Organization PIO'],
            6 => ['Club Adviser'],
            7 => ['Regular Member'],
            8 => ['Guest'],
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