<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class PipelineProgress
{
    public static function start(string $batchId, int $total): void
    {
        Cache::put("pipeline:{$batchId}", [
            'total' => $total,
            'completed' => 0,
            'failed' => 0,
            'status' => 'running',
        ], 3600);
    }

    public static function increment(string $batchId): void
    {
        $progress = Cache::get("pipeline:{$batchId}");

        if ($progress) {
            $progress['completed']++;
            Cache::put("pipeline:{$batchId}", $progress, 3600);
        }
    }

    public static function markFailed(string $batchId): void
    {
        $progress = Cache::get("pipeline:{$batchId}");

        if ($progress) {
            $progress['failed']++;
            Cache::put("pipeline:{$batchId}", $progress, 3600);
        }
    }

    public static function complete(string $batchId): void
    {
        $progress = Cache::get("pipeline:{$batchId}");

        if ($progress) {
            $progress['status'] = 'completed';
            Cache::put("pipeline:{$batchId}", $progress, 3600);
        }
    }

    public static function get(string $batchId): array
    {
        return Cache::get("pipeline:{$batchId}", [
            'total' => 0,
            'completed' => 0,
            'failed' => 0,
            'status' => 'idle',
        ]);
    }
}
