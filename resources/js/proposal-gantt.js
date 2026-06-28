const configElement = document.getElementById('gantt-config');
const ganttContainer = document.getElementById('gantt_here');

if (configElement && ganttContainer && window.gantt) {
    const config = JSON.parse(configElement.textContent || '{}');
    const routes = config.routes || {};
    const zoomLevels = Array.isArray(config.zoom_levels) ? config.zoom_levels : [];
    const defaultZoom = config.default_zoom || zoomLevels[0]?.key || null;
    const zoomSelect = document.querySelector('[data-gantt-zoom-select]');
    const zoomButtons = document.querySelectorAll('[data-gantt-zoom-step]');
    const developersModal = document.getElementById('gantt-developers-modal');
    const csrfToken = config.csrf_token
        || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

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

    // Add a "Developers" button to the lightbox footer that opens
    // the dedicated developer manager modal. Returning false in the
    // event handler keeps the lightbox open until the user is done.
    if (gantt.config.buttons_left && gantt.locale?.labels) {
        gantt.config.buttons_left = gantt.config.buttons_left.slice();
        gantt.config.buttons_left.push('gantt_developers_btn');
        gantt.locale.labels['gantt_developers_btn'] = 'Developers';
    }

    let pendingDevelopersTaskId = null;
    const taskIdAliases = new Map();

    const getActiveLightboxTaskId = () => {
        const lightboxState = gantt.getState?.().lightbox;

        if (lightboxState !== undefined && lightboxState !== null) {
            return lightboxState;
        }

        return gantt.getSelectedId?.() || null;
    };

    const isPersistedTask = (taskId) => {
        if (taskId === undefined || taskId === null || !gantt.isTaskExists(taskId)) {
            return false;
        }

        const task = gantt.getTask(taskId);

        return Boolean(task) && task._persisted === true;
    };

    const resolvePersistedTaskId = (taskId) => {
        let resolvedTaskId = taskId;

        while (taskIdAliases.has(resolvedTaskId)) {
            resolvedTaskId = taskIdAliases.get(resolvedTaskId);
        }

        if (isPersistedTask(resolvedTaskId)) {
            return resolvedTaskId;
        }

        const activeTaskId = getActiveLightboxTaskId();

        if (isPersistedTask(activeTaskId)) {
            return activeTaskId;
        }

        return resolvedTaskId;
    };

    const completePendingDevelopersFlow = (oldId, newId) => {
        if (newId === undefined || newId === null) {
            return;
        }

        taskIdAliases.set(oldId, newId);

        const task = gantt.isTaskExists(newId) ? gantt.getTask(newId) : null;

        if (task) {
            task._persisted = true;
        }

        if (String(window.__ganttDevelopersState?.taskId) === String(oldId)) {
            window.__ganttDevelopersState.taskId = newId;
        }

        if (String(pendingDevelopersTaskId) === String(oldId)) {
            pendingDevelopersTaskId = null;
            openDevelopersModal(newId);
        }
    };

    gantt.attachEvent('onLightboxButton', function (buttonId) {
        if (buttonId === 'gantt_developers_btn') {
            const taskId = getActiveLightboxTaskId();

            if (!isPersistedTask(taskId)) {
                pendingDevelopersTaskId = taskId;

                if (typeof gantt.saveLightbox === 'function') {
                    gantt.saveLightbox();
                }

                return false;
            }

            openDevelopersModal(taskId);
            return false;
        }

        return true;
    });

    gantt.attachEvent('onTaskIdChange', function (oldId, newId) {
        completePendingDevelopersFlow(oldId, newId);
    });

    gantt.attachEvent('onLightbox', function (taskId) {
        syncHoursLightboxState(taskId);
    });

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

    const normalizeDeveloperEntry = (entry) => {
        if (!entry) {
            return null;
        }

        const developerId = entry.developer_id ?? entry.id ?? null;

        if (developerId === null) {
            return null;
        }

        return {
            developer_id: developerId,
            name: entry.name || entry.user?.name || `Developer #${developerId}`,
            hours: entry.hours ?? entry.pivot?.hours ?? null,
        };
    };

    const getCurrentDevelopers = (taskId) => {
        const task = gantt.getTask(taskId);
        if (!Array.isArray(task?.developers)) {
            return [];
        }

        return task.developers
            .map(normalizeDeveloperEntry)
            .filter(Boolean);
    };

    const setCurrentDevelopers = (taskId, developers) => {
        const task = gantt.getTask(taskId);
        if (task) {
            task.developers = developers
                .map(normalizeDeveloperEntry)
                .filter(Boolean);
        }
    };

    const taskUsesCalculatedHours = (taskId) => {
        return getCurrentDevelopers(taskId)
            .some((developer) => developer.hours !== null && developer.hours !== '');
    };

    const syncHoursLightboxState = (taskId, hoursValue = null) => {
        const section = gantt.getLightboxSection?.('hours');
        const lightbox = gantt.getLightbox?.();

        if (!section || !lightbox) {
            return;
        }

        const task = gantt.getTask(taskId);
        const calculated = taskUsesCalculatedHours(taskId);
        const nextHours = hoursValue ?? task?.hours ?? '';

        section.setValue(String(nextHours ?? ''));

        const textareas = lightbox.querySelectorAll('textarea');
        const hoursInput = textareas.length > 1 ? textareas[1] : null;

        if (!hoursInput) {
            return;
        }

        hoursInput.readOnly = calculated;

        let hint = lightbox.querySelector('[data-gantt-hours-hint]');

        if (!hint) {
            hint = document.createElement('p');
            hint.dataset.ganttHoursHint = 'true';
            hint.className = 'mt-1 text-xs text-amber-600 hidden';
            hoursInput.parentElement?.appendChild(hint);
        }

        if (calculated) {
            hint.textContent = 'This value is being calculated from developer hours.';
            hint.classList.remove('hidden');
        } else {
            hint.textContent = '';
            hint.classList.add('hidden');
        }
    };

    const renderDeveloperRows = (container, developers) => {
        container.innerHTML = '';

        if (!developers.length) {
            const empty = document.createElement('p');
            empty.className = 'gantt-developers-empty';
            empty.textContent = 'No developers assigned yet.';
            container.appendChild(empty);
            return;
        }

        developers.forEach((entry, index) => {
            const row = document.createElement('div');
            row.className = 'gantt-developers-row';
            row.dataset.index = String(index);
            row.innerHTML = `
                <span class="gantt-developers-row__name"></span>
                <input type="number" min="0" step="0.25" class="gantt-developers-row__hours" />
                <button type="button" class="gantt-developers-row__remove" aria-label="Remove developer">&times;</button>
            `;
            row.querySelector('.gantt-developers-row__name').textContent = entry.name || `Developer #${entry.developer_id}`;
            row.querySelector('.gantt-developers-row__hours').value = entry.hours ?? '';
            container.appendChild(row);
        });
    };

    const collectDeveloperEntries = (container) => {
        return Array.from(container.querySelectorAll('.gantt-developers-row')).map((row) => {
            const index = Number(row.dataset.index || 0);
            const hoursInput = row.querySelector('.gantt-developers-row__hours');
            const hours = hoursInput.value === '' ? null : Number(hoursInput.value);

            return {
                entry: window.__ganttDevelopersState.entries[index],
                hours: hours,
            };
        }).map((row) => {
            return {
                developer_id: row.entry.developer_id,
                name: row.entry.name,
                hours: Number.isNaN(row.hours) ? null : row.hours,
            };
        });
    };

    const showFlash = (message, type) => {
        if (!developersModal) {
            return;
        }

        const flash = developersModal.querySelector('[data-gantt-developers-flash]');
        if (!flash) {
            return;
        }

        flash.textContent = message;
        flash.dataset.flashType = type || 'info';
        flash.classList.remove('hidden');
    };

    const hideFlash = () => {
        if (!developersModal) {
            return;
        }
        const flash = developersModal.querySelector('[data-gantt-developers-flash]');
        if (flash) {
            flash.classList.add('hidden');
            flash.textContent = '';
        }
    };

    const searchDevelopers = async (query) => {
        if (!routes.developer_search) {
            return [];
        }

        const url = new URL(routes.developer_search, window.location.origin);
        if (query) {
            url.searchParams.set('q', query);
        }

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            return [];
        }

        const payload = await response.json();
        return Array.isArray(payload.data) ? payload.data : [];
    };

    const quickStoreDeveloper = async (form) => {
        if (!routes.developer_quick_store) {
            return null;
        }

        const response = await fetch(routes.developer_quick_store, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify(form),
        });

        if (!response.ok) {
            return null;
        }

        const payload = await response.json();
        return payload.data || null;
    };

    const openDevelopersModal = async (taskId) => {
        if (!developersModal) {
            return;
        }

        const current = getCurrentDevelopers(taskId);
        window.__ganttDevelopersState = {
            taskId: taskId,
            entries: current.map((entry) => ({ ...entry })),
        };

        const rowsContainer = developersModal.querySelector('[data-gantt-developers-rows]');
        const searchInput = developersModal.querySelector('[data-gantt-developers-search]');
        const resultsContainer = developersModal.querySelector('[data-gantt-developers-results]');

        renderDeveloperRows(rowsContainer, current);
        if (searchInput) {
            searchInput.value = '';
        }
        if (resultsContainer) {
            resultsContainer.innerHTML = '';
        }
        hideFlash();
        developersModal.classList.remove('hidden');
        document.body.classList.add('overflow-y-hidden');
    };

    const closeDevelopersModal = () => {
        if (!developersModal) {
            return;
        }
        developersModal.classList.add('hidden');
        document.body.classList.remove('overflow-y-hidden');
    };

    const renderSearchResults = (container, results) => {
        container.innerHTML = '';

        if (!results.length) {
            const empty = document.createElement('p');
            empty.className = 'gantt-developers-search-empty';
            empty.textContent = 'No developers match the search.';
            container.appendChild(empty);
            return;
        }

        results.forEach((developer) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'gantt-developers-search-result';
            button.dataset.developerId = String(developer.id);
            button.innerHTML = `
                <span class="gantt-developers-search-result__name"></span>
                <span class="gantt-developers-search-result__email"></span>
            `;
            button.querySelector('.gantt-developers-search-result__name').textContent = developer.name || `Developer #${developer.id}`;
            button.querySelector('.gantt-developers-search-result__email').textContent = developer.email || '';
            container.appendChild(button);
        });
    };

    const addDeveloperToList = (developer) => {
        if (!window.__ganttDevelopersState) {
            return;
        }

        const existing = window.__ganttDevelopersState.entries
            .find((entry) => Number(entry.developer_id) === Number(developer.id));

        if (existing) {
            showFlash('Ese developer ya está asignado.', 'info');
            return;
        }

        window.__ganttDevelopersState.entries.push({
            developer_id: developer.id,
            name: developer.name,
            hours: null,
        });

        const rowsContainer = developersModal.querySelector('[data-gantt-developers-rows]');
        renderDeveloperRows(rowsContainer, window.__ganttDevelopersState.entries);
    };

    const bindDevelopersModalEvents = () => {
        if (!developersModal) {
            return;
        }

        const closeBtn = developersModal.querySelector('[data-gantt-developers-close]');
        const searchInput = developersModal.querySelector('[data-gantt-developers-search]');
        const resultsContainer = developersModal.querySelector('[data-gantt-developers-results]');
        const rowsContainer = developersModal.querySelector('[data-gantt-developers-rows]');
        const saveBtn = developersModal.querySelector('[data-gantt-developers-save]');
        const newForm = developersModal.querySelector('[data-gantt-developers-new-form]');

        if (closeBtn) {
            closeBtn.addEventListener('click', closeDevelopersModal);
        }

        if (searchInput) {
            let timer = null;
            searchInput.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(async () => {
                    const results = await searchDevelopers(searchInput.value.trim());
                    renderSearchResults(resultsContainer, results);
                }, 200);
            });
        }

        if (resultsContainer) {
            resultsContainer.addEventListener('click', (event) => {
                const button = event.target.closest('.gantt-developers-search-result');
                if (!button) {
                    return;
                }

                const developerId = Number(button.dataset.developerId);
                const developer = window.__ganttLastSearch?.find((d) => Number(d.id) === developerId);

                if (developer) {
                    addDeveloperToList(developer);
                }
            });
        }

        if (resultsContainer) {
            const observer = new MutationObserver(() => {
                window.__ganttLastSearch = Array.from(resultsContainer.querySelectorAll('.gantt-developers-search-result'))
                    .map((button) => ({
                        id: Number(button.dataset.developerId),
                        name: button.querySelector('.gantt-developers-search-result__name')?.textContent || '',
                    }));
            });
            observer.observe(resultsContainer, { childList: true });
        }

        if (rowsContainer) {
            rowsContainer.addEventListener('click', (event) => {
                const remove = event.target.closest('.gantt-developers-row__remove');
                if (!remove) {
                    return;
                }

                const row = event.target.closest('.gantt-developers-row');
                const index = Number(row.dataset.index || 0);

                if (!Number.isNaN(index) && window.__ganttDevelopersState) {
                    window.__ganttDevelopersState.entries.splice(index, 1);
                    renderDeveloperRows(rowsContainer, window.__ganttDevelopersState.entries);
                }
            });
        }

        if (saveBtn) {
            saveBtn.addEventListener('click', async () => {
                if (!window.__ganttDevelopersState) {
                    return;
                }

                const entries = collectDeveloperEntries(rowsContainer);
                const payload = {
                    developers: entries.map((entry) => ({
                        developer_id: entry.developer_id,
                        hours: entry.hours,
                    })),
                };

                const taskId = resolvePersistedTaskId(window.__ganttDevelopersState.taskId);

                if (!isPersistedTask(taskId)) {
                    showFlash('Guardá la tarea antes de asignar developers.', 'error');
                    return;
                }

                window.__ganttDevelopersState.taskId = taskId;

                const url = resolveRoute(routes.task_developers_sync, taskId);

                if (!url) {
                    showFlash('No se pudo guardar la asignación.', 'error');
                    return;
                }

                saveBtn.disabled = true;

                try {
                    const response = await fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) {
                        showFlash('No se pudo guardar la asignación.', 'error');
                        return;
                    }

                    const data = await response.json();
                    setCurrentDevelopers(
                        taskId,
                        entries
                    );
                    if (typeof data.hours === 'number') {
                        const task = gantt.getTask(taskId);

                        if (task) {
                            task.hours = data.hours;
                            syncHoursLightboxState(taskId, data.hours);
                            gantt.refreshTask(taskId);
                        }
                    }
                    closeDevelopersModal();
                } catch (error) {
                    showFlash('No se pudo guardar la asignación.', 'error');
                } finally {
                    saveBtn.disabled = false;
                }
            });
        }

        if (newForm) {
            newForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const formData = new FormData(newForm);
                const payload = {
                    name: formData.get('name'),
                    email: formData.get('email'),
                    password: formData.get('password'),
                    rol_id: formData.get('rol_id'),
                    cost_per_hour: formData.get('cost_per_hour') || null,
                };

                if (!payload.name || !payload.email || !payload.password || !payload.rol_id) {
                    showFlash('Completá los campos requeridos.', 'error');
                    return;
                }

                const developer = await quickStoreDeveloper(payload);

                if (!developer) {
                    showFlash('No se pudo crear el developer.', 'error');
                    return;
                }

                addDeveloperToList(developer);
                newForm.reset();
            });
        }
    };

    gantt.init('gantt_here');

    if (defaultZoom) {
        applyZoom(defaultZoom);
    }

    if (routes.load) {
        gantt.load(routes.load);
    }

    gantt.attachEvent('onParse', function () {
        const tasks = gantt.getTaskByTime();
        tasks.forEach((task) => {
            if (!Array.isArray(task.developers)) {
                task.developers = [];
            }

            task._persisted = true;
        });
    });

    gantt.attachEvent('onAfterTaskAdd', function (id, task) {
        if (task) {
            task._persisted = false;
        }
    });

    gantt.createDataProcessor(function (entity, action, data, id) {
        data._token = csrfToken;
        data.proposal_id = config.proposal_id;

        if (data.start_date) {
            data.start_date = getFormattedDate(data.start_date);
        }

        if (action === 'create' && routes.create) {
            return gantt.ajax.post(routes.create, data).then((response) => {
                const payload = JSON.parse(response.responseText || '{}');
                const persistedTaskId = payload.tid ?? payload.id ?? null;

                if (persistedTaskId !== null) {
                    completePendingDevelopersFlow(id, persistedTaskId);
                }

                return payload;
            });
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

    bindDevelopersModalEvents();
}
