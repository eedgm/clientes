<?php

namespace Tests\Feature\Controllers;

use App\Models\Developer;
use App\Models\Priority;
use App\Models\Proposal;
use App\Models\Statu;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GanttBulkTaskTest extends TestCase
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

    /** @test */
    public function it_previews_valid_bulk_payload()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'alta']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);
        $developer = Developer::factory()->create();
        $developer->user->update(['name' => 'Juan Pérez']);

        $payload = [
            'tasks' => [
                [
                    'text' => 'Database schema design',
                    'hours' => 16,
                    'priority' => 'alta',
                    'status' => 'pendiente',
                    'developers' => [
                        ['name' => 'Juan Pérez', 'hours' => 8],
                    ],
                ],
                [
                    'text' => 'API implementation',
                    'hours' => 24,
                    'priority' => 'alta',
                    'status' => 'pendiente',
                    'developers' => [
                        ['name' => 'Juan Pérez', 'hours' => 12],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-preview', $proposal),
            $payload
        );

        $response
            ->assertOk()
            ->assertJsonPath('action', 'preview')
            ->assertJsonPath('total_tasks', 2)
            ->assertJsonPath('has_issues', false)
            ->assertJsonCount(0, 'issues')
            ->assertJsonCount(2, 'preview');

        $this->assertTrue($response->json('preview.0.priority.resolved'));
        $this->assertTrue($response->json('preview.0.status.resolved'));
        $this->assertTrue($response->json('preview.0.developers.0.resolved'));
        $this->assertSame(8.0, (float) $response->json('preview.0.developers.0.hours'));

        // effective_hours should be sum of developer hours when present
        $this->assertSame(8.0, (float) $response->json('preview.0.effective_hours'));
        $this->assertSame(12.0, (float) $response->json('preview.1.effective_hours'));
    }

    /** @test */
    public function it_reports_issues_in_preview_for_unknown_references()
    {
        $proposal = Proposal::factory()->create();
        Priority::factory()->create(['name' => 'media']);
        Statu::factory()->create(['name' => 'pendiente']);

        $payload = [
            'tasks' => [
                [
                    'text' => 'Task with bad refs',
                    'hours' => 8,
                    'priority' => 'nonexistent_priority',
                    'status' => 'pendiente',
                    'developers' => [
                        ['name' => 'Unknown Developer'],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-preview', $proposal),
            $payload
        );

        $response
            ->assertOk()
            ->assertJsonPath('has_issues', true);

        $issues = $response->json('issues');
        $this->assertCount(2, $issues);

        $priorityFound = false;
        $devFound = false;

        foreach ($issues as $issue) {
            if (str_contains($issue, 'nonexistent_priority')) {
                $priorityFound = true;
            }
            if (str_contains($issue, 'Unknown Developer')) {
                $devFound = true;
            }
        }

        $this->assertTrue($priorityFound, 'Missing priority issue in preview');
        $this->assertTrue($devFound, 'Missing developer issue in preview');

        // Verify the preview shows unresolved status
        $this->assertFalse($response->json('preview.0.priority.resolved'));
        $this->assertFalse($response->json('preview.0.developers.0.resolved'));

        // effective_hours should fall back to raw hours when no dev hours
        $this->assertSame(8.0, (float) $response->json('preview.0.effective_hours'));
    }

    /** @test */
    public function it_stores_tasks_from_valid_bulk_payload()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'alta']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);
        $developer = Developer::factory()->create();
        $developer->user->update(['name' => 'Juan Pérez']);

        $payload = [
            'tasks' => [
                [
                    'text' => 'Database schema design',
                    'hours' => 16,
                    'priority' => 'alta',
                    'status' => 'pendiente',
                    'developers' => [
                        ['name' => 'Juan Pérez', 'hours' => 8],
                    ],
                ],
                [
                    'text' => 'API implementation',
                    'hours' => 24,
                    'priority' => 'alta',
                    'status' => 'pendiente',
                    'developers' => [
                        ['name' => 'Juan Pérez', 'hours' => 12],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            $payload
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath('action', 'bulk_stored')
            ->assertJsonPath('count', 2);

        // Verify response payload shape: tasks array with serialized fields
        $response->assertJsonStructure([
            'action',
            'count',
            'tasks' => [
                '*' => ['id', 'text', 'start_date', 'duration', 'sort_order', 'priority_id', 'statu_id', 'proposal_id'],
            ],
        ]);

        $this->assertCount(2, $response->json('tasks'));

        // The syncTaskDeveloperAssignments recalculates task hours from
        // developer pivot hours, so the stored hours reflect the
        // developer assignment sum (8 for the first task, 12 for the second).
        $firstTask = Task::where('text', 'Database schema design')->first();
        $this->assertDatabaseHas('tasks', [
            'id' => $firstTask->id,
            'hours' => 8,
        ]);

        $secondTask = Task::where('text', 'API implementation')->first();
        $this->assertDatabaseHas('tasks', [
            'id' => $secondTask->id,
            'hours' => 12,
        ]);

        // Verify developer assignments
        $this->assertDatabaseHas('developer_task', [
            'task_id' => $firstTask->id,
            'developer_id' => $developer->id,
            'hours' => 8,
        ]);

        $this->assertDatabaseHas('developer_task', [
            'task_id' => $secondTask->id,
            'developer_id' => $developer->id,
            'hours' => 12,
        ]);
    }

    /** @test */
    public function it_rejects_store_with_unknown_priority()
    {
        $proposal = Proposal::factory()->create();
        Statu::factory()->create(['name' => 'pendiente']);

        $payload = [
            'tasks' => [
                [
                    'text' => 'Bad task',
                    'hours' => 8,
                    'priority' => 'nonexistent',
                    'status' => 'pendiente',
                ],
            ],
        ];

        $this->withExceptionHandling();

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            $payload
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_rejects_store_with_unknown_developer()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        $payload = [
            'tasks' => [
                [
                    'text' => 'Task with unknown dev',
                    'hours' => 8,
                    'priority' => 'media',
                    'status' => 'pendiente',
                    'developers' => [
                        ['name' => 'Nobody', 'hours' => 4],
                    ],
                ],
            ],
        ];

        $this->withExceptionHandling();

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            $payload
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_rejects_invalid_json_structure()
    {
        $proposal = Proposal::factory()->create();

        $this->withExceptionHandling();

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            ['tasks' => 'not-an-array']
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_rejects_empty_tasks_array()
    {
        $proposal = Proposal::factory()->create();

        $this->withExceptionHandling();

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            ['tasks' => []]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_resolves_developer_by_email_first()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        // Two developers with same name but different emails
        $user1 = User::factory()->create(['name' => 'Carlos Lopez', 'email' => 'carlos@example.com']);
        $dev1 = Developer::factory()->create(['user_id' => $user1->id]);

        $user2 = User::factory()->create(['name' => 'Carlos Lopez', 'email' => 'carlos.otro@example.com']);
        $dev2 = Developer::factory()->create(['user_id' => $user2->id]);

        // Should resolve by email to dev1
        $payload = [
            'tasks' => [
                [
                    'text' => 'Task by email',
                    'hours' => 8,
                    'priority' => 'media',
                    'status' => 'pendiente',
                    'developers' => [
                        ['name' => 'Carlos Lopez', 'email' => 'carlos@example.com', 'hours' => 8],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            $payload
        );

        $response->assertStatus(201);

        $task = Task::where('text', 'Task by email')->first();
        $this->assertDatabaseHas('developer_task', [
            'task_id' => $task->id,
            'developer_id' => $dev1->id,
        ]);

        $this->assertDatabaseMissing('developer_task', [
            'task_id' => $task->id,
            'developer_id' => $dev2->id,
        ]);
    }

    /** @test */
    public function it_creates_tasks_without_developer_assignments()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'baja']);
        $statu = Statu::factory()->create(['name' => 'completado']);

        $payload = [
            'tasks' => [
                [
                    'text' => 'No developers task',
                    'hours' => 4,
                    'priority' => 'baja',
                    'status' => 'completado',
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            $payload
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath('count', 1);

        $this->assertDatabaseHas('tasks', [
            'proposal_id' => $proposal->id,
            'text' => 'No developers task',
            'hours' => 4,
        ]);
    }

    /** @test */
    public function it_uses_case_insensitive_matching_for_priority_and_status()
    {
        $proposal = Proposal::factory()->create();
        Priority::factory()->create(['name' => 'Media']);
        Statu::factory()->create(['name' => 'Pendiente']);

        $payload = [
            'tasks' => [
                [
                    'text' => 'Case insensitive test',
                    'hours' => 8,
                    'priority' => 'media',
                    'status' => 'pendiente',
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            $payload
        );

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', [
            'proposal_id' => $proposal->id,
            'text' => 'Case insensitive test',
        ]);
    }

    /** @test */
    public function it_rejects_ambiguous_developer_name_without_email()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        // Two developers sharing the same user name
        $sharedName = 'Carlos Lopez';
        $user1 = User::factory()->create(['name' => $sharedName, 'email' => 'carlos@example.com']);
        Developer::factory()->create(['user_id' => $user1->id]);

        $user2 = User::factory()->create(['name' => $sharedName, 'email' => 'carlos.otro@example.com']);
        Developer::factory()->create(['user_id' => $user2->id]);

        $this->withExceptionHandling();

        // Name-only resolution should fail as ambiguous
        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            [
                'tasks' => [
                    [
                        'text' => 'Ambiguous dev',
                        'hours' => 8,
                        'priority' => 'media',
                        'status' => 'pendiente',
                        'developers' => [
                            ['name' => $sharedName, 'hours' => 4],
                        ],
                    ],
                ],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('tasks.0.developers.0.name');
    }

    /** @test */
    public function it_reports_ambiguous_developer_in_preview()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        $sharedName = 'Carlos Lopez';
        $user1 = User::factory()->create(['name' => $sharedName, 'email' => 'carlos@example.com']);
        Developer::factory()->create(['user_id' => $user1->id]);

        $user2 = User::factory()->create(['name' => $sharedName, 'email' => 'carlos.otro@example.com']);
        Developer::factory()->create(['user_id' => $user2->id]);

        $response = $this->postJson(
            route('tasks.gantt.bulk-preview', $proposal),
            [
                'tasks' => [
                    [
                        'text' => 'Ambiguous dev',
                        'hours' => 8,
                        'priority' => 'media',
                        'status' => 'pendiente',
                        'developers' => [
                            ['name' => $sharedName, 'hours' => 4],
                        ],
                    ],
                ],
            ]
        );

        $response->assertOk();
        $this->assertTrue($response->json('has_issues'));
        $this->assertFalse($response->json('preview.0.developers.0.resolved'));
        $this->assertTrue($response->json('preview.0.developers.0.ambiguous'));

        $issueText = $issues = $response->json('issues');
        $this->assertNotEmpty(array_filter($issues, fn ($i) => str_contains($i, 'ambiguous')));
    }

    /** @test */
    public function it_rejects_unauthenticated_bulk_store()
    {
        $proposal = Proposal::factory()->create();

        // Log out the current user
        $this->withExceptionHandling();
        Auth::logout();
        $this->app['auth']->forgetGuards();

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            ['tasks' => []]
        );

        $response->assertStatus(401);
    }

    /** @test */
    public function it_rejects_unauthorized_bulk_store()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        // Use a user without permissions
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->withExceptionHandling();

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            [
                'tasks' => [
                    [
                        'text' => 'Not allowed',
                        'hours' => 8,
                        'priority' => 'media',
                        'status' => 'pendiente',
                    ],
                ],
            ]
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function preview_effective_hours_matches_stored_hours()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'alta']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);
        $developer = Developer::factory()->create();
        $developer->user->update(['name' => 'Ana Gómez']);

        $payload = [
            'tasks' => [
                [
                    'text' => 'Design API',
                    'hours' => 16,
                    'priority' => 'alta',
                    'status' => 'pendiente',
                    'developers' => [
                        ['name' => 'Ana Gómez', 'hours' => 10],
                    ],
                ],
                [
                    'text' => 'Implement tests',
                    'hours' => 12,
                    'priority' => 'alta',
                    'status' => 'pendiente',
                ],
            ],
        ];

        // Preview: effective_hours should be developer sum (10) for task 0,
        // and raw hours (12) for task 1 (no developer hours)
        $previewResponse = $this->postJson(
            route('tasks.gantt.bulk-preview', $proposal),
            $payload
        );

        $previewResponse->assertOk();
        $this->assertSame(10.0, (float) $previewResponse->json('preview.0.effective_hours'));
        $this->assertSame(12.0, (float) $previewResponse->json('preview.1.effective_hours'));

        // Store: the persisted hours should match effective_hours from preview
        $storeResponse = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            $payload
        );

        $storeResponse->assertStatus(201);

        $task0 = Task::where('text', 'Design API')->first();
        $task1 = Task::where('text', 'Implement tests')->first();

        $this->assertSame(10.0, (float) $task0->hours);
        $this->assertSame(12.0, (float) $task1->hours);
    }

    /** @test */
    public function it_previews_email_only_developer()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'alta']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);
        $user = User::factory()->create(['name' => 'María Gómez', 'email' => 'maria@example.com']);
        $developer = Developer::factory()->create(['user_id' => $user->id]);

        $payload = [
            'tasks' => [
                [
                    'text' => 'Email-only dev task',
                    'hours' => 12,
                    'priority' => 'alta',
                    'status' => 'pendiente',
                    'developers' => [
                        ['email' => 'maria@example.com', 'hours' => 6],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-preview', $proposal),
            $payload
        );

        $response
            ->assertOk()
            ->assertJsonPath('has_issues', false)
            ->assertJsonPath('total_tasks', 1);

        $dev = $response->json('preview.0.developers.0');
        $this->assertTrue($dev['resolved'], 'Email-only developer should be resolved');
        $this->assertSame($developer->id, $dev['developer_id'], 'Should resolve to correct developer ID');
        $this->assertSame('María Gómez', $dev['name'], 'Name in preview should be the resolved user name');
        $this->assertSame('maria@example.com', $dev['email']);
        $this->assertSame(6.0, (float) $dev['hours']);

        // effective_hours should come from developer hours
        $this->assertSame(6.0, (float) $response->json('preview.0.effective_hours'));
    }

    /** @test */
    public function it_stores_email_only_developer()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'alta']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);
        $user = User::factory()->create(['name' => 'María Gómez', 'email' => 'maria@example.com']);
        $developer = Developer::factory()->create(['user_id' => $user->id]);

        $payload = [
            'tasks' => [
                [
                    'text' => 'Email-only store task',
                    'hours' => 12,
                    'priority' => 'alta',
                    'status' => 'pendiente',
                    'developers' => [
                        ['email' => 'maria@example.com', 'hours' => 6],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            $payload
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath('action', 'bulk_stored')
            ->assertJsonPath('count', 1);

        $task = Task::where('text', 'Email-only store task')->first();
        $this->assertNotNull($task);

        $this->assertDatabaseHas('developer_task', [
            'task_id' => $task->id,
            'developer_id' => $developer->id,
            'hours' => 6,
        ]);
    }

    /** @test */
    public function it_rejects_unknown_email_only_in_preview()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        $payload = [
            'tasks' => [
                [
                    'text' => 'Unknown email dev',
                    'hours' => 8,
                    'priority' => 'media',
                    'status' => 'pendiente',
                    'developers' => [
                        ['email' => 'nonexistent@example.com', 'hours' => 4],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-preview', $proposal),
            $payload
        );

        $response->assertOk();
        $this->assertTrue($response->json('has_issues'));
        $this->assertFalse($response->json('preview.0.developers.0.resolved'));

        $issues = $response->json('issues');
        $emailIssue = array_filter($issues, fn ($i) => str_contains($i, 'nonexistent@example.com'));
        $this->assertNotEmpty($emailIssue, 'Preview should report unknown email as unknown');
    }

    /** @test */
    public function it_rejects_unknown_email_only_in_store()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        $this->withExceptionHandling();

        $payload = [
            'tasks' => [
                [
                    'text' => 'Unknown email store',
                    'hours' => 8,
                    'priority' => 'media',
                    'status' => 'pendiente',
                    'developers' => [
                        ['email' => 'nonexistent@example.com', 'hours' => 4],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            $payload
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_rejects_developer_without_name_or_email()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        $this->withExceptionHandling();

        $payload = [
            'tasks' => [
                [
                    'text' => 'No id dev',
                    'hours' => 8,
                    'priority' => 'media',
                    'status' => 'pendiente',
                    'developers' => [
                        ['hours' => 4],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-preview', $proposal),
            $payload
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_rejects_developer_without_name_or_email_in_store()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        $this->withExceptionHandling();

        $payload = [
            'tasks' => [
                [
                    'text' => 'No id dev store',
                    'hours' => 8,
                    'priority' => 'media',
                    'status' => 'pendiente',
                    'developers' => [
                        ['hours' => 4],
                    ],
                ],
            ],
        ];

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            $payload
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function it_rejects_unauthenticated_bulk_preview()
    {
        $proposal = Proposal::factory()->create();

        $this->withExceptionHandling();
        Auth::logout();
        $this->app['auth']->forgetGuards();

        $response = $this->postJson(
            route('tasks.gantt.bulk-preview', $proposal),
            ['tasks' => [['text' => 'test', 'hours' => 1, 'priority' => 'media', 'status' => 'pendiente']]]
        );

        $response->assertStatus(401);
    }

    /** @test */
    public function it_rejects_unauthorized_bulk_preview()
    {
        $proposal = Proposal::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->withExceptionHandling();

        $response = $this->postJson(
            route('tasks.gantt.bulk-preview', $proposal),
            ['tasks' => [['text' => 'test', 'hours' => 1, 'priority' => 'media', 'status' => 'pendiente']]]
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function validation_failure_returns_field_errors_shape()
    {
        $proposal = Proposal::factory()->create();

        $this->withExceptionHandling();

        // Store with unknown priority — the frontend reads payload.errors
        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            [
                'tasks' => [
                    [
                        'text' => 'Bad task',
                        'hours' => 8,
                        'priority' => 'does_not_exist',
                        'status' => 'pendiente',
                    ],
                ],
            ]
        );

        $response->assertStatus(422);

        // Verify the shape used by the frontend: { errors: { "tasks.0.priority": [...] } }
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'tasks.0.priority',
            ],
        ]);

        $errors = $response->json('errors');
        $this->assertIsArray($errors['tasks.0.priority']);
        $this->assertNotEmpty($errors['tasks.0.priority'][0]);
    }

    /** @test */
    public function it_rejects_wrong_email_even_when_name_matches()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        // A developer exists with this name and email
        $user = User::factory()->create(['name' => 'Pedro Ramirez', 'email' => 'pedro@example.com']);
        Developer::factory()->create(['user_id' => $user->id]);

        // Wrong email but matching name — must be rejected because email
        // is authoritative and does not match any developer.
        $this->withExceptionHandling();

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            [
                'tasks' => [
                    [
                        'text' => 'Wrong email task',
                        'hours' => 8,
                        'priority' => 'media',
                        'status' => 'pendiente',
                        'developers' => [
                            ['name' => 'Pedro Ramirez', 'email' => 'wrong@example.com', 'hours' => 8],
                        ],
                    ],
                ],
            ]
        );

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('tasks.0.developers.0.name');
    }

    /** @test */
    public function it_aborts_all_tasks_when_any_task_reference_is_invalid()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'alta']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);
        $developer = Developer::factory()->create();
        $developer->user->update(['name' => 'Luis García']);

        $this->withExceptionHandling();

        // Two valid tasks and one with an unknown priority — NO task should be created
        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            [
                'tasks' => [
                    [
                        'text' => 'Valid task 1',
                        'hours' => 8,
                        'priority' => 'alta',
                        'status' => 'pendiente',
                        'developers' => [
                            ['name' => 'Luis García', 'hours' => 8],
                        ],
                    ],
                    [
                        'text' => 'Invalid task',
                        'hours' => 8,
                        'priority' => 'nonesiste',
                        'status' => 'pendiente',
                    ],
                    [
                        'text' => 'Valid task 2',
                        'hours' => 16,
                        'priority' => 'alta',
                        'status' => 'pendiente',
                    ],
                ],
            ]
        );

        $response->assertStatus(422);

        // No tasks should have been created at all (atomic all-or-nothing)
        $this->assertDatabaseMissing('tasks', ['text' => 'Valid task 1']);
        $this->assertDatabaseMissing('tasks', ['text' => 'Valid task 2']);
        $this->assertDatabaseMissing('tasks', ['text' => 'Invalid task']);
    }

    /** @test */
    public function it_rolls_back_transaction_on_db_failure_after_writes_begin()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'alta']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        // Override setUp — we need exception handling to catch the
        // server error (the runtime int overflow inside the scheduler
        // is triggered by a value that passes validation but cannot
        // be processed, proving the transaction catches mid-stream
        // failures and rolls back prior writes).
        $this->withExceptionHandling();

        $response = $this->postJson(
            route('tasks.gantt.bulk-store', $proposal),
            [
                'tasks' => [
                    [
                        'text' => 'First valid task',
                        'hours' => 8,
                        'priority' => 'alta',
                        'status' => 'pendiente',
                    ],
                    [
                        'text' => 'Overflow task',
                        'hours' => 99999999999999999999999999999999,
                        'priority' => 'alta',
                        'status' => 'pendiente',
                    ],
                ],
            ]
        );

        // The overflow causes a PHP int overflow inside the scheduler
        // (outside the request validation gates) → server error.
        // The transaction rolls back both the failed INSERT and the
        // earlier successful INSERT on the first task.
        $response->assertStatus(500);

        $this->assertDatabaseMissing('tasks', ['text' => 'First valid task']);
        $this->assertDatabaseMissing('tasks', ['text' => 'Overflow task']);
    }

    /** @test */
    public function preview_rejects_wrong_email_even_when_name_matches()
    {
        $proposal = Proposal::factory()->create();
        $priority = Priority::factory()->create(['name' => 'media']);
        $statu = Statu::factory()->create(['name' => 'pendiente']);

        $user = User::factory()->create(['name' => 'Pedro Ramirez', 'email' => 'pedro@example.com']);
        Developer::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson(
            route('tasks.gantt.bulk-preview', $proposal),
            [
                'tasks' => [
                    [
                        'text' => 'Preview wrong email',
                        'hours' => 8,
                        'priority' => 'media',
                        'status' => 'pendiente',
                        'developers' => [
                            ['name' => 'Pedro Ramirez', 'email' => 'wrong@example.com', 'hours' => 8],
                        ],
                    ],
                ],
            ]
        );

        $response->assertOk();
        $this->assertTrue($response->json('has_issues'));
        $this->assertFalse($response->json('preview.0.developers.0.resolved'));
        // The issue message must reference the email (not the name) since email
        // was provided and is authoritative
        $issues = $response->json('issues');
        $emailIssue = array_filter($issues, fn ($i) => str_contains($i, 'wrong@example.com'));
        $this->assertNotEmpty($emailIssue, 'Preview should report the email as unknown, not fall back to name');
    }
}
