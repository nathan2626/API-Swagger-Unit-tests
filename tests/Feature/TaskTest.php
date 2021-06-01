<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TaskTest extends TestCase
{
    public function createUser()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make($this->faker->password(8)),
        ];

        return User::create($userData);
    }

    public function test_show_task_unauthorized()
    {
        $response = $this->getJson('/api/tasks/1');

        $response->assertStatus(401);
    }

    public function test_show_task_not_found()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $response = $this->actingAs($user)->getJson('/api/tasks/1');

        $response->assertStatus(404);
    }

    public function test_show_task_forbidden()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $task = Task::create([
            'user_id' => 9,
            'body' => $this->faker->title,
            'done' => $this->faker->numberBetween(0, 1)
        ]);

        $response = $this->actingAs($user)->getJson('/api/tasks/1');

        $response->assertStatus(403);
    }

    public function test_show_task_success()
    {
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $task = Task::create([
            'user_id' => 1,
            'body' => $this->faker->title,
            'done' => $this->faker->numberBetween(0, 1)
        ]);

        $response = $this->actingAs($user)->getJson('/api/tasks/1');

        $response->assertStatus(200);
    }
}
