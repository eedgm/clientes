<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Icon;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IconControllerTest extends TestCase
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
    public function it_displays_index_view_with_icons()
    {
        $icons = Icon::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('icons.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.icons.index')
            ->assertViewHas('icons');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_icon()
    {
        $response = $this->get(route('icons.create'));

        $response->assertOk()->assertViewIs('app.icons.create');
    }

    /**
     * @test
     */
    public function it_stores_the_icon()
    {
        $data = Icon::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('icons.store'), $data);

        $this->assertDatabaseHas('icons', $data);

        $icon = Icon::latest('id')->first();

        $response->assertRedirect(route('icons.edit', $icon));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_icon()
    {
        $icon = Icon::factory()->create();

        $response = $this->get(route('icons.show', $icon));

        $response
            ->assertOk()
            ->assertViewIs('app.icons.show')
            ->assertViewHas('icon');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_icon()
    {
        $icon = Icon::factory()->create();

        $response = $this->get(route('icons.edit', $icon));

        $response
            ->assertOk()
            ->assertViewIs('app.icons.edit')
            ->assertViewHas('icon');
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

        $response = $this->put(route('icons.update', $icon), $data);

        $data['id'] = $icon->id;

        $this->assertDatabaseHas('icons', $data);

        $response->assertRedirect(route('icons.edit', $icon));
    }

    /**
     * @test
     */
    public function it_deletes_the_icon()
    {
        $icon = Icon::factory()->create();

        $response = $this->delete(route('icons.destroy', $icon));

        $response->assertRedirect(route('icons.index'));

        $this->assertModelMissing($icon);
    }
}
