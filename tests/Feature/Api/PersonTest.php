<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Person;

use App\Models\Rol;
use App\Models\Client;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonTest extends TestCase
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
    public function it_gets_people_list()
    {
        $people = Person::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.people.index'));

        $response->assertOk()->assertSee($people[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_person()
    {
        $data = Person::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.people.store'), $data);

        $this->assertDatabaseHas('people', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_person()
    {
        $person = Person::factory()->create();

        $client = Client::factory()->create();
        $rol = Rol::factory()->create();
        $user = User::factory()->create();

        $data = [
            'description' => $this->faker->sentence(15),
            'phone' => $this->faker->phoneNumber,
            'skype' => $this->faker->text(255),
            'client_id' => $client->id,
            'rol_id' => $rol->id,
            'user_id' => $user->id,
        ];

        $response = $this->putJson(route('api.people.update', $person), $data);

        $data['id'] = $person->id;

        $this->assertDatabaseHas('people', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_person()
    {
        $person = Person::factory()->create();

        $response = $this->deleteJson(route('api.people.destroy', $person));

        $this->assertModelMissing($person);

        $response->assertNoContent();
    }
}
