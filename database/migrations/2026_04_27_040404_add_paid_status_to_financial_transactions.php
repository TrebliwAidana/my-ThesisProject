<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'paid' to the status ENUM — used exclusively by receivables
        // that have been manually marked as collected
        DB::statement("ALTER TABLE financial_transactions MODIFY COLUMN `status` ENUM('pending','audited','approved','rejected','paid') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert — WARNING: any rows with status='paid' will break this rollback
        DB::statement("ALTER TABLE financial_transactions MODIFY COLUMN `status` ENUM('pending','audited','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};