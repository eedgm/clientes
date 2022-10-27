<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Developer;

use App\Models\Rol;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeveloperControllerTest extends TestCase
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
    public function it_displays_index_view_with_developers()
    {
        $developers = Developer::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('developers.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.developers.index')
            ->assertViewHas('developers');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_developer()
    {
        $response = $this->get(route('developers.create'));

        $response->assertOk()->assertViewIs('app.developers.create');
    }

    /**
     * @test
     */
    public function it_stores_the_developer()
    {
        $data = Developer::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('developers.store'), $data);

        $this->assertDatabaseHas('developers', $data);

        $developer = Developer::latest('id')->first();

        $response->assertRedirect(route('developers.edit', $developer));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_developer()
    {
        $developer = Developer::factory()->create();

        $response = $this->get(route('developers.show', $developer));

        $response
            ->assertOk()
            ->assertViewIs('app.developers.show')
            ->assertViewHas('developer');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_developer()
    {
        $developer = Developer::factory()->create();

        $response = $this->get(route('developers.edit', $developer));

        $response
            ->assertOk()
            ->assertViewIs('app.developers.edit')
            ->assertViewHas('developer');
    }

    /**
     * @test
     */
    public function it_updates_the_developer()
    {
        $developer = Developer::factory()->create();

        $user = User::factory()->create();
        $rol = Rol::factory()->create();

        $data = [
            'user_id' => $user->id,
            'rol_id' => $rol->id,
        ];

        $response = $this->put(route('developers.update', $developer), $data);

        $data['id'] = $developer->id;

        $this->assertDatabaseHas('developers', $data);

        $response->assertRedirect(route('developers.edit', $developer));
    }

    /**
     * @test
     */
    public function it_deletes_the_developer()
    {
        $developer = Developer::factory()->create();

        $response = $this->delete(route('developers.destroy', $developer));

        $response->assertRedirect(route('developers.index'));

        $this->assertModelMissing($developer);
    }
}
