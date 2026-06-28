<?php

namespace Tests\Feature\Controllers;

use App\Models\Developer;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Version;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VersionControllerTest extends TestCase
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
    public function it_displays_index_view_with_versions()
    {
        $versions = Version::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('versions.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.versions.index')
            ->assertViewHas('versions');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_version()
    {
        $response = $this->get(route('versions.create'));

        $response->assertOk()->assertViewIs('app.versions.create');
    }

    /**
     * @test
     */
    public function it_stores_the_version()
    {
        $data = Version::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('versions.store'), $data);

        $this->assertDatabaseHas('versions', $data);

        $version = Version::latest('id')->first();

        $response->assertRedirect(route('versions.edit', $version));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_version()
    {
        $version = Version::factory()->create();

        $response = $this->get(route('versions.show', $version));

        $response
            ->assertOk()
            ->assertViewIs('app.versions.show')
            ->assertViewHas('version');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_version()
    {
        $version = Version::factory()->create();

        $response = $this->get(route('versions.edit', $version));

        $response
            ->assertOk()
            ->assertViewIs('app.versions.edit')
            ->assertViewHas('version');
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

        $response = $this->put(route('versions.update', $version), $data);

        $data['id'] = $version->id;

        $this->assertDatabaseHas('versions', $data);

        $response->assertRedirect(route('versions.edit', $version));
    }

    /**
     * @test
     */
    public function it_deletes_the_version()
    {
        $version = Version::factory()->create();

        $response = $this->delete(route('versions.destroy', $version));

        $response->assertRedirect(route('versions.index'));

        $this->assertModelMissing($version);
    }

    /**
     * @test
     */
    public function it_syncs_developer_cost_overrides_per_version()
    {
        $version = Version::factory()->create();
        $first = Developer::factory()->create();
        $second = Developer::factory()->create();

        $response = $this->putJson(
            route('versions.developer-costs.update', $version),
            [
                'overrides' => [
                    ['developer_id' => $first->id, 'cost_per_hour' => 50],
                    ['developer_id' => $second->id, 'cost_per_hour' => 75.5],
                ],
            ]
        );

        $response
            ->assertOk()
            ->assertJsonPath('action', 'updated');

        $this->assertDatabaseHas('developer_version', [
            'version_id' => $version->id,
            'developer_id' => $first->id,
            'cost_per_hour' => 50,
        ]);

        $this->assertDatabaseHas('developer_version', [
            'version_id' => $version->id,
            'developer_id' => $second->id,
            'cost_per_hour' => 75.5,
        ]);
    }

    /**
     * @test
     */
    public function it_detaches_developer_overrides_omitted_from_the_payload()
    {
        $version = Version::factory()->create();
        $kept = Developer::factory()->create();
        $dropped = Developer::factory()->create();

        $version->developers()->attach($kept->id, ['cost_per_hour' => 30]);
        $version->developers()->attach($dropped->id, ['cost_per_hour' => 40]);

        $this->putJson(
            route('versions.developer-costs.update', $version),
            [
                'overrides' => [
                    ['developer_id' => $kept->id, 'cost_per_hour' => 99],
                ],
            ]
        )->assertOk();

        $this->assertDatabaseHas('developer_version', [
            'version_id' => $version->id,
            'developer_id' => $kept->id,
            'cost_per_hour' => 99,
        ]);

        $this->assertDatabaseMissing('developer_version', [
            'version_id' => $version->id,
            'developer_id' => $dropped->id,
        ]);
    }
}
