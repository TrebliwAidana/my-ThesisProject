o<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('position_change_logs')) {
            Schema::create('position_change_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('member_id')->constrained()->onDelete('cascade');
                $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
                $table->string('old_position');
                $table->string('new_position');
                $table->text('reason')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
                
                // Add indexes for better performance
                $table->index(['member_id', 'created_at']);
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('position_change_logs');
    }
};