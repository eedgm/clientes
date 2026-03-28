<?php

namespace Tests\Feature\Api;

use App\Models\Proposal;
use App\Models\User;
use App\Models\Version;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VersionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email' => 'admin@admin.com']);

        Sanctum::actingAs($user, [], 'web');

        $this->seed(PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_versions_list()
    {
        $versions = Version::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.versions.index'));

        $response->assertOk()->assertSee($versions[0]->attachment);
    }

    /**
     * @test
     */
    public function it_stores_the_version()
    {
        $data = Version::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.versions.store'), $data);

        $this->assertDatabaseHas('versions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_version()
    {
        $version = Version::factory()->create();

        $proposal = Proposal::factory()->create();
        $user = User::factory()->create();

        $data = [
            'attachment' => $this->faker->text(255),
            'total' => $this->faker->randomFloat(2, 0, 9999),
            'time' => $this->faker->date,
            'cost_per_hour' => $this->faker->randomNumber(1),
            'hour_per_day' => $this->faker->randomNumber(1),
            'months_to_pay' => $this->faker->randomNumber(1),
            'unexpected' => $this->faker->randomNumber(1),
            'company_gain' => $this->faker->randomNumber(1),
            'seller_commission_percentage' => $this->faker->randomFloat(2, 0, 100),
            'bank_tax' => $this->faker->randomNumber(1),
            'first_payment' => $this->faker->randomNumber(1),
            'proposal_id' => $proposal->id,
            'user_id' => $user->id,
        ];

        $response = $this->putJson(
            route('api.versions.update', $version),
            $data
        );

        $data['id'] = $version->id;

        $this->assertDatabaseHas('versions', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_version()
    {
        $version = Version::factory()->create();

        $response = $this->deleteJson(route('api.versions.destroy', $version));

        $this->assertModelMissing($version);

        $response->assertNoContent();
    }
}
