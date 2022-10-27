<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Task;
use App\Models\Developer;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskDevelopersTest extends TestCase
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
    public function it_gets_task_developers()
    {
        $task = Task::factory()->create();
        $developer = Developer::factory()->create();

        $task->developers()->attach($developer);

        $response = $this->getJson(route('api.tasks.developers.index', $task));

        $response->assertOk()->assertSee($developer->id);
    }

    /**
     * @test
     */
    public function it_can_attach_developers_to_task()
    {
        $task = Task::factory()->create();
        $developer = Developer::factory()->create();

        $response = $this->postJson(
            route('api.tasks.developers.store', [$task, $developer])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $task
                ->developers()
                ->where('developers.id', $developer->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_developers_from_task()
    {
        $task = Task::factory()->create();
        $developer = Developer::factory()->create();

        $response = $this->deleteJson(
            route('api.tasks.developers.store', [$task, $developer])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $task
                ->developers()
                ->where('developers.id', $developer->id)
                ->exists()
        );
    }
}
