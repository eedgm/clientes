<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Color;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ColorControllerTest extends TestCase
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
    public function it_displays_index_view_with_colors()
    {
        $colors = Color::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('colors.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.colors.index')
            ->assertViewHas('colors');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_color()
    {
        $response = $this->get(route('colors.create'));

        $response->assertOk()->assertViewIs('app.colors.create');
    }

    /**
     * @test
     */
    public function it_stores_the_color()
    {
        $data = Color::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('colors.store'), $data);

        $this->assertDatabaseHas('colors', $data);

        $color = Color::latest('id')->first();

        $response->assertRedirect(route('colors.edit', $color));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_color()
    {
        $color = Color::factory()->create();

        $response = $this->get(route('colors.show', $color));

        $response
            ->assertOk()
            ->assertViewIs('app.colors.show')
            ->assertViewHas('color');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_color()
    {
        $color = Color::factory()->create();

        $response = $this->get(route('colors.edit', $color));

        $response
            ->assertOk()
            ->assertViewIs('app.colors.edit')
            ->assertViewHas('color');
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

        $response = $this->put(route('colors.update', $color), $data);

        $data['id'] = $color->id;

        $this->assertDatabaseHas('colors', $data);

        $response->assertRedirect(route('colors.edit', $color));
    }

    /**
     * @test
     */
    public function it_deletes_the_color()
    {
        $color = Color::factory()->create();

        $response = $this->delete(route('colors.destroy', $color));

        $response->assertRedirect(route('colors.index'));

        $this->assertModelMissing($color);
    }
}
