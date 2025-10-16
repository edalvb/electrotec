<?php /* equipos.php (UI dinámica) */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Equipos</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'equipos'; include __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Gestión de Equipos';
            $pageSubtitle = 'Administra el inventario de equipos de medición';
            $headerActionsHtml = <<<HTML
<div class="d-flex flex-wrap gap-2">
    <a id="openNewEquipmentBtn" href="nuevo-equipo.php" class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2" data-equipment-mode="create" aria-label="Crear nuevo equipo">
        <span aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>
        Nuevo Equipo
    </a>
    <button class="btn btn-outline-light btn-lg d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#equipmentTypesModal" aria-label="Gestionar tipos de equipo">
        <span aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6h16M4 12h10M4 18h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>
        Tipos de equipo
    </button>
</div>
HTML;
            include __DIR__ . '/partials/header.php';
            ?>

            <section class="card glass p-3 mb-4 rounded-lg">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="position-relative">
                            <input id="searchInput" type="text" class="form-control" placeholder="Buscar por serie, marca o modelo..." aria-label="Buscar equipos">
                            <span class="position-absolute" style="right:12px; top:50%; transform:translateY(-50%); color: rgba(255,255,255,0.7);" aria-hidden="true">
                                <!-- icon: search -->
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/><path d="M20 20l-3-3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 text-md-end">
                        <div class="d-inline-flex align-items-center gap-2">
                            <label for="clientSelect" class="form-label mb-0">Filtrar por cliente:</label>
                            <select id="clientSelect" class="form-select" style="min-width: 260px" aria-label="Seleccionar cliente"></select>
                        </div>
                    </div>
                </div>

                <div class="row mt-3 g-2 align-items-center">
                    <div class="col-12 col-md-7">
                        <div class="text-muted">
                            <span id="listMeta">Mostrando 0 de 0 equipos</span>
                            <span class="mx-2">•</span>
                            Filtro: <span id="currentClientName" class="badge badge-glass">Todos</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 text-md-end">
                        <div class="d-inline-flex gap-2">
                            <button id="refreshBtn" class="btn btn-secondary btn-sm d-inline-flex align-items-center gap-2" type="button" aria-label="Refrescar lista">
                                <span aria-hidden="true">
                                    <!-- icon: refresh -->
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 12a8 8 0 10-1.68 4.92" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 12V7m0 5h-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </span>
                                Refrescar
                            </button>
                            <button id="exportBtn" class="btn btn-secondary btn-sm d-inline-flex align-items-center gap-2" type="button" aria-label="Exportar a CSV" disabled>
                                <span aria-hidden="true">
                                    <!-- icon: download -->
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3v12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 11l4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 21h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </span>
                                Exportar CSV
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card glass p-3 rounded-lg">
                <div class="table-responsive">
                    <table class="table table-custom table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>Número de Serie</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Tipo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="equipmentTbody">
                            <tr id="loadingRow"><td colspan="5" class="text-center text-muted py-4">Cargando…</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>
            <?php include __DIR__ . '/partials/footer.php'; ?>
        </main>
    </div>
    <?php include_once 'partials/modal-manage-equipment-types.html'; ?>
    <?php include_once 'partials/modal-confirm-delete-equipment.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        // Verificar autenticación
        try {
            Auth.requireAuth('admin');
        } catch (e) {
            return;
        }

        const api = {
            clients: (limit = 100, offset = 0) => `api/clients.php?action=list&limit=${limit}&offset=${offset}`,
            equipmentAll: (limit = 50, offset = 0) => `api/equipment.php?action=list&limit=${limit}&offset=${offset}`,
            equipmentByClient: (clientId, limit = 50, offset = 0) => `api/equipment.php?action=listByClientId&client_id=${encodeURIComponent(clientId)}&limit=${limit}&offset=${offset}`,
            equipmentTypes: () => `api/equipment.php?action=listTypes`,
            createEquipment: () => `api/equipment.php?action=create`,
            updateEquipment: (id) => `api/equipment.php?action=update&id=${encodeURIComponent(id)}`,
            deleteEquipment: (id) => `api/equipment.php?action=delete&id=${encodeURIComponent(id)}`,
            createEquipmentType: () => `api/equipment.php?action=createType`,
            updateEquipmentType: (id) => `api/equipment.php?action=updateType&id=${encodeURIComponent(id)}`,
            deleteEquipmentType: (id) => `api/equipment.php?action=deleteType&id=${encodeURIComponent(id)}`,
        };

        const TABLE_COLUMN_COUNT = 5;

        const state = {
            clients: [],
            clientMap: new Map(),
            currentClientId: null,
            equipment: [],
            filtered: [],
            equipmentTypes: [],
            modalMode: 'create',
            editingEquipmentId: null,
            editingEquipment: null,
            equipmentMap: new Map(),
            equipmentToDelete: null,
        };

        const els = {
            tbody: document.getElementById('equipmentTbody'),
            search: document.getElementById('searchInput'),
            clientSelect: document.getElementById('clientSelect'),
            meta: document.getElementById('listMeta'),
            currentClientName: document.getElementById('currentClientName'),
            refreshBtn: document.getElementById('refreshBtn'),
            exportBtn: document.getElementById('exportBtn'),
            typeModal: document.getElementById('equipmentTypesModal'),
            typeError: document.getElementById('equipmentTypeManagerError'),
            typeTable: document.getElementById('equipmentTypeTableBody'),
            typeMeta: document.getElementById('equipmentTypeMeta'),
            newTypeName: document.getElementById('newTypeName'),
            addTypeBtn: document.getElementById('addTypeBtn'),
            deleteModal: document.getElementById('confirmDeleteEquipmentModal'),
            deleteName: document.getElementById('deleteEquipmentName'),
            deleteWarning: document.getElementById('deleteEquipmentWarning'),
            deleteError: document.getElementById('deleteEquipmentError'),
            deleteConfirmBtn: document.getElementById('confirmDeleteEquipmentBtn'),
        };

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, s => ({
                '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'
            }[s]));
        }

        async function fetchJson(url) {
            const response = await Auth.fetchWithAuth(url);
            // Auth.fetchWithAuth retorna un objeto con estructura {ok: true, data: [...]}
            // Extraer el array de data si existe, o retornar array vacío
            if (response && response.ok && response.data) {
                return Array.isArray(response.data) ? response.data : [];
            }
            // Si la respuesta es directamente un array (compatibilidad)
            return Array.isArray(response) ? response : [];
        }

        async function sendJson(url, { method = 'POST', body = null } = {}) {
            const options = { method };
            if (body !== null) {
                options.headers = { 'Content-Type': 'application/json' };
                options.body = typeof body === 'string' ? body : JSON.stringify(body);
            }
            return await Auth.fetchWithAuth(url, options);
        }

        function normalizeEquipment(row) {
            const certificateCount = Number(row?.certificate_count ?? row?.certificates_count ?? 0);
            const clientIds = Array.isArray(row?.client_ids) ? row.client_ids : [];
            const normalizedClientIds = [];
            if (clientIds.length > 0) {
                const unique = {};
                for (const candidate of clientIds) {
                    if (typeof candidate !== 'string') continue;
                    const value = candidate.trim();
                    if (value !== '') {
                        unique[value] = true;
                    }
                }
                for (const key of Object.keys(unique)) {
                    normalizedClientIds.push(key);
                }
            }

            return {
                id: row?.id !== undefined && row?.id !== null ? String(row.id) : null,
                serial_number: row?.serial_number ?? '',
                brand: row?.brand ?? '',
                model: row?.model ?? '',
                equipment_type_id: Number(row?.equipment_type_id ?? 0),
                equipment_type_name: row?.equipment_type_name ?? '',
                certificate_count: certificateCount,
                client_ids: normalizedClientIds,
                created_at: row?.created_at ?? null,
            };
        }

        function setDeleteModalError(message) {
            if (!els.deleteError) return;
            if (!message) {
                els.deleteError.textContent = '';
                els.deleteError.classList.add('d-none');
                return;
            }
            els.deleteError.textContent = message;
            els.deleteError.classList.remove('d-none');
        }

        function setDeleteModalWarning(message) {
            if (!els.deleteWarning) return;
            if (!message) {
                els.deleteWarning.textContent = '';
                els.deleteWarning.classList.add('d-none');
                return;
            }
            els.deleteWarning.textContent = message;
            els.deleteWarning.classList.remove('d-none');
        }

        async function loadClients(preselectId = null) {
            const list = await fetchJson(api.clients());
            state.clients = list;
            state.clientMap = new Map(list.map(c => [c.id, c.name]));
            populateClientSelect(list, preselectId);
        }

        async function loadEquipmentTypes() {
            const list = await fetchJson(api.equipmentTypes());
            state.equipmentTypes = Array.isArray(list)
                ? list.map(t => ({
                    id: Number(t?.id ?? 0),
                    name: String(t?.name ?? ''),
                    equipment_count: Number(t?.equipment_count ?? 0),
                }))
                : [];
            populateEquipmentTypeSelect();
            renderTypeManagerRows();
            updateTypeManagerMeta();
            return state.equipmentTypes;
        }

        function populateEquipmentTypeSelect() {
            if (!els.eqType) return;
            const sel = els.eqType;
            sel.innerHTML = '';
            const list = Array.isArray(state.equipmentTypes) ? state.equipmentTypes : [];
            for (const t of list) {
                const opt = document.createElement('option');
                opt.value = String(t.id);
                opt.textContent = t.name;
                sel.appendChild(opt);
            }
            if (!list.length) {
                const opt = document.createElement('option');
                opt.textContent = 'No hay tipos definidos';
                opt.disabled = true;
                opt.selected = true;
                sel.appendChild(opt);
            }
        }

        function renderTypeManagerRows() {
            if (!els.typeTable) return;
            const list = Array.isArray(state.equipmentTypes) ? state.equipmentTypes : [];
            if (list.length === 0) {
                els.typeTable.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">No hay tipos registrados.</td></tr>';
                return;
            }

            const rowsHtml = list.map((t) => {
                const count = Number(t?.equipment_count ?? 0);
                const deleteAttrs = count > 0 ? ' disabled title="No se puede eliminar mientras existan equipos asociados."' : '';
                return `
                    <tr data-type-id="${t.id}">
                        <td>
                            <input type="text" class="form-control form-control-sm type-name-input" value="${escapeHtml(t?.name ?? '')}" aria-label="Nombre del tipo de equipo" />
                        </td>
                        <td class="text-center">
                            <span class="badge badge-glass">${count}</span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-primary" data-type-action="save">Guardar</button>
                                <button type="button" class="btn btn-outline-danger" data-type-action="delete"${deleteAttrs}>Eliminar</button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');

            els.typeTable.innerHTML = rowsHtml;
        }

        function updateTypeManagerMeta() {
            if (!els.typeMeta) return;
            const list = Array.isArray(state.equipmentTypes) ? state.equipmentTypes : [];
            if (list.length === 0) {
                els.typeMeta.textContent = 'Sin tipos registrados.';
                return;
            }

            const total = list.length;
            const inUse = list.filter(t => Number(t?.equipment_count ?? 0) > 0).length;
            const label = total === 1 ? 'tipo' : 'tipos';
            els.typeMeta.textContent = `${total} ${label} registrados • ${inUse} en uso`;
        }

        function setTypeManagerError(message) {
            if (!els.typeError) return;
            if (!message) {
                els.typeError.textContent = '';
                els.typeError.classList.add('d-none');
                return;
            }
            els.typeError.textContent = message;
            els.typeError.classList.remove('d-none');
        }

        function updateAddTypeButtonState() {
            if (!els.addTypeBtn) return;
            const name = els.newTypeName?.value?.trim() || '';
            els.addTypeBtn.disabled = name === '';
        }

        async function refreshTypeManager() {
            if (els.typeTable) {
                els.typeTable.innerHTML = '<tr><td colspan="3" class="text-center text-muted py-4">Cargando…</td></tr>';
            }
            setTypeManagerError('');
            try {
                await loadEquipmentTypes();
            } catch (e) {
                setTypeManagerError(e.message || 'Error al cargar los tipos de equipo.');
                if (els.typeTable) {
                    els.typeTable.innerHTML = '<tr><td colspan="3" class="text-center text-danger py-4">Error al cargar</td></tr>';
                }
            } finally {
                updateAddTypeButtonState();
            }
        }

        function populateClientSelect(list, preselectId) {
            els.clientSelect.innerHTML = '';
            const optAll = document.createElement('option');
            optAll.value = '';
            optAll.textContent = 'Todos los clientes';
            if (!preselectId) optAll.selected = true;
            els.clientSelect.appendChild(optAll);

            if (Array.isArray(list) && list.length > 0) {
                for (const c of list) {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name;
                    if (preselectId && preselectId === c.id) {
                        opt.selected = true;
                        optAll.selected = false;
                    }
                    els.clientSelect.appendChild(opt);
                }
            }

            updateCurrentClientName();
        }

        function updateCurrentClientName() {
            if (!els.currentClientName) return;
            if (!state.currentClientId) {
                els.currentClientName.textContent = 'Todos';
                return;
            }
            const name = state.clientMap.get(state.currentClientId) || '—';
            els.currentClientName.textContent = name;
        }

        async function loadEquipment(clientId) {
            state.currentClientId = clientId || null;
            showLoading();
            try {
                const rows = state.currentClientId ? await fetchJson(api.equipmentByClient(state.currentClientId)) : await fetchJson(api.equipmentAll());
                const list = Array.isArray(rows) ? rows.map(normalizeEquipment) : [];
                state.equipment = list;
                state.equipmentMap = new Map(list.filter(item => item.id).map(item => [item.id, item]));
                applyFilter();
                updateCurrentClientName();
            } catch (e) {
                showError(e.message || 'Error cargando equipos');
            }
        }

        function showLoading() {
            els.tbody.innerHTML = `<tr><td colspan="${TABLE_COLUMN_COUNT}" class="text-center text-muted py-4">Cargando…</td></tr>`;
        }

        function showError(message) {
            els.tbody.innerHTML = `<tr><td colspan="${TABLE_COLUMN_COUNT}" class="text-center text-danger py-4">${escapeHtml(message || 'Error')}</td></tr>`;
            if (els.meta) els.meta.textContent = 'Mostrando 0 de 0 equipos';
            if (els.exportBtn) els.exportBtn.disabled = true;
        }

        function resetEquipmentForm() {
            if (els.eqId) els.eqId.value = '';
            if (els.eqSerial) els.eqSerial.value = '';
            if (els.eqBrand) els.eqBrand.value = '';
            if (els.eqModel) els.eqModel.value = '';
            if (els.eqType) {
                if (els.eqType.options.length > 0) {
                    els.eqType.selectedIndex = 0;
                }
            }
        }

        function fillEquipmentForm(equipment) {
            if (!equipment) return;
            if (els.eqId) els.eqId.value = equipment.id || '';
            if (els.eqSerial) els.eqSerial.value = equipment.serial_number || '';
            if (els.eqBrand) els.eqBrand.value = equipment.brand || '';
            if (els.eqModel) els.eqModel.value = equipment.model || '';
            if (els.eqType) {
                const target = String(equipment.equipment_type_id ?? '');
                if (target !== '') {
                    let exists = false;
                    Array.from(els.eqType.options).forEach(opt => {
                        if (opt.value === target) {
                            exists = true;
                        }
                    });
                    if (!exists && equipment.equipment_type_name) {
                        const opt = document.createElement('option');
                        opt.value = target;
                        opt.textContent = equipment.equipment_type_name;
                        els.eqType.appendChild(opt);
                    }
                    els.eqType.value = target;
                }
            }
        }

        function updateMeta() {
            const total = Array.isArray(state.equipment) ? state.equipment.length : 0;
            const showing = Array.isArray(state.filtered) ? state.filtered.length : 0;
            if (els.meta) {
                els.meta.textContent = `Mostrando ${showing} de ${total} equipos`;
            }
            if (els.exportBtn) {
                els.exportBtn.disabled = showing === 0;
            }
        }

        function renderRows() {
            if (!state.filtered || state.filtered.length === 0) {
                els.tbody.innerHTML = `<tr><td colspan="${TABLE_COLUMN_COUNT}" class="text-center text-muted py-4">Sin resultados</td></tr>`;
                return;
            }
            const rowsHtml = state.filtered.map(e => {
                const typeBadge = escapeHtml(e.equipment_type_name || e.equipment_type_id || '—');
                const sn = escapeHtml(e.serial_number || '');
                const brand = escapeHtml(e.brand || '');
                const model = escapeHtml(e.model || '');
                const equipmentId = escapeHtml(String(e.id || ''));
                const certCount = Number(e.certificate_count ?? 0);
                const certBadge = certCount > 0
                    ? `<span class="badge badge-glass me-2" title="Certificados asociados">${certCount} certificado${certCount === 1 ? '' : 's'}</span>`
                    : '';
                const deleteAttrs = certCount > 0
                    ? ' disabled aria-disabled="true" title="No se puede eliminar porque tiene certificados asociados."'
                    : '';
                return `
                    <tr data-equipment-id="${equipmentId}">
                        <td><span class="badge badge-glass">${sn || '—'}</span></td>
                        <td>${brand || '—'}</td>
                        <td>${model || '—'}</td>
                        <td><span class="badge badge-glass">${typeBadge}</span></td>
                        <td>
                            ${certBadge}
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="editar-equipo.php?id=${equipmentId}" class="btn btn-secondary">Editar</a>
                                <button type="button" class="btn btn-outline-danger" data-equipment-action="delete" data-equipment-id="${equipmentId}" data-equipment-cert-count="${certCount}"${deleteAttrs}>Eliminar</button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');
            els.tbody.innerHTML = rowsHtml;
        }

        function applyFilter() {
            const q = (els.search?.value || '').toLowerCase().trim();
            if (!q) {
                state.filtered = state.equipment.slice();
            } else {
                state.filtered = state.equipment.filter(e => {
                    const sn = String(e.serial_number || '').toLowerCase();
                    const brand = String(e.brand || '').toLowerCase();
                    const model = String(e.model || '').toLowerCase();
                    return sn.includes(q) || brand.includes(q) || model.includes(q);
                });
            }
            updateMeta();
            renderRows();
        }

        updateAddTypeButtonState();
        els.newTypeName?.addEventListener('input', () => updateAddTypeButtonState());
        els.newTypeName?.addEventListener('keydown', (ev) => {
            if (ev.key === 'Enter' && !ev.shiftKey) {
                ev.preventDefault();
                els.addTypeBtn?.click();
            }
        });

        // Eventos
        els.clientSelect.addEventListener('change', () => {
            const id = els.clientSelect.value;
            const url = new URL(window.location.href);
            if (id) {
                url.searchParams.set('client_id', id);
                loadEquipment(id);
            } else {
                url.searchParams.delete('client_id');
                loadEquipment(null);
            }
            history.replaceState({}, '', url);
        });
        els.search.addEventListener('input', () => applyFilter());

        // Acciones de barra
        els.refreshBtn?.addEventListener('click', () => {
            loadEquipment(state.currentClientId);
        });
        els.exportBtn?.addEventListener('click', () => {
            // Exporta la vista filtrada a CSV
            const rows = state.filtered || [];
            if (!rows.length) return;
            const headers = ['serial_number','brand','model','equipment_type'];
            const lines = [headers.join(',')];
            for (const e of rows) {
                const typeName = e.equipment_type_name || e.equipment_type_id || '';
                const vals = [e.serial_number, e.brand, e.model, typeName].map(v => {
                    const s = String(v ?? '');
                    return /[",\n]/.test(s) ? `"${s.replace(/"/g,'""')}"` : s;
                });
                lines.push(vals.join(','));
            }
            const blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = 'equipos.csv';
            document.body.appendChild(a); a.click(); a.remove();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        });

        // Inicio
        (async function init() {
            try {
                const urlCid = new URLSearchParams(window.location.search).get('client_id') || null;
                const preselect = urlCid && urlCid.trim() !== '' ? urlCid : null;
                await loadClients(preselect);
                await loadEquipment(preselect);
                updateMeta();
            } catch (e) {
                showError(e.message || 'Error inicializando');
            }
        })();

        // Modales: preparar contenido
        document.addEventListener('shown.bs.modal', async (ev) => {
            const modalId = ev.target?.id;
            if (modalId === 'equipmentTypesModal') {
                await refreshTypeManager();
                if (els.newTypeName) {
                    els.newTypeName.focus();
                }
            }
        });

        document.addEventListener('hidden.bs.modal', (ev) => {
            const modalId = ev.target?.id;
            if (modalId === 'equipmentTypesModal') {
                setTypeManagerError('');
                if (els.newTypeName) {
                    els.newTypeName.value = '';
                }
                updateAddTypeButtonState();
                return;
            }

            if (modalId === 'confirmDeleteEquipmentModal') {
                state.equipmentToDelete = null;
                setDeleteModalError('');
                setDeleteModalWarning('');
                if (els.deleteConfirmBtn) {
                    els.deleteConfirmBtn.removeAttribute('disabled');
                }
                return;
            }

            // No hay más modal de nuevo/editar equipo
        });

        // Guardar equipo y manejar acciones del modal de tipos
        document.addEventListener('click', async (ev) => {
            if (!(ev.target instanceof Element)) return;

            const createTrigger = ev.target.closest('[data-equipment-mode="create"]');
            if (createTrigger instanceof HTMLElement) {
                state.modalMode = 'create';
                state.editingEquipment = null;
                state.editingEquipmentId = null;
            }

            const equipmentActionBtn = ev.target.closest('[data-equipment-action]');
            if (equipmentActionBtn instanceof HTMLElement) {
                const action = equipmentActionBtn.dataset.equipmentAction;
                const equipmentId = equipmentActionBtn.dataset.equipmentId || '';
                if (!equipmentId) return;

                // edición ahora via página dedicada (link en el render)

                if (action === 'delete') {
                    const record = state.equipmentMap.get(equipmentId) || state.equipment.find(item => String(item?.id ?? '') === equipmentId);
                    if (!record) {
                        window.alert('No se encontró información del equipo seleccionado.');
                        return;
                    }
                    const certCount = Number(record.certificate_count ?? 0);
                    if (certCount > 0) {
                        return;
                    }

                    state.equipmentToDelete = record;
                    if (els.deleteName) {
                        const serial = record.serial_number ? `#${record.serial_number}` : '';
                        const label = [serial, record.brand, record.model].filter(Boolean).join(' ');
                        els.deleteName.textContent = label || 'este equipo';
                    }
                    const linkCount = Array.isArray(record.client_ids) ? record.client_ids.length : 0;
                    if (linkCount > 0) {
                        const label = linkCount === 1 ? 'cliente' : 'clientes';
                        setDeleteModalWarning(`Se eliminará la vinculación con ${linkCount} ${label}.`);
                    } else {
                        setDeleteModalWarning('');
                    }
                    setDeleteModalError('');
                    const modal = bootstrap.Modal.getOrCreateInstance(els.deleteModal);
                    modal.show();
                    return;
                }
            }

            const addTypeBtn = ev.target.closest('#addTypeBtn');
            if (addTypeBtn instanceof HTMLButtonElement) {
                if (addTypeBtn.disabled) return;
                const name = els.newTypeName?.value?.trim() || '';
                if (name === '') return;
                setTypeManagerError('');
                addTypeBtn.disabled = true;
                try {
                    await sendJson(api.createEquipmentType(), { method: 'POST', body: { name } });
                    if (els.newTypeName) {
                        els.newTypeName.value = '';
                    }
                    await refreshTypeManager();
                } catch (e) {
                    setTypeManagerError(e.message || 'Error al crear el tipo.');
                } finally {
                    addTypeBtn.disabled = false;
                    updateAddTypeButtonState();
                }
                return;
            }

            const typeActionBtn = ev.target.closest('[data-type-action]');
            if (typeActionBtn instanceof HTMLElement) {
                const action = typeActionBtn.dataset.typeAction;
                const row = typeActionBtn.closest('tr[data-type-id]');
                const typeId = row ? parseInt(row.getAttribute('data-type-id') || '0', 10) : 0;
                if (!row || !typeId) return;

                if (action === 'save') {
                    const input = row.querySelector('.type-name-input');
                    if (!(input instanceof HTMLInputElement)) return;
                    const newName = input.value.trim();
                    if (newName === '') {
                        setTypeManagerError('El nombre es obligatorio.');
                        input.focus();
                        return;
                    }
                    typeActionBtn.setAttribute('disabled', 'true');
                    input.setAttribute('disabled', 'true');
                    try {
                        await sendJson(api.updateEquipmentType(typeId), { method: 'PUT', body: { id: typeId, name: newName } });
                        setTypeManagerError('');
                        await refreshTypeManager();
                    } catch (e) {
                        setTypeManagerError(e.message || 'Error al actualizar el tipo.');
                    } finally {
                        typeActionBtn.removeAttribute('disabled');
                        input.removeAttribute('disabled');
                    }
                    return;
                }

                if (action === 'delete') {
                    if (typeActionBtn.hasAttribute('disabled')) return;
                    const confirmed = window.confirm('¿Deseas eliminar este tipo de equipo? Esta acción no se puede deshacer.');
                    if (!confirmed) return;
                    typeActionBtn.setAttribute('disabled', 'true');
                    try {
                        await sendJson(api.deleteEquipmentType(typeId), { method: 'DELETE' });
                        setTypeManagerError('');
                        await refreshTypeManager();
                    } catch (e) {
                        setTypeManagerError(e.message || 'Error al eliminar el tipo.');
                    } finally {
                        typeActionBtn.removeAttribute('disabled');
                    }
                    return;
                }
            }

            const confirmDeleteBtn = ev.target.closest('#confirmDeleteEquipmentBtn');
            if (confirmDeleteBtn instanceof HTMLElement) {
                if (!state.equipmentToDelete || !state.equipmentToDelete.id) {
                    setDeleteModalError('No se encontró el equipo a eliminar.');
                    return;
                }
                confirmDeleteBtn.setAttribute('disabled', 'true');
                setDeleteModalError('');
                try {
                    await sendJson(api.deleteEquipment(state.equipmentToDelete.id), { method: 'DELETE' });
                    const modalInstance = bootstrap.Modal.getInstance(els.deleteModal) || bootstrap.Modal.getOrCreateInstance(els.deleteModal);
                    modalInstance.hide();
                    state.equipmentToDelete = null;
                    await loadEquipment(state.currentClientId);
                } catch (e) {
                    setDeleteModalError(e.message || 'Error al eliminar el equipo.');
                } finally {
                    confirmDeleteBtn.removeAttribute('disabled');
                }
                return;
            }

            // Ya no hay guardado vía modal
        });
    })();
    </script>
</body>
</html>