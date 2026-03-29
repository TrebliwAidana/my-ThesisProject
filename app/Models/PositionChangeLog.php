<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $member_id
 * @property int $changed_by
 * @property string $old_position
 * @property string $new_position
 * @property string|null $reason
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Member $member
 * @property-read \App\Models\User $changer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog whereChangedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog whereMemberId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog whereNewPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog whereOldPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PositionChangeLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PositionChangeLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'member_id',
        'changed_by',
        'old_position',
        'new_position',
        'reason',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the member that had the position change.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who changed the position.
     */
    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get a human-readable description of the position change.
     */
    public function getDescriptionAttribute(): string
    {
        $oldLabel = Member::POSITIONS[$this->old_position] ?? ucfirst($this->old_position);
        $newLabel = Member::POSITIONS[$this->new_position] ?? ucfirst($this->new_position);
        
        return "Position changed from {$oldLabel} to {$newLabel}";
    }

    /**
     * Get the changer's name.
     */
    public function getChangerNameAttribute(): string
    {
        return $this->changer->name ?? 'Unknown';
    }

    /**
     * Get the member's name.
     */
    public function getMemberNameAttribute(): string
    {
        return $this->member->user->name ?? 'Unknown';
    }

    /**
     * Scope to get logs for a specific member.
     */
    public function scopeForMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    /**
     * Scope to get logs for a specific position.
     */
    public function scopeWithOldPosition($query, $position)
    {
        return $query->where('old_position', $position);
    }

    /**
     * Scope to get logs for a specific new position.
     */
    public function scopeWithNewPosition($query, $position)
    {
        return $query->where('new_position', $position);
    }

    /**
     * Scope to get logs from a specific changer.
     */
    public function scopeChangedBy($query, $userId)
    {
        return $query->where('changed_by', $userId);
    }

    /**
     * Scope to get logs within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent logs.
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }
}