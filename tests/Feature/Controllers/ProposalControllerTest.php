<?php

namespace Tests\Feature\Controllers;

use App\Models\Client;
use App\Models\Developer;
use App\Models\Priority;
use App\Models\Proposal;
use App\Models\Statu;
use App\Models\Task;
use App\Models\User;
use App\Models\Version;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProposalControllerTest extends TestCase
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
    public function it_displays_index_view_with_proposals()
    {
        $proposals = Proposal::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('proposals.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.proposals.index')
            ->assertViewHas('proposals');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_proposal()
    {
        $response = $this->get(route('proposals.create'));

        $response->assertOk()->assertViewIs('app.proposals.create');
    }

    /**
     * @test
     */
    public function it_stores_the_proposal()
    {
        $data = Proposal::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('proposals.store'), $data);

        $this->assertDatabaseHas('proposals', $data);

        $proposal = Proposal::latest('id')->first();

        $response->assertRedirect(route('proposals.edit', $proposal));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_proposal()
    {
        $proposal = Proposal::factory()->create();

        $response = $this->get(route('proposals.show', $proposal));

        $response
            ->assertOk()
            ->assertViewIs('app.proposals.show')
            ->assertViewHas('proposal');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_proposal()
    {
        $proposal = Proposal::factory()->create();

        $response = $this->get(route('proposals.edit', $proposal));

        $response
            ->assertOk()
            ->assertViewIs('app.proposals.edit')
            ->assertViewHas('proposal');
    }

    /**
     * @test
     */
    public function it_displays_gantt_view_with_server_side_config_and_assets()
    {
        $proposal = Proposal::factory()->create();
        Priority::factory()->count(2)->create();
        Statu::factory()->count(2)->create();

        $response = $this->get(route('gantt', $proposal));

        $response
            ->assertOk()
            ->assertViewIs('app.proposals.gantt')
            ->assertViewHas('proposal')
            ->assertViewHas('ganttConfig')
            ->assertSee('id="gantt-config"', false)
            ->assertSee('data-gantt-zoom-select', false)
            ->assertSee('proposal-gantt.css', false)
            ->assertSee('proposal-gantt.js', false);
    }

    /**
     * @test
     */
    public function it_exposes_hour_per_day_from_latest_version_in_gantt_config()
    {
        $proposal = Proposal::factory()->create();
        Version::factory()->create([
            'proposal_id' => $proposal->id,
            'hour_per_day' => 6,
        ]);
        Priority::factory()->create();
        Statu::factory()->create();

        $response = $this->get(route('gantt', $proposal));

        $config = $response->viewData('ganttConfig');

        $this->assertSame(6, $config['hour_per_day']);
        $this->assertArrayHasKey('reorder', $config['routes']);
    }

    /**
     * @test
     */
    public function it_defaults_gantt_zoom_to_day()
    {
        $proposal = Proposal::factory()->create();
        Priority::factory()->create();
        Statu::factory()->create();

        $response = $this->get(route('gantt', $proposal));

        $config = $response->viewData('ganttConfig');

        $this->assertSame('day', $config['default_zoom']);
    }

    /**
     * @test
     */
    public function it_defaults_hour_per_day_to_eight_when_no_version_exists()
    {
        $proposal = Proposal::factory()->create();
        Priority::factory()->create();
        Statu::factory()->create();

        $response = $this->get(route('gantt', $proposal));

        $config = $response->viewData('ganttConfig');

        $this->assertSame(8, $config['hour_per_day']);
    }

    /**
     * @test
     */
    public function it_updates_the_proposal()
    {
        $proposal = Proposal::factory()->create();

        $client = Client::factory()->create();

        $data = [
            'product_name' => $this->faker->text(255),
            'description' => $this->faker->sentence(15),
            'client_id' => $client->id,
        ];

        $response = $this->put(route('proposals.update', $proposal), $data);

        $data['id'] = $proposal->id;

        $this->assertDatabaseHas('proposals', $data);

        $response->assertRedirect(route('proposals.edit', $proposal));
    }

    /**
     * @test
     */
    public function it_deletes_the_proposal()
    {
        $proposal = Proposal::factory()->create();

        $response = $this->delete(route('proposals.destroy', $proposal));

        $response->assertRedirect(route('proposals.index'));

        $this->assertModelMissing($proposal);
    }

    /**
     * @test
     */
    public function gantt_page_has_hours_table_toggle_and_container()
    {
        $proposal = Proposal::factory()->create();
        Priority::factory()->count(2)->create();
        Statu::factory()->count(2)->create();

        $response = $this->get(route('gantt', $proposal));

        $response
            ->assertOk()
            // View toggle element
            ->assertSee('data-gantt-view-toggle', false)
            // Default active state is "gantt"
            ->assertSee('data-gantt-view="gantt"', false)
            // Hours option exists
            ->assertSee('data-gantt-view="hours"', false)
            // Hours table container present and initially hidden
            ->assertSee('id="gantt-hours-table"', false);
    }

    /**
     * @test
     */
    public function gantt_page_exposes_task_developers_sync_route_in_config()
    {
        $proposal = Proposal::factory()->create();
        Priority::factory()->create();
        Statu::factory()->create();

        $response = $this->get(route('gantt', $proposal));

        $config = $response->viewData('ganttConfig');

        $this->assertArrayHasKey('task_developers_sync', $config['routes']);
        $this->assertStringContainsString('__TASK__', $config['routes']['task_developers_sync']);
    }

    /**
     * @test
     */
    public function gantt_page_includes_developer_catalog_in_config()
    {
        $proposal = Proposal::factory()->create();
        Priority::factory()->create();
        Statu::factory()->create();
        Developer::factory()->count(3)->create();

        $response = $this->get(route('gantt', $proposal));

        $config = $response->viewData('ganttConfig');

        $this->assertArrayHasKey('developers', $config);
        $this->assertCount(3, $config['developers']);
        $this->assertArrayHasKey('id', $config['developers'][0]);
        $this->assertArrayHasKey('name', $config['developers'][0]);
        $this->assertArrayHasKey('email', $config['developers'][0]);
    }

    /**
     * @test
     */
    public function gantt_page_developers_is_empty_array_when_no_developers_exist()
    {
        $proposal = Proposal::factory()->create();
        Priority::factory()->create();
        Statu::factory()->create();

        $response = $this->get(route('gantt', $proposal));

        $config = $response->viewData('ganttConfig');

        $this->assertArrayHasKey('developers', $config);
        $this->assertCount(0, $config['developers']);
    }

    /**
     * @test
     */
    public function gantt_page_with_tasks_renders_no_errors()
    {
        $proposal = Proposal::factory()->create();
        Priority::factory()->count(3)->create();
        Statu::factory()->count(3)->create();
        Task::factory()->count(2)->create([
            'proposal_id' => $proposal->id,
        ]);

        $response = $this->get(route('gantt', $proposal));

        $response->assertOk();
        $this->assertCount(2, $proposal->tasks);
    }
}
