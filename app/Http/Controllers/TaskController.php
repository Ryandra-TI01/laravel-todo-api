<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Traits\CachesUserTasks;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{

    use CachesUserTasks;

    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    public function index(Request $request)
    {

        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'is_completed' => 'nullable',
            'search' => 'nullable|string|max:255',
        ]);

        $userId = $request->user()->id;

        $queryParams = [
            'page' => (int) $request->input('page', 1),
            'is_completed' => normalize_boolean($request->input('is_completed')),// bisa null, true, false
            'search' => $request->input('search'),
        ];

        return $this->cacheUserTasks($userId, $queryParams, function () use ($userId, $queryParams) {
            $query = Task::where('user_id', $userId)
                ->select(['id', 'title', 'description', 'due_date', 'is_completed']);

            if (isset($queryParams['is_completed'])) {
                $query->where('is_completed', $queryParams['is_completed']);
            }

            if (!empty($queryParams['search'])) {
                $query->where('title', 'ilike', '%' . $queryParams['search'] . '%');
                $query->whereRaw('title ilike ?', ['%' . $queryParams['search'] . '%']);
            }

            return $query->latest()->paginate(10);
        });
    }
    public function calendarTasks(Request $request)
    {
        $userId = $request->user()->id;

        $tasks = $this->cacheUserTasks($userId, [], fn() => Task::where('user_id', $userId)->get());

        return response()->json([
            'data' => TaskResource::collection($tasks)
        ]);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = $request->user()->tasks()->create($request->validated());
        $this->clearUserTaskCache($request->user()->id);
        return response()->json([
            'data' => new TaskResource($task)
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());
        $this->clearUserTaskCache($request->user()->id);
        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        $this->clearUserTaskCache($task->user_id);
        return response()->json(['message' => 'Task deleted']);
    }

    public function stats(Request $resquest)
    {
        $userId = $resquest->user()->id;
        $total = Task::where('user_id', $userId)->count();
        $completed = Task::where('user_id', $userId)->where('is_completed', true)->count();
        $active = $total - $completed;
        $rate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        return response()->json([
            'completed' => $completed,
            'active' => $active,
            'rate' => $rate,
            'total' => $total

        ]);
    }
}
