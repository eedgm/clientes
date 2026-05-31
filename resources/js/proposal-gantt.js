const configElement = document.getElementById('gantt-config');
const ganttContainer = document.getElementById('gantt_here');

if (configElement && ganttContainer && window.gantt) {
    const config = JSON.parse(configElement.textContent || '{}');
    const routes = config.routes || {};
    const zoomLevels = Array.isArray(config.zoom_levels) ? config.zoom_levels : [];
    const defaultZoom = config.default_zoom || zoomLevels[0]?.key || null;
    const zoomSelect = document.querySelector('[data-gantt-zoom-select]');
    const zoomButtons = document.querySelectorAll('[data-gantt-zoom-step]');

    gantt.config.date_format = config.date_format || '%Y-%m-%d %H:%i:%s';
    gantt.config.work_time = true;
    gantt.config.skip_off_time = true;
    gantt.config.order_branch = true;
    gantt.config.order_branch_free = true;

    const desktopGridWidth = config.grid?.desktop_width || 300;
    const mobileGridWidth = config.grid?.mobile_width || 170;
    const mobileQuery = window.matchMedia('(max-width: 768px)');

    const setColumns = () => {
        const width = mobileQuery.matches ? mobileGridWidth : desktopGridWidth;

        gantt.config.columns = [
            { name: 'text', label: 'Task name', width, tree: true, resize: true },
            { name: 'start_date', label: 'Start time', align: 'center', resize: true },
            { name: 'hours', label: 'Hours', align: 'center', resize: true },
            { name: 'add', label: '', width: 44 },
        ];
    };

    setColumns();

    gantt.templates.scale_cell_class = function (date) {
        if (date.getDay() === 0 || date.getDay() === 6) {
            return 'weekend';
        }
    };

    gantt.templates.timeline_cell_class = function (task, date) {
        if (date.getDay() === 0 || date.getDay() === 6) {
            return 'weekend';
        }
    };

    gantt.locale.labels.section_priority_id = 'Priority';
    gantt.locale.labels.section_statu_id = 'Status';
    gantt.locale.labels.section_hours = 'Hours';

    gantt.config.lightbox.sections = [
        { name: 'description', height: 50, map_to: 'text', type: 'textarea', focus: true },
        {
            name: 'priority_id',
            height: 34,
            map_to: 'priority_id',
            type: 'select',
            options: config.lightbox?.priorities || [],
            default_value: config.lightbox?.default_priority_id,
        },
        {
            name: 'statu_id',
            height: 34,
            map_to: 'statu_id',
            type: 'select',
            options: config.lightbox?.statuses || [],
            default_value: config.lightbox?.default_statu_id,
        },
        { name: 'hours', height: 50, map_to: 'hours', type: 'textarea' },
        { name: 'time', height: 35, map_to: 'auto', type: 'duration' },
    ];

    let dragId = null;

    gantt.attachEvent('onRowDragStart', function (id) {
        dragId = id;
        return true;
    });

    gantt.attachEvent('onRowDragEnd', function () {
        dragId = null;
        gantt.render();
    });

    gantt.templates.grid_row_class = function (start, end, task) {
        if (dragId && task.id !== dragId) {
            if (task.$level !== gantt.getTask(dragId).$level) {
                return 'cant-drop';
            }
        }

        return '';
    };

    gantt.templates.task_class = function (start, end, task) {
        const map = config.priority_class_map || {};

        return map[String(task.priority_id)] || '';
    };

    const applyZoom = (zoomKey) => {
        const zoomLevel = zoomLevels.find((level) => level.key === zoomKey);

        if (!zoomLevel) {
            return;
        }

        gantt.config.scale_height = zoomLevel.scale_height || 50;
        gantt.config.scales = zoomLevel.scales || gantt.config.scales;

        if (zoomSelect) {
            zoomSelect.value = zoomLevel.key;
        }

        gantt.render();
    };

    const getFormattedDate = (date) => {
        const dt = new Date(date);
        const pad = (nr) => `${nr}`.padStart(2, '0');

        return `${dt.getFullYear()}-${pad(dt.getMonth() + 1)}-${pad(dt.getDate())} 00:00:00`;
    };

    const resolveRoute = (routePattern, taskId) => {
        if (!routePattern) {
            return null;
        }

        return routePattern.replace('__TASK__', taskId);
    };

    gantt.init('gantt_here');

    if (defaultZoom) {
        applyZoom(defaultZoom);
    }

    if (routes.load) {
        gantt.load(routes.load);
    }

    const csrfToken = config.csrf_token || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    gantt.createDataProcessor(function (entity, action, data, id) {
        data._token = csrfToken;
        data.proposal_id = config.proposal_id;

        if (data.start_date) {
            data.start_date = getFormattedDate(data.start_date);
        }

        if (action === 'create' && routes.create) {
            return gantt.ajax.post(routes.create, data);
        }

        if (action === 'update') {
            const updateRoute = resolveRoute(routes.update, id);

            if (updateRoute) {
                return gantt.ajax.put(updateRoute, data);
            }
        }

        if (action === 'delete') {
            const deleteRoute = resolveRoute(routes.delete, id);

            if (deleteRoute) {
                return gantt.ajax.put(deleteRoute, data);
            }
        }
    });

    if (zoomSelect) {
        zoomSelect.addEventListener('change', function (event) {
            applyZoom(event.target.value);
        });
    }

    zoomButtons.forEach((button) => {
        button.addEventListener('click', function () {
            if (!zoomLevels.length || !zoomSelect) {
                return;
            }

            const currentIndex = zoomLevels.findIndex(
                (level) => level.key === zoomSelect.value
            );

            const step = Number(button.dataset.ganttZoomStep || 0);
            const nextIndex = Math.min(
                zoomLevels.length - 1,
                Math.max(0, currentIndex + step)
            );

            applyZoom(zoomLevels[nextIndex].key);
        });
    });

    window.addEventListener('resize', function () {
        setColumns();
        gantt.render();
    });
}
