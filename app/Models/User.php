<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Role;
use App\Models\Member;
use App\Models\Document;
use App\Models\Budget;

/**
 * @property int $id
 * @property string $full_name
 * @property string $email
 * @property string $password
 * @property int $role_id
 * @property string|null $position
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable, HasUuids;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role_id',
         'theme',          // ← add this
         'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
    
    protected $casts = [
        // ... your existing casts ...
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'theme'             => 'string',  // ← add this
    ];

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
        if (!$permission) return true;

        $roleName = strtolower($this->role?->name);

        $permissions = config("permissions.roles.$roleName", []);

        return in_array($permission, $permissions);
    }

    /**
 * Set default password for new members
 */
protected static function boot()
{
    parent::boot();
    
    static::creating(function ($user) {
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
}