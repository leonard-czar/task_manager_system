<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_list_tasks()
    {
        $tasks = Task::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'title', 'description', 'completed', 'due_date', 'created_at', 'updated_at']
            ]);
    }

    public function test_user_can_create_task()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'completed' => false,
            'due_date' => "2024-12-31"
        ];

        $response = $this->actingAs($this->user)->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'task was added successfully']);
    }

    public function test_user_can_update_task()
    {

        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updatedData = [
            'title' => 'Updated Task',
            'completed' => true,
            'due_date' => "2024-12-31"
        ];

        $response = $this->actingAs($this->user)->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'task was updated successfully']);
    }

    public function test_user_can_delete_task()
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
