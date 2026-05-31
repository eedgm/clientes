<?php

namespace Tests\Feature\Controllers;

use App\Models\Priority;
use App\Models\Proposal;
use App\Models\Receipt;
use App\Models\Statu;
use App\Models\Task;
use App\Models\User;
use App\Models\Version;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->seed(PermissionsSeeder::class);

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

    /**
     * @test
     */
    public function it_returns_gantt_tasks_payload_for_a_proposal()
    {
        $proposal = Proposal::factory()->create();
        $otherProposal = Proposal::factory()->create();

        $firstTask = $this->createGanttTask($proposal, 'Kickoff');
        $secondTask = $this->createGanttTask($proposal, 'Implementation');
        $this->createGanttTask($otherProposal, 'Out of scope');

        $response = $this->getJson(route('proposal.tasks', $proposal));

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $firstTask->id, 'text' => $firstTask->text])
            ->assertJsonFragment(['id' => $secondTask->id, 'text' => $secondTask->text]);
    }

    /**
     * @test
     */
    public function it_stores_a_gantt_task_with_valid_payload()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $payload = [
            'proposal_id' => $proposal->id,
            'text' => 'Plan sprint',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 2,
            'hours' => 8,
            'priority_id' => $priority->id,
            'statu_id' => $statu->id,
            'progress' => 0.4,
            'parent' => 0,
        ];

        $response = $this->postJson(route('tasks.gantt.store'), $payload);

        $response
            ->assertStatus(201)
            ->assertJsonFragment(['action' => 'inserted']);

        $this->assertDatabaseHas('tasks', [
            'proposal_id' => $proposal->id,
            'text' => 'Plan sprint',
            'priority_id' => $priority->id,
            'statu_id' => $statu->id,
        ]);
    }

    /**
     * @test
     */
    public function it_rejects_invalid_gantt_create_payload()
    {
        $this->withExceptionHandling();

        $proposal = Proposal::factory()->create();

        $response = $this->postJson(route('tasks.gantt.store'), [
            'proposal_id' => $proposal->id,
            'text' => '',
            'start_date' => 'invalid-date',
            'duration' => -1,
            'hours' => -2,
            'priority_id' => 999999,
            'statu_id' => 999999,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'text',
                'start_date',
                'duration',
                'hours',
                'priority_id',
                'statu_id',
            ]);
    }

    /**
     * @test
     */
    public function it_updates_a_gantt_task_and_preserves_original_proposal_id()
    {
        $proposal = Proposal::factory()->create();
        $anotherProposal = Proposal::factory()->create();
        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();
        $newPriority = Priority::factory()->create();
        $newStatu = Statu::factory()->create();

        $task = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Initial task',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => now()->subDay()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 4,
        ]);

        $response = $this->putJson(route('tasks.gantt.update', $task), [
            'proposal_id' => $anotherProposal->id,
            'text' => 'Updated task',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 5,
            'hours' => 12,
            'priority_id' => $newPriority->id,
            'statu_id' => $newStatu->id,
            'progress' => 0.75,
            'parent' => 0,
        ]);

        $response
            ->assertOk()
            ->assertJson(['action' => 'updated']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'proposal_id' => $proposal->id,
            'text' => 'Updated task',
            'priority_id' => $newPriority->id,
            'statu_id' => $newStatu->id,
        ]);
    }

    /**
     * @test
     */
    public function it_rejects_invalid_gantt_update_payload()
    {
        $this->withExceptionHandling();

        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $task = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Initial task',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => now()->subDay()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 4,
        ]);

        $response = $this->putJson(route('tasks.gantt.update', $task), [
            'text' => '',
            'start_date' => 'not-a-date',
            'duration' => -1,
            'hours' => -1,
            'priority_id' => 999999,
            'statu_id' => 999999,
            'progress' => 2,
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'text',
                'start_date',
                'duration',
                'hours',
                'priority_id',
                'statu_id',
                'progress',
            ]);
    }

    protected function createGanttTask(Proposal $proposal, string $text): Task
    {
        return Task::create([
            'proposal_id' => $proposal->id,
            'text' => $text,
            'statu_id' => Statu::factory()->create()->id,
            'priority_id' => Priority::factory()->create()->id,
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 2,
            'progress' => 0,
            'parent' => 0,
            'hours' => 6,
        ]);
    }
}
