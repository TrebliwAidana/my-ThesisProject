<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The roles table has a legacy `permissions` string/JSON column that
 * shadows the BelongsToMany relationship of the same name.
 * Eloquent returns the column value (a raw string) instead of the
 * pivot-based Collection, causing "Call to a member function pluck() on string".
 *
 * Permissions are now stored in the role_permission pivot table.
 * This column is safe to drop.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'permissions')) {
                $table->dropColumn('permissions');
            }
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->json('permissions')->nullable()->after('level');
        });
    }
};