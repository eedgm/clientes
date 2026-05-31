<div class="gantt-toolbar">
    <div class="gantt-toolbar__zoom">
        <span class="gantt-toolbar__zoom-label">Zoom</span>
        <button type="button" class="gantt-toolbar__zoom-btn" data-gantt-zoom-step="-1" aria-label="Zoom out">−</button>
        <select id="gantt-zoom-level" class="gantt-toolbar__zoom-select" data-gantt-zoom-select>
            @foreach ($ganttConfig['zoom_levels'] as $zoomLevel)
                <option value="{{ $zoomLevel['key'] }}" @selected($ganttConfig['default_zoom'] === $zoomLevel['key'])>
                    {{ $zoomLevel['label'] }}
                </option>
            @endforeach
        </select>
        <button type="button" class="gantt-toolbar__zoom-btn" data-gantt-zoom-step="1" aria-label="Zoom in">+</button>
    </div>
</div>
