<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Statu;

use App\Models\Icon;
use App\Models\Color;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StatuControllerTest extends TestCase
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
    public function it_displays_index_view_with_status()
    {
        $status = Statu::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('status.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.status.index')
            ->assertViewHas('status');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_statu()
    {
        $response = $this->get(route('status.create'));

        $response->assertOk()->assertViewIs('app.status.create');
    }

    /**
     * @test
     */
    public function it_stores_the_statu()
    {
        $data = Statu::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('status.store'), $data);

        $this->assertDatabaseHas('status', $data);

        $statu = Statu::latest('id')->first();

        $response->assertRedirect(route('status.edit', $statu));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_statu()
    {
        $statu = Statu::factory()->create();

        $response = $this->get(route('status.show', $statu));

        $response
            ->assertOk()
            ->assertViewIs('app.status.show')
            ->assertViewHas('statu');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_statu()
    {
        $statu = Statu::factory()->create();

        $response = $this->get(route('status.edit', $statu));

        $response
            ->assertOk()
            ->assertViewIs('app.status.edit')
            ->assertViewHas('statu');
    }

    /**
     * @test
     */
    public function it_updates_the_statu()
    {
        $statu = Statu::factory()->create();

        $color = Color::factory()->create();
        $icon = Icon::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'limit' => $this->faker->randomNumber(0),
            'color_id' => $color->id,
            'icon_id' => $icon->id,
        ];

        $response = $this->put(route('status.update', $statu), $data);

        $data['id'] = $statu->id;

        $this->assertDatabaseHas('status', $data);

        $response->assertRedirect(route('status.edit', $statu));
    }

    /**
     * @test
     */
    public function it_deletes_the_statu()
    {
        $statu = Statu::factory()->create();

        $response = $this->delete(route('status.destroy', $statu));

        $response->assertRedirect(route('status.index'));

        $this->assertSoftDeleted($statu);
    }
}
