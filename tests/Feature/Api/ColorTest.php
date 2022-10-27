<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Color;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ColorTest extends TestCase
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
    public function it_gets_colors_list()
    {
        $colors = Color::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.colors.index'));

        $response->assertOk()->assertSee($colors[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_color()
    {
        $data = Color::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.colors.store'), $data);

        $this->assertDatabaseHas('colors', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_color()
    {
        $color = Color::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'code' => $this->faker->text(255),
        ];

        $response = $this->putJson(route('api.colors.update', $color), $data);

        $data['id'] = $color->id;

        $this->assertDatabaseHas('colors', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_color()
    {
        $color = Color::factory()->create();

        $response = $this->deleteJson(route('api.colors.destroy', $color));

        $this->assertModelMissing($color);

        $response->assertNoContent();
    }
}
