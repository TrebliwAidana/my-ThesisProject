<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    // Position/Role Constants
    const POSITION_MEMBER = 'member';
    const POSITION_OFFICER = 'officer';
    const POSITION_ADVISER = 'adviser';
    const POSITION_ADMIN = 'admin';
    
    // Available positions with labels
    const POSITIONS = [
        self::POSITION_MEMBER => 'Member',
        self::POSITION_OFFICER => 'Officer',
        self::POSITION_ADVISER => 'Adviser',
        self::POSITION_ADMIN => 'Admin',
    ];
    
    protected $fillable = [
        'user_id',
        'position',
        'term_start',
        'term_end',
        'joined_at',
        'position_changed_at',
        'position_changed_by',
    ];

    protected $casts = [
        'term_start' => 'date',
        'term_end'   => 'date',
        'joined_at'  => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'position_changed_at' => 'datetime',
    ];

    /**
     * A member belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who changed this member's position
     */
    public function positionChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'position_changed_by');
    }

    /**
     * Get the position change history for this member
     */
    public function positionChangeHistory(): HasMany
    {
        return $this->hasMany(PositionChangeLog::class);
    }

    /**
     * Budgets relationship
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

    /**
     * Get member's initials from their user's name
     */
    public function getInitialsAttribute()
    {
        $fullName = $this->user->name ?? '';
        return collect(explode(' ', $fullName))
            ->filter(fn($n) => strlen($n) > 0)
            ->map(fn($n) => substr($n, 0, 1))
            ->take(2)
            ->implode('');
    }

    /**
     * Check if position change is valid
     * 
     * @param string $currentPosition The current position of the member
     * @param string $newPosition The new position to change to
     * @param \App\Models\User $changingUser The user who is making the change
     * @param \App\Models\Member|null $targetMember The member being changed (optional, for self-change checks)
     * @return bool
     */
    public static function isValidPositionChange($currentPosition, $newPosition, $changingUser, $targetMember = null)
    {
        // Get changing user's member record
        $changingUserMember = $changingUser->member;
        
        if (!$changingUserMember) {
            return false;
        }
        
        $changingUserPosition = $changingUserMember->position;
        
        // Only admin and adviser can change positions
        if (!in_array($changingUserPosition, [self::POSITION_ADMIN, self::POSITION_ADVISER])) {
            return false;
        }
        
        // Admin can change any position
        if ($changingUserPosition === self::POSITION_ADMIN) {
            return true;
        }
        
        // Adviser cannot change admin positions
        if ($newPosition === self::POSITION_ADMIN || $currentPosition === self::POSITION_ADMIN) {
            return false;
        }
        
        // Adviser cannot change their own position
        if ($targetMember && $changingUser->id === $targetMember->user_id) {
            return false;
        }
        
        return true;
    }

    /**
     * Check if the member is an admin
     */
    public function isAdmin(): bool
    {
        return $this->position === self::POSITION_ADMIN;
    }

    /**
     * Check if the member is an adviser
     */
    public function isAdviser(): bool
    {
        return $this->position === self::POSITION_ADVISER;
    }

    /**
     * Check if the member is an officer
     */
    public function isOfficer(): bool
    {
        return $this->position === self::POSITION_OFFICER;
    }

    /**
     * Check if the member is a regular member
     */
    public function isRegularMember(): bool
    {
        return $this->position === self::POSITION_MEMBER;
    }

    /**
     * Get the position label
     */
    public function getPositionLabelAttribute(): string
    {
        return self::POSITIONS[$this->position] ?? ucfirst($this->position);
    }

    /**
     * Check if the member's position was recently changed (within last X days)
     */
    public function wasPositionChangedRecently(int $days = 7): bool
    {
        if (!$this->position_changed_at) {
            return false;
        }
        
        return $this->position_changed_at->diffInDays(now()) <= $days;
    }

    /**
     * Get the member's full name from their user
     */
    public function getFullNameAttribute()
    {
        return $this->user->name ?? 'Unknown';
    }

    /**
     * Get the member's email from their user
     */
    public function getEmailAttribute()
    {
        return $this->user->email ?? '';
    }

    /**
     * Check if the member can perform certain actions based on position
     */
    public function can(string $permission): bool
    {
        // Define permissions based on position
        $permissions = [
            self::POSITION_ADMIN => [
                'manage_all', 'edit_members', 'delete_members', 'change_positions',
                'view_budgets', 'approve_budgets', 'manage_settings'
            ],
            self::POSITION_ADVISER => [
                'edit_members', 'change_positions', 'view_budgets', 'approve_budgets'
            ],
            self::POSITION_OFFICER => [
                'view_budgets', 'submit_budgets'
            ],
            self::POSITION_MEMBER => [
                'view_own_profile'
            ],
        ];
        
        $userPermissions = $permissions[$this->position] ?? [];
        
        return in_array($permission, $userPermissions);
    }
}