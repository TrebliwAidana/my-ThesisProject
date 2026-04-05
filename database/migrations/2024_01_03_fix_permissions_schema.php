<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Add module, action, label columns to permissions ───────────────
        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'module')) {
                $table->string('module')->nullable()->after('name');
            }
            if (!Schema::hasColumn('permissions', 'action')) {
                $table->string('action', 50)->nullable()->after('module');
            }
            if (!Schema::hasColumn('permissions', 'label')) {
                $table->string('label')->nullable()->after('action');
            }
        });

        // ── 2. Backfill module+action from existing name column ───────────────
        // e.g. name = "documents.view"  →  module = "documents", action = "view"
        DB::table('permissions')->get()->each(function ($perm) {
            if ($perm->module || $perm->action) return; // already filled
            $parts = explode('.', $perm->name);
            if (count($parts) === 2) {
                DB::table('permissions')->where('id', $perm->id)->update([
                    'module' => $parts[0],
                    'action' => $parts[1],
                    'label'  => ucwords(str_replace(['.', '_', '-'], ' ', $perm->name)),
                ]);
            }
        });

        // ── 3. Fix pivot table name if it was 'role_permissions' (plural) ─────
        // If your DB already has 'role_permissions', rename it to 'role_permission'.
        // If it never existed or is already 'role_permission', this is a no-op.
        if (Schema::hasTable('role_permissions') && !Schema::hasTable('role_permission')) {
            Schema::rename('role_permissions', 'role_permission');
        }

        // Create pivot table if neither version exists yet
        if (!Schema::hasTable('role_permission')) {
            Schema::create('role_permission', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['role_id', 'permission_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(array_filter(['module', 'action', 'label'], function ($col) {
                return Schema::hasColumn('permissions', $col);
            }));
        });
    }
};