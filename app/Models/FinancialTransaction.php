<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $table = 'financial_transactions';

    protected $fillable = [
        'description',
        'amount',
        'type',
        'category',
        'transaction_date',
        'user_id',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'receipt_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return '₱' . number_format($this->amount, 2);
    }

    public function getIsIncomeAttribute(): bool
    {
        return $this->type === 'income';
    }

    public function getIsExpenseAttribute(): bool
    {
        return $this->type === 'expense';
    }
}