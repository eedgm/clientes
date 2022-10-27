<?php

namespace Tests\Feature\Controllers;

use App\Models\Rol;
use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolControllerTest extends TestCase
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
    public function it_displays_index_view_with_rols()
    {
        $rols = Rol::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('rols.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.rols.index')
            ->assertViewHas('rols');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_rol()
    {
        $response = $this->get(route('rols.create'));

        $response->assertOk()->assertViewIs('app.rols.create');
    }

    /**
     * @test
     */
    public function it_stores_the_rol()
    {
        $data = Rol::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('rols.store'), $data);

        $this->assertDatabaseHas('rols', $data);

        $rol = Rol::latest('id')->first();

        $response->assertRedirect(route('rols.edit', $rol));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_rol()
    {
        $rol = Rol::factory()->create();

        $response = $this->get(route('rols.show', $rol));

        $response
            ->assertOk()
            ->assertViewIs('app.rols.show')
            ->assertViewHas('rol');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_rol()
    {
        $rol = Rol::factory()->create();

        $response = $this->get(route('rols.edit', $rol));

        $response
            ->assertOk()
            ->assertViewIs('app.rols.edit')
            ->assertViewHas('rol');
    }

    /**
     * @test
     */
    public function it_updates_the_rol()
    {
        $rol = Rol::factory()->create();

        $data = [
            'name' => $this->faker->name,
        ];

        $response = $this->put(route('rols.update', $rol), $data);

        $data['id'] = $rol->id;

        $this->assertDatabaseHas('rols', $data);

        $response->assertRedirect(route('rols.edit', $rol));
    }

    /**
     * @test
     */
    public function it_deletes_the_rol()
    {
        $rol = Rol::factory()->create();

        $response = $this->delete(route('rols.destroy', $rol));

        $response->assertRedirect(route('rols.index'));

        $this->assertModelMissing($rol);
    }
}
