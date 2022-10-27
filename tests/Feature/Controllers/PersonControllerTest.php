<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Person;

use App\Models\Rol;
use App\Models\Client;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PersonControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_people()
    {
        $people = Person::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('people.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.people.index')
            ->assertViewHas('people');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_person()
    {
        $response = $this->get(route('people.create'));

        $response->assertOk()->assertViewIs('app.people.create');
    }

    /**
     * @test
     */
    public function it_stores_the_person()
    {
        $data = Person::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('people.store'), $data);

        $this->assertDatabaseHas('people', $data);

        $person = Person::latest('id')->first();

        $response->assertRedirect(route('people.edit', $person));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_person()
    {
        $person = Person::factory()->create();

        $response = $this->get(route('people.show', $person));

        $response
            ->assertOk()
            ->assertViewIs('app.people.show')
            ->assertViewHas('person');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_person()
    {
        $person = Person::factory()->create();

        $response = $this->get(route('people.edit', $person));

        $response
            ->assertOk()
            ->assertViewIs('app.people.edit')
            ->assertViewHas('person');
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

        $response = $this->put(route('people.update', $person), $data);

        $data['id'] = $person->id;

        $this->assertDatabaseHas('people', $data);

        $response->assertRedirect(route('people.edit', $person));
    }

    /**
     * @test
     */
    public function it_deletes_the_person()
    {
        $person = Person::factory()->create();

        $response = $this->delete(route('people.destroy', $person));

        $response->assertRedirect(route('people.index'));

        $this->assertModelMissing($person);
    }
}
