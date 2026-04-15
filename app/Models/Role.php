<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// FIX: Added missing Position import — without this, the positions()
// relationship throws "Class Position not found" at runtime.
use App\Models\Position;

class Role extends Model
{
    protected $fillable = ['name', 'abbreviation', 'level', 'is_predefined', 'is_visible', 'allowed_positions'];

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

    // ── Permission helpers ────────────────────────────────────────────────────

    /**
     * Check if this role has a permission by slug.
     *
     * Accepts:
     *   hasPermission('documents.view')        — slug (preferred)
     *   hasPermission('documents', 'view')     — two-arg form
     */
    public function hasPermission(string $moduleOrSlug, string $action = ''): bool
    {
        $slug = $action
            ? "{$moduleOrSlug}.{$action}"
            : $moduleOrSlug;

        if (!$this->relationLoaded('permissions')) {
            $this->load('permissions');
        }

        return $this->permissions->contains('slug', $slug);
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
}