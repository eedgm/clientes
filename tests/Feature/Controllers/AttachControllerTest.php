<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Attach;

use App\Models\Task;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachControllerTest extends TestCase
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
    public function it_displays_index_view_with_attaches()
    {
        $attaches = Attach::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('attaches.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.attaches.index')
            ->assertViewHas('attaches');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_attach()
    {
        $response = $this->get(route('attaches.create'));

        $response->assertOk()->assertViewIs('app.attaches.create');
    }

    /**
     * @test
     */
    public function it_stores_the_attach()
    {
        $data = Attach::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('attaches.store'), $data);

        $this->assertDatabaseHas('attaches', $data);

        $attach = Attach::latest('id')->first();

        $response->assertRedirect(route('attaches.edit', $attach));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_attach()
    {
        $attach = Attach::factory()->create();

        $response = $this->get(route('attaches.show', $attach));

        $response
            ->assertOk()
            ->assertViewIs('app.attaches.show')
            ->assertViewHas('attach');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_attach()
    {
        $attach = Attach::factory()->create();

        $response = $this->get(route('attaches.edit', $attach));

        $response
            ->assertOk()
            ->assertViewIs('app.attaches.edit')
            ->assertViewHas('attach');
    }

    /**
     * @test
     */
    public function it_updates_the_attach()
    {
        $attach = Attach::factory()->create();

        $task = Task::factory()->create();
        $user = User::factory()->create();

        $data = [
            'attachment' => $this->faker->text(255),
            'description' => $this->faker->sentence(15),
            'task_id' => $task->id,
            'user_id' => $user->id,
        ];

        $response = $this->put(route('attaches.update', $attach), $data);

        $data['id'] = $attach->id;

        $this->assertDatabaseHas('attaches', $data);

        $response->assertRedirect(route('attaches.edit', $attach));
    }

    /**
     * @test
     */
    public function it_deletes_the_attach()
    {
        $attach = Attach::factory()->create();

        $response = $this->delete(route('attaches.destroy', $attach));

        $response->assertRedirect(route('attaches.index'));

        $this->assertModelMissing($attach);
    }
}
