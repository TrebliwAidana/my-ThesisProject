<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->foreignId('income_transaction_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('financial_transactions')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropForeign(['income_transaction_id']);
            $table->dropColumn('income_transaction_id');
        });
    }
};