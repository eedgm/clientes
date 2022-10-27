<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Attach;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAttachesTest extends TestCase
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
    public function it_gets_user_attaches()
    {
        $user = User::factory()->create();
        $attaches = Attach::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->getJson(route('api.users.attaches.index', $user));

        $response->assertOk()->assertSee($attaches[0]->attachment);
    }

    /**
     * @test
     */
    public function it_stores_the_user_attaches()
    {
        $user = User::factory()->create();
        $data = Attach::factory()
            ->make([
                'user_id' => $user->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.users.attaches.store', $user),
            $data
        );

        $this->assertDatabaseHas('attaches', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $attach = Attach::latest('id')->first();

        $this->assertEquals($user->id, $attach->user_id);
    }
}
