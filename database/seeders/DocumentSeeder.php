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
                'title'       => 'Annual Report 2024',
                'file_path'   => 'uploads/documents/annual_report_2024.pdf',
                'uploaded_by' => 1, // Alice Reyes — Admin
                'uploaded_at' => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'title'       => 'Meeting Minutes - January',
                'file_path'   => 'uploads/documents/meeting_minutes_jan.pdf',
                'uploaded_by' => 2, // Bob Santos — Officer
                'uploaded_at' => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                // ✅ FIXED: was uploaded_by = 3 (Clara Dela Cruz — Auditor)
                // Auditors have read-only access and cannot upload documents.
                // Reassigned to user 2 (Bob Santos — Officer).
                'title'       => 'Budget Proposal Q1',
                'file_path'   => 'uploads/documents/budget_proposal_q1.xlsx',
                'uploaded_by' => 2, // Bob Santos — Officer
                'uploaded_at' => now(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
