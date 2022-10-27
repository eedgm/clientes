<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Developer;

use App\Models\Rol;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeveloperTest extends TestCase
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
    public function it_gets_developers_list()
    {
        $developers = Developer::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.developers.index'));

        $response->assertOk()->assertSee($developers[0]->id);
    }

    /**
     * @test
     */
    public function it_stores_the_developer()
    {
        $data = Developer::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.developers.store'), $data);

        $this->assertDatabaseHas('developers', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_developer()
    {
        $developer = Developer::factory()->create();

        $user = User::factory()->create();
        $rol = Rol::factory()->create();

        $data = [
            'user_id' => $user->id,
            'rol_id' => $rol->id,
        ];

        $response = $this->putJson(
            route('api.developers.update', $developer),
            $data
        );

        $data['id'] = $developer->id;

        $this->assertDatabaseHas('developers', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_developer()
    {
        $developer = Developer::factory()->create();

        $response = $this->deleteJson(
            route('api.developers.destroy', $developer)
        );

        $this->assertModelMissing($developer);

        $response->assertNoContent();
    }
}
