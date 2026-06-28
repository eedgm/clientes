<?php

namespace Tests\Feature\Controllers;

use App\Models\Developer;
use App\Models\Rol;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeveloperControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->seed(PermissionsSeeder::class);

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

    /**
     * @test
     */
    public function it_searches_developers_by_user_name()
    {
        $matching = Developer::factory()
            ->for(User::factory(['name' => 'Ada Lovelace']))
            ->create();

        Developer::factory()
            ->for(User::factory(['name' => 'Grace Hopper']))
            ->create();

        $response = $this->getJson(route('developers.search', ['q' => 'Ada']));

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $matching->id)
            ->assertJsonPath('data.0.name', 'Ada Lovelace');
    }

    /**
     * @test
     */
    public function it_creates_developer_inline_creating_user_when_email_is_new()
    {
        $rol = Rol::factory()->create();

        $payload = [
            'name' => 'Linus Torvalds',
            'email' => 'linus@example.test',
            'password' => 'secret-pass',
            'rol_id' => $rol->id,
            'cost_per_hour' => 42.5,
        ];

        $response = $this->postJson(route('developers.quick-store'), $payload);

        $response
            ->assertStatus(201)
            ->assertJsonPath('data.name', 'Linus Torvalds')
            ->assertJsonPath('data.email', 'linus@example.test')
            ->assertJsonPath('data.cost_per_hour', 42.5);

        $this->assertDatabaseHas('users', ['email' => 'linus@example.test']);
        $this->assertDatabaseHas('developers', [
            'rol_id' => $rol->id,
            'cost_per_hour' => 42.5,
        ]);
    }

    /**
     * @test
     */
    public function it_creates_developer_inline_reusing_existing_user_for_email()
    {
        $user = User::factory()->create([
            'email' => 'duplicate@example.test',
        ]);
        $rol = Rol::factory()->create();

        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'ignored',
            'rol_id' => $rol->id,
        ];

        $response = $this->postJson(route('developers.quick-store'), $payload);

        $response->assertStatus(201)->assertJsonPath('data.id', fn ($id) => is_int($id));

        $this->assertSame(1, User::where('email', $user->email)->count());

        $this->assertDatabaseHas('developers', [
            'user_id' => $user->id,
            'rol_id' => $rol->id,
        ]);
    }
}
