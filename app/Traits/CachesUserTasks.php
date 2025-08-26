<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CachesUserTasks
{
    // Define cache tags and keys for user tasks based on user ID
    protected function getUserTaskCacheTags($userId): array
    {
        return ['user_tasks', "user_{$userId}"];
    }

    // create a unique cache key based on user ID and query parameters
    protected function getUserTaskCacheKey(int $userId, array $queryParams): string
    {
        // Normalize and sort query for consistency
        ksort($queryParams);

        $queryKey = http_build_query(array_merge([
            'user_id' => $userId,
        ], $queryParams));

        return "user_tasks:{$queryKey}";
    }
    
    // Cache the result of a callback function with user-specific tags and keys
    protected function cacheUserTasks(int $userId, array $queryParams, \Closure $callback)
    {
        $tags = $this->getUserTaskCacheTags($userId);
        $key = $this->getUserTaskCacheKey($userId, $queryParams);

        return Cache::tags($tags)->remember($key, now()->addMinutes(5), $callback);
    }

    // Clear cache for a specific user's tasks
    protected function clearUserTaskCache(int $userId): void
    {
        Cache::tags($this->getUserTaskCacheTags($userId))->flush();
    }
}
