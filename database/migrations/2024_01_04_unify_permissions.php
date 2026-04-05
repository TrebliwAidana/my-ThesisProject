<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * This migration is safe to run on both a fresh install and an existing DB.
 *
 * What it does:
 *  1. Ensures permissions table has: slug, name, module, action, label, description
 *  2. Backfills module + action from slug for any existing rows
 *  3. Standardises the pivot table name to 'role_permission' (singular)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── permissions table ─────────────────────────────────────────────────
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('slug')->unique();           // e.g. "documents.view"  ← canonical key
                $table->string('name');                     // e.g. "View Documents"
                $table->string('module', 50);               // e.g. "documents"
                $table->string('action', 50);               // e.g. "view"
                $table->string('label')->nullable();        // display label
                $table->text('description')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('permissions', 'slug'))   $table->string('slug')->unique()->nullable()->after('id');
                if (!Schema::hasColumn('permissions', 'module')) $table->string('module', 50)->nullable()->after('slug');
                if (!Schema::hasColumn('permissions', 'action')) $table->string('action', 50)->nullable()->after('module');
                if (!Schema::hasColumn('permissions', 'label'))  $table->string('label')->nullable()->after('action');
            });
        }

        // ── backfill module + action from slug ────────────────────────────────
        DB::table('permissions')->get()->each(function ($p) {
            $updates = [];

            // Derive slug from name if slug column is empty
            if (empty($p->slug) && !empty($p->name)) {
                $updates['slug'] = \Illuminate\Support\Str::slug($p->name, '.');
            }

            $slug = $p->slug ?? ($updates['slug'] ?? '');
            if ($slug && str_contains($slug, '.')) {
                [$module, $action] = explode('.', $slug, 2);
                if (empty($p->module)) $updates['module'] = $module;
                if (empty($p->action)) $updates['action'] = $action;
                if (empty($p->label))  $updates['label']  = ucwords(str_replace(['.', '_', '-'], ' ', $slug));
            }

            if ($updates) {
                DB::table('permissions')->where('id', $p->id)->update($updates);
            }
        });

        // ── pivot table: standardise to 'role_permission' (singular) ─────────
        if (Schema::hasTable('role_permissions') && !Schema::hasTable('role_permission')) {
            Schema::rename('role_permissions', 'role_permission');
        }

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
            foreach (['slug', 'module', 'action', 'label'] as $col) {
                if (Schema::hasColumn('permissions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};