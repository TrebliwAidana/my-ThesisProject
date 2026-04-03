<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\SoftDeletes;   // <-- added

use App\Models\Role;
use App\Models\Member;
use App\Models\Document;
use App\Models\Budget;
use App\Notifications\VerifyEmail;

/**
 * @property int $id
 * @property string $full_name
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property int $role_id
 * @property string|null $position
 * @property string|null $student_id
 * @property string|null $year_level
 * @property bool $is_active
 * @property string|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $theme
 * @property string|null $remember_token
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Budget> $budgets
 * @property-read int|null $budgets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Document> $documents
 * @property-read int|null $documents_count
 * @property-read Member|null $member
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Member> $members
 * @property-read int|null $members_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Role $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereYearLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User unverified()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, MustVerifyEmailTrait, SoftDeletes;

    protected $fillable = [
        'full_name',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'password',
        'role_id',
        'position',
        'student_id',
        'year_level',
        'is_active',
        'last_login_at',
        'theme',
        'remember_token',
        'gender', 
        'phone',
        'birthday',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'birthday' => 'date',
        ];
    }

    /**
     * Get full name from first and last name
     */
    public function getFullNameAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        if ($this->first_name && $this->last_name) {
            $name = $this->first_name . ' ' . $this->last_name;
            if ($this->middle_name) {
                $name = $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
            }
            return $name;
        }
        
        return $this->attributes['full_name'] ?? '';
    }

    /**
     * Set full name and split into first/last
     */
    public function setFullNameAttribute($value)
    {
        $this->attributes['full_name'] = $value;
        
        $parts = preg_split('/\s+/', trim($value));
        $this->attributes['first_name'] = $parts[0] ?? '';
        $this->attributes['last_name'] = count($parts) > 1 ? end($parts) : '';
        if (count($parts) >= 3) {
            $this->attributes['middle_name'] = $parts[1] ?? null;
        }
    }

    /**
     * Get user initials
     */
    public function getInitialsAttribute()
    {
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';
        
        $initials = '';
        if ($firstName) {
            $initials .= strtoupper(substr($firstName, 0, 1));
        }
        if ($lastName) {
            $initials .= strtoupper(substr($lastName, 0, 1));
        }
        
        return $initials ?: 'U';
    }

    /**
     * A user belongs to a role.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * A user has one primary member profile.
     */
    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    /**
     * A user can have multiple member records.
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * A user has many uploaded documents.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    /**
     * A user has many reviewed budgets.
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'reviewed_by');
    }

    /**
     * Get permissions through the user's role.
     */
    public function permissions()
    {
        return $this->role ? $this->role->permissions : collect();
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role && strcasecmp($this->role->name, $role) === 0;
    }

    /**
     * Check if user has a specific permission via their role.
     */
    public function hasPermission($permission)
    {
        // System Administrator (role_id = 1) has ALL permissions
        if ($this->role_id == 1) {
            return true;
        }
        
        // Also check by role name for safety
        if ($this->role && $this->role->name === 'System Administrator') {
            return true;
        }
        
        // If no role, deny access
        if (!$this->role) {
            return false;
        }
        
        // Get permissions from config
        $roleName = $this->role->name;
        $permissions = config("permissions.roles.$roleName", []);
        
        // Add hierarchy inheritance from parent role
        if ($this->role->parent) {
            $parentPermissions = config("permissions.roles.{$this->role->parent->name}", []);
            $permissions = array_merge($permissions, $parentPermissions);
        }
        
        // Check if user has the permission
        return in_array($permission, $permissions);
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Activate user account
     */
    public function activate(): void
    {
        $this->is_active = true;
        $this->save();
    }

    /**
     * Deactivate user account
     */
    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->last_login_at = now();
        $this->save();
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive users
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for users by year level
     */
    public function scopeByYearLevel($query, $yearLevel)
    {
        return $query->where('year_level', $yearLevel);
    }

    // =========================================================================
    // EMAIL VERIFICATION METHODS
    // =========================================================================

    /**
     * Determine if the user has verified their email address.
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    /**
     * Get the email address that should be used for verification.
     */
    public function getEmailForVerification()
    {
        return $this->email;
    }

    /**
     * Get email verification status badge class
     */
    public function getEmailVerificationBadgeClassAttribute(): string
    {
        if ($this->hasVerifiedEmail()) {
            return 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400';
        }
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-400';
    }

    /**
     * Get email verification text
     */
    public function getEmailVerificationTextAttribute(): string
    {
        if ($this->hasVerifiedEmail()) {
            return 'Verified';
        }
        return 'Unverified';
    }

    /**
     * Get formatted verification date
     */
    public function getVerifiedDateAttribute(): string
    {
        if ($this->hasVerifiedEmail()) {
            return $this->email_verified_at->format('M d, Y');
        }
        return '—';
    }

    /**
     * Get verification status with icon (HTML)
     */
    public function getVerificationStatusHtmlAttribute(): string
    {
        if ($this->hasVerifiedEmail()) {
            return '<span class="inline-flex items-center gap-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 text-xs font-semibold px-2 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Verified
                    </span>';
        }
        return '<span class="inline-flex items-center gap-1 bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 text-xs font-semibold px-2 py-1 rounded-full">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Unverified
                </span>';
    }

    /**
     * Scope for verified users
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Scope for unverified users
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    /**
     * Set default password for new members
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            // Set default is_active if not provided
            if (!isset($user->is_active)) {
                $user->is_active = true;
            }
            
            // If user is a member (role_id = 4) and no password set
            if ($user->role_id == 4 && empty($user->password)) {
                $user->password = bcrypt('password');
            }
            
            // If user is a member and no email set
            if ($user->role_id == 4 && empty($user->email)) {
                $user->email = \App\Helpers\UserHelper::generateUniqueMemberEmail($user->full_name);
            }
        });
    }

    // Membership Edit Logs
    public function memberEditLogs()
    {
        return $this->hasMany(MemberEditLog::class, 'member_id');
    }

    public function editsMade()
    {
        return $this->hasMany(MemberEditLog::class, 'edited_by');
    }
    
    public function positionChangeLogs()
    {
        return $this->hasMany(PositionChangeLog::class, 'member_id');
    }
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucwords(strtolower(trim($value)));
    }
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucwords(strtolower(trim($value)));
    }

    public function setMiddleNameAttribute($value)
    {
        if ($value) {
            $this->attributes['middle_name'] = ucwords(strtolower(trim($value)));
        } else {
            $this->attributes['middle_name'] = null;
        }
    }
        /**
     * Determine if the user is a student (has a student role).
     */
    public function isStudent(): bool
    {
        // Non-student role IDs or names
        $nonStudentRoles = [
            'System Administrator',
            'Club Adviser',
            'Guest',
        ];

        // Also check by abbreviation if needed
        $nonStudentAbbr = ['SysAdmin', 'CA', 'Guest'];

        return !(
            in_array($this->role?->name, $nonStudentRoles) ||
            in_array($this->role?->abbreviation, $nonStudentAbbr)
        );
    }
    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('images/default-avatar.png');
    }
}