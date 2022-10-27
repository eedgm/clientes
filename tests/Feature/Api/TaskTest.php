<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Task;

use App\Models\Statu;
use App\Models\Version;
use App\Models\Receipt;
use App\Models\Priority;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
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
    public function it_gets_tasks_list()
    {
        $tasks = Task::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.tasks.index'));

        $response->assertOk()->assertSee($tasks[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_task()
    {
        $data = Task::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.tasks.store'), $data);

        $this->assertDatabaseHas('tasks', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_task()
    {
        $task = Task::factory()->create();

        $statu = Statu::factory()->create();
        $priority = Priority::factory()->create();
        $version = Version::factory()->create();
        $receipt = Receipt::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'hours' => $this->faker->randomNumber(0),
            'real_hours' => $this->faker->randomNumber(1),
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'version_id' => $version->id,
            'receipt_id' => $receipt->id,
        ];

        $response = $this->putJson(route('api.tasks.update', $task), $data);

        $data['id'] = $task->id;

        $this->assertDatabaseHas('tasks', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_task()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson(route('api.tasks.destroy', $task));

        $this->assertSoftDeleted($task);

        $response->assertNoContent();
    }
}
