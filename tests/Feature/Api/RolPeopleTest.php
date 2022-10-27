<?php

namespace Tests\Feature\Api;

use App\Models\Rol;
use App\Models\User;
use App\Models\Person;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolPeopleTest extends TestCase
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
    public function it_gets_rol_people()
    {
        $rol = Rol::factory()->create();
        $people = Person::factory()
            ->count(2)
            ->create([
                'rol_id' => $rol->id,
            ]);

        $response = $this->getJson(route('api.rols.people.index', $rol));

        $response->assertOk()->assertSee($people[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_rol_people()
    {
        $rol = Rol::factory()->create();
        $data = Person::factory()
            ->make([
                'rol_id' => $rol->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.rols.people.store', $rol),
            $data
        );

        $this->assertDatabaseHas('people', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $person = Person::latest('id')->first();

        $this->assertEquals($rol->id, $person->rol_id);
    }
}
