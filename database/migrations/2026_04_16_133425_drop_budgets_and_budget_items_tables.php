<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budgets');
    }

    public function down()
    {
        // You can recreate the schema if needed, but not required
    }
};