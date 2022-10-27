<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\IconController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\StatuController;
use App\Http\Controllers\Api\AttachController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\PersonController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PayableController;
use App\Http\Controllers\Api\VersionController;
use App\Http\Controllers\Api\ReceiptController;
use App\Http\Controllers\Api\ProposalController;
use App\Http\Controllers\Api\PriorityController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\DeveloperController;
use App\Http\Controllers\Api\RolPeopleController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\IconStatusController;
use App\Http\Controllers\Api\StatuTasksController;
use App\Http\Controllers\Api\UserPeopleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ClientUsersController;
use App\Http\Controllers\Api\client_userController;
use App\Http\Controllers\Api\ColorStatusController;
use App\Http\Controllers\Api\UserClientsController;
use App\Http\Controllers\Api\ClientPeopleController;
use App\Http\Controllers\Api\VersionTasksController;
use App\Http\Controllers\Api\TaskAttachesController;
use App\Http\Controllers\Api\ReceiptTasksController;
use App\Http\Controllers\Api\StatuTicketsController;
use App\Http\Controllers\Api\UserVersionsController;
use App\Http\Controllers\Api\UserAttachesController;
use App\Http\Controllers\Api\PersonTicketsController;
use App\Http\Controllers\Api\VersionPeopleController;
use App\Http\Controllers\Api\PriorityTasksController;
use App\Http\Controllers\Api\RolDevelopersController;
use App\Http\Controllers\Api\ClientProductsController;
use App\Http\Controllers\Api\ClientReceiptsController;
use App\Http\Controllers\Api\ProductTicketsController;
use App\Http\Controllers\Api\PersonVersionsController;
use App\Http\Controllers\Api\TaskDevelopersController;
use App\Http\Controllers\Api\ReceiptTicketsController;
use App\Http\Controllers\Api\DeveloperTasksController;
use App\Http\Controllers\Api\developer_taskController;
use App\Http\Controllers\Api\UserDevelopersController;
use App\Http\Controllers\Api\person_versionController;
use App\Http\Controllers\Api\ClientProposalsController;
use App\Http\Controllers\Api\ProductPayablesController;
use App\Http\Controllers\Api\ReceiptPayablesController;
use App\Http\Controllers\Api\ColorPrioritiesController;
use App\Http\Controllers\Api\DeveloperSkillsController;
use App\Http\Controllers\Api\developer_skillController;
use App\Http\Controllers\Api\PriorityTicketsController;
use App\Http\Controllers\Api\SkillDevelopersController;
use App\Http\Controllers\Api\UserAttachmentsController;
use App\Http\Controllers\Api\TicketDevelopersController;
use App\Http\Controllers\Api\developer_ticketController;
use App\Http\Controllers\Api\ProposalVersionsController;
use App\Http\Controllers\Api\DeveloperTicketsController;
use App\Http\Controllers\Api\SupplierPayablesController;
use App\Http\Controllers\Api\TicketAttachmentsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user();
    })
    ->name('api.user');

Route::name('api.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);

        Route::apiResource('attaches', AttachController::class);

        Route::apiResource('attachments', AttachmentController::class);

        Route::apiResource('clients', ClientController::class);

        // Client Products
        Route::get('/clients/{client}/products', [
            ClientProductsController::class,
            'index',
        ])->name('clients.products.index');
        Route::post('/clients/{client}/products', [
            ClientProductsController::class,
            'store',
        ])->name('clients.products.store');

        // Client People
        Route::get('/clients/{client}/people', [
            ClientPeopleController::class,
            'index',
        ])->name('clients.people.index');
        Route::post('/clients/{client}/people', [
            ClientPeopleController::class,
            'store',
        ])->name('clients.people.store');

        // Client Proposals
        Route::get('/clients/{client}/proposals', [
            ClientProposalsController::class,
            'index',
        ])->name('clients.proposals.index');
        Route::post('/clients/{client}/proposals', [
            ClientProposalsController::class,
            'store',
        ])->name('clients.proposals.store');

        // Client Receipts
        Route::get('/clients/{client}/receipts', [
            ClientReceiptsController::class,
            'index',
        ])->name('clients.receipts.index');
        Route::post('/clients/{client}/receipts', [
            ClientReceiptsController::class,
            'store',
        ])->name('clients.receipts.store');

        // Client Users
        Route::get('/clients/{client}/users', [
            ClientUsersController::class,
            'index',
        ])->name('clients.users.index');
        Route::post('/clients/{client}/users/{user}', [
            ClientUsersController::class,
            'store',
        ])->name('clients.users.store');
        Route::delete('/clients/{client}/users/{user}', [
            ClientUsersController::class,
            'destroy',
        ])->name('clients.users.destroy');

        Route::apiResource('colors', ColorController::class);

        // Color Status
        Route::get('/colors/{color}/status', [
            ColorStatusController::class,
            'index',
        ])->name('colors.status.index');
        Route::post('/colors/{color}/status', [
            ColorStatusController::class,
            'store',
        ])->name('colors.status.store');

        // Color Priorities
        Route::get('/colors/{color}/priorities', [
            ColorPrioritiesController::class,
            'index',
        ])->name('colors.priorities.index');
        Route::post('/colors/{color}/priorities', [
            ColorPrioritiesController::class,
            'store',
        ])->name('colors.priorities.store');

        Route::apiResource('developers', DeveloperController::class);

        // Developer Tasks
        Route::get('/developers/{developer}/tasks', [
            DeveloperTasksController::class,
            'index',
        ])->name('developers.tasks.index');
        Route::post('/developers/{developer}/tasks/{task}', [
            DeveloperTasksController::class,
            'store',
        ])->name('developers.tasks.store');
        Route::delete('/developers/{developer}/tasks/{task}', [
            DeveloperTasksController::class,
            'destroy',
        ])->name('developers.tasks.destroy');

        // Developer Tickets
        Route::get('/developers/{developer}/tickets', [
            DeveloperTicketsController::class,
            'index',
        ])->name('developers.tickets.index');
        Route::post('/developers/{developer}/tickets/{ticket}', [
            DeveloperTicketsController::class,
            'store',
        ])->name('developers.tickets.store');
        Route::delete('/developers/{developer}/tickets/{ticket}', [
            DeveloperTicketsController::class,
            'destroy',
        ])->name('developers.tickets.destroy');

        // Developer Skills
        Route::get('/developers/{developer}/skills', [
            DeveloperSkillsController::class,
            'index',
        ])->name('developers.skills.index');
        Route::post('/developers/{developer}/skills/{skill}', [
            DeveloperSkillsController::class,
            'store',
        ])->name('developers.skills.store');
        Route::delete('/developers/{developer}/skills/{skill}', [
            DeveloperSkillsController::class,
            'destroy',
        ])->name('developers.skills.destroy');

        Route::apiResource('icons', IconController::class);

        // Icon Status
        Route::get('/icons/{icon}/status', [
            IconStatusController::class,
            'index',
        ])->name('icons.status.index');
        Route::post('/icons/{icon}/status', [
            IconStatusController::class,
            'store',
        ])->name('icons.status.store');

        Route::apiResource('payables', PayableController::class);

        Route::apiResource('people', PersonController::class);

        // Person Tickets
        Route::get('/people/{person}/tickets', [
            PersonTicketsController::class,
            'index',
        ])->name('people.tickets.index');
        Route::post('/people/{person}/tickets', [
            PersonTicketsController::class,
            'store',
        ])->name('people.tickets.store');

        // Person Versions
        Route::get('/people/{person}/versions', [
            PersonVersionsController::class,
            'index',
        ])->name('people.versions.index');
        Route::post('/people/{person}/versions/{version}', [
            PersonVersionsController::class,
            'store',
        ])->name('people.versions.store');
        Route::delete('/people/{person}/versions/{version}', [
            PersonVersionsController::class,
            'destroy',
        ])->name('people.versions.destroy');

        Route::apiResource('priorities', PriorityController::class);

        // Priority Tickets
        Route::get('/priorities/{priority}/tickets', [
            PriorityTicketsController::class,
            'index',
        ])->name('priorities.tickets.index');
        Route::post('/priorities/{priority}/tickets', [
            PriorityTicketsController::class,
            'store',
        ])->name('priorities.tickets.store');

        // Priority Tasks
        Route::get('/priorities/{priority}/tasks', [
            PriorityTasksController::class,
            'index',
        ])->name('priorities.tasks.index');
        Route::post('/priorities/{priority}/tasks', [
            PriorityTasksController::class,
            'store',
        ])->name('priorities.tasks.store');

        Route::apiResource('products', ProductController::class);

        // Product Tickets
        Route::get('/products/{product}/tickets', [
            ProductTicketsController::class,
            'index',
        ])->name('products.tickets.index');
        Route::post('/products/{product}/tickets', [
            ProductTicketsController::class,
            'store',
        ])->name('products.tickets.store');

        // Product Payables
        Route::get('/products/{product}/payables', [
            ProductPayablesController::class,
            'index',
        ])->name('products.payables.index');
        Route::post('/products/{product}/payables', [
            ProductPayablesController::class,
            'store',
        ])->name('products.payables.store');

        Route::apiResource('proposals', ProposalController::class);

        // Proposal Versions
        Route::get('/proposals/{proposal}/versions', [
            ProposalVersionsController::class,
            'index',
        ])->name('proposals.versions.index');
        Route::post('/proposals/{proposal}/versions', [
            ProposalVersionsController::class,
            'store',
        ])->name('proposals.versions.store');

        Route::apiResource('receipts', ReceiptController::class);

        // Receipt Tickets
        Route::get('/receipts/{receipt}/tickets', [
            ReceiptTicketsController::class,
            'index',
        ])->name('receipts.tickets.index');
        Route::post('/receipts/{receipt}/tickets', [
            ReceiptTicketsController::class,
            'store',
        ])->name('receipts.tickets.store');

        // Receipt Tasks
        Route::get('/receipts/{receipt}/tasks', [
            ReceiptTasksController::class,
            'index',
        ])->name('receipts.tasks.index');
        Route::post('/receipts/{receipt}/tasks', [
            ReceiptTasksController::class,
            'store',
        ])->name('receipts.tasks.store');

        // Receipt Payables
        Route::get('/receipts/{receipt}/payables', [
            ReceiptPayablesController::class,
            'index',
        ])->name('receipts.payables.index');
        Route::post('/receipts/{receipt}/payables', [
            ReceiptPayablesController::class,
            'store',
        ])->name('receipts.payables.store');

        Route::apiResource('rols', RolController::class);

        // Rol People
        Route::get('/rols/{rol}/people', [
            RolPeopleController::class,
            'index',
        ])->name('rols.people.index');
        Route::post('/rols/{rol}/people', [
            RolPeopleController::class,
            'store',
        ])->name('rols.people.store');

        // Rol Developers
        Route::get('/rols/{rol}/developers', [
            RolDevelopersController::class,
            'index',
        ])->name('rols.developers.index');
        Route::post('/rols/{rol}/developers', [
            RolDevelopersController::class,
            'store',
        ])->name('rols.developers.store');

        Route::apiResource('skills', SkillController::class);

        // Skill Developers
        Route::get('/skills/{skill}/developers', [
            SkillDevelopersController::class,
            'index',
        ])->name('skills.developers.index');
        Route::post('/skills/{skill}/developers/{developer}', [
            SkillDevelopersController::class,
            'store',
        ])->name('skills.developers.store');
        Route::delete('/skills/{skill}/developers/{developer}', [
            SkillDevelopersController::class,
            'destroy',
        ])->name('skills.developers.destroy');

        Route::apiResource('status', StatuController::class);

        // Statu Tickets
        Route::get('/status/{statu}/tickets', [
            StatuTicketsController::class,
            'index',
        ])->name('status.tickets.index');
        Route::post('/status/{statu}/tickets', [
            StatuTicketsController::class,
            'store',
        ])->name('status.tickets.store');

        // Statu Tasks
        Route::get('/status/{statu}/tasks', [
            StatuTasksController::class,
            'index',
        ])->name('status.tasks.index');
        Route::post('/status/{statu}/tasks', [
            StatuTasksController::class,
            'store',
        ])->name('status.tasks.store');

        Route::apiResource('suppliers', SupplierController::class);

        // Supplier Payables
        Route::get('/suppliers/{supplier}/payables', [
            SupplierPayablesController::class,
            'index',
        ])->name('suppliers.payables.index');
        Route::post('/suppliers/{supplier}/payables', [
            SupplierPayablesController::class,
            'store',
        ])->name('suppliers.payables.store');

        Route::apiResource('tasks', TaskController::class);

        // Task Attaches
        Route::get('/tasks/{task}/attaches', [
            TaskAttachesController::class,
            'index',
        ])->name('tasks.attaches.index');
        Route::post('/tasks/{task}/attaches', [
            TaskAttachesController::class,
            'store',
        ])->name('tasks.attaches.store');

        // Task Developers
        Route::get('/tasks/{task}/developers', [
            TaskDevelopersController::class,
            'index',
        ])->name('tasks.developers.index');
        Route::post('/tasks/{task}/developers/{developer}', [
            TaskDevelopersController::class,
            'store',
        ])->name('tasks.developers.store');
        Route::delete('/tasks/{task}/developers/{developer}', [
            TaskDevelopersController::class,
            'destroy',
        ])->name('tasks.developers.destroy');

        Route::apiResource('tickets', TicketController::class);

        // Ticket Attachments
        Route::get('/tickets/{ticket}/attachments', [
            TicketAttachmentsController::class,
            'index',
        ])->name('tickets.attachments.index');
        Route::post('/tickets/{ticket}/attachments', [
            TicketAttachmentsController::class,
            'store',
        ])->name('tickets.attachments.store');

        // Ticket Developers
        Route::get('/tickets/{ticket}/developers', [
            TicketDevelopersController::class,
            'index',
        ])->name('tickets.developers.index');
        Route::post('/tickets/{ticket}/developers/{developer}', [
            TicketDevelopersController::class,
            'store',
        ])->name('tickets.developers.store');
        Route::delete('/tickets/{ticket}/developers/{developer}', [
            TicketDevelopersController::class,
            'destroy',
        ])->name('tickets.developers.destroy');

        Route::apiResource('users', UserController::class);

        // User Versions
        Route::get('/users/{user}/versions', [
            UserVersionsController::class,
            'index',
        ])->name('users.versions.index');
        Route::post('/users/{user}/versions', [
            UserVersionsController::class,
            'store',
        ])->name('users.versions.store');

        // User People
        Route::get('/users/{user}/people', [
            UserPeopleController::class,
            'index',
        ])->name('users.people.index');
        Route::post('/users/{user}/people', [
            UserPeopleController::class,
            'store',
        ])->name('users.people.store');

        // User Developers
        Route::get('/users/{user}/developers', [
            UserDevelopersController::class,
            'index',
        ])->name('users.developers.index');
        Route::post('/users/{user}/developers', [
            UserDevelopersController::class,
            'store',
        ])->name('users.developers.store');

        // User Attachments
        Route::get('/users/{user}/attachments', [
            UserAttachmentsController::class,
            'index',
        ])->name('users.attachments.index');
        Route::post('/users/{user}/attachments', [
            UserAttachmentsController::class,
            'store',
        ])->name('users.attachments.store');

        // User Attaches
        Route::get('/users/{user}/attaches', [
            UserAttachesController::class,
            'index',
        ])->name('users.attaches.index');
        Route::post('/users/{user}/attaches', [
            UserAttachesController::class,
            'store',
        ])->name('users.attaches.store');

        // User Clients
        Route::get('/users/{user}/clients', [
            UserClientsController::class,
            'index',
        ])->name('users.clients.index');
        Route::post('/users/{user}/clients/{client}', [
            UserClientsController::class,
            'store',
        ])->name('users.clients.store');
        Route::delete('/users/{user}/clients/{client}', [
            UserClientsController::class,
            'destroy',
        ])->name('users.clients.destroy');

        Route::apiResource('versions', VersionController::class);

        // Version Tasks
        Route::get('/versions/{version}/tasks', [
            VersionTasksController::class,
            'index',
        ])->name('versions.tasks.index');
        Route::post('/versions/{version}/tasks', [
            VersionTasksController::class,
            'store',
        ])->name('versions.tasks.store');

        // Version People
        Route::get('/versions/{version}/people', [
            VersionPeopleController::class,
            'index',
        ])->name('versions.people.index');
        Route::post('/versions/{version}/people/{person}', [
            VersionPeopleController::class,
            'store',
        ])->name('versions.people.store');
        Route::delete('/versions/{version}/people/{person}', [
            VersionPeopleController::class,
            'destroy',
        ])->name('versions.people.destroy');
    });
