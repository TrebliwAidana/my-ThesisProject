<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Drop all foreign keys that reference the organizations table
        $tables = ['users', 'documents', 'budgets']; // add any other tables that have organization_id
        foreach ($tables as $table) {
            // Get the foreign key constraint name
            $fkName = DB::select("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE REFERENCED_TABLE_NAME = 'organizations'
                AND TABLE_NAME = ?
            ", [$table]);

            if (!empty($fkName)) {
                Schema::table($table, function (Blueprint $table) use ($fkName) {
                    $table->dropForeign($fkName[0]->CONSTRAINT_NAME);
                });
            }
        }

        // 2. Drop the organization_id columns
        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'organization_id')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn('organization_id');
                });
            }
        }

        // 3. Drop the organizations table
        Schema::dropIfExists('organizations');
    }

    public function down()
    {
        // Recreate organizations table
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Re-add organization_id columns (as nullable foreign keys)
        $tables = ['users', 'documents', 'budgets'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('set null');
            });
        }
    }
};