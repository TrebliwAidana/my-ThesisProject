<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'abbreviation',
        'level',
        'desc',          // ✅ dynamic dashboard description
        'is_predefined',
        'is_visible',
        'allowed_positions',    // stored as JSON (optional)
        'parent_id',    // ➕ add this
        'is_system',
    ];

    protected $casts = [
        'is_predefined' => 'boolean',
        'is_visible'    => 'boolean',
        'allowed_positions' => 'array',   // automatically cast JSON to array
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Pivot is 'role_permission' (singular) — matches Permission model and migration.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    // ── Permission Helpers (enhanced for financial module) ───────────────────

    /**
     * Check if this role has a specific permission by slug.
     *
     * Accepts:
     *   hasPermission('financial.view')            — full slug
     *   hasPermission('financial', 'view')         — module + action
     */
    public function hasPermission(string $moduleOrSlug, string $action = ''): bool
    {
        $slug = $action ? "{$moduleOrSlug}.{$action}" : $moduleOrSlug;

        if (!$this->relationLoaded('permissions')) {
            $this->load('permissions');
        }

        return $this->permissions->contains('slug', $slug);
    }

    /**
     * Check if the role has ANY of the given permissions.
     *
     * @param array $permissions  Array of permission slugs or [module, action] pairs
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if (is_array($perm) && count($perm) === 2) {
                if ($this->hasPermission($perm[0], $perm[1])) {
                    return true;
                }
            } elseif (is_string($perm)) {
                if ($this->hasPermission($perm)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check if the role has ALL of the given permissions.
     *
     * @param array $permissions  Array of permission slugs or [module, action] pairs
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $perm) {
            if (is_array($perm) && count($perm) === 2) {
                if (!$this->hasPermission($perm[0], $perm[1])) {
                    return false;
                }
            } elseif (is_string($perm)) {
                if (!$this->hasPermission($perm)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Sync permissions for one module only, preserving all others.
     */
    public function syncModulePermissions(string $module, array $permissionIds): void
    {
        $otherIds = $this->permissions()
            ->where('module', '!=', $module)
            ->pluck('permissions.id')
            ->toArray();

        $this->permissions()->sync(array_merge($otherIds, $permissionIds));
    }

    // ── Description Accessor (optional, but ensures fallback) ────────────────

    /**
     * Get the role description, with a fallback for predefined roles if missing.
     */
    public function getDescriptionAttribute(?string $value): string
    {
        if ($value) {
            return $value;
        }

        // Optional fallback for predefined roles that lack a description
        $fallbacks = [
            'System Administrator' => 'Full system control – manage users, roles, finances, documents, and settings.',
            'Supreme Admin'        => 'Oversee all organization activities, approve financial transactions, and manage key members.',
            'Supreme Officer'      => 'Record financial transactions, upload documents, and manage member records.',
            'Org Admin'            => 'Manage your organization’s members, financial records, and documents.',
            'Org Officer'          => 'Submit financial transactions, upload documents, and view member lists.',
            'Club Adviser'         => 'Guide the organization, approve financial requests, and oversee activities.',
            'Org Member'           => 'View your profile, documents, and financial summaries.',
            'Guest'                => 'Limited access – can only view public information.',
        ];

        return $fallbacks[$this->name] ?? 'Manage your account and participate in organization activities.';
    }
}