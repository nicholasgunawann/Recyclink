<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{
    // ponytail: thin service to log user actions
    public function log(string $action, ?string $tableName = null, ?int $recordId = null, ?string $description = null, array $properties = []): ActivityLog
    {
        return ActivityLog::record($action, $tableName, $recordId, $description, $properties);
    }
}
