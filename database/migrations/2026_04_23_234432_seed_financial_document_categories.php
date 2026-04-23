<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        $categories = [
            'Approved Income',
            'Approved Expense', 
            'Approved Receivable',
        ];

        foreach ($categories as $name) {
            \App\Models\DocumentCategory::firstOrCreate(
                ['name' => $name],
                ['description' => "Auto-generated financial approval documents — {$name}"]
            );
        }
    }

    public function down(): void
    {
        \App\Models\DocumentCategory::whereIn('name', [
            'Approved Income',
            'Approved Expense',
            'Approved Receivable',
        ])->delete();
    }
};
