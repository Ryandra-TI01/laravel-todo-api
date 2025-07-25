<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->count(100)
            ->create()
            ->each(function ($user) {
                Task::factory()->count(500)->create([
                    'user_id' => $user->id,
                ]);
            });
    }
}
