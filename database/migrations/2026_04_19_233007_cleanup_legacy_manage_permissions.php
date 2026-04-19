<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Delete all permissions where the action is 'manage'
        DB::table('permissions')->where('action', 'manage')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is destructive; we cannot automatically restore the deleted rows.
        // To restore, you would need to re‑seed the old permission set using the PermissionMatrixSeeder
        // from before the fine‑grained changes.
    }
};