<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TestTaskController extends Controller
{

    public function index($id)
    {
        $cacheKey = "user_tasks_{$id}_page_" . request('page', 1);

        $tasks = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($id) {
            $user = User::findOrFail($id);

            return $user->tasks()
                ->select(['id', 'title', 'description', 'due_date', 'is_completed', 'created_at', 'updated_at'])
                ->latest()
                ->paginate(50);
        });

        return TaskResource::collection($tasks);
    }

}
