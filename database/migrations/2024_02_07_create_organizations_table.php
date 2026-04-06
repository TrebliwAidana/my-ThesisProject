<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation', 20)->nullable();
            $table->text('description')->nullable();
            $table->string('type')->default('club');         // ssg | club | sports | academic | cultural
            $table->string('academic_year', 20)->nullable(); // e.g. "2025-2026"
            $table->foreignId('adviser_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('logo')->nullable();              // storage path
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Add organization_id to users if it doesn't exist
        if (!Schema::hasColumn('users', 'organization_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('organization_id')->nullable()->after('role_id')
                      ->constrained('organizations')->nullOnDelete();
            });
        }

        // Add organization_id to documents if it doesn't exist
        if (!Schema::hasColumn('documents', 'organization_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->foreignId('organization_id')->nullable()->after('uploaded_by')
                      ->constrained('organizations')->nullOnDelete();
            });
        }

        // Add organization_id to budgets if it doesn't exist
        if (!Schema::hasColumn('budgets', 'organization_id')) {
            Schema::table('budgets', function (Blueprint $table) {
                $table->foreignId('organization_id')->nullable()->after('requested_by')
                      ->constrained('organizations')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users',     fn($t) => $t->dropConstrainedForeignId('organization_id'));
        Schema::table('documents', fn($t) => $t->dropConstrainedForeignId('organization_id'));
        Schema::table('budgets',   fn($t) => $t->dropConstrainedForeignId('organization_id'));
        Schema::dropIfExists('organizations');
    }
};