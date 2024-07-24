<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;


    // protected function setUp(): void
    // {
    //     parent::setUp();
    // }

    public function test_authenticated_user_can_list_tasks()
    {
        $user=User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'description', 'completed', 'due_date', 'created_at', 'updated_at']
            ]);
    }

    public function test_authenticated_user_can_list_a_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson($task->toArray());
    }

    public function test_authenticated_user_can_create_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'completed' => false,
            'due_date' => "2024-12-31"
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'task was added successfully']);
    }

    public function test_authenticated_user_can_update_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $task = Task::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'title' => 'Updated Task',
            'completed' => true,
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'task was updated successfully']);
    }

    public function test_authenticated_user_can_delete_task()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }


    public function test_unauthenticated_user_cannot_create_task()
    {
        User::factory()->create();

        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'completed' => false,
            'due_date' => "2024-12-31"
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_unauthenticated_user_cannot_delete_task()
    {
        $user = User::factory()->create();

        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_unauthenticated_user_cannot_update_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'title' => 'Updated Task',
            'completed' => true,
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }


    public function test_unauthenticated_user_cannot_list_tasks()
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }
   
}
