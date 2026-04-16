<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialController extends Controller
{
    public function __construct()
    {
        // Temporary: allow any authenticated user to see the page
        // Later replace with proper permission: $this->authorize('financial.view');
    }

    public function index()
    {
        // Dummy data for demonstration
        $incomeTotal = 12500.00;
        $expenseTotal = 4780.50;
        $balance = $incomeTotal - $expenseTotal;

        $recentTransactions = [
            (object)['type' => 'income', 'description' => 'Membership fees', 'amount' => 3500.00, 'date' => now()->subDays(2)],
            (object)['type' => 'expense', 'description' => 'Event supplies', 'amount' => 1200.00, 'date' => now()->subDays(5)],
            (object)['type' => 'income', 'description' => 'Fundraising', 'amount' => 5000.00, 'date' => now()->subDays(7)],
        ];

        return view('financial.index', compact('incomeTotal', 'expenseTotal', 'balance', 'recentTransactions'));
    }

    public function createIncome()
    {
        return view('financial.create', ['type' => 'income']);
    }

    public function createExpense()
    {
        return view('financial.create', ['type' => 'expense']);
    }

    public function storeIncome(Request $request)
    {
        // Temporary: just redirect with a demo message
        return redirect()->route('financial.index')->with('success', 'Income recorded (demo – storage not yet implemented).');
    }

    public function storeExpense(Request $request)
    {
        return redirect()->route('financial.index')->with('success', 'Expense recorded (demo – storage not yet implemented).');
    }
}