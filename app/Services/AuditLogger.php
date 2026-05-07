<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    /**
     * Log a basic audit entry.
     *
     * @param array<string, mixed> $context
     */
    public function log(?User $actor, string $action, Model $entity, array $context = []): AuditLog
    {
        return AuditLog::create([
            'actor_id' => $actor?->id,
            'action' => $action,
            'entity_type' => class_basename($entity),
            'entity_id' => (int) $entity->getKey(),
            'context_json' => empty($context) ? null : $context,
        ]);
    }
}
