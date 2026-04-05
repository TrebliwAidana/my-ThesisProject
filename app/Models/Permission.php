<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'slug',          // canonical key: "documents.view"
        'name',          // display name: "View Documents"
        'module',        // "documents"
        'action',        // "view"
        'label',         // optional override display label
        'description',
    ];

    // ── Pivot ─────────────────────────────────────────────────────────────────
    // singular 'role_permission' — matches Role model and migration
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /**
     * Derive module from slug if the dedicated column is empty.
     * "documents.view" → "documents"
     */
    public function getModuleAttribute($value): string
    {
        if ($value) return $value;
        return explode('.', $this->attributes['slug'] ?? '.')[0];
    }

    /**
     * Derive action from slug if the dedicated column is empty.
     * "documents.view" → "view"
     */
    public function getActionAttribute($value): string
    {
        if ($value) return $value;
        $parts = explode('.', $this->attributes['slug'] ?? '.');
        return $parts[1] ?? '';
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function groupedByModule(): \Illuminate\Support\Collection
    {
        return static::all()->groupBy('module');
    }
}