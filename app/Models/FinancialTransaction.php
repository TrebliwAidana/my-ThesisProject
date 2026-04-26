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
        'type',
        'user_id',
        'status',
        'description',
        'amount',
        'category',
        'transaction_date',
        'notes',
        'approved_by',
        'approved_at',
        'audited_by',       // ← added
        'audited_at',       // ← added
        'receivable_id',
        'is_receivable',
        'receivable_paid',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'approved_at'      => 'datetime',
        'audited_at'       => 'datetime',  // ← added
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

    public function receivable(): BelongsTo
    {
        return $this->belongsTo(Receivable::class);
    }

    public function documents(): MorphToMany
    {
        return $this->morphToMany(Document::class, 'attachable', 'attachments')
                    ->withTimestamps();
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeIncome($query)   { return $query->where('type', 'income'); }
    public function scopeExpense($query)  { return $query->where('type', 'expense'); }
    public function scopePending($query)  { return $query->where('status', 'pending'); }
    public function scopeAudited($query)  { return $query->where('status', 'audited'); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
    public function scopeRejected($query) { return $query->where('status', 'rejected'); }

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

    // ── Boot ───────────────────────────────────────────────────────────────

    protected static function booted()
    {
        static::deleting(function ($transaction) {
            if (!$transaction->isForceDeleting()) return;
            $transaction->documents()->detach();
        });
    }
}