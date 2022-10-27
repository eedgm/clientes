<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Attachment;

use App\Models\Ticket;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachmentControllerTest extends TestCase
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
    public function it_displays_index_view_with_attachments()
    {
        $attachments = Attachment::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('attachments.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.attachments.index')
            ->assertViewHas('attachments');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_attachment()
    {
        $response = $this->get(route('attachments.create'));

        $response->assertOk()->assertViewIs('app.attachments.create');
    }

    /**
     * @test
     */
    public function it_stores_the_attachment()
    {
        $data = Attachment::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('attachments.store'), $data);

        $this->assertDatabaseHas('attachments', $data);

        $attachment = Attachment::latest('id')->first();

        $response->assertRedirect(route('attachments.edit', $attachment));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_attachment()
    {
        $attachment = Attachment::factory()->create();

        $response = $this->get(route('attachments.show', $attachment));

        $response
            ->assertOk()
            ->assertViewIs('app.attachments.show')
            ->assertViewHas('attachment');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_attachment()
    {
        $attachment = Attachment::factory()->create();

        $response = $this->get(route('attachments.edit', $attachment));

        $response
            ->assertOk()
            ->assertViewIs('app.attachments.edit')
            ->assertViewHas('attachment');
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

        $response = $this->put(route('attachments.update', $attachment), $data);

        $data['id'] = $attachment->id;

        $this->assertDatabaseHas('attachments', $data);

        $response->assertRedirect(route('attachments.edit', $attachment));
    }

    /**
     * @test
     */
    public function it_deletes_the_attachment()
    {
        $attachment = Attachment::factory()->create();

        $response = $this->delete(route('attachments.destroy', $attachment));

        $response->assertRedirect(route('attachments.index'));

        $this->assertModelMissing($attachment);
    }
}
