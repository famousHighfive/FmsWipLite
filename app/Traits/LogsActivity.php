<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public function logActivity(
        string $action,
        string $modelType,
        int|string|null $modelId,
        string $description
    ): void {

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}

