<?php

namespace Tests\Feature\Controllers;

use App\Models\Developer;
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
    public function it_stores_a_gantt_task_when_hours_is_omitted()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $payload = [
            'proposal_id' => $proposal->id,
            'text' => 'New task without hours',
            'start_date' => now()->format('Y-m-d H:i:s'),
            'duration' => 1,
            'priority_id' => $priority->id,
            'statu_id' => $statu->id,
        ];

        $response = $this->postJson(route('tasks.gantt.store'), $payload);

        $response
            ->assertStatus(201)
            ->assertJsonFragment(['action' => 'inserted']);

        $this->assertDatabaseHas('tasks', [
            'proposal_id' => $proposal->id,
            'text' => 'New task without hours',
            'hours' => 0,
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

    /**
     * @test
     */
    public function it_syncs_gantt_task_developers_and_recomputes_task_hours()
    {
        $task = $this->createGanttTask(Proposal::factory()->create(), 'Build feature');
        $first = Developer::factory()->create();
        $second = Developer::factory()->create();

        $response = $this->putJson(
            route('tasks.gantt.developers.sync', $task),
            [
                'developers' => [
                    ['developer_id' => $first->id, 'hours' => 3],
                    ['developer_id' => $second->id, 'hours' => 5],
                ],
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath('action', 'updated')
            ->assertJsonPath('hours', 8);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'hours' => 8,
        ]);

        $this->assertDatabaseHas('developer_task', [
            'task_id' => $task->id,
            'developer_id' => $first->id,
            'hours' => 3,
        ]);

        $this->assertDatabaseHas('developer_task', [
            'task_id' => $task->id,
            'developer_id' => $second->id,
            'hours' => 5,
        ]);
    }

    /**
     * @test
     */
    public function it_detaches_developers_omitted_from_the_sync_payload()
    {
        $task = $this->createGanttTask(Proposal::factory()->create(), 'Build feature');
        $kept = Developer::factory()->create();
        $dropped = Developer::factory()->create();

        $task->developers()->attach($kept->id, ['hours' => 2]);
        $task->developers()->attach($dropped->id, ['hours' => 4]);

        $response = $this->putJson(
            route('tasks.gantt.developers.sync', $task),
            [
                'developers' => [
                    ['developer_id' => $kept->id, 'hours' => 6],
                ],
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath('hours', 6);

        $this->assertDatabaseHas('developer_task', [
            'task_id' => $task->id,
            'developer_id' => $kept->id,
            'hours' => 6,
        ]);

        $this->assertDatabaseMissing('developer_task', [
            'task_id' => $task->id,
            'developer_id' => $dropped->id,
        ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'hours' => 6,
        ]);
    }

    /**
     * @test
     */
    public function it_keeps_legacy_task_hours_when_no_assignment_hours_are_provided()
    {
        $task = $this->createGanttTask(Proposal::factory()->create(), 'Legacy task');
        $task->update(['hours' => 12]);
        $developer = Developer::factory()->create();

        // attach without hours, mimicking legacy pivot rows
        $task->developers()->attach($developer->id);

        $response = $this->putJson(
            route('tasks.gantt.developers.sync', $task),
            ['developers' => []]
        );

        $response
            ->assertOk()
            ->assertJsonPath('hours', 12);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'hours' => 12,
        ]);
    }

    /**
     * @test
     */
    public function it_returns_the_persisted_task_in_the_create_response()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);
        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $payload = [
            'proposal_id' => $proposal->id,
            'text' => 'Persisted with task payload',
            'start_date' => '2026-01-01 00:00:00',
            'hours' => 8,
            'priority_id' => $priority->id,
            'statu_id' => $statu->id,
        ];

        $response = $this->postJson(route('tasks.gantt.store'), $payload);

        $response
            ->assertStatus(201)
            ->assertJsonFragment(['action' => 'inserted'])
            ->assertJsonStructure([
                'action',
                'tid',
                'task' => ['id', 'start_date', 'duration', 'sort_order'],
            ]);

        $task = Task::latest('id')->first();

        $this->assertSame($task->id, (int) $response->json('tid'));
        $this->assertSame($task->id, (int) $response->json('task.id'));
        $this->assertSame(1, (int) $response->json('task.sort_order'));
    }

    /**
     * @test
     */
    public function it_creates_gantt_task_with_duration_derived_from_hour_per_day()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);
        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $payload = [
            'proposal_id' => $proposal->id,
            'text' => 'Sized from hours',
            'start_date' => '2026-01-05 00:00:00',
            'hours' => 20,
            'priority_id' => $priority->id,
            'statu_id' => $statu->id,
        ];

        $response = $this->postJson(route('tasks.gantt.store'), $payload);

        $response->assertStatus(201);

        $task = Task::latest('id')->first();

        $this->assertSame(3, (int) $task->duration);
        $this->assertSame(1, (int) $task->sort_order);
    }

    /**
     * @test
     */
    public function it_uses_eight_as_default_hour_per_day_when_proposal_has_no_version()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $payload = [
            'proposal_id' => $proposal->id,
            'text' => 'Default hour per day',
            'start_date' => '2026-01-05 00:00:00',
            'hours' => 9,
            'priority_id' => $priority->id,
            'statu_id' => $statu->id,
        ];

        $response = $this->postJson(route('tasks.gantt.store'), $payload);

        $response->assertStatus(201);

        $task = Task::latest('id')->first();

        $this->assertSame(2, (int) $task->duration);
    }

    /**
     * @test
     */
    public function it_recomputes_duration_when_hours_change_on_update()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $task = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Resize on update',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-05 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'hours' => 4,
        ]);

        $response = $this->putJson(route('tasks.gantt.update', $task), [
            'text' => 'Resized',
            'start_date' => '2026-01-05 00:00:00',
            'hours' => 18,
            'priority_id' => $priority->id,
            'statu_id' => $statu->id,
        ]);

        $response->assertOk();

        $this->assertSame(3, (int) $task->fresh()->duration);
    }

    /**
     * @test
     */
    public function it_cascades_following_tasks_when_a_task_shifts_on_update()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $first = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'First',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-05 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 1,
            'hours' => 4,
        ]);

        $second = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Second',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-06 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 2,
            'hours' => 4,
        ]);

        $third = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Third',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-07 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 3,
            'hours' => 4,
        ]);

        $this->putJson(route('tasks.gantt.update', $first), [
            'text' => 'First',
            'start_date' => '2026-01-12 00:00:00',
            'duration' => 3,
            'hours' => 24,
            'priority_id' => $priority->id,
            'statu_id' => $statu->id,
        ])->assertOk();

        $this->assertSame('2026-01-16 00:00:00', $second->fresh()->start_date->format('Y-m-d H:i:s'));
        $this->assertSame('2026-01-19 00:00:00', $third->fresh()->start_date->format('Y-m-d H:i:s'));
    }

    /**
     * @test
     */
    public function it_persists_manual_reorder_via_reorder_endpoint()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $first = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'A',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-05 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 1,
            'hours' => 4,
        ]);

        $second = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'B',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-06 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 2,
            'hours' => 4,
        ]);

        $third = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'C',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-07 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 3,
            'hours' => 4,
        ]);

        $response = $this->postJson(route('proposal.tasks.reorder', $proposal), [
            'ordered_ids' => [$third->id, $first->id, $second->id],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('action', 'reordered')
            ->assertJsonPath('count', 3)
            ->assertJsonPath('tasks.0.id', $third->id)
            ->assertJsonPath('tasks.0.sort_order', 1)
            ->assertJsonPath('tasks.1.id', $first->id)
            ->assertJsonPath('tasks.1.sort_order', 2)
            ->assertJsonPath('tasks.2.id', $second->id)
            ->assertJsonPath('tasks.2.sort_order', 3);

        $ordered = $proposal->orderedTasks()->get();

        $this->assertSame([$third->id, $first->id, $second->id], $ordered->pluck('id')->all());
    }

    /**
     * @test
     */
    public function it_returns_full_task_payload_after_cascading_update()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);

        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $first = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'First',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-05 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 1,
            'hours' => 4,
        ]);

        $middle = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Middle',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-06 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 2,
            'hours' => 4,
        ]);

        $third = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Third',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-07 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 3,
            'hours' => 4,
        ]);

        $response = $this->putJson(route('tasks.gantt.update', $middle), [
            'text' => 'Middle',
            'start_date' => '2026-01-20 00:00:00',
            'duration' => 2,
            'hours' => 16,
            'priority_id' => $priority->id,
            'statu_id' => $statu->id,
        ])->assertOk();

        $response
            ->assertJsonPath('action', 'updated')
            ->assertJsonPath('task.id', $middle->id)
            ->assertJsonPath('task.start_date', '2026-01-20 00:00:00')
            ->assertJsonPath('task.duration', 2)
            ->assertJsonPath('task.sort_order', 2);

        // The middle task is the cursor; only the third task may be
        // shifted. The first task must remain at 2026-01-05 (it comes
        // BEFORE the cursor and must NOT be moved to a later slot).
        $response
            ->assertJsonPath('tasks.0.id', $first->id)
            ->assertJsonPath('tasks.0.start_date', '2026-01-05 00:00:00')
            ->assertJsonPath('tasks.1.id', $middle->id)
            ->assertJsonPath('tasks.1.start_date', '2026-01-20 00:00:00')
            ->assertJsonPath('tasks.2.id', $third->id)
            ->assertJsonPath('tasks.2.start_date', '2026-01-23 00:00:00');

        $this->assertSame('2026-01-05 00:00:00', $first->fresh()->start_date->format('Y-m-d H:i:s'));
        $this->assertSame('2026-01-23 00:00:00', $third->fresh()->start_date->format('Y-m-d H:i:s'));
    }

    /**
     * @test
     */
    public function it_returns_full_task_payload_after_gantt_create()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 8,
        ]);
        $priority = Priority::factory()->create();
        $statu = Statu::factory()->create();

        $existing = Task::create([
            'proposal_id' => $proposal->id,
            'text' => 'Existing',
            'statu_id' => $statu->id,
            'priority_id' => $priority->id,
            'start_date' => '2026-01-05 00:00:00',
            'duration' => 1,
            'progress' => 0,
            'parent' => 0,
            'sort_order' => 1,
            'hours' => 4,
        ]);

        $response = $this->postJson(route('tasks.gantt.store'), [
            'proposal_id' => $proposal->id,
            'text' => 'New task',
            'start_date' => '2026-01-01 00:00:00',
            'hours' => 8,
            'priority_id' => $priority->id,
            'statu_id' => $statu->id,
        ])->assertStatus(201);

        $newTask = Task::where('text', 'New task')->firstOrFail();

        $response
            ->assertJsonPath('action', 'inserted')
            ->assertJsonPath('tid', $newTask->id)
            ->assertJsonPath('task.id', $newTask->id)
            ->assertJsonPath('task.sort_order', 2)
            // The new task was auto-placed on the next business day
            // after the existing task ends (Mon 2026-01-05 + 1 day
            // duration = ends Tue 2026-01-06, next business day =
            // Wed 2026-01-07), and the response should reflect the
            // authoritative server-computed start_date.
            ->assertJsonPath('task.start_date', '2026-01-07 00:00:00');

        $this->assertCount(2, $response->json('tasks'));
        $this->assertSame($existing->id, $response->json('tasks.0.id'));
        $this->assertSame($newTask->id, $response->json('tasks.1.id'));
    }

    /**
     * @test
     */
    public function it_rejects_reorder_payload_without_array()
    {
        $proposal = Proposal::factory()->create();

        $this->withExceptionHandling();

        $response = $this->postJson(route('proposal.tasks.reorder', $proposal), [
            'ordered_ids' => 'not-an-array',
        ]);

        $response->assertStatus(422);
    }
}
