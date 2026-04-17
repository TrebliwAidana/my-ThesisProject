<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add owner_id column (nullable first)
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('owner_id')->nullable()->after('id');
        });

        // 2. Copy data from uploaded_by to owner_id (if uploaded_by exists)
        if (Schema::hasColumn('documents', 'uploaded_by')) {
            DB::statement('UPDATE documents SET owner_id = uploaded_by');
        }

        // 3. Now add current_version_id after owner_id
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('current_version_id')->nullable()->after('owner_id');
        });

        // 4. Add foreign key constraints (optional but recommended)
        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
            // current_version_id foreign will be added after document_versions table exists
        });

        // 5. Drop the old uploaded_by column if you want to clean up
        if (Schema::hasColumn('documents', 'uploaded_by')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropForeign(['uploaded_by']); // drop foreign if exists
                $table->dropColumn('uploaded_by');
            });
        }

        // 6. Remove old file columns (optional, can be separate migration)
        $columnsToDrop = ['file_path', 'file_name', 'mime_type', 'size'];
        foreach ($columnsToDrop as $column) {
            if (Schema::hasColumn('documents', $column)) {
                Schema::table('documents', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropForeign(['current_version_id']);
            $table->dropColumn(['owner_id', 'current_version_id']);
            
            // Restore uploaded_by if needed
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->foreign('uploaded_by')->references('id')->on('users');
            
            // Restore file columns
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
        });
    }
};