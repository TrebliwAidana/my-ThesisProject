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

class User extends Authenticatable
{
    use Notifiable, HasUuids;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role_id',
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
}