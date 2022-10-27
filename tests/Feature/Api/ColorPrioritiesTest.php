<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Color;
use App\Models\Priority;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ColorPrioritiesTest extends TestCase
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
    public function it_gets_color_priorities()
    {
        $color = Color::factory()->create();
        $priorities = Priority::factory()
            ->count(2)
            ->create([
                'color_id' => $color->id,
            ]);

        $response = $this->getJson(
            route('api.colors.priorities.index', $color)
        );

        $response->assertOk()->assertSee($priorities[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_color_priorities()
    {
        $color = Color::factory()->create();
        $data = Priority::factory()
            ->make([
                'color_id' => $color->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.colors.priorities.store', $color),
            $data
        );

        $this->assertDatabaseHas('priorities', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $priority = Priority::latest('id')->first();

        $this->assertEquals($color->id, $priority->color_id);
    }
}
