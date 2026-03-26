<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    // ✅ FIXED: Removed HasUuids — migration uses integer id()
    protected $fillable = [
        // ✅ FIXED: Synced to match migration columns exactly
        'user_id',
        'position',
        'term_start',
        'term_end',
    ];

    protected $casts = [
        'term_start' => 'date',
        'term_end'   => 'date',
    ];

    /**
     * A member belongs to a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ✅ FIXED: Added budgets() relationship.
     * Required by MemberController@show which calls $member->load('user', 'budgets')
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'reviewed_by');
    }

    // ✅ FIXED: Removed scopeActive/scopeInactive/scopeSuspended
    // — they queried a 'status' column that does not exist in the migration.
}
