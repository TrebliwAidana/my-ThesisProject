<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Document;
use App\Models\DocumentCategory;

return new class extends Migration
{

    public function up()
    {
        // 1. Add column ONLY if it doesn't exist
        if (!Schema::hasColumn('documents', 'document_category_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->foreignId('document_category_id')->nullable()->after('category');
            });
        }

        // 2. Migrate existing data: create categories from distinct values
        $existingCategories = Document::whereNotNull('category')
            ->distinct()
            ->pluck('category');

        foreach ($existingCategories as $catName) {
            $category = DocumentCategory::firstOrCreate([
                'name' => trim($catName),
            ], [
                'is_active' => true,
            ]);

            Document::where('category', $catName)
                ->whereNull('document_category_id') // only update if not already set
                ->update(['document_category_id' => $category->id]);
        }

        // 3. Make column required (if it's currently nullable and you want it required)
        //    Skip if you want to keep nullable for documents without category
        Schema::table('documents', function (Blueprint $table) {
            // Only add foreign key if it doesn't exist
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                                    WHERE TABLE_NAME = 'documents' AND COLUMN_NAME = 'document_category_id' 
                                    AND CONSTRAINT_NAME != 'PRIMARY'");
            if (empty($foreignKeys)) {
                $table->foreign('document_category_id')->references('id')->on('document_categories')->onDelete('restrict');
            }
        });

        // 4. Drop the old free-text column (only if it exists)
        if (Schema::hasColumn('documents', 'category')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->dropForeign(['document_category_id']);
            $table->dropColumn('document_category_id');
        });
    }
};