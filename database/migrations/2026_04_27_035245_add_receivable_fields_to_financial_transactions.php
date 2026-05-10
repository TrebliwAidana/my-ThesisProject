<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Since type is now a string (not enum), just update the check constraint
        if (DB::getDriverName() !== 'mysql') {
            // Drop old check constraint if exists, then add new one with 'receivable'
            DB::statement("ALTER TABLE financial_transactions DROP CONSTRAINT IF EXISTS financial_transactions_type_check");
            DB::statement("ALTER TABLE financial_transactions ADD CONSTRAINT financial_transactions_type_check CHECK (type IN ('income', 'expense', 'receivable'))");
        }

        // Add receivable-specific columns
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
        // Revert check constraint
        if (DB::getDriverName() !== 'mysql') {
            DB::statement("ALTER TABLE financial_transactions DROP CONSTRAINT IF EXISTS financial_transactions_type_check");
            DB::statement("ALTER TABLE financial_transactions ADD CONSTRAINT financial_transactions_type_check CHECK (type IN ('income', 'expense'))");
        }

        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'due_date']);
        });
    }
};