<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('organizations')) {
                return;
            }
            Schema::table('organizations', function (Blueprint $table) {
                // Add columns only if they don't already exist
                if (!Schema::hasColumn('organizations', 'type')) {
                    $table->string('type')->default('club')->after('abbreviation');
                }
                if (!Schema::hasColumn('organizations', 'academic_year')) {
                    $table->string('academic_year')->nullable()->after('type');
                }
            });
    }

    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['type', 'academic_year']);
        });
    }
};