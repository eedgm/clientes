<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProposalStoreRequest;
use App\Http\Requests\ProposalUpdateRequest;
use App\Models\Client;
use App\Models\Priority;
use App\Models\Proposal;
use App\Models\Rol;
use App\Models\Statu;
use App\Models\Task;
use App\Services\GanttTaskScheduler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ProposalController extends Controller
{
    /**
     * @return Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Proposal::class);

        $search = $request->get('search', '');

        $proposals = Proposal::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.proposals.index', compact('proposals', 'search'));
    }

    public function gantt(Request $request, Proposal $proposal, GanttTaskScheduler $scheduler)
    {
        $this->authorize('view', $proposal);

        $priorities = Priority::query()
            ->orderBy('id')
            ->get(['id', 'name']);

        $statuses = Statu::query()
            ->orderBy('id')
            ->get(['id', 'name']);

        $rols = Rol::query()
            ->orderBy('id')
            ->get(['id', 'name']);

        $hourPerDay = $scheduler->hourPerDayFor($proposal);

        $height = (int) $request->get('height', 700);
        $height = max(420, min($height, 1200));

        $zoomLevels = [
            [
                'key' => 'day',
                'label' => 'Day',
                'scale_height' => 27,
                'scales' => [
                    ['unit' => 'day', 'step' => 1, 'format' => '%d %M'],
                ],
            ],
            [
                'key' => 'week',
                'label' => 'Week',
                'scale_height' => 50,
                'scales' => [
                    ['unit' => 'week', 'step' => 1, 'format' => 'Week #%W'],
                    ['unit' => 'day', 'step' => 1, 'format' => '%D'],
                ],
            ],
            [
                'key' => 'month',
                'label' => 'Month',
                'scale_height' => 50,
                'scales' => [
                    ['unit' => 'month', 'step' => 1, 'format' => '%F %Y'],
                    ['unit' => 'week', 'step' => 1, 'format' => 'Week #%W'],
                ],
            ],
        ];

        $ganttConfig = [
            'proposal_id' => $proposal->id,
            'csrf_token' => csrf_token(),
            'height' => $height,
            'date_format' => '%Y-%m-%d %H:%i:%s',
            'hour_per_day' => $hourPerDay,
            'grid' => [
                'desktop_width' => 300,
                'mobile_width' => 170,
            ],
            'zoom_levels' => $zoomLevels,
            'default_zoom' => 'day',
            'lightbox' => [
                'priorities' => $priorities
                    ->map(fn ($priority) => [
                        'key' => $priority->id,
                        'label' => $priority->name,
                    ])
                    ->values(),
                'statuses' => $statuses
                    ->map(fn ($status) => [
                        'key' => $status->id,
                        'label' => $status->name,
                    ])
                    ->values(),
                'rols' => $rols
                    ->map(fn ($rol) => [
                        'key' => $rol->id,
                        'label' => $rol->name,
                    ])
                    ->values(),
                'default_priority_id' => $priorities->first()->id ?? null,
                'default_statu_id' => $statuses->first()->id ?? null,
            ],
            'routes' => [
                'load' => route('proposal.tasks', $proposal),
                'create' => route('tasks.gantt.store'),
                'update' => route('tasks.gantt.update', ['task' => '__TASK__']),
                'delete' => route('tasks.gantt.destroy', ['task' => '__TASK__']),
                'reorder' => route('proposal.tasks.reorder', $proposal),
                'task_developers_sync' => route('tasks.gantt.developers.sync', ['task' => '__TASK__']),
                'developer_search' => route('developers.search'),
                'developer_quick_store' => route('developers.quick-store'),
            ],
            'priority_class_map' => $priorities
                ->mapWithKeys(function ($priority) {
                    $name = Str::of($priority->name)->lower()->value();

                    if (str_contains($name, 'high') || str_contains($name, 'alta')) {
                        return [(string) $priority->id => 'high'];
                    }

                    if (str_contains($name, 'low') || str_contains($name, 'baja')) {
                        return [(string) $priority->id => 'low'];
                    }

                    return [(string) $priority->id => 'medium'];
                })
                ->all(),
        ];

        return view('app.proposals.gantt', compact('proposal', 'ganttConfig'));
    }

    public function board(Request $request)
    {
        $proposals = Proposal::get();

        return view('app.proposals.board', compact('proposals'));
    }

    /**
     * @return Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Proposal::class);

        $clients = Client::pluck('name', 'id');

        return view('app.proposals.create', compact('clients'));
    }

    /**
     * @return Response
     */
    public function store(ProposalStoreRequest $request)
    {
        $this->authorize('create', Proposal::class);

        $validated = $request->validated();

        $proposal = Proposal::create($validated);

        return redirect()
            ->route('proposals.edit', $proposal)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @return Response
     */
    public function show(Request $request, Proposal $proposal)
    {
        $this->authorize('view', $proposal);

        return view('app.proposals.show', compact('proposal'));
    }

    /**
     * @return Response
     */
    public function edit(Request $request, Proposal $proposal)
    {
        $this->authorize('update', $proposal);

        $clients = Client::pluck('name', 'id');

        return view('app.proposals.edit', compact('proposal', 'clients'));
    }

    /**
     * @return Response
     */
    public function update(ProposalUpdateRequest $request, Proposal $proposal)
    {
        $this->authorize('update', $proposal);

        $validated = $request->validated();

        $proposal->update($validated);

        return redirect()
            ->route('proposals.edit', $proposal)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @return Response
     */
    public function destroy(Request $request, Proposal $proposal)
    {
        $this->authorize('delete', $proposal);

        $proposal->delete();

        return redirect()
            ->route('proposals.index')
            ->withSuccess(__('crud.common.removed'));
    }

    public function destroyDashboard(Request $request, Proposal $proposal)
    {
        $this->authorize('delete', $proposal);

        $proposal->delete();

        Task::where('proposal_id', $proposal->id)->delete();

        return redirect()
            ->route('board')
            ->withSuccess(__('crud.common.removed'));
    }
}
