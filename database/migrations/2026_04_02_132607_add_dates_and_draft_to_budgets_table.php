<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
            $table->date('end_date')->nullable()->after('start_date');
            $table->enum('status', ['draft', 'pending', 'reviewed', 'approved', 'rejected', 'disbursed'])
                ->default('pending')->change(); // ensure 'draft' is allowed
            $table->unsignedBigInteger('copied_from_id')->nullable()->after('attachment_path');
            $table->foreign('copied_from_id')->references('id')->on('budgets')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropForeign(['copied_from_id']);
            $table->dropColumn(['start_date', 'end_date', 'copied_from_id']);
        });
    }

};