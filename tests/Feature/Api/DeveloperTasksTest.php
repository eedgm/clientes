<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Task;
use App\Models\Developer;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeveloperTasksTest extends TestCase
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
    public function it_gets_developer_tasks()
    {
        $developer = Developer::factory()->create();
        $task = Task::factory()->create();

        $developer->tasks()->attach($task);

        $response = $this->getJson(
            route('api.developers.tasks.index', $developer)
        );

        $response->assertOk()->assertSee($task->name);
    }

    /**
     * @test
     */
    public function it_can_attach_tasks_to_developer()
    {
        $developer = Developer::factory()->create();
        $task = Task::factory()->create();

        $response = $this->postJson(
            route('api.developers.tasks.store', [$developer, $task])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $developer
                ->tasks()
                ->where('tasks.id', $task->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_tasks_from_developer()
    {
        $developer = Developer::factory()->create();
        $task = Task::factory()->create();

        $response = $this->deleteJson(
            route('api.developers.tasks.store', [$developer, $task])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $developer
                ->tasks()
                ->where('tasks.id', $task->id)
                ->exists()
        );
    }
}
