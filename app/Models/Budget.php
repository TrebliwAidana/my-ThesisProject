<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'amount',
        'category',
        'status',
        'requested_by',
        'reviewed_by',
        'approved_by',
        'reviewed_at',
        'approved_at',
        'disbursed_at',
        'review_remarks',
        'approval_remarks',
        'attachment_path',
        'copied_from_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function copiedFrom(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'copied_from_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDisbursed($query)
    {
        return $query->where('status', 'disbursed');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeNotDraft($query)
    {
        return $query->where('status', '!=', 'draft');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isReviewed(): bool
    {
        return $this->status === 'reviewed';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isDisbursed(): bool
    {
        return $this->status === 'disbursed';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function canReview(): bool
    {
        return in_array($this->status, ['pending', 'reviewed']);
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft'    => 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300',
            'pending'  => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
            'reviewed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
            'disbursed'=> 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            default    => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}