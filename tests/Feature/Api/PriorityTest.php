<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Priority;

use App\Models\Color;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriorityTest extends TestCase
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
    public function it_gets_priorities_list()
    {
        $priorities = Priority::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.priorities.index'));

        $response->assertOk()->assertSee($priorities[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_priority()
    {
        $data = Priority::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.priorities.store'), $data);

        $this->assertDatabaseHas('priorities', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_priority()
    {
        $priority = Priority::factory()->create();

        $color = Color::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'color_id' => $color->id,
        ];

        $response = $this->putJson(
            route('api.priorities.update', $priority),
            $data
        );

        $data['id'] = $priority->id;

        $this->assertDatabaseHas('priorities', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_priority()
    {
        $priority = Priority::factory()->create();

        $response = $this->deleteJson(
            route('api.priorities.destroy', $priority)
        );

        $this->assertModelMissing($priority);

        $response->assertNoContent();
    }
}
