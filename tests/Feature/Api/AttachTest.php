<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Attach;

use App\Models\Task;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachTest extends TestCase
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
    public function it_gets_attaches_list()
    {
        $attaches = Attach::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.attaches.index'));

        $response->assertOk()->assertSee($attaches[0]->attachment);
    }

    /**
     * @test
     */
    public function it_stores_the_attach()
    {
        $data = Attach::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.attaches.store'), $data);

        $this->assertDatabaseHas('attaches', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
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

        $response = $this->putJson(
            route('api.attaches.update', $attach),
            $data
        );

        $data['id'] = $attach->id;

        $this->assertDatabaseHas('attaches', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_attach()
    {
        $attach = Attach::factory()->create();

        $response = $this->deleteJson(route('api.attaches.destroy', $attach));

        $this->assertModelMissing($attach);

        $response->assertNoContent();
    }
}
