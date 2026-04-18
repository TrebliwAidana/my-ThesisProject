<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public static function log(
        string $event,
        ?Model $auditable = null,
        ?string $label = null,
        array $oldValues = [],
        array $newValues = []
    ): void {
        $user = auth()->user();

        AuditLog::create([
            'user_id'        => $user?->id,
            'user_name'      => $user?->full_name,
            'role'           => $user?->role?->name,
            'event'          => $event,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id'   => $auditable?->id,
            'auditable_label'=> $label,
            'old_values'     => empty($oldValues) ? null : $oldValues,
            'new_values'     => empty($newValues) ? null : $newValues,
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }
}