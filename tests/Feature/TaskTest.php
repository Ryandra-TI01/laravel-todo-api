<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsUser()
    {
        return User::factory()->create();
    }

    public function test_user_can_create_task()
    {
        $user = $this->actingAsUser();

        $payload = [
            'title' => 'Tugas Baru',
            'description' => 'Deskripsi Tugas',
            'due_date' => now()->addDays(2)->toDateString(),
            'is_completed' => false,
        ];

        $response = $this->actingAs($user)->postJson('/api/tasks', $payload);

        $response->assertCreated()
                 ->assertJsonFragment(['title' => 'Tugas Baru']);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tugas Baru',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_list_own_tasks()
    {
        $user = $this->actingAsUser();

        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/tasks');

        $response->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_user_can_view_single_own_task()
    {
        $user = $this->actingAsUser();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

        $response->assertOk()
                 ->assertJsonFragment(['id' => $task->id]);
    }

    public function test_user_can_update_own_task()
    {
        $user = $this->actingAsUser();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertOk()
                 ->assertJsonFragment(['title' => 'Updated Title']);

        $this->assertDatabaseHas('tasks', ['title' => 'Updated Title']);
    }

    public function test_user_can_delete_own_task()
    {
        $user = $this->actingAsUser();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_access_other_users_task()
    {
        $userA = $this->actingAsUser();
        $userB = $this->actingAsUser();
        $task = Task::factory()->create(['user_id' => $userB->id]);

        $this->actingAs($userA)->getJson("/api/tasks/{$task->id}")->assertForbidden();
        $this->actingAs($userA)->putJson("/api/tasks/{$task->id}", ['title' => 'Hack'])->assertForbidden();
        $this->actingAs($userA)->deleteJson("/api/tasks/{$task->id}")->assertForbidden();
    }
}
