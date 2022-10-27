<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Attachment;

use App\Models\Ticket;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachmentTest extends TestCase
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
    public function it_gets_attachments_list()
    {
        $attachments = Attachment::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.attachments.index'));

        $response->assertOk()->assertSee($attachments[0]->attachment);
    }

    /**
     * @test
     */
    public function it_stores_the_attachment()
    {
        $data = Attachment::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.attachments.store'), $data);

        $this->assertDatabaseHas('attachments', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_attachment()
    {
        $attachment = Attachment::factory()->create();

        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $data = [
            'attachment' => $this->faker->text(255),
            'description' => $this->faker->text,
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
        ];

        $response = $this->putJson(
            route('api.attachments.update', $attachment),
            $data
        );

        $data['id'] = $attachment->id;

        $this->assertDatabaseHas('attachments', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_attachment()
    {
        $attachment = Attachment::factory()->create();

        $response = $this->deleteJson(
            route('api.attachments.destroy', $attachment)
        );

        $this->assertModelMissing($attachment);

        $response->assertNoContent();
    }
}
