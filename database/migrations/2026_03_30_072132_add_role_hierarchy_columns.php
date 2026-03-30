<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleHierarchyColumns extends Migration
{
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            // Add parent relationship
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->foreign('parent_id')->references('id')->on('roles')->onDelete('set null');
            
            // Add role level for easier querying
            $table->integer('level')->default(1)->after('name');
            
            // Add permissions JSON column
            $table->json('permissions')->nullable()->after('level');
            
            // Mark system roles that cannot be deleted
            $table->boolean('is_system')->default(false)->after('permissions');
        });
    }

    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'level', 'permissions', 'is_system']);
        });
    }
}