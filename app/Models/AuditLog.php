<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'event',
        'auditable_type',
        'auditable_id',
        'auditable_label',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a human‑readable description of the audit event.
     */
    public function getDescriptionAttribute(): string
    {
        $user = $this->user_name ?? ($this->user->full_name ?? 'System');
        $target = $this->auditable_label 
            ? $this->auditable_label 
            : (class_basename($this->auditable_type) . ' #' . $this->auditable_id);

        switch ($this->event) {
            case 'created':
                return "{$user} created {$target}.";
            case 'updated':
                return "{$user} updated {$target}.";
            case 'deleted':
                return "{$user} deleted {$target}.";
            case 'restored':
                return "{$user} restored {$target}.";
            case 'login':
                return "{$user} logged in.";
            case 'logout':
                return "{$user} logged out.";
            default:
                return "{$user} performed {$this->event} on {$target}.";
        }
    }
}