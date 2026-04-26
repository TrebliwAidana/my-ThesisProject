<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',             // income | expense | receivable
        'user_id',
        'status',           // pending | audited | approved | rejected | paid (receivable only)
        'description',
        'amount',
        'category',
        'transaction_date',
        'notes',
        'approved_by',
        'approved_at',
        'audited_by',
        'audited_at',
        'customer_name',    // receivable only
        'due_date',         // receivable only
        // legacy columns kept for backward compatibility — no longer used in new flow
        'receivable_id',
        'is_receivable',
        'receivable_paid',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'approved_at'      => 'datetime',
        'audited_at'       => 'datetime',
        'due_date'         => 'date',
        'amount'           => 'decimal:2',
        'is_receivable'    => 'boolean',
        'receivable_paid'  => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'audited_by');
    }

    public function documents(): MorphToMany
    {
        return $this->morphToMany(Document::class, 'attachable', 'attachments')
                    ->withTimestamps();
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeIncome($query)
    {
        // Strict income only — excludes receivables
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeReceivable($query)
    {
        return $query->where('type', 'receivable');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAudited($query)
    {
        return $query->where('status', 'audited');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getFormattedAmountAttribute(): string
    {
        return '₱' . number_format($this->amount, 2);
    }

    public function getReceiptAttribute(): ?Document
    {
        return $this->documents->first();
    }

    public function hasReceipt(): bool
    {
        return $this->documents()->exists();
    }

    public function isReceivable(): bool
    {
        return $this->type === 'receivable';
    }

    public function isOverdue(): bool
    {
        return $this->type === 'receivable'
            && $this->due_date
            && $this->due_date->isPast()
            && $this->status !== 'paid';
    }

    // ── Boot ───────────────────────────────────────────────────────────────

    protected static function booted()
    {
        static::deleting(function ($transaction) {
            if (!$transaction->isForceDeleting()) return;
            $transaction->documents()->detach();
        });
    }
}
