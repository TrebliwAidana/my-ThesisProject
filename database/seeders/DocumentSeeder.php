<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('documents')->insert([
            [
                'title'                => 'Annual Report 2024',
                'description'         => '',
                'owner_id'            => 1,
                'document_category_id'=> null,
                'current_version_id'  => null,
                'tags'                => null,
                'uploaded_at'         => now(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'title'                => 'Meeting Minutes - January',
                'description'         => '',
                'owner_id'            => 2,
                'document_category_id'=> null,
                'current_version_id'  => null,
                'tags'                => null,
                'uploaded_at'         => now(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'title'                => 'Budget Proposal Q1',
                'description'         => '',
                'owner_id'            => 2,
                'document_category_id'=> null,
                'current_version_id'  => null,
                'tags'                => null,
                'uploaded_at'         => now(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);
    }
}