<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
            // Add the 'type' column (adjust allowed values as needed)
            $table->string('type')->default('club')->after('abbreviation');
            
            // Add the 'academic_year' column
            $table->string('academic_year')->nullable()->after('type');
        });
    }

    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['type', 'academic_year']);
        });
    }
};