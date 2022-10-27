<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Task;

use App\Models\Statu;
use App\Models\Version;
use App\Models\Receipt;
use App\Models\Priority;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_tasks()
    {
        $tasks = Task::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('tasks.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.tasks.index')
            ->assertViewHas('tasks');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_task()
    {
        $response = $this->get(route('tasks.create'));

        $response->assertOk()->assertViewIs('app.tasks.create');
    }

    /**
     * @test
     */
    public function it_stores_the_task()
    {
        $data = Task::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('tasks.store'), $data);

        $this->assertDatabaseHas('tasks', $data);

        $task = Task::latest('id')->first();

        $response->assertRedirect(route('tasks.edit', $task));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_task()
    {
        $task = Task::factory()->create();

        $response = $this->get(route('tasks.show', $task));

        $response
            ->assertOk()
            ->assertViewIs('app.tasks.show')
            ->assertViewHas('task');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_task()
    {
        $task = Task::factory()->create();

        $response = $this->get(route('tasks.edit', $task));

        $response
            ->assertOk()
            ->assertViewIs('app.tasks.edit')
            ->assertViewHas('task');
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

        $response = $this->put(route('tasks.update', $task), $data);

        $data['id'] = $task->id;

        $this->assertDatabaseHas('tasks', $data);

        $response->assertRedirect(route('tasks.edit', $task));
    }

    /**
     * @test
     */
    public function it_deletes_the_task()
    {
        $task = Task::factory()->create();

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));

        $this->assertSoftDeleted($task);
    }
}
