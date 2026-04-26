<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1 — Modify the type ENUM to include 'receivable'
        DB::statement("ALTER TABLE financial_transactions MODIFY COLUMN `type` ENUM('income', 'expense', 'receivable') NOT NULL");

        // Step 2 — Add receivable-specific columns (nullable so existing rows are unaffected)
        Schema::table('financial_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('financial_transactions', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('financial_transactions', 'due_date')) {
                $table->date('due_date')->nullable()->after('customer_name');
            }
        });
    }

    public function down(): void
    {
        // Revert type ENUM back to original
        DB::statement("ALTER TABLE financial_transactions MODIFY COLUMN `type` ENUM('income', 'expense') NOT NULL");

        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'due_date']);
        });
    }
};
