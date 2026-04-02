<?php

namespace App\Traits;

use App\Models\MemberEditLog;
use App\Models\PositionChangeLog;
use Illuminate\Support\Facades\Request;

trait LogsMemberEdits
{
    /**
     * Log a single field change on a member.
     * Skips if old and new values are identical.
     */
    protected function logMemberEdit($member, string $field, $oldValue, $newValue, ?string $reason = null): void
    {
        if ($oldValue == $newValue) {
            return;
        }

        MemberEditLog::create([
            'member_id'     => $member->id,
            'edited_by'     => auth()->id(),
            'field_changed' => $field,
            'old_value'     => $this->formatLogValue($oldValue),
            'new_value'     => $this->formatLogValue($newValue),
            'reason'        => $reason,
            'ip_address'    => Request::ip(),
            'user_agent'    => Request::userAgent(),
        ]);
    }

    /**
     * Log multiple field changes at once.
     * Each entry in $changes: ['field' => ['old' => ..., 'new' => ...]]
     */
    protected function logMultipleChanges($member, array $changes, ?string $reason = null): void
    {
        foreach ($changes as $field => $values) {
            $this->logMemberEdit($member, $field, $values['old'], $values['new'], $reason);
        }
    }

    /**
     * Log a position change specifically into PositionChangeLog.
     * Call this after the member record has been saved.
     */
    protected function logPositionChange($memberRecord, string $oldPosition, string $newPosition, ?string $reason = null): void
    {
        if (trim($oldPosition) === trim($newPosition)) {
            return;
        }

        PositionChangeLog::create([
            'member_id'    => $memberRecord->id,
            'changed_by'   => auth()->id(),
            'old_position' => $oldPosition,
            'new_position' => $newPosition,
            'reason'       => $reason ?? 'Position updated',
            'ip_address'   => Request::ip(),
        ]);
    }

    /**
     * Format any value into a string suitable for logging.
     */
    private function formatLogValue($value): string
    {
        if (is_null($value)) {
            return 'NULL';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }
}
