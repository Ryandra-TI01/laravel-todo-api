<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', RegisterController::class);
    Route::post('/login', LoginController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', UserController::class);
        Route::post('/logout', LogoutController::class);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('tasks')->group(function () {
        Route::get('/stats', [TaskController::class, 'stats']);
        Route::get('/calendar', [TaskController::class, 'calendarTasks']);
    });
    Route::apiResource('/tasks', TaskController::class);

});

Route::get('/public-tasks', [TaskController::class, 'publicIndex']);
Route::get('/test-tasks/{id}', [\App\Http\Controllers\Test\TestTaskController::class, 'index']);