<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ActivityLogger
{
    public static function log(
        string $action,
        string $detail,
        ?User $actor = null,
        ?Model $subject = null,
        ?int $tourId = null,
        ?int $showId = null,
        array $properties = []
    ): ActivityLog {
        return ActivityLog::create([
            'actor_id' => $actor?->id,
            'actor_name' => $actor?->name,
            'action' => $action,
            'detail' => $detail,
            'tour_id' => $tourId,
            'show_id' => $showId,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'properties' => $properties ?: null,
        ]);
    }
}
