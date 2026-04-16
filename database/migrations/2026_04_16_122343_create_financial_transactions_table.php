<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->enum('type', ['income', 'expense']);
            $table->string('category')->nullable();
            $table->date('transaction_date');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who submitted
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_path')->nullable(); // optional attachment
            $table->timestamps();

            $table->index(['type', 'transaction_date']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_transactions');
    }
};