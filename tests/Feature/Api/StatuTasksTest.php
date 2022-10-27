<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Task;
use App\Models\Statu;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatuTasksTest extends TestCase
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
    public function it_gets_statu_tasks()
    {
        $statu = Statu::factory()->create();
        $tasks = Task::factory()
            ->count(2)
            ->create([
                'statu_id' => $statu->id,
            ]);

        $response = $this->getJson(route('api.status.tasks.index', $statu));

        $response->assertOk()->assertSee($tasks[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_statu_tasks()
    {
        $statu = Statu::factory()->create();
        $data = Task::factory()
            ->make([
                'statu_id' => $statu->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.status.tasks.store', $statu),
            $data
        );

        $this->assertDatabaseHas('tasks', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $task = Task::latest('id')->first();

        $this->assertEquals($statu->id, $task->statu_id);
    }
}
