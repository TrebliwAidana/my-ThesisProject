<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->foreignId('receivable_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('receivables')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropForeign(['receivable_id']);
            $table->dropColumn('receivable_id');
        });
    }
};