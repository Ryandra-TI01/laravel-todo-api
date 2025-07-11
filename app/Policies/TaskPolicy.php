<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    // Lihat semua task (biasanya hanya task milik sendiri)
    public function viewAny(User $user): bool
    {
        return true;
    }

    // Lihat task tertentu
    public function view(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    // Boleh buat task
    public function create(User $user): bool
    {
        return true;
    }

    // Boleh update kalau milik sendiri
    public function update(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    // Boleh delete kalau milik sendiri
    public function delete(User $user, Task $task): bool
    {
        return $task->user_id === $user->id;
    }

    // Optional: restore dan force delete
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}

