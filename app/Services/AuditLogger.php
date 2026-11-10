<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AuditLogger
{
    private const EVENTS = ['created', 'updated', 'deleted'];

    public function record(
        string $event,
        string $subjectType,
        ?int $subjectId,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): void {
        if (!in_array($event, self::EVENTS, true)) {
            throw new InvalidArgumentException('Unsupported audit event: '.$event);
        }

        $subjectType = class_basename($subjectType);
        if ($subjectType === '' || mb_strlen($subjectType) > 50) {
            throw new InvalidArgumentException('Audit subject type must be between 1 and 50 characters.');
        }

        $user = Auth::user();
        $userAgent = request()->userAgent();

        DB::table('audit_logs')->insert([
            'name' => $user ? mb_substr((string) $user->name, 0, 50) : 'System',
            'event' => $event,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'old_values' => $this->encodeValues($oldValues),
            'new_values' => $this->encodeValues($newValues),
            'description' => $description,
            'ip_address' => mb_substr((string) request()->ip(), 0, 50),
            'user_agent' => $userAgent ? mb_substr($userAgent, 0, 50) : null,
            'created-at' => now(),
        ]);
    }

    private function encodeValues(?array $values): ?string
    {
        if ($values === null) {
            return null;
        }

        return json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
