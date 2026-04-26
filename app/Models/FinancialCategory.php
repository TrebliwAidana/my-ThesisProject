<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Return categories applicable to a given transaction type.
     */
    public function scopeForType($query, string $type)
    {
        return $query->where(function ($q) use ($type) {
            $q->where('type', $type)->orWhere('type', 'both');
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'income'  => 'success',
            'expense' => 'danger',
            default   => 'secondary',
        };
    }
}