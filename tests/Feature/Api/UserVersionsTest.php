<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Version;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserVersionsTest extends TestCase
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
    public function it_gets_user_versions()
    {
        $user = User::factory()->create();
        $versions = Version::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->getJson(route('api.users.versions.index', $user));

        $response->assertOk()->assertSee($versions[0]->attachment);
    }

    /**
     * @test
     */
    public function it_stores_the_user_versions()
    {
        $user = User::factory()->create();
        $data = Version::factory()
            ->make([
                'user_id' => $user->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.users.versions.store', $user),
            $data
        );

        $this->assertDatabaseHas('versions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $version = Version::latest('id')->first();

        $this->assertEquals($user->id, $version->user_id);
    }
}
