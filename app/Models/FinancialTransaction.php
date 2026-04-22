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
        'receivable_id',
        'is_receivable',
        'receivable_paid',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'approved_at'      => 'datetime',
        'amount'           => 'decimal:2',
        'is_receivable'    => 'boolean',
        'receivable_paid'  => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Polymorphic relationship to documents via attachments table.
     */
    public function documents(): MorphToMany
    {
        return $this->morphToMany(Document::class, 'attachable', 'attachments')
                    ->withTimestamps();
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // -------------------------------------------------------------------------
    // Accessors & Helpers
    // -------------------------------------------------------------------------

    /**
     * Format amount with currency symbol.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₱' . number_format($this->amount, 2);
    }

    /**
     * Get the primary receipt document (first attached).
     */
    public function getReceiptAttribute(): ?Document
    {
        return $this->documents->first();
    }

    /**
     * Check if transaction has a receipt attached.
     */
    public function hasReceipt(): bool
    {
        return $this->documents()->exists();
    }

    // -------------------------------------------------------------------------
    // Boot
    // -------------------------------------------------------------------------

    protected static function booted()
    {
        static::deleting(function ($transaction) {
            // When transaction is force‑deleted, detach documents but don't delete them
            // (documents may be linked to other records)
            if (! $transaction->isForceDeleting()) {
                return;
            }

            $transaction->documents()->detach();
        });
    }
    public function auditor()
    {
        return $this->belongsTo(User::class, 'audited_by');
    }
    public function scopeAudited($query)
    {
        return $query->where('status', 'audited');
    }
    public function receivable(): BelongsTo
    {
        return $this->belongsTo(Receivable::class);
    }

}