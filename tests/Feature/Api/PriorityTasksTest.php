<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Task;
use App\Models\Priority;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriorityTasksTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email' => 'admin@admin.com']);

        Sanctum::actingAs($user, [], 'web');

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_priority_tasks()
    {
        $priority = Priority::factory()->create();
        $tasks = Task::factory()
            ->count(2)
            ->create([
                'priority_id' => $priority->id,
            ]);

        $response = $this->getJson(
            route('api.priorities.tasks.index', $priority)
        );

        $response->assertOk()->assertSee($tasks[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_priority_tasks()
    {
        $priority = Priority::factory()->create();
        $data = Task::factory()
            ->make([
                'priority_id' => $priority->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.priorities.tasks.store', $priority),
            $data
        );

        $this->assertDatabaseHas('tasks', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $task = Task::latest('id')->first();

        $this->assertEquals($priority->id, $task->priority_id);
    }
}
