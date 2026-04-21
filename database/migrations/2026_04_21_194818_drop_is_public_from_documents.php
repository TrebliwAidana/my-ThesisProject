<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->boolean('is_public')->default(true)->after('document_category_id');
        });
    }
};
