<?php

namespace App\Services;

use App\Models\JobDetail;
use Illuminate\Support\Facades\Cache;

class AiCache
{
    public static function key(JobDetail $detail, string $cvText, string $instructions, bool $isFollowUp): string
    {
        return 'ai:'.md5($detail->id.'|'.$cvText.'|'.$instructions.'|'.($isFollowUp ? 'followup' : 'first'));
    }

    public static function get(string $key): ?array
    {
        return Cache::get($key);
    }

    public static function set(string $key, array $result): void
    {
        Cache::put($key, $result, now()->addDays(7));
    }

    public static function bustByJobDetail(JobDetail $detail): void
    {
        // Cannot bust individual cached entries without knowing the CV/instructions combo,
        // but we can bust all keys for this job detail by clearing a tag.
        // For database cache, we rely on TTL expiry instead.
    }
}
