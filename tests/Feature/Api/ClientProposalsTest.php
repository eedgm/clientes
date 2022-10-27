<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Client;
use App\Models\Proposal;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientProposalsTest extends TestCase
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
    public function it_gets_client_proposals()
    {
        $client = Client::factory()->create();
        $proposals = Proposal::factory()
            ->count(2)
            ->create([
                'client_id' => $client->id,
            ]);

        $response = $this->getJson(
            route('api.clients.proposals.index', $client)
        );

        $response->assertOk()->assertSee($proposals[0]->product_name);
    }

    /**
     * @test
     */
    public function it_stores_the_client_proposals()
    {
        $client = Client::factory()->create();
        $data = Proposal::factory()
            ->make([
                'client_id' => $client->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.clients.proposals.store', $client),
            $data
        );

        $this->assertDatabaseHas('proposals', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $proposal = Proposal::latest('id')->first();

        $this->assertEquals($client->id, $proposal->client_id);
    }
}
