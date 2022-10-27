<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Person;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPeopleTest extends TestCase
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
    public function it_gets_user_people()
    {
        $user = User::factory()->create();
        $people = Person::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->getJson(route('api.users.people.index', $user));

        $response->assertOk()->assertSee($people[0]->description);
    }

    /**
     * @test
     */
    public function it_stores_the_user_people()
    {
        $user = User::factory()->create();
        $data = Person::factory()
            ->make([
                'user_id' => $user->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.users.people.store', $user),
            $data
        );

        $this->assertDatabaseHas('people', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $person = Person::latest('id')->first();

        $this->assertEquals($user->id, $person->user_id);
    }
}
