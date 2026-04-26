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
        Schema::table('cache', function (Blueprint $table) {
            $table->index('key');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->index('id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
