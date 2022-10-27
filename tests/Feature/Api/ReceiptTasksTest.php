<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Task;
use App\Models\Receipt;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReceiptTasksTest extends TestCase
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
    public function it_gets_receipt_tasks()
    {
        $receipt = Receipt::factory()->create();
        $tasks = Task::factory()
            ->count(2)
            ->create([
                'receipt_id' => $receipt->id,
            ]);

        $response = $this->getJson(route('api.receipts.tasks.index', $receipt));

        $response->assertOk()->assertSee($tasks[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_receipt_tasks()
    {
        $receipt = Receipt::factory()->create();
        $data = Task::factory()
            ->make([
                'receipt_id' => $receipt->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.receipts.tasks.store', $receipt),
            $data
        );

        $this->assertDatabaseHas('tasks', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $task = Task::latest('id')->first();

        $this->assertEquals($receipt->id, $task->receipt_id);
    }
}
