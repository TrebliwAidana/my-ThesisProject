<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Cloudinary\Cloudinary;

use App\Models\Role;
use App\Models\Member;
use App\Models\Document;
use App\Models\FinancialTransaction;
use App\Notifications\VerifyEmail;

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
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime:Y-m-d H:i:s',
            'birthday'          => 'date',
        ];
    }

    // =========================================================================
    // ACCESSORS & MUTATORS
    // =========================================================================

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

    public function setFullNameAttribute($value)
    {
        $this->attributes['full_name'] = $value;

        $parts = preg_split('/\s+/', trim($value));
        $this->attributes['first_name'] = $parts[0] ?? '';
        $this->attributes['last_name']  = count($parts) > 1 ? end($parts) : '';
        if (count($parts) >= 3) {
            $this->attributes['middle_name'] = $parts[1] ?? null;
        }
    }

    public function getInitialsAttribute()
    {
        $firstName = $this->first_name ?? '';
        $lastName  = $this->last_name  ?? '';

        $initials = '';
        if ($firstName) $initials .= strtoupper(substr($firstName, 0, 1));
        if ($lastName)  $initials .= strtoupper(substr($lastName,  0, 1));

        return $initials ?: 'U';
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
        $this->attributes['middle_name'] = $value ? ucwords(strtolower(trim($value))) : null;
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'owner_id');
    }

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

    // =========================================================================
    // PERMISSIONS
    // =========================================================================

    public function hasPermission(string $moduleOrSlug, string $action = ''): bool
    {
        $slug = $action
            ? "{$moduleOrSlug}.{$action}"
            : $moduleOrSlug;

        if (!$this->role_id) return false;

        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        if (!$this->role) return false;

        // FIX: cast to int before strict comparison to handle DB returning string "1"
        if ((int) ($this->role->level ?? 999) === 1) {
            return true;
        }

        $cacheKey = "user_perms_{$this->id}";

        $permissions = cache()->remember($cacheKey, now()->addMinutes(5), function () {
            $perms = $this->role?->load('permissions')->permissions ?? collect();
            return $perms->pluck('slug')->toArray();
        });

        // 1. Direct match
        if (in_array($slug, $permissions)) return true;

        // 2. Manage fallback — if role has members.manage, it passes any members.* check
        $parts = explode('.', $slug);
        if (count($parts) === 2) {
            $manageSlug = $parts[0] . '.manage';
            if (in_array($manageSlug, $permissions)) return true;
        }

        return false;
    }

    public function canDo(string $module, string $action): bool
    {
        return $this->hasPermission($module, $action);
    }

    public function hasModuleAccess(string $module): bool
    {
        return $this->hasPermission("{$module}.view");
    }

    public function hasRole(string $role): bool
    {
        return $this->role && strcasecmp($this->role->name, $role) === 0;
    }

    /**
     * Clear cached permissions — call after any role/permission change.
     */
    public function clearPermissionCache(): void
    {
        cache()->forget("user_perms_{$this->id}");
    }

    public function canAccessModule(string $module): bool
    {
        if ($this->hasRole('System Administrator') || $this->hasRole('Supreme Admin')) {
            return true;
        }

        return $this->permissions()
            ->whereHas('module', fn($q) => $q->where('name', $module))
            ->exists();
    }

    // =========================================================================
    // ACCOUNT STATUS HELPERS
    // =========================================================================

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function activate(): void
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate(): void
    {
        $this->is_active = false;
        $this->save();
    }

    public function updateLastLogin(): void
    {
        if ($this->member) {
            $this->member->last_login_at = now();
            $this->member->save();
        }
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByYearLevel($query, $yearLevel)
    {
        return $query->where('year_level', $yearLevel);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    // =========================================================================
    // EMAIL VERIFICATION
    // =========================================================================

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    public function getEmailForVerification()
    {
        return $this->email;
    }

    public function getEmailVerificationBadgeClassAttribute(): string
    {
        if ($this->hasVerifiedEmail()) {
            return 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400';
        }
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-400';
    }

    public function getEmailVerificationTextAttribute(): string
    {
        return $this->hasVerifiedEmail() ? 'Verified' : 'Unverified';
    }

    public function getVerifiedDateAttribute(): string
    {
        return $this->hasVerifiedEmail() ? $this->email_verified_at->format('M d, Y') : '—';
    }

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

    // =========================================================================
    // STUDENT CHECK
    // =========================================================================

    public function isStudent(): bool
    {
        $nonStudentRoles = ['System Administrator', 'Club Adviser', 'Guest'];
        $nonStudentAbbr  = ['SysAdmin', 'CA', 'Guest'];

        return !(
            in_array($this->role?->name, $nonStudentRoles) ||
            in_array($this->role?->abbreviation, $nonStudentAbbr)
        );
    }

    // =========================================================================
    // AVATAR
    // =========================================================================
        public function getAvatarUrlAttribute()
    {
        if (!$this->avatar) {
            return asset('images/default-avatar.png');
        }
        if (str_starts_with($this->avatar, 'http')) {
            return $this->avatar; // Cloudinary URL — return as-is
        }
        return asset('storage/' . $this->avatar); // local path
    }

    // =========================================================================
    // BOOT
    // =========================================================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!isset($user->is_active)) {
                $user->is_active = true;
            }

            if ($user->role_id == 4 && empty($user->password)) {
                $user->password = bcrypt('password');
            }

            if ($user->role_id == 4 && empty($user->email)) {
                $user->email = \App\Helpers\UserHelper::generateUniqueMemberEmail($user->full_name);
            }
        });
    }

}