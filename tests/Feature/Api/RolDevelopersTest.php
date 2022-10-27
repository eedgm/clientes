<?php

namespace Tests\Feature\Api;

use App\Models\Rol;
use App\Models\User;
use App\Models\Developer;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolDevelopersTest extends TestCase
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
    public function it_gets_rol_developers()
    {
        $rol = Rol::factory()->create();
        $developers = Developer::factory()
            ->count(2)
            ->create([
                'rol_id' => $rol->id,
            ]);

        $response = $this->getJson(route('api.rols.developers.index', $rol));

        $response->assertOk()->assertSee($developers[0]->id);
    }

    /**
     * @test
     */
    public function it_stores_the_rol_developers()
    {
        $rol = Rol::factory()->create();
        $data = Developer::factory()
            ->make([
                'rol_id' => $rol->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.rols.developers.store', $rol),
            $data
        );

        $this->assertDatabaseHas('developers', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $developer = Developer::latest('id')->first();

        $this->assertEquals($rol->id, $developer->rol_id);
    }
}
