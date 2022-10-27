<?php

namespace App\Http\Controllers;

use App\Models\Icon;
use App\Models\Statu;
use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Requests\StatuStoreRequest;
use App\Http\Requests\StatuUpdateRequest;

class StatuController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Statu::class);

        $search = $request->get('search', '');

        $status = Statu::search($search)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('app.status.index', compact('status', 'search'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Statu::class);

        $colors = Color::pluck('name', 'id');
        $icons = Icon::pluck('name', 'id');

        return view('app.status.create', compact('colors', 'icons'));
    }

    /**
     * @param \App\Http\Requests\StatuStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StatuStoreRequest $request)
    {
        $this->authorize('create', Statu::class);

        $validated = $request->validated();

        $statu = Statu::create($validated);

        return redirect()
            ->route('status.edit', $statu)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Statu $statu)
    {
        $this->authorize('view', $statu);

        return view('app.status.show', compact('statu'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Statu $statu)
    {
        $this->authorize('update', $statu);

        $colors = Color::pluck('name', 'id');
        $icons = Icon::pluck('name', 'id');

        return view('app.status.edit', compact('statu', 'colors', 'icons'));
    }

    /**
     * @param \App\Http\Requests\StatuUpdateRequest $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function update(StatuUpdateRequest $request, Statu $statu)
    {
        $this->authorize('update', $statu);

        $validated = $request->validated();

        $statu->update($validated);

        return redirect()
            ->route('status.edit', $statu)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Statu $statu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Statu $statu)
    {
        $this->authorize('delete', $statu);

        $statu->delete();

        return redirect()
            ->route('status.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
