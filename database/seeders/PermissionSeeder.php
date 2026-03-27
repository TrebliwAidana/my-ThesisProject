<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;


class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------
        | 1. PERMISSIONS
        |--------------------------------------------------------------
        | Slug                  | Description
        |--------------------------------------------------------------
        | manage-users          | Create / edit / delete user accounts
        | manage-settings       | Access portal settings (theme etc.)
        | view-audit-logs       | View audit / activity logs
        | manage-members        | Create / edit / delete members
        | view-members          | View members list
        | manage-documents      | Upload / edit / delete documents
        | view-documents        | View & download documents
        | manage-budgets        | Create / edit / delete budget entries
        | view-budgets          | View budget records
        |--------------------------------------------------------------
        */

        $permissions = [
            'manage-users',
            'manage-settings',
            'view-audit-logs',
            'manage-members',
            'view-members',
            'manage-documents',
            'view-documents',
            'manage-budgets',
            'view-budgets',
        ];

        foreach ($permissions as $name) {
            DB::table('permissions')->insertOrIgnore([
                'name'       => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        /*
        |--------------------------------------------------------------
        | 2. ROLE → PERMISSION MATRIX
        |--------------------------------------------------------------
        | Permission            | Admin | Officer | Auditor | Member |
        |--------------------------------------------------------------
        | manage-users          |   ✓   |         |         |        |
        | manage-settings       |   ✓   |         |         |        |
        | view-audit-logs       |   ✓   |         |    ✓    |        |
        | manage-members        |   ✓   |    ✓    |         |        |
        | view-members          |   ✓   |    ✓    |    ✓    |   ✓    |
        | manage-documents      |   ✓   |    ✓    |         |        |
        | view-documents        |   ✓   |    ✓    |    ✓    |   ✓    |
        | manage-budgets        |   ✓   |    ✓    |         |        |
        | view-budgets          |   ✓   |    ✓    |    ✓    |        |
        |--------------------------------------------------------------
        | role_id               |   1   |    2    |    3    |    4   |
        |--------------------------------------------------------------
        */

        // Helper: resolve permission id by name
        $pid = fn (string $name) => DB::table('permissions')->where('name', $name)->value('id');

        $matrix = [
            // role_id => [permission slugs]
            1 => [ // Admin — full access
                'manage-users',
                'manage-settings',
                'view-audit-logs',
                'manage-members',
                'view-members',
                'manage-documents',
                'view-documents',
                'manage-budgets',
                'view-budgets',
            ],
            2 => [ // Officer — manage content, no user admin
                'manage-members',
                'view-members',
                'manage-documents',
                'view-documents',
                'manage-budgets',
                'view-budgets',
            ],
            3 => [ // Auditor — read-only across all modules
                'view-audit-logs',
                'view-members',
                'view-documents',
                'view-budgets',
            ],
            4 => [ // Member — view own-scope data only
                'view-members',
                'view-documents',
            ],
        ];

        foreach ($matrix as $roleId => $perms) {
            foreach ($perms as $perm) {
                DB::table('role_permissions')->insertOrIgnore([
                    'role_id'       => $roleId,
                    'permission_id' => $pid($perm),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }
    }
}