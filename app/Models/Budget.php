<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    // ✅ FIXED: Removed HasUuids — migration uses integer id()
    protected $fillable = [
        // ✅ FIXED: Synced to match migration columns exactly
        'amount',
        'desc',
        'reviewed_by',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * ✅ FIXED: reviewer() now correctly points to Member (not User)
     * because reviewed_by is a foreign key to the members table.
     * Use reviewer.user to get the actual user: Budget::with('reviewer.user')
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'reviewed_by');
    }

    /**
     * Scope: budgets by status.
     */
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
}
