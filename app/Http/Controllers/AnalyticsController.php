<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Completed vs Uncompleted
        $completed = Task::where('user_id', $userId)->where('is_completed', true)->count();
        $uncompleted = Task::where('user_id', $userId)->where('is_completed', false)->count();

        // Rata-rata / jumlah task dibuat per hari
        $totalTasks = Task::where('user_id', $userId)->count();
        $daysSinceFirstTask = Task::where('user_id', $userId)
            ->min('created_at')
            ? Carbon::parse(Task::where('user_id', $userId)->min('created_at'))->diffInDays(Carbon::now()) + 1
            : 1;
        $averagePerDay = $totalTasks > 0 ? round($totalTasks / $daysSinceFirstTask, 2) : 0;

        // Pertumbuhan jumlah task per hari (Line Chart)
        $taskGrowth = Task::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // periode tanggal deadline dea
        $taskDeadlines = Task::where('user_id', $userId)
            ->whereNotNull('due_date')
            ->selectRaw('DATE(due_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Task per periode (Bar/Line Chart) → bisa filter hari/minggu/bulan
        $taskPerMonth = Task::where('user_id', $userId)
            ->selectRaw('DATE_FORMAT(\'month\', created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($row) {
                return [
                    'month' => Carbon::parse($row->month)->format('Y-m'),
                    'count' => $row->count
                ];
            });

        return response()->json([
            'status' => [
                'completed' => $completed,
                'uncompleted' => $uncompleted,
            ],
            'average_per_day' => $averagePerDay,
            'growth' => $taskGrowth,
            'deadlines' => $taskDeadlines,
            'task_per_month' => $taskPerMonth,
        ]);
    }
}
