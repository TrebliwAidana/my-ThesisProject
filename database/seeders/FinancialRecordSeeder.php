<?php

namespace Database\Seeders;

use App\Models\FinancialTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Http\Controllers\Financial\FinancialHelperTrait;
use Illuminate\Support\Facades\Auth;

class FinancialRecordSeeder extends Seeder
{
    use FinancialHelperTrait;

    // -------------------------------------------------------------------------
    // Realistic category pools per transaction type
    // -------------------------------------------------------------------------
    private array $incomeCategories = [
        'Membership Fee',
        'Event Registration',
        'Sponsorship',
        'Fundraising',
        'Donation',
        'Workshop Fee',
        'Seminar Fee',
        'Competition Entry Fee',
    ];

    private array $expenseCategories = [
        'Office Supplies',
        'Event Materials',
        'Transportation',
        'Food & Beverages',
        'Printing & Documentation',
        'Equipment Rental',
        'Venue Rental',
        'Communication',
        'Awards & Certificates',
        'Miscellaneous',
    ];

    private array $receivableCategories = [
        'Membership Fee',
        'Event Registration',
        'Workshop Fee',
        'Seminar Fee',
        'Competition Entry Fee',
        'Sponsorship Pledge',
    ];

    // -------------------------------------------------------------------------
    // Run
    // -------------------------------------------------------------------------
    public function run(): void
    {
        // Resolve user IDs from the DB so foreign-key constraints are satisfied.
        $allUserIds      = User::where('is_active', true)->pluck('id')->toArray();
        $treasurerIds    = User::whereHas('role', fn ($q) => $q->where('name', 'Treasurer'))->pluck('id')->toArray();
        $auditorIds      = User::whereHas('role', fn ($q) => $q->where('name', 'Auditor'))->pluck('id')->toArray();
        $adviserIds      = User::whereHas('role', fn ($q) => $q->where('name', 'Club Adviser'))->pluck('id')->toArray();
        $adminIds        = User::whereHas('role', fn ($q) => $q->where('name', 'System Administrator'))->pluck('id')->toArray();

        // Approvers are advisers or admins; auditors are auditors or admins.
        $approverPool    = array_merge($adviserIds, $adminIds) ?: $allUserIds;
        $auditorPool     = array_merge($auditorIds, $adminIds) ?: $allUserIds;
        $submitterPool   = array_merge($treasurerIds, $allUserIds) ?: $allUserIds;

        if (empty($allUserIds)) {
            $this->command->warn('FinancialRecordSeeder: No active users found. Run UserSeeder first.');
            return;
        }

        // Fetch a System Administrator to act as the "actor" for document creation
        // (since no user is logged in during seeding)
        $adminUser = User::whereHas('role', fn ($q) => $q->where('name', 'System Administrator'))
            ->where('is_active', true)
            ->first();

        if (!$adminUser) {
            $this->command->warn('FinancialRecordSeeder: No active System Administrator found. Documents will not be generated.');
        }
        else {
        // 🔐 Log in the admin so that addVersion() gets uploaded_by
        Auth::login($adminUser);
        }

        $now   = Carbon::now();
        $start = $now->copy()->subMonths(6); // seed 6 months of history

        $this->command->info('Seeding financial transactions...');

        // ── Income records ────────────────────────────────────────────────
        $this->seedIncome($submitterPool, $approverPool, $auditorPool, $start, $now, $adminUser);

        // ── Expense records ───────────────────────────────────────────────
        $this->seedExpenses($submitterPool, $approverPool, $auditorPool, $start, $now, $adminUser);

        // ── Receivable records ────────────────────────────────────────────
        $this->seedReceivables($submitterPool, $approverPool, $auditorPool, $start, $now, $adminUser);

        $total = FinancialTransaction::count();
        $this->command->info("Done. {$total} financial transactions seeded.");

          if ($adminUser) {
        Auth::logout(); // 👈 Log out after seeding
        }
    }

    // -------------------------------------------------------------------------
    // Income
    // -------------------------------------------------------------------------
    private function seedIncome(array $submitters, array $approvers, array $auditors, Carbon $start, Carbon $end, ?User $actorUser): void
    {
        $records = [
            // Fully approved — counted in chart income total
            ['description' => 'Annual Membership Fees Collection',          'amount' => 5000.00,  'status' => 'approved', 'category' => 'Membership Fee'],
            ['description' => 'Tech Summit 2024 Registration Fees',         'amount' => 3200.00,  'status' => 'approved', 'category' => 'Event Registration'],
            ['description' => 'Corporate Sponsorship — ABC Corp',           'amount' => 10000.00, 'status' => 'approved', 'category' => 'Sponsorship'],
            ['description' => 'Holiday Fundraising Drive',                  'amount' => 4500.00,  'status' => 'approved', 'category' => 'Fundraising'],
            ['description' => 'Python Workshop Registration Fees',          'amount' => 2800.00,  'status' => 'approved', 'category' => 'Workshop Fee'],
            ['description' => 'Q1 Membership Renewal Batch',                'amount' => 1500.00,  'status' => 'approved', 'category' => 'Membership Fee'],
            ['description' => 'UI/UX Design Seminar Fees',                  'amount' => 1800.00,  'status' => 'approved', 'category' => 'Seminar Fee'],
            ['description' => 'Hackathon Entry Fees',                       'amount' => 2200.00,  'status' => 'approved', 'category' => 'Competition Entry Fee'],
            ['description' => 'Alumni Donation Drive',                      'amount' => 6000.00,  'status' => 'approved', 'category' => 'Donation'],
            ['description' => 'Spring Gala Ticket Sales',                   'amount' => 3800.00,  'status' => 'approved', 'category' => 'Event Registration'],

            // Audited — awaiting approval
            ['description' => 'Mid-Year Membership Fees',                   'amount' => 2500.00,  'status' => 'audited',  'category' => 'Membership Fee'],
            ['description' => 'DevFest Workshop Fees',                      'amount' => 1400.00,  'status' => 'audited',  'category' => 'Workshop Fee'],
            ['description' => 'Guest Speaker Donations',                    'amount' => 3000.00,  'status' => 'audited',  'category' => 'Donation'],

            // Pending — not yet in chart totals
            ['description' => 'Incoming Sponsorship — XYZ Tech',            'amount' => 8000.00,  'status' => 'pending',  'category' => 'Sponsorship'],
            ['description' => 'New Member Registration Batch',              'amount' => 1200.00,  'status' => 'pending',  'category' => 'Membership Fee'],
            ['description' => 'Capstone Symposium Entry Fees',              'amount' => 950.00,   'status' => 'pending',  'category' => 'Event Registration'],

            // Rejected
            ['description' => 'Duplicate Sponsorship Entry (rejected)',     'amount' => 5000.00,  'status' => 'rejected', 'category' => 'Sponsorship'],
        ];

        foreach ($records as $record) {
            $txDate    = $this->randomDate($start, $end);
            $submitter = $this->pick($submitters);

            [$auditedBy, $auditedAt, $approvedBy, $approvedAt] =
                $this->resolveApprovalStamps($record['status'], $auditors, $approvers, $txDate);

            $transaction = FinancialTransaction::create([
                'type'             => 'income',
                'user_id'          => $submitter,
                'status'           => $record['status'],
                'description'      => $record['description'],
                'amount'           => $record['amount'],
                'category'         => $record['category'],
                'transaction_date' => $txDate,
                'notes'            => $this->randomNote($record['status']),
                'audited_by'       => $auditedBy,
                'audited_at'       => $auditedAt,
                'approved_by'      => $approvedBy,
                'approved_at'      => $approvedAt,
                'created_at'       => $txDate,
                'updated_at'       => $approvedAt ?? $auditedAt ?? $txDate,
            ]);

            // Generate approval document if the transaction is approved (income/expense)
            if ($record['status'] === 'approved' && $actorUser) {
                $this->saveApprovedTransactionAsDocument($transaction, $actorUser);
            }
        }

        $this->command->line('  ✅ Income records seeded (' . count($records) . ')');
    }

    // -------------------------------------------------------------------------
    // Expenses
    // -------------------------------------------------------------------------
    private function seedExpenses(array $submitters, array $approvers, array $auditors, Carbon $start, Carbon $end, ?User $actorUser): void
    {
        $records = [
            // Approved
            ['description' => 'Printing of Event Tarpaulins and Posters',   'amount' => 850.00,   'status' => 'approved', 'category' => 'Printing & Documentation'],
            ['description' => 'Venue Rental — Gymnasium for Tech Summit',    'amount' => 5000.00,  'status' => 'approved', 'category' => 'Venue Rental'],
            ['description' => 'Catering Services — Annual General Meeting',  'amount' => 3200.00,  'status' => 'approved', 'category' => 'Food & Beverages'],
            ['description' => 'Office Supplies Q1',                          'amount' => 620.00,   'status' => 'approved', 'category' => 'Office Supplies'],
            ['description' => 'Transportation — Regional Competition',       'amount' => 1400.00,  'status' => 'approved', 'category' => 'Transportation'],
            ['description' => 'Projector and Sound System Rental',           'amount' => 2500.00,  'status' => 'approved', 'category' => 'Equipment Rental'],
            ['description' => 'Certificates and Trophies — Hackathon',       'amount' => 1800.00,  'status' => 'approved', 'category' => 'Awards & Certificates'],
            ['description' => 'Internet Load — Officers Communication',      'amount' => 400.00,   'status' => 'approved', 'category' => 'Communication'],
            ['description' => 'Snacks — Workshop Participants',              'amount' => 780.00,   'status' => 'approved', 'category' => 'Food & Beverages'],
            ['description' => 'Event Banner Production',                     'amount' => 1100.00,  'status' => 'approved', 'category' => 'Printing & Documentation'],
            ['description' => 'Miscellaneous Supplies — Fund Drive',         'amount' => 450.00,   'status' => 'approved', 'category' => 'Miscellaneous'],

            // Audited
            ['description' => 'Venue Deposit — Year-End Party',             'amount' => 3000.00,  'status' => 'audited',  'category' => 'Venue Rental'],
            ['description' => 'Meals — Committee Meeting',                   'amount' => 560.00,   'status' => 'audited',  'category' => 'Food & Beverages'],
            ['description' => 'Office Supplies Q2 Restock',                  'amount' => 340.00,   'status' => 'audited',  'category' => 'Office Supplies'],

            // Pending
            ['description' => 'Upcoming Seminar Materials',                  'amount' => 1200.00,  'status' => 'pending',  'category' => 'Event Materials'],
            ['description' => 'Transportation — Outreach Program',           'amount' => 900.00,   'status' => 'pending',  'category' => 'Transportation'],
            ['description' => 'Printing — End-of-Year Report',               'amount' => 480.00,   'status' => 'pending',  'category' => 'Printing & Documentation'],

            // Rejected
            ['description' => 'Over-budget Catering Proposal (rejected)',    'amount' => 8500.00,  'status' => 'rejected', 'category' => 'Food & Beverages'],
        ];

        foreach ($records as $record) {
            $txDate    = $this->randomDate($start, $end);
            $submitter = $this->pick($submitters);

            [$auditedBy, $auditedAt, $approvedBy, $approvedAt] =
                $this->resolveApprovalStamps($record['status'], $auditors, $approvers, $txDate);

            $transaction = FinancialTransaction::create([
                'type'             => 'expense',
                'user_id'          => $submitter,
                'status'           => $record['status'],
                'description'      => $record['description'],
                'amount'           => $record['amount'],
                'category'         => $record['category'],
                'transaction_date' => $txDate,
                'notes'            => $this->randomNote($record['status']),
                'audited_by'       => $auditedBy,
                'audited_at'       => $auditedAt,
                'approved_by'      => $approvedBy,
                'approved_at'      => $approvedAt,
                'created_at'       => $txDate,
                'updated_at'       => $approvedAt ?? $auditedAt ?? $txDate,
            ]);

            // Generate approval document if the transaction is approved (income/expense)
            if ($record['status'] === 'approved' && $actorUser) {
                $this->saveApprovedTransactionAsDocument($transaction, $actorUser);
            }
        }

        $this->command->line('  ✅ Expense records seeded (' . count($records) . ')');
    }

    // -------------------------------------------------------------------------
    // Receivables
    // -------------------------------------------------------------------------
    private function seedReceivables(array $submitters, array $approvers, array $auditors, Carbon $start, Carbon $end, ?User $actorUser): void
    {
        $records = [
            // Paid — counted in chart receivable income total
            ['description' => 'John Smith — Membership Fee',                'amount' => 500.00,   'status' => 'paid',     'category' => 'Membership Fee',      'customer' => 'John Smith',        'due_offset' => 30],
            ['description' => 'Maria Santos — Event Registration',          'amount' => 350.00,   'status' => 'paid',     'category' => 'Event Registration',  'customer' => 'Maria Santos',      'due_offset' => 14],
            ['description' => 'TechStart Inc. — Sponsorship Pledge',        'amount' => 5000.00,  'status' => 'paid',     'category' => 'Sponsorship Pledge',  'customer' => 'TechStart Inc.',    'due_offset' => 60],
            ['description' => 'Carlos Reyes — Workshop Fee',                'amount' => 280.00,   'status' => 'paid',     'category' => 'Workshop Fee',        'customer' => 'Carlos Reyes',      'due_offset' => 7],
            ['description' => 'Ana Lim — Seminar Registration',             'amount' => 400.00,   'status' => 'paid',     'category' => 'Seminar Fee',         'customer' => 'Ana Lim',           'due_offset' => 21],
            ['description' => 'Batch 2023 — Group Membership',              'amount' => 2500.00,  'status' => 'paid',     'category' => 'Membership Fee',      'customer' => 'Batch 2023 Block A', 'due_offset' => 30],

            // Approved — awaiting payment (not yet in chart income)
            ['description' => 'GlobalSoft PH — Sponsorship Pledge',        'amount' => 8000.00,  'status' => 'approved', 'category' => 'Sponsorship Pledge',  'customer' => 'GlobalSoft PH',     'due_offset' => 45],
            ['description' => 'Michael Tan — Competition Entry',            'amount' => 250.00,   'status' => 'approved', 'category' => 'Competition Entry Fee','customer' => 'Michael Tan',       'due_offset' => 10],
            ['description' => 'Sofia Cruz — Membership Fee',               'amount' => 500.00,   'status' => 'approved', 'category' => 'Membership Fee',      'customer' => 'Sofia Cruz',        'due_offset' => 30],
            // Overdue approved receivable
            ['description' => 'Overdue Pledge — XYZ Solutions',            'amount' => 3000.00,  'status' => 'approved', 'category' => 'Sponsorship Pledge',  'customer' => 'XYZ Solutions',     'due_offset' => -15],

            // Audited — awaiting approval
            ['description' => 'Jake Torres — Workshop Registration',        'amount' => 280.00,   'status' => 'audited',  'category' => 'Workshop Fee',        'customer' => 'Jake Torres',       'due_offset' => 14],
            ['description' => 'Ella Gomez — Seminar Fee',                   'amount' => 400.00,   'status' => 'audited',  'category' => 'Seminar Fee',         'customer' => 'Ella Gomez',        'due_offset' => 21],

            // Pending
            ['description' => 'New Sponsor Pledge — DevHub Co.',            'amount' => 6000.00,  'status' => 'pending',  'category' => 'Sponsorship Pledge',  'customer' => 'DevHub Co.',        'due_offset' => 60],
            ['description' => 'Luis Ramos — Event Registration',            'amount' => 350.00,   'status' => 'pending',  'category' => 'Event Registration',  'customer' => 'Luis Ramos',        'due_offset' => 14],

            // Rejected
            ['description' => 'Invalid Pledge — Anonymous (rejected)',      'amount' => 1000.00,  'status' => 'rejected', 'category' => 'Sponsorship Pledge',  'customer' => 'Anonymous',         'due_offset' => 30],
        ];

        foreach ($records as $record) {
            $txDate    = $this->randomDate($start, $end);
            $submitter = $this->pick($submitters);
            $dueDate   = $txDate->copy()->addDays($record['due_offset']);

            [$auditedBy, $auditedAt, $approvedBy, $approvedAt] =
                $this->resolveApprovalStamps($record['status'], $auditors, $approvers, $txDate);

            $transaction = FinancialTransaction::create([
                'type'             => 'receivable',
                'user_id'          => $submitter,
                'status'           => $record['status'],
                'description'      => $record['description'],
                'amount'           => $record['amount'],
                'category'         => $record['category'],
                'transaction_date' => $txDate,
                'customer_name'    => $record['customer'],
                'due_date'         => $dueDate,
                'notes'            => $this->receivableNote($record['status'], $record['customer']),
                'audited_by'       => $auditedBy,
                'audited_at'       => $auditedAt,
                'approved_by'      => $approvedBy,
                'approved_at'      => $approvedAt,
                'created_at'       => $txDate,
                'updated_at'       => $approvedAt ?? $auditedAt ?? $txDate,
            ]);

            // Generate approval document for paid receivables
            if ($record['status'] === 'paid' && $actorUser) {
                $this->saveApprovedTransactionAsDocument($transaction, $actorUser);
            }
        }

        $this->command->line('  ✅ Receivable records seeded (' . count($records) . ')');
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Resolve audited_by/audited_at/approved_by/approved_at based on status.
     */
    private function resolveApprovalStamps(
        string  $status,
        array   $auditors,
        array   $approvers,
        Carbon  $txDate
    ): array {
        $auditedBy  = null;
        $auditedAt  = null;
        $approvedBy = null;
        $approvedAt = null;

        if (in_array($status, ['audited', 'approved', 'paid'])) {
            $auditedBy = $this->pick($auditors);
            $auditedAt = $txDate->copy()->addDays(rand(1, 3));
        }

        if (in_array($status, ['approved', 'paid'])) {
            $approvedBy = $this->pick($approvers);
            $approvedAt = ($auditedAt ?? $txDate)->copy()->addDays(rand(1, 2));
        }

        return [$auditedBy, $auditedAt, $approvedBy, $approvedAt];
    }

    private function pick(array $items): mixed
    {
        if (empty($items)) return null;
        return $items[array_rand($items)];
    }

    private function randomDate(Carbon $from, Carbon $to): Carbon
    {
        return Carbon::createFromTimestamp(rand($from->timestamp, $to->timestamp));
    }

    private function randomNote(string $status): ?string
    {
        return match ($status) {
            'pending'  => 'Submitted for review. Awaiting audit.',
            'audited'  => 'Verified by auditor. Awaiting final approval.',
            'approved' => 'Approved and recorded.',
            'rejected' => 'Rejected due to incomplete documentation.',
            'paid'     => 'Payment received and confirmed.',
            default    => null,
        };
    }

    private function receivableNote(string $status, string $customer): ?string
    {
        return match ($status) {
            'pending'  => "Awaiting audit for pledge from {$customer}.",
            'audited'  => "Audited. Pending adviser approval for {$customer}.",
            'approved' => "Approved. Awaiting payment from {$customer}.",
            'paid'     => "Payment received from {$customer}. Amount credited to income.",
            'rejected' => "Pledge from {$customer} rejected — insufficient documentation.",
            default    => null,
        };
    }
}