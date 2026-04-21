<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('restored_backups', function (Blueprint $table) {
            $table->id();
            $table->string('backup_filename');
            $table->string('backup_hash', 64)->nullable(); // optional, for integrity
            $table->foreignId('restored_by')->constrained('users');
            $table->timestamp('restored_at')->useCurrent();
            $table->unique('backup_filename'); // prevent duplicate
        });
    }

    public function down()
    {
        Schema::dropIfExists('restored_backups');
    }
};