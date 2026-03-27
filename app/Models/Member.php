<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $position
 * @property \Illuminate\Support\Carbon $term_start
 * @property \Illuminate\Support\Carbon|null $term_end
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Budget> $budgets
 * @property-read int|null $budgets_count
 * @property-read mixed $initials
 * @property-read string $status
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereTermEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereTermStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Member whereUserId($value)
 * @mixin \Eloquent
 */
class Member extends Model
{
    // ✅ FIXED: Removed HasUuids — migration uses integer id()
    protected $fillable = [
        // ✅ FIXED: Synced to match migration columns exactly
        'user_id',
        'position',
        'term_start',
        'term_end',
        'joined_at', // Add this if you have joined_at in your database
    ];

    protected $casts = [
        'term_start' => 'date',
        'term_end'   => 'date',
        'joined_at'  => 'date', // Add this if you have joined_at
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * A member belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ FIXED: Added budgets() relationship.
     * Required by MemberController@show which calls $member->load('user', 'budgets')
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'reviewed_by');
    }

    /**
     * Check if the member is currently active
     * A member is active if term_end is null OR term_end is in the future
     */
    public function isActive(): bool
    {
        return is_null($this->term_end) || $this->term_end >= now();
    }

    /**
     * Get the member's status (Active/Inactive)
     */
    public function getStatusAttribute(): string
    {
        return $this->isActive() ? 'Active' : 'Inactive';
    }

    /**
     * Scope query to only active members
     */
    public function scopeActive($query)
    {
        return $query->whereNull('term_end')
                     ->orWhere('term_end', '>=', now());
    }

    /**
     * Scope query to only inactive members
     */
    public function scopeInactive($query)
    {
        return $query->whereNotNull('term_end')
                     ->where('term_end', '<', now());
    }

    public function getInitialsAttribute()
    {
        return collect(explode(' ', $this->full_name ?? ''))
            ->filter(fn($n) => strlen($n) > 0)
            ->map(fn($n) => substr($n, 0, 1))
            ->take(2)
            ->implode('');
    }
}