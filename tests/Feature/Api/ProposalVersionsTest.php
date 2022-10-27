<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Version;
use App\Models\Proposal;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProposalVersionsTest extends TestCase
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
    public function it_gets_proposal_versions()
    {
        $proposal = Proposal::factory()->create();
        $versions = Version::factory()
            ->count(2)
            ->create([
                'proposal_id' => $proposal->id,
            ]);

        $response = $this->getJson(
            route('api.proposals.versions.index', $proposal)
        );

        $response->assertOk()->assertSee($versions[0]->attachment);
    }

    /**
     * @test
     */
    public function it_stores_the_proposal_versions()
    {
        $proposal = Proposal::factory()->create();
        $data = Version::factory()
            ->make([
                'proposal_id' => $proposal->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.proposals.versions.store', $proposal),
            $data
        );

        $this->assertDatabaseHas('versions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $version = Version::latest('id')->first();

        $this->assertEquals($proposal->id, $version->proposal_id);
    }
}
