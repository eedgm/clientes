<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Task;
use App\Models\Attach;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskAttachesTest extends TestCase
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
    public function it_gets_task_attaches()
    {
        $task = Task::factory()->create();
        $attaches = Attach::factory()
            ->count(2)
            ->create([
                'task_id' => $task->id,
            ]);

        $response = $this->getJson(route('api.tasks.attaches.index', $task));

        $response->assertOk()->assertSee($attaches[0]->attachment);
    }

    /**
     * @test
     */
    public function it_stores_the_task_attaches()
    {
        $task = Task::factory()->create();
        $data = Attach::factory()
            ->make([
                'task_id' => $task->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.tasks.attaches.store', $task),
            $data
        );

        $this->assertDatabaseHas('attaches', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $attach = Attach::latest('id')->first();

        $this->assertEquals($task->id, $attach->task_id);
    }
}
