<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('members', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('members', 'position_changed_at')) {
                $table->timestamp('position_changed_at')->nullable();
            }
            
            if (!Schema::hasColumn('members', 'position_changed_by')) {
                $table->unsignedBigInteger('position_changed_by')->nullable();
                $table->foreign('position_changed_by')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['position_changed_by']);
            $table->dropColumn(['position_changed_at', 'position_changed_by']);
        });
    }
};