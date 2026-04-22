<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('financial_transactions', function (Blueprint $table) {
            $table->boolean('is_receivable')->default(false)->after('notes');
            $table->boolean('receivable_paid')->default(false)->after('is_receivable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            //
        });
    }
};
