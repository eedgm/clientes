<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Priority;

use App\Models\Color;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PriorityControllerTest extends TestCase
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
    public function it_displays_index_view_with_priorities()
    {
        $priorities = Priority::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('priorities.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.priorities.index')
            ->assertViewHas('priorities');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_priority()
    {
        $response = $this->get(route('priorities.create'));

        $response->assertOk()->assertViewIs('app.priorities.create');
    }

    /**
     * @test
     */
    public function it_stores_the_priority()
    {
        $data = Priority::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('priorities.store'), $data);

        $this->assertDatabaseHas('priorities', $data);

        $priority = Priority::latest('id')->first();

        $response->assertRedirect(route('priorities.edit', $priority));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_priority()
    {
        $priority = Priority::factory()->create();

        $response = $this->get(route('priorities.show', $priority));

        $response
            ->assertOk()
            ->assertViewIs('app.priorities.show')
            ->assertViewHas('priority');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_priority()
    {
        $priority = Priority::factory()->create();

        $response = $this->get(route('priorities.edit', $priority));

        $response
            ->assertOk()
            ->assertViewIs('app.priorities.edit')
            ->assertViewHas('priority');
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

        $response = $this->put(route('priorities.update', $priority), $data);

        $data['id'] = $priority->id;

        $this->assertDatabaseHas('priorities', $data);

        $response->assertRedirect(route('priorities.edit', $priority));
    }

    /**
     * @test
     */
    public function it_deletes_the_priority()
    {
        $priority = Priority::factory()->create();

        $response = $this->delete(route('priorities.destroy', $priority));

        $response->assertRedirect(route('priorities.index'));

        $this->assertModelMissing($priority);
    }
}
