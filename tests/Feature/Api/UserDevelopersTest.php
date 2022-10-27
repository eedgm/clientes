<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Developer;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserDevelopersTest extends TestCase
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
    public function it_gets_user_developers()
    {
        $user = User::factory()->create();
        $developers = Developer::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->getJson(route('api.users.developers.index', $user));

        $response->assertOk()->assertSee($developers[0]->id);
    }

    /**
     * @test
     */
    public function it_stores_the_user_developers()
    {
        $user = User::factory()->create();
        $data = Developer::factory()
            ->make([
                'user_id' => $user->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.users.developers.store', $user),
            $data
        );

        $this->assertDatabaseHas('developers', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $developer = Developer::latest('id')->first();

        $this->assertEquals($user->id, $developer->user_id);
    }
}
