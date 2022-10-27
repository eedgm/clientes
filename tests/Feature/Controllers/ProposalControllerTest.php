<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Proposal;

use App\Models\Client;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProposalControllerTest extends TestCase
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
}
