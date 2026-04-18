<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentCategory;

class DocumentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Minutes',
            'Reports',
            'Policies',
            'Forms',
            'Financial Receipts',
            'Correspondence',
            'Others',
        ];

        foreach ($categories as $cat) {
            DocumentCategory::firstOrCreate(['name' => $cat]);
        }

        $this->command->info('Document categories seeded!');
    }
}