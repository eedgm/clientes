<?php

use App\Http\Livewire\KanbanTasks;
use App\Http\Livewire\PayablesClone;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IconController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\StatuController;
use App\Http\Controllers\AttachController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\VersionController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(route('login'));
});

Route::middleware(['auth:sanctum', 'verified'])
    ->get('/', [ClientController::class, 'dashboard'])
    ->name('dashboard');

Route::prefix('/')
    ->middleware(['auth:sanctum', 'verified'])
    ->group(function () {
        Route::get('/client/receipt/pdf/{receipt}', [ReceiptController::class, 'createPDF'])->name('client.receipt');

        Route::get('proposals/gantt/{proposal}', [ProposalController::class, 'gantt'])->name('gantt');
        Route::get('proposals/board', [ProposalController::class, 'board'])->name('board');
        Route::delete('proposals/destroy-dashboard/{proposal}', [ProposalController::class, 'destroyDashboard'])->name('destroy-dashboard');
        Route::get('/proposal/tasks/{proposal}', [TaskController::class, 'getTasks']);
        Route::post('/tasks/task', [TaskController::class, 'addGanttTask']);
        Route::put('/tasks/task/update/{task}', [TaskController::class, 'updateGanttTask']);
        Route::put('/tasks/task/delete/{task}', [TaskController::class, 'destroyGanttTask']);
        Route::get('proposal/kanban/{proposal}', KanbanTasks::class)->name('proposal.kanban');
        Route::get('payables/clone', PayablesClone::class)->name('payables.clone');

        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);

        Route::resource('attaches', AttachController::class);
        Route::resource('attachments', AttachmentController::class);
        Route::resource('clients', ClientController::class);
        Route::resource('colors', ColorController::class);
        Route::resource('developers', DeveloperController::class);
        Route::resource('icons', IconController::class);
        Route::resource('payables', PayableController::class);
        Route::resource('people', PersonController::class);
        Route::resource('priorities', PriorityController::class);
        Route::resource('products', ProductController::class);
        Route::resource('proposals', ProposalController::class);
        Route::resource('receipts', ReceiptController::class);
        Route::resource('rols', RolController::class);
        Route::resource('skills', SkillController::class);
        Route::resource('status', StatuController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('tasks', TaskController::class);
        Route::resource('tickets', TicketController::class);
        Route::resource('users', UserController::class);
        Route::resource('versions', VersionController::class);
    });
