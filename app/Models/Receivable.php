<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receivable extends Model
{
    protected $fillable = [
        'reference_no', 
        'customer_name',
        'description', 
        'total_amount',
        'paid_amount', 
        'due_date', 
        'status', 
        'created_by',
        'income_transaction_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount'  => 'decimal:2',
    ];

    public function payments()
    {
        return $this->hasMany(FinancialTransaction::class, 'receivable_id')
                    ->where('type', 'income');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRemainingAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    public function updateStatus()
    {
        if ($this->paid_amount >= $this->total_amount) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'pending';
        }
        
        // Check overdue
        if ($this->due_date && $this->due_date < now()->toDateString() && $this->status !== 'paid') {
            $this->status = 'overdue';
        }
        
        $this->save();
    }

    public static function generateReference()
    {
        return 'R-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
    public function incomeTransaction()
    {
        return $this->belongsTo(FinancialTransaction::class, 'income_transaction_id');
    }
    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->save();

        $transaction = $this->incomeTransaction;
        $transaction->receivable_paid = true;
        if ($transaction->status !== 'approved') {
            
        }
        $transaction->save();
    }
}