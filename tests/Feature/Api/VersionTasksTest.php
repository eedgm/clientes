<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Task;
use App\Models\Version;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VersionTasksTest extends TestCase
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
    public function it_gets_version_tasks()
    {
        $version = Version::factory()->create();
        $tasks = Task::factory()
            ->count(2)
            ->create([
                'version_id' => $version->id,
            ]);

        $response = $this->getJson(route('api.versions.tasks.index', $version));

        $response->assertOk()->assertSee($tasks[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_version_tasks()
    {
        $version = Version::factory()->create();
        $data = Task::factory()
            ->make([
                'version_id' => $version->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.versions.tasks.store', $version),
            $data
        );

        $this->assertDatabaseHas('tasks', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $task = Task::latest('id')->first();

        $this->assertEquals($version->id, $task->version_id);
    }
}
