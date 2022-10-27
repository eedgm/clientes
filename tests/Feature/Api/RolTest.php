<?php

namespace Tests\Feature\Api;

use App\Models\Rol;
use App\Models\User;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolTest extends TestCase
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
    public function it_gets_rols_list()
    {
        $rols = Rol::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.rols.index'));

        $response->assertOk()->assertSee($rols[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_rol()
    {
        $data = Rol::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.rols.store'), $data);

        $this->assertDatabaseHas('rols', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_rol()
    {
        $rol = Rol::factory()->create();

        $data = [
            'name' => $this->faker->name,
        ];

        $response = $this->putJson(route('api.rols.update', $rol), $data);

        $data['id'] = $rol->id;

        $this->assertDatabaseHas('rols', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_rol()
    {
        $rol = Rol::factory()->create();

        $response = $this->deleteJson(route('api.rols.destroy', $rol));

        $this->assertModelMissing($rol);

        $response->assertNoContent();
    }
}
