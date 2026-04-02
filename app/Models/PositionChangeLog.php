<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class PositionChangeLog extends Model
{
    protected $fillable = [
        'member_id',
        'changed_by',
        'old_position',
        'new_position',
        'reason',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Human-readable description of the position change.
     */
    public function getDescriptionAttribute(): string
    {
        return "Position changed from '{$this->old_position}' to '{$this->new_position}'";
    }

    /**
     * Full name of the admin who made the change.
     */
    public function getChangerNameAttribute(): string
    {
        return $this->changer->full_name ?? 'Unknown';
    }

    /**
     * Full name of the member whose position was changed.
     */
    public function getMemberNameAttribute(): string
    {
        return $this->member->user->full_name ?? 'Unknown';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeForMember(Builder $query, int $memberId): Builder
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeChangedBy(Builder $query, int $userId): Builder
    {
        return $query->where('changed_by', $userId);
    }

    public function scopeWithOldPosition(Builder $query, string $position): Builder
    {
        return $query->where('old_position', $position);
    }

    public function scopeWithNewPosition(Builder $query, string $position): Builder
    {
        return $query->where('new_position', $position);
    }

    public function scopeDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeRecent(Builder $query, int $limit = 10): Builder
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}
