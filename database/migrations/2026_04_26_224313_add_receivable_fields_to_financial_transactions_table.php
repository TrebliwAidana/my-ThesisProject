<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            // Receivable-specific fields — nullable so income/expense rows are unaffected
            $table->string('customer_name')->nullable()->after('notes');
            $table->date('due_date')->nullable()->after('customer_name');
            // 'paid' is the new status for receivables that have been manually marked paid
            // existing statuses: pending, audited, approved, rejected
        });
    }

    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'due_date']);
        });
    }
};
