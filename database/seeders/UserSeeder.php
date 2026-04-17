<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Lookup roles by name
        $sysAdmin = Role::where('name', 'System Administrator')->first();
        $adviser  = Role::where('name', 'Club Adviser')->first();
        $treasurer = Role::where('name', 'Treasurer')->first();
        $auditor  = Role::where('name', 'Auditor')->first();
        $member   = Role::where('name', 'Org Member')->first();
        $guest    = Role::where('name', 'Guest')->first();

        // System Administrator
        User::firstOrCreate(
            ['email' => 'sysadmin@gmail.com'],
            [
                'full_name'       => 'System Administrator',
                'first_name'      => 'System',
                'last_name'       => 'Administrator',
                'password'        => Hash::make('password123'),
                'role_id'         => $sysAdmin?->id,
                'is_active'       => true,
                'email_verified_at' => now(),
                'theme'           => 'navy',
            ]
        );

        // Club Adviser
        User::firstOrCreate(
            ['email' => 'adviser@gmail.com'],
            [
                'full_name'       => 'Club Adviser',
                'first_name'      => 'Club',
                'last_name'       => 'Adviser',
                'password'        => Hash::make('password123'),
                'role_id'         => $adviser?->id,
                'is_active'       => true,
                'email_verified_at' => now(),
                'theme'           => 'navy',
            ]
        );

        // Treasurer
        User::firstOrCreate(
            ['email' => 'treasurer@gmail.com'],
            [
                'full_name'       => 'Treasurer User',
                'first_name'      => 'Treasurer',
                'last_name'       => 'User',
                'password'        => Hash::make('password123'),
                'role_id'         => $treasurer?->id,
                'is_active'       => true,
                'email_verified_at' => now(),
                'theme'           => 'navy',
            ]
        );

        // Auditor
        User::firstOrCreate(
            ['email' => 'auditor@gmail.com'],
            [
                'full_name'       => 'Auditor User',
                'first_name'      => 'Auditor',
                'last_name'       => 'User',
                'password'        => Hash::make('password123'),
                'role_id'         => $auditor?->id,
                'is_active'       => true,
                'email_verified_at' => now(),
                'theme'           => 'navy',
            ]
        );

        // Org Member
        User::firstOrCreate(
            ['email' => 'member@gmail.com'],
            [
                'full_name'       => 'Regular Member',
                'first_name'      => 'Regular',
                'last_name'       => 'Member',
                'password'        => Hash::make('password123'),
                'role_id'         => $member?->id,
                'is_active'       => true,
                'email_verified_at' => now(),
                'theme'           => 'navy',
            ]
        );

        // Guest (if not already exists)
        User::firstOrCreate(
            ['email' => 'guest@gmail.com'],
            [
                'full_name'       => 'Guest User',
                'first_name'      => 'Guest',
                'last_name'       => 'User',
                'password'        => Hash::make(Str::random(40)),
                'role_id'         => $guest?->id,
                'is_active'       => true,
                'email_verified_at' => now(),
                'theme'           => 'navy',
            ]
        );

        $this->command->info('Sample users seeded successfully!');
    }
}