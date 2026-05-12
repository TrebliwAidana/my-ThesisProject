<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_backups', function (Blueprint $table) {
            // Add only the columns that are missing — check your table first
            if (!Schema::hasColumn('document_backups', 'category_slug')) {
                $table->string('category_slug')->nullable()->after('cloudinary_public_id');
            }
            if (!Schema::hasColumn('document_backups', 'category_label')) {
                $table->string('category_label')->nullable()->after('category_slug');
            }
            if (!Schema::hasColumn('document_backups', 'file_type')) {
                $table->string('file_type')->nullable()->after('category_label');
            }
            if (!Schema::hasColumn('document_backups', 'document_count')) {
                $table->integer('document_count')->default(0)->after('file_type');
            }
            if (!Schema::hasColumn('document_backups', 'financial_count')) {
                $table->integer('financial_count')->default(0)->after('document_count');
            }
            if (!Schema::hasColumn('document_backups', 'file_count')) {
                $table->integer('file_count')->default(0)->after('financial_count');
            }
            if (!Schema::hasColumn('document_backups', 'size_bytes')) {
                $table->bigInteger('size_bytes')->default(0)->after('file_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_backups', function (Blueprint $table) {
            $table->dropColumn([
                'category_slug', 'category_label', 'file_type',
                'document_count', 'financial_count', 'file_count', 'size_bytes',
            ]);
        });
    }
};