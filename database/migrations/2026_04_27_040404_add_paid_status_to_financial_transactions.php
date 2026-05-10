<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'paid' to status — used by receivables marked as collected
        if (DB::getDriverName() === 'mysql') {
            // MySQL still uses MODIFY (local Laragon dev)
            DB::statement("ALTER TABLE financial_transactions MODIFY COLUMN `status` ENUM('pending','audited','approved','rejected','paid') NOT NULL DEFAULT 'pending'");
        } else {
            // PostgreSQL (Render) — update check constraint
            DB::statement("ALTER TABLE financial_transactions DROP CONSTRAINT IF EXISTS financial_transactions_status_check");
            DB::statement("ALTER TABLE financial_transactions ADD CONSTRAINT financial_transactions_status_check CHECK (status IN ('pending','audited','approved','rejected','paid'))");
        }
    }

    public function down(): void
    {
        // WARNING: any rows with status='paid' will break this rollback
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE financial_transactions MODIFY COLUMN `status` ENUM('pending','audited','approved','rejected') NOT NULL DEFAULT 'pending'");
        } else {
            DB::statement("ALTER TABLE financial_transactions DROP CONSTRAINT IF EXISTS financial_transactions_status_check");
            DB::statement("ALTER TABLE financial_transactions ADD CONSTRAINT financial_transactions_status_check CHECK (status IN ('pending','audited','approved','rejected'))");
        }
    }
};