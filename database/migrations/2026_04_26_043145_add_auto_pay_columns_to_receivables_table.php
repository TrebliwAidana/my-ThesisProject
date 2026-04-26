<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            // Track who marked it paid and when (auto or manual)
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropConstrainedForeignId('paid_by');
            $table->dropColumn('paid_at');
        });
    }
};