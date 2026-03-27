<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property numeric $amount
 * @property string|null $desc
 * @property int|null $reviewed_by
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Member|null $reviewer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget approved()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget rejected()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereReviewedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
