<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_backups', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('cloudinary_url');           // ← ZIP stored on Cloudinary
            $table->string('cloudinary_public_id');     // ← for deletion
            $table->string('category_label')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('document_count')->default(0);
            $table->integer('financial_count')->default(0);
            $table->integer('file_count')->default(0);
            $table->bigInteger('size_bytes')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_backups');
    }
};
