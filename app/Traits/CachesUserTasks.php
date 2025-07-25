<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CachesUserTasks
{
    protected function getUserTaskCacheTags($userId): array
    {
        return ['user_tasks', "user_{$userId}"];
    }

    protected function getUserTaskCacheKey(int $userId, array $queryParams): string
    {
        // Normalize and sort query for consistency
        ksort($queryParams);

        $queryKey = http_build_query(array_merge([
            'user_id' => $userId,
        ], $queryParams));

        return "user_tasks:{$queryKey}";
    }

    protected function cacheUserTasks(int $userId, array $queryParams, \Closure $callback)
    {
        $tags = $this->getUserTaskCacheTags($userId);
        $key = $this->getUserTaskCacheKey($userId, $queryParams);

        return Cache::tags($tags)->remember($key, now()->addMinutes(5), $callback);
    }

    protected function clearUserTaskCache(int $userId): void
    {
        Cache::tags($this->getUserTaskCacheTags($userId))->flush();
    }
}
