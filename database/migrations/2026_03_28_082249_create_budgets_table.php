<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('category');
            $table->enum('status', ['pending', 'reviewed', 'approved', 'rejected', 'disbursed'])->default('pending');
            
            // Foreign keys
            $table->foreignId('requested_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Timestamps
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            
            // Additional fields
            $table->text('review_remarks')->nullable();
            $table->text('approval_remarks')->nullable();
            $table->string('attachment_path')->nullable();
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('status');
            $table->index('category');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};