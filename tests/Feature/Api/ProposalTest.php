<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Proposal;

use App\Models\Client;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProposalTest extends TestCase
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
    public function it_gets_proposals_list()
    {
        $proposals = Proposal::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.proposals.index'));

        $response->assertOk()->assertSee($proposals[0]->product_name);
    }

    /**
     * @test
     */
    public function it_stores_the_proposal()
    {
        $data = Proposal::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.proposals.store'), $data);

        $this->assertDatabaseHas('proposals', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
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

        $response = $this->putJson(
            route('api.proposals.update', $proposal),
            $data
        );

        $data['id'] = $proposal->id;

        $this->assertDatabaseHas('proposals', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_proposal()
    {
        $proposal = Proposal::factory()->create();

        $response = $this->deleteJson(
            route('api.proposals.destroy', $proposal)
        );

        $this->assertModelMissing($proposal);

        $response->assertNoContent();
    }
}
