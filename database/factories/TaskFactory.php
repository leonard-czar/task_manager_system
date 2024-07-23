<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'completed' => $this->faker->boolean,
            'user_id' => User::factory(),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'), // Add this line
        ];
    }
}
