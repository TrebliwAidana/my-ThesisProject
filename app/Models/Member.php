<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
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
        'term_start'          => 'date',
        'term_end'            => 'date',
        'joined_at'           => 'date',
        'position_changed_at' => 'datetime',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Valid Positions Map
    |--------------------------------------------------------------------------
    | Keyed by role_id. Used for validation in booted() and in the controller.
    */
    public const VALID_POSITIONS = [
        1 => ['System Administrator'],
        2 => ['SSLG President', 'SSLG Adviser', 'Student Affairs Head'],
        3 => ['SSLG Secretary', 'SSLG Treasurer', 'SSLG PIO'],
        4 => ['Organization President', 'Organization Vice President'],
        5 => ['Organization Secretary', 'Organization Treasurer', 'Organization Auditor', 'Organization PIO'],
        6 => ['Club Adviser'],
        7 => ['Regular Member'],
        8 => ['Guest'],
    ];

    /*
    |--------------------------------------------------------------------------
    | Model Boot — Position Validation on Save
    |--------------------------------------------------------------------------
    */
    protected static function booted(): void
    {
        static::saving(function (Member $member) {
            $user = $member->user;

            if (! $user || ! $user->role) {
                return;
            }

            $validForRole = self::VALID_POSITIONS[$user->role_id] ?? [];

            if (! in_array($member->position, $validForRole)) {
                throw new \Exception(
                    "Invalid position '{$member->position}' for role '{$user->role->name}'. " .
                    "Valid positions: " . implode(', ', $validForRole)
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function positionChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'position_changed_by');
    }

    public function positionChangeLogs(): HasMany
    {
        return $this->hasMany(PositionChangeLog::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusAttribute(): string
    {
        return $this->isActive() ? 'Active' : 'Inactive';
    }

    public function getInitialsAttribute(): string
    {
        $fullName = $this->user->full_name ?? '';

        return collect(explode(' ', $fullName))
            ->filter(fn($n) => strlen($n) > 0)
            ->map(fn($n) => strtoupper(substr($n, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function getFullNameAttribute(): string
    {
        return $this->user->full_name ?? 'Unknown';
    }

    public function getEmailAttribute(): string
    {
        return $this->user->email ?? '';
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->whereNull('term_end')
                     ->orWhere('term_end', '>=', now());
    }

    public function scopeInactive($query)
    {
        return $query->whereNotNull('term_end')
                     ->where('term_end', '<', now());
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return is_null($this->term_end) || $this->term_end >= now();
    }

    /**
     * Return valid positions for a given role_id.
     */
    public static function getValidPositionsForRole(int $roleId): array
    {
        return self::VALID_POSITIONS[$roleId] ?? [];
    }

    /**
     * Check if this member's user has a given permission.
     */
    public function can(string $permission): bool
    {
        $role = $this->user->role;

        if (! $role) {
            return false;
        }

        $rolePermissions = [
            'System Administrator' => [
                'manage_all', 'edit_members', 'delete_members', 'change_positions',
                'view_budgets', 'approve_budgets', 'manage_settings',
            ],
            'Supreme Admin' => [
                'manage_all', 'edit_members', 'change_positions',
                'view_budgets', 'approve_budgets',
            ],
            'Club Adviser' => [
                'edit_members', 'change_positions',
                'view_budgets', 'approve_budgets',
            ],
            'Org Admin'      => ['edit_members', 'view_budgets'],
            'Supreme Officer'=> ['view_budgets', 'submit_budgets'],
            'Org Officer'    => ['view_budgets', 'submit_budgets'],
            'Org Member'     => ['view_own_profile'],
        ];

        return in_array($permission, $rolePermissions[$role->name] ?? []);
    }
}
