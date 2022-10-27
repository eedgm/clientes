<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Icon;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IconTest extends TestCase
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
    public function it_gets_icons_list()
    {
        $icons = Icon::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.icons.index'));

        $response->assertOk()->assertSee($icons[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_icon()
    {
        $data = Icon::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.icons.store'), $data);

        $this->assertDatabaseHas('icons', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_icon()
    {
        $icon = Icon::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'icon' => $this->faker->text(255),
        ];

        $response = $this->putJson(route('api.icons.update', $icon), $data);

        $data['id'] = $icon->id;

        $this->assertDatabaseHas('icons', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_icon()
    {
        $icon = Icon::factory()->create();

        $response = $this->deleteJson(route('api.icons.destroy', $icon));

        $this->assertModelMissing($icon);

        $response->assertNoContent();
    }
}
