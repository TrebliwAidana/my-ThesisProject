<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'abbreviation',
        'description',
        'type',
        'academic_year',
        'adviser_id',
        'logo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function adviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function members(): HasMany
    {
        // Active members only (users with a member record in this org)
        return $this->hasMany(User::class)->whereHas('member');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByYear($query, string $year)
    {
        return $query->where('academic_year', $year);
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('images/default-org.png');
    }

    public function getMemberCountAttribute(): int
    {
        return $this->users()->count();
    }

    public function getDocumentCountAttribute(): int
    {
        return $this->documents()->count();
    }

    public function getBudgetCountAttribute(): int
    {
        return $this->budgets()->count();
    }

    public function getTotalBudgetApprovedAttribute(): float
    {
        return $this->budgets()->where('status', 'approved')->sum('amount');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function types(): array
    {
        return [
            'sslg'      => 'Supreme Student Learner Government',
            'club'     => 'Club',
            'sports'   => 'Sports',
            'academic' => 'Academic',
            'cultural' => 'Cultural',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return static::types()[$this->type] ?? ucfirst($this->type);
    }
}