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
    const hourPerDay = Number(config.hour_per_day) > 0 ? Number(config.hour_per_day) : 8;
    let pendingReorderTimer = null;
    let pendingReorderSignature = null;
    let isApplyingServerSync = false;

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

    const computeDurationFromHours = (hoursValue) => {
        const hours = Number(hoursValue);

        if (!Number.isFinite(hours) || hours <= 0) {
            return 0;
        }

        return Math.max(1, Math.ceil(hours / hourPerDay));
    };

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
        {
            name: 'hours',
            height: 50,
            map_to: 'hours',
            type: 'textarea',
        },
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

    gantt.attachEvent('onAfterLightbox', function (taskId) {
        const lightbox = gantt.getLightbox?.();

        if (!lightbox) {
            return;
        }

        const textareas = lightbox.querySelectorAll('textarea');
        const hoursInput = textareas.length > 1 ? textareas[1] : null;

        if (!hoursInput) {
            return;
        }

        const onHoursChange = () => {
            const task = gantt.getTask(taskId);

            if (!task) {
                return;
            }

            task.duration = computeDurationFromHours(hoursInput.value);
        };

        hoursInput.addEventListener('input', onHoursChange);
    });

    gantt.attachEvent('onAfterTaskUpdate', function (id, task) {
        if (!task) {
            return;
        }

        const hours = Number(task.hours);
        const computed = computeDurationFromHours(hours);

        if (computed > 0 && Number(task.duration) !== computed) {
            task.duration = computed;
        }
    });

    gantt.attachEvent('onAfterTaskDrag', function () {
        scheduleReorderPersist();
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

    const collectOrderedTaskIds = () => {
        try {
            return gantt.getTaskByTime().map((task) => task.id);
        } catch (error) {
            return [];
        }
    };

    const persistReorder = async () => {
        if (!routes.reorder) {
            return;
        }

        const orderedIds = collectOrderedTaskIds();

        if (orderedIds.length === 0) {
            return;
        }

        const signature = orderedIds.join(',');

        if (signature === pendingReorderSignature) {
            return;
        }

        pendingReorderSignature = signature;

        try {
            const response = await fetch(routes.reorder, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ ordered_ids: orderedIds }),
            });

            if (!response.ok) {
                return;
            }

            const payload = await response.json().catch(() => null);

            if (payload && Array.isArray(payload.tasks)) {
                syncGanttTasksWithServer(payload.tasks);
            }
        } catch (error) {
            // Network/order failures are non-blocking; the gantt still
            // works locally and the next drag will try again.
        }
    };

    const scheduleReorderPersist = () => {
        if (pendingReorderTimer) {
            clearTimeout(pendingReorderTimer);
        }

        pendingReorderTimer = setTimeout(() => {
            pendingReorderTimer = null;
            persistReorder();
        }, 300);
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

    /**
     * Persist developer assignments on the server and update the
     * gantt task's in-memory state atomically. Shared by the modal
     * save flow and the hours-table inline save flow.
     *
     * @param {string|number} taskId
     * @param {Array<{developer_id: number, name?: string, hours: number|null}>} developers
     * @returns {Promise<{ok: boolean, hours: number|null}>}
     */
    const saveDevelopersForTask = async (taskId, developers) => {
        const url = resolveRoute(routes.task_developers_sync, taskId);
        if (!url) return { ok: false, hours: null };

        let response;
        try {
            response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    developers: developers.map(d => ({
                        developer_id: d.developer_id,
                        hours: d.hours,
                    })),
                }),
            });
        } catch {
            return { ok: false, hours: null };
        }

        if (!response.ok) return { ok: false, hours: null };

        let data;
        try { data = await response.json(); } catch { return { ok: false, hours: null }; }

        // Update gantt in-memory state
        const task = gantt.getTask(taskId);
        if (task) {
            // Build a name lookup from the current task developers so the
            // payload (which may not carry names from the table flow) still
            // produces well-formed developer objects.
            const nameMap = {};
            (task.developers || []).forEach(d => {
                const n = normalizeDeveloperEntry(d);
                if (n) nameMap[n.developer_id] = n.name;
            });

            task.developers = developers.map(d => ({
                developer_id: d.developer_id,
                name: d.name || nameMap[d.developer_id] || `Developer #${d.developer_id}`,
                hours: d.hours,
            }));

            if (typeof data.hours === 'number') {
                task.hours = data.hours;
                gantt.refreshTask(taskId);
            }
        }

        return { ok: true, hours: data.hours ?? null };
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
            const hours = hoursInput.value === '' ? 0 : Number(hoursInput.value);

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

                const taskId = resolvePersistedTaskId(window.__ganttDevelopersState.taskId);

                if (!isPersistedTask(taskId)) {
                    showFlash('Guardá la tarea antes de asignar developers.', 'error');
                    return;
                }

                window.__ganttDevelopersState.taskId = taskId;
                saveBtn.disabled = true;

                const result = await saveDevelopersForTask(taskId, entries);

                if (!result.ok) {
                    showFlash('No se pudo guardar la asignación.', 'error');
                    saveBtn.disabled = false;
                    return;
                }

                // The shared helper already updated task.developers and
                // task.hours. Lightbox sync is specific to the modal flow.
                if (result.hours !== null) {
                    syncHoursLightboxState(taskId, result.hours);
                }

                closeDevelopersModal();
                saveBtn.disabled = false;
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

    const bindImportModalEvents = () => {
        const importBtn = document.getElementById('gantt-import-btn');
        const importModal = document.getElementById('gantt-import-modal');
        const textarea = document.getElementById('gantt-import-textarea');
        const previewBtn = document.getElementById('gantt-import-preview-btn');
        const storeBtn = document.getElementById('gantt-import-store-btn');
        const previewContainer = document.getElementById('gantt-import-preview');
        const previewBody = document.getElementById('gantt-import-preview-body');
        const previewCount = document.getElementById('gantt-import-preview-count');
        const issuesContainer = document.getElementById('gantt-import-issues');
        const issuesBody = document.getElementById('gantt-import-issues-body');
        const flash = document.querySelector('[data-gantt-import-flash]');

        if (!importBtn || !importModal) {
            return;
        }

        const showImportFlash = (message, type) => {
            if (!flash) return;
            flash.textContent = message;
            flash.className = `rounded-lg border px-3 py-2 text-sm ${
                type === 'error'
                    ? 'border-rose-200 bg-rose-50 text-rose-700'
                    : type === 'success'
                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                        : 'border-amber-200 bg-amber-50 text-amber-700'
            }`;
            flash.classList.remove('hidden');
        };

        const hideImportFlash = () => {
            if (flash) {
                flash.classList.add('hidden');
                flash.textContent = '';
            }
        };

        const closeImportModal = () => {
            importModal.classList.add('hidden');
            document.body.classList.remove('overflow-y-hidden');
            hideImportFlash();
            previewContainer.classList.add('hidden');
            issuesContainer.classList.add('hidden');
            storeBtn.disabled = true;
        };

        const resetImportButtonLabels = () => {
            storeBtn.disabled = true;
            storeBtn.innerHTML = '<i class="bx bx-import"></i> Importar';
            previewBtn.disabled = false;
            previewBtn.innerHTML = '<i class="bx bx-search-alt"></i> Vista previa';
        };

        const openImportModal = () => {
            importModal.classList.remove('hidden');
            document.body.classList.add('overflow-y-hidden');
            textarea.value = '';
            hideImportFlash();
            previewContainer.classList.add('hidden');
            issuesContainer.classList.add('hidden');
            resetImportButtonLabels();
            textarea.focus();
        };

        const closeButtons = importModal.querySelectorAll('[data-gantt-import-close]');
        closeButtons.forEach((btn) => {
            btn.addEventListener('click', closeImportModal);
        });

        importBtn.addEventListener('click', openImportModal);

        importModal.addEventListener('click', (event) => {
            if (event.target === importModal) {
                closeImportModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !importModal.classList.contains('hidden')) {
                closeImportModal();
            }
        });

        const renderPreviewCard = (task) => {
            const card = document.createElement('div');
            card.className = 'rounded-lg border border-gray-200 bg-white p-3';

            const header = document.createElement('div');
            header.className = 'flex items-center justify-between';

            const nameSpan = document.createElement('span');
            nameSpan.className = 'font-medium text-gray-900';
            nameSpan.textContent = task.text;
            header.appendChild(nameSpan);

            const hoursSpan = document.createElement('span');
            hoursSpan.className = 'text-xs text-gray-500';
            hoursSpan.textContent = `${task.hours}h`;
            header.appendChild(hoursSpan);

            card.appendChild(header);

            const meta = document.createElement('div');
            meta.className = 'mt-1 flex items-center gap-3 text-xs text-gray-600';

            // Priority label
            const pSpan = document.createElement('span');
            pSpan.textContent = 'P: ';
            const pVal = document.createElement('span');
            pVal.className = task.priority?.resolved ? 'text-emerald-600 font-medium' : 'text-rose-600';
            pVal.textContent = task.priority?.name ?? '?';
            if (!task.priority?.resolved) {
                pVal.textContent += ' (no resuelta)';
            }
            pSpan.appendChild(pVal);
            meta.appendChild(pSpan);

            // Status label
            const sSpan = document.createElement('span');
            sSpan.textContent = 'E: ';
            const sVal = document.createElement('span');
            sVal.className = task.status?.resolved ? 'text-emerald-600 font-medium' : 'text-rose-600';
            sVal.textContent = task.status?.name ?? '?';
            if (!task.status?.resolved) {
                sVal.textContent += ' (no resuelta)';
            }
            sSpan.appendChild(sVal);
            meta.appendChild(sSpan);

            // Developers label
            const dSpan = document.createElement('span');
            dSpan.textContent = 'D: ';
            const devs = Array.isArray(task.developers) && task.developers.length > 0
                ? task.developers.map((d) => {
                    const el = document.createElement('span');
                    el.className = d.resolved ? 'text-emerald-600' : (d.ambiguous ? 'text-amber-600' : 'text-rose-600');
                    el.textContent = d.name;
                    if (!d.resolved) {
                        el.textContent += d.ambiguous ? ' (ambigüo)' : ' (no resuelto)';
                    }
                    return el;
                })
                : [];

            if (devs.length > 0) {
                devs.forEach((el, idx) => {
                    if (idx > 0) {
                        const sep = document.createTextNode(', ');
                        dSpan.appendChild(sep);
                    }
                    dSpan.appendChild(el);
                });
            } else {
                const none = document.createElement('span');
                none.className = 'text-gray-400';
                none.textContent = 'sin developers';
                dSpan.appendChild(none);
            }
            meta.appendChild(dSpan);

            // Effective hours hint
            if (task.effective_hours !== undefined && task.effective_hours !== null && task.effective_hours !== task.hours) {
                const eHint = document.createElement('span');
                eHint.className = 'text-amber-600';
                eHint.textContent = ` → ${task.effective_hours}h efectivas`;
                meta.appendChild(eHint);
            }

            card.appendChild(meta);
            return card;
        };

        const renderIssueItem = (issueText) => {
            const li = document.createElement('li');
            li.className = 'flex items-center gap-2 text-sm text-rose-700';

            const icon = document.createElement('i');
            icon.className = 'bx bx-x-circle';
            li.appendChild(icon);

            const text = document.createElement('span');
            text.textContent = issueText;
            li.appendChild(text);

            return li;
        };

        previewBtn.addEventListener('click', async () => {
            const raw = textarea.value.trim();
            if (!raw) {
                showImportFlash('Pegá el JSON primero.', 'error');
                return;
            }

            let parsed;
            try {
                parsed = JSON.parse(raw);
            } catch (e) {
                showImportFlash('JSON inválido. Revisá la sintaxis.', 'error');
                return;
            }

            if (!parsed.tasks || !Array.isArray(parsed.tasks) || parsed.tasks.length === 0) {
                showImportFlash('El JSON debe contener un array "tasks" con al menos un elemento.', 'error');
                return;
            }

            hideImportFlash();
            previewBtn.disabled = true;
            previewBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Analizando...';

            try {
                const response = await fetch(routes.bulk_preview, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(parsed),
                });

                const payload = await response.json().catch(() => null);

                // Surface server validation errors instead of a generic message
                if (!response.ok) {
                    if (payload?.errors) {
                        const firstKey = Object.keys(payload.errors)[0];
                        const msgs = payload.errors[firstKey];
                        showImportFlash(Array.isArray(msgs) ? msgs[0] : String(msgs), 'error');
                    } else {
                        showImportFlash(payload?.message || 'Error del servidor.', 'error');
                    }
                    return;
                }

                const hasIssues = payload.has_issues === true;
                const issues = Array.isArray(payload.issues) ? payload.issues : [];
                const preview = Array.isArray(payload.preview) ? payload.preview : [];

                // Render preview cards with safe DOM APIs
                previewBody.textContent = '';
                if (preview.length > 0) {
                    previewCount.textContent = `${preview.length} tarea(s)`;
                    preview.forEach((task) => {
                        previewBody.appendChild(renderPreviewCard(task));
                    });
                    previewContainer.classList.remove('hidden');
                } else {
                    previewContainer.classList.add('hidden');
                }

                // Render issue items with safe DOM APIs
                issuesBody.textContent = '';
                if (hasIssues && issues.length > 0) {
                    issues.forEach((issue) => {
                        issuesBody.appendChild(renderIssueItem(issue));
                    });
                    issuesContainer.classList.remove('hidden');
                    storeBtn.disabled = true;
                } else {
                    issuesContainer.classList.add('hidden');
                    storeBtn.disabled = false;
                }

                if (!hasIssues) {
                    showImportFlash('Todas las referencias se resolvieron correctamente.', 'success');
                }
            } catch (error) {
                showImportFlash('Error de conexión.', 'error');
            } finally {
                previewBtn.disabled = false;
                previewBtn.innerHTML = '<i class="bx bx-search-alt"></i> Vista previa';
            }
        });

        storeBtn.addEventListener('click', async () => {
            const raw = textarea.value.trim();
            if (!raw) return;

            let parsed;
            try {
                parsed = JSON.parse(raw);
            } catch (e) {
                showImportFlash('JSON inválido.', 'error');
                return;
            }

            if (!parsed.tasks || !Array.isArray(parsed.tasks) || parsed.tasks.length === 0) {
                showImportFlash('El JSON debe contener un array "tasks".', 'error');
                return;
            }

            storeBtn.disabled = true;
            storeBtn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Importando...';
            hideImportFlash();

            try {
                const response = await fetch(routes.bulk_store, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(parsed),
                });

                const payload = await response.json().catch(() => null);

                if (!response.ok) {
                    const errors = payload?.errors || {};
                    const firstError = Object.values(errors).flat()[0] || 'Error desconocido';
                    showImportFlash(firstError, 'error');
                    storeBtn.disabled = false;
                    storeBtn.innerHTML = '<i class="bx bx-import"></i> Importar';
                    return;
                }

                // Sync the gantt with the authoritative server state
                if (payload && Array.isArray(payload.tasks)) {
                    syncGanttTasksWithServer(payload.tasks);
                }

                showImportFlash(`¡${payload.count || 'todas las'} tarea(s) importadas correctamente!`, 'success');
                storeBtn.innerHTML = '<i class="bx bx-check"></i> Importado';
                storeBtn.disabled = true;

                setTimeout(() => {
                    closeImportModal();
                }, 1500);
            } catch (error) {
                showImportFlash('Error de conexión.', 'error');
                storeBtn.disabled = false;
                storeBtn.innerHTML = '<i class="bx bx-import"></i> Importar';
            }
        });
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
        if (isApplyingServerSync) {
            return Promise.resolve({ action: 'updated' });
        }

        data._token = csrfToken;
        data.proposal_id = config.proposal_id;

        if (data.start_date) {
            data.start_date = getFormattedDate(data.start_date);
        }

        const applyServerTasks = (payload) => {
            if (!payload || !Array.isArray(payload.tasks)) {
                return;
            }

            syncGanttTasksWithServer(payload.tasks);
        };

        if (action === 'create' && routes.create) {
            return gantt.ajax.post(routes.create, data).then((response) => {
                const payload = JSON.parse(response.responseText || '{}');
                const persistedTaskId = payload.tid ?? payload.id ?? null;

                if (persistedTaskId !== null) {
                    completePendingDevelopersFlow(id, persistedTaskId);
                }

                // Cascade may have shifted the start_date of any task
                // that comes after the new one. Pull the full
                // authoritative list from the server so the gantt
                // reorders and re-dates without a page refresh.
                applyServerTasks(payload);

                return payload;
            });
        }

        if (action === 'update') {
            const updateRoute = resolveRoute(routes.update, id);

            if (updateRoute) {
                return gantt.ajax.put(updateRoute, data).then((response) => {
                    const payload = JSON.parse(response.responseText || '{}');
                    applyServerTasks(payload);
                    return response;
                });
            }
        }

        if (action === 'delete') {
            const deleteRoute = resolveRoute(routes.delete, id);

            if (deleteRoute) {
                return gantt.ajax.put(deleteRoute, data);
            }
        }
    });

    /**
     * Replace the gantt's local task rows with the authoritative
     * server payload. Tasks are matched by id; missing rows are
     * removed, unknown rows are inserted, and every row gets the
     * server's start_date / duration / sort_order so the timeline
     * and ordering stay in sync without a full reload.
     */
    const syncGanttTasksWithServer = (serverTasks) => {
        if (!Array.isArray(serverTasks) || serverTasks.length === 0) {
            return;
        }

        const serverIds = new Set();
        const orderedIds = [];

        isApplyingServerSync = true;

        try {
            gantt.silent(() => {
                serverTasks.forEach((row) => {
                    const serverId = Number(row.id);
                    if (!Number.isFinite(serverId) || serverId <= 0) {
                        return;
                    }

                    serverIds.add(serverId);
                    orderedIds.push(serverId);

                    // The server ships `start_date` as a formatted
                    // string, but the gantt keeps it as a Date object.
                    // `gantt.addTask` parses the string for us, but the
                    // `Object.assign` path below would overwrite the
                    // existing Date with a raw string and break the
                    // timeline render. Normalize once, up front.
                    if (typeof row.start_date === 'string' && row.start_date) {
                        let parsed = null;
                        if (typeof gantt.date.parseDate === 'function') {
                            parsed = gantt.date.parseDate(row.start_date, gantt.config.date_format);
                        } else if (typeof gantt.date.str_to_date === 'function') {
                            parsed = gantt.date.str_to_date(gantt.config.date_format)(row.start_date);
                        }
                        if ((!parsed || Number.isNaN(parsed.getTime())) && typeof Date !== 'undefined') {
                            parsed = new Date(row.start_date);
                        }
                        if (parsed && !Number.isNaN(parsed.getTime())) {
                            row.start_date = parsed;
                        }
                    }

                    if (gantt.isTaskExists(serverId)) {
                        const task = gantt.getTask(serverId);
                        Object.assign(task, row, {
                            id: serverId,
                            _persisted: true,
                        });
                    } else {
                        gantt.addTask({
                            ...row,
                            id: serverId,
                            _persisted: true,
                        });
                    }
                });

                const localIds = gantt.getTaskByTime()
                    .map((task) => Number(task.id))
                    .filter((taskId) => Number.isFinite(taskId) && taskId > 0);

                localIds.forEach((taskId) => {
                    if (!serverIds.has(taskId) && gantt.isTaskExists(taskId)) {
                        gantt.deleteTask(taskId);
                    }
                });

                const positions = new Map();
                orderedIds.forEach((taskId, index) => {
                    positions.set(taskId, index);
                });

                gantt.eachTask((task) => {
                    const order = positions.get(Number(task.id));
                    if (order === undefined) {
                        return;
                    }

                    task.sort_order = order + 1;
                });
            });
        } finally {
            isApplyingServerSync = false;
        }

        gantt.render();
    };

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

    const generatePrompt = () => {
        const statuses = Array.isArray(config.lightbox?.statuses)
            ? config.lightbox.statuses.map((s) => s.label).join(', ')
            : 'pendiente, en progreso, completado';

        const priorities = Array.isArray(config.lightbox?.priorities)
            ? config.lightbox.priorities.map((p) => p.label).join(', ')
            : 'baja, media, alta';

        const proposalName = config.proposal_name || `Proposal #${config.proposal_id}`;
        const proposalDesc = config.proposal_description || '(no description)';

        const developers = Array.isArray(config.developers) ? config.developers : [];
        const developerSection = developers.length > 0
            ? developers.map((d) => `  {"id": ${d.id}, "name": "${d.name}", "email": "${d.email}"}`).join(',\n')
            : '  // No developers saved yet. Ask the user to create them first.';

        return `You are a project management assistant helping generate tasks for the Gantt chart of "${proposalName}".

Proposal context: ${proposalDesc}

## Instructions
- Return ONLY valid JSON. No markdown, no code fences, no explanations.
- Use the exact JSON shape shown below.
- Allowed statuses: ${statuses}
- Allowed priorities: ${priorities}
- Each task MUST have a unique "text" name.
- "hours" must be a positive number.
- "start_date" is optional; if omitted the system will place it. Format: "YYYY-MM-DD HH:MM:SS".
- "developers" array references existing developers by id. Each entry requires "id" and "hours".

## Available developers (id, name, email)
[
${developerSection}
]

## Expected JSON shape
{
  "tasks": [
    {
      "text": "Task name",
      "hours": 8,
      "priority": "media",
      "status": "pendiente",
      "start_date": "2026-07-21 08:00:00",
      "developers": [
        {"id": 1, "hours": 4}
      ]
    }
  ]
}

Generate between 3 and 15 tasks for this proposal.`;
    };

    const showToast = (message, type) => {
        const toast = document.getElementById('gantt-toast');
        if (!toast) return;

        toast.textContent = message;
        toast.className =
            `fixed bottom-6 right-6 z-[100] flex items-center gap-2.5 rounded-xl px-5 py-3 text-sm font-medium text-white shadow-2xl transition-all duration-300 ${
                type === 'success'
                    ? 'bg-emerald-600'
                    : type === 'error'
                        ? 'bg-red-600'
                        : 'bg-gray-900'
            }`;
        toast.classList.remove('hidden', 'opacity-0');
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(8px)';
            setTimeout(() => {
                toast.classList.add('hidden');
                toast.style.opacity = '';
                toast.style.transform = '';
            }, 300);
        }, 3000);
    };

    const copyPromptToClipboard = async () => {
        const btn = document.getElementById('gantt-copy-prompt-btn');
        if (!btn) return;

        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="bx bx-loader-alt bx-spin text-lg"></i> Generating...';

        try {
            const prompt = generatePrompt();
            await navigator.clipboard.writeText(prompt);
            showToast('Prompt copied to clipboard!', 'success');
        } catch (err) {
            showToast('Could not copy to clipboard. Check permissions.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    };

    const bindCopyPromptButton = () => {
        const btn = document.getElementById('gantt-copy-prompt-btn');
        if (btn) {
            btn.addEventListener('click', copyPromptToClipboard);
        }
    };

    bindDevelopersModalEvents();
    bindImportModalEvents();
    bindCopyPromptButton();

    // ─── Hours Quick-Edit Table ───────────────────────────────

    const viewToggle = document.querySelector('[data-gantt-view-toggle]');
    const ganttWrapper = document.querySelector('[data-gantt-panel="gantt"]');
    const hoursContainer = document.getElementById('gantt-hours-table');
    const hoursBody = document.getElementById('gantt-hours-table-body');

    /**
     * Build a textContent-based safe string from a user-supplied value.
     */
    const esc = (str) => {
        const d = document.createElement('div');
        d.textContent = str ?? '';
        return d.innerHTML;
    };

    /**
     * Developer-name lookup keyed by id, populated by buildHoursTable
     * so save helpers that only have a developer_id can produce a
     * well-formed name.
     */
    let hoursDeveloperNames = {};

    const buildStatusLabel = (statuId) => {
        const s = (config.lightbox?.statuses || []).find(s => Number(s.key) === Number(statuId));
        return s ? s.label : '-';
    };

    const buildPriorityLabel = (priorityId) => {
        const p = (config.lightbox?.priorities || []).find(p => Number(p.key) === Number(priorityId));
        return p ? p.label : '-';
    };

    const buildHoursTable = () => {
        const tasks = gantt.getTaskByTime();
        if (!tasks.length) {
            hoursBody.innerHTML = '<div class="flex items-center justify-center py-16 text-gray-400"><i class="bx bx-inbox text-3xl mr-2"></i><span class="text-sm">No hay tareas cargadas.</span></div>';
            return;
        }

        // Collect unique developers — primary source: catalog, then merge task assignments
        const devMap = new Map();
        const catalogDevs = Array.isArray(config.developers) ? config.developers : [];
        catalogDevs.forEach(d => {
            if (d && d.id && !devMap.has(d.id)) {
                devMap.set(d.id, d.name || `Developer #${d.id}`);
            }
        });
        tasks.forEach(t => {
            (t.developers || []).forEach(d => {
                const n = normalizeDeveloperEntry(d);
                if (n && !devMap.has(n.developer_id)) {
                    devMap.set(n.developer_id, n.name);
                }
            });
        });

        const developers = Array.from(devMap.entries()).map(([id, name]) => ({ developer_id: id, name }));
        hoursDeveloperNames = Object.fromEntries(devMap);

        // Build table HTML
        let html = '<table class="min-w-full divide-y divide-gray-200">';

        // ── THEAD ──
        html += '<thead class="bg-gray-50"><tr>';
        html += '<th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600">Tarea</th>';
        html += '<th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Estado</th>';
        html += '<th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">Prioridad</th>';
        html += '<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Planif.</th>';
        developers.forEach(dev => {
            html += `<th class="px-3 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600" title="${esc(dev.name)}">${esc(dev.name)}</th>`;
        });
        html += '<th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-600">Total</th>';
        html += '</tr></thead>';

        // ── TBODY ──
        html += '<tbody class="divide-y divide-gray-100 bg-white">';

        const devTotals = {};
        developers.forEach(dev => { devTotals[dev.developer_id] = 0; });
        let grandTotal = 0;
        let planGrandTotal = 0;

        tasks.forEach(task => {
            const devHours = {};
            (task.developers || []).forEach(d => {
                const n = normalizeDeveloperEntry(d);
                if (n) {
                    devHours[n.developer_id] = n.hours;
                }
            });

            const taskDevTotal = Object.values(devHours).reduce((s, h) => s + (Number(h) || 0), 0);
            const planHrs = Number(task.hours) || 0;
            planGrandTotal += planHrs;
            grandTotal += taskDevTotal;

            html += `<tr class="hover:bg-indigo-50/50 transition-colors" data-task-id="${esc(task.id)}">`;
            html += `<td class="px-4 py-3 text-sm text-gray-900 max-w-[200px] truncate" title="${esc(task.text)}">${esc(task.text)}</td>`;

            // Status badge
            const sLabel = buildStatusLabel(task.statu_id);
            html += `<td class="px-4 py-3 text-center"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700">${esc(sLabel)}</span></td>`;

            // Priority badge
            const pLabel = buildPriorityLabel(task.priority_id);
            html += `<td class="px-4 py-3 text-center"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700">${esc(pLabel)}</span></td>`;

            // Planned hours (Planif.)
            html += `<td class="px-4 py-3 text-right font-mono text-sm text-gray-700" data-hr-plan="${esc(task.id)}">${planHrs.toFixed(1)}</td>`;

            // Developer columns (editable)
            developers.forEach(dev => {
                const hVal = devHours[dev.developer_id];
                const val = hVal !== undefined && hVal !== null ? hVal : '';
                html += `<td class="px-3 py-3 text-center">
                    <input type="number" min="0" step="0.25"
                           data-hr-task="${esc(task.id)}"
                           data-hr-dev="${esc(dev.developer_id)}"
                           class="w-20 rounded-md border border-gray-300 px-2 py-1 text-center text-sm font-mono focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200"
                           value="${esc(val)}" placeholder="—" />
                </td>`;
                if (hVal !== undefined && hVal !== null && Number(hVal) > 0) {
                    devTotals[dev.developer_id] += Number(hVal);
                }
            });

            // Total column
            const totalClass = taskDevTotal > planHrs ? 'text-amber-600' : 'text-gray-700';
            html += `<td class="px-4 py-3 text-right font-mono text-sm font-medium ${totalClass}" data-hr-row-total="${esc(task.id)}">${taskDevTotal.toFixed(1)}</td>`;
            html += '</tr>';
        });

        // ── TFOOT ──
        html += '</tbody><tfoot class="bg-gray-50 border-t-2 border-gray-200"><tr>';
        html += '<td class="px-4 py-3 text-sm font-semibold text-gray-700">Totales</td>';
        html += '<td></td><td></td>';
        html += `<td class="px-4 py-3 text-right font-mono text-sm font-semibold text-gray-700">${planGrandTotal.toFixed(1)}</td>`;
        // Reset ALL footer cells — including developers with zero total
        developers.forEach(dev => {
            const total = devTotals[dev.developer_id] || 0;
            html += `<td class="px-3 py-3 text-center font-mono text-sm font-semibold text-gray-700" data-hr-footer-dev="${esc(dev.developer_id)}">${total.toFixed(1)}</td>`;
        });
        html += `<td class="px-4 py-3 text-right font-mono text-sm font-semibold text-gray-700" data-hr-footer-grand>${grandTotal.toFixed(1)}</td>`;
        html += '</tr></tfoot></table>';

        hoursBody.innerHTML = html;

        // Attach change handler for inline save
        hoursBody.querySelectorAll('input[data-hr-task]').forEach(input => {
            input.addEventListener('change', onHoursCellChange);
        });
    };

    /**
     * Recalculate row totals and ALL footer cells from the current
     * input values without rebuilding the table.  Iterates the full
     * set of developer columns so cells that dropped to zero are
     * correctly reset.
     */
    const recalcTableTotals = () => {
        if (!hoursBody) return;

        const rows = hoursBody.querySelectorAll('tbody tr[data-task-id]');
        const footerDevCells = hoursBody.querySelectorAll('[data-hr-footer-dev]');
        const grandCell = hoursBody.querySelector('[data-hr-footer-grand]');

        // Collect all developer IDs that have a footer cell
        const allDevIds = Array.from(footerDevCells).map(cell => cell.dataset.hrFooterDev);

        // Init every developer to zero so dropped entries are reset
        const devTotals = {};
        allDevIds.forEach(id => { devTotals[id] = 0; });

        let grandTotal = 0;

        rows.forEach(row => {
            const taskId = row.dataset.taskId;
            const inputs = row.querySelectorAll('input[data-hr-task]');
            let rowTotal = 0;

            inputs.forEach(input => {
                const devId = input.dataset.hrDev;
                const h = input.value === '' ? null : Number(input.value);
                if (h !== null && !Number.isNaN(h) && h > 0) {
                    rowTotal += h;
                    devTotals[devId] = (devTotals[devId] || 0) + h;
                }
            });

            grandTotal += rowTotal;

            const totalCell = row.querySelector(`[data-hr-row-total="${taskId}"]`);
            const planCell = row.querySelector(`[data-hr-plan="${taskId}"]`);
            if (totalCell) {
                const planHrs = planCell ? Number(planCell.textContent) || 0 : 0;
                totalCell.textContent = rowTotal.toFixed(1);
                totalCell.className = `px-4 py-3 text-right font-mono text-sm font-medium ${rowTotal > planHrs ? 'text-amber-600' : 'text-gray-700'}`;
            }
        });

        // Update ALL footer developer cells — zero totals included
        allDevIds.forEach(devId => {
            const cell = hoursBody.querySelector(`[data-hr-footer-dev="${devId}"]`);
            if (cell) {
                cell.textContent = (devTotals[devId] || 0).toFixed(1);
            }
        });

        if (grandCell) {
            grandCell.textContent = grandTotal.toFixed(1);
        }
    };

    /**
     * Handle cell change: save to server via the shared helper, then
     * reconcile table state — roll back on failure, apply server
     * hours on success, and refresh totals.
     */
    const onHoursCellChange = async (event) => {
        const input = event.target;
        const taskId = input.dataset.hrTask;
        const devId = Number(input.dataset.hrDev);

        if (!taskId || !Number.isFinite(devId)) return;

        // Collect all inputs for this task
        const taskInputs = hoursBody.querySelectorAll(`input[data-hr-task="${taskId}"]`);
        const existingTask = gantt.getTask(taskId);
        const existingDevIds = existingTask ? new Set((existingTask.developers || []).map(d => {
            const n = normalizeDeveloperEntry(d);
            return n ? n.developer_id : null;
        }).filter(Boolean)) : new Set();

        const payloadDevs = [];
        taskInputs.forEach(inp => {
            const dId = Number(inp.dataset.hrDev);
            const h = inp.value === '' ? null : Number(inp.value);
            const hasValue = h !== null && !Number.isNaN(h);
            const wasAssigned = existingDevIds.has(dId);

            // Only include developers that were previously assigned OR have a value
            if (wasAssigned || hasValue) {
                // For previously assigned devs with a blank cell, send 0 instead of
                // null — null tells the backend to preserve existing pivot hours,
                // which causes UI/backend divergence after a clear.
                const effectiveHours = wasAssigned && !hasValue ? 0 : (hasValue ? h : null);
                payloadDevs.push({
                    developer_id: dId,
                    hours: effectiveHours,
                });
            }
        });

        input.disabled = true;

        const result = await saveDevelopersForTask(taskId, payloadDevs);

        if (!result.ok) {
            // Roll back: rebuild the table from authoritative gantt
            // in-memory state so the UI cannot diverge from persisted data.
            showToast('Error al guardar. Revertido.', 'error');
            input.disabled = false;
            buildHoursTable();
            return;
        }

        // Success — update Plan cell from server-confirmed hours
        const planCell = hoursBody.querySelector(`[data-hr-plan="${taskId}"]`);
        const task = gantt.getTask(taskId);
        if (planCell && task) {
            const serverHrs = Number(result.hours ?? task.hours ?? 0);
            planCell.textContent = serverHrs.toFixed(1);
        }

        // Reconcile the changed input value with what we actually sent, so the
        // UI cell matches the backend state (especially important when blank was
        // sent as 0 — the server saved 0, so the input must show 0, not blank).
        const savedPayload = payloadDevs.find(p => p.developer_id === devId);
        if (savedPayload) {
            input.value = savedPayload.hours !== null && savedPayload.hours !== undefined
                ? String(savedPayload.hours)
                : '';
        }

        // Refresh all table totals from current (now authoritative) input values
        recalcTableTotals();
        input.disabled = false;
    };

    // ─── View Toggle ──────────────────────────────────────

    if (viewToggle) {
        viewToggle.addEventListener('click', (event) => {
            const btn = event.target.closest('[data-gantt-view]');
            if (!btn) return;

            const view = btn.dataset.ganttView;

            // Update toggle active state
            viewToggle.querySelectorAll('[data-gantt-view]').forEach(b => {
                b.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                b.classList.add('text-gray-500');
            });
            btn.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
            btn.classList.remove('text-gray-500');

            // Show/hide panels
            const showGantt = view === 'gantt';

            if (ganttWrapper) {
                ganttWrapper.classList.toggle('hidden', !showGantt);
            }

            if (hoursContainer) {
                hoursContainer.classList.toggle('hidden', showGantt);
            }

            // Build table on first switch to hours view
            if (!showGantt && hoursBody) {
                buildHoursTable();
            }
        });

        // Also rebuild table when the gantt fires a parse event (new data loaded)
        gantt.attachEvent('onParse', function () {
            if (hoursContainer && !hoursContainer.classList.contains('hidden')) {
                buildHoursTable();
            }
        });
    }
}
