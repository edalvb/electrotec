<?php /* equipos.php (UI dinámica) */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Equipos</title>
    <link href="assets/css/global.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar text-center">
            <h5 class="my-4">ELECTROTEC<br><small class="text-muted">Sistema de certificados</small></h5>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="certificados.php" class="list-group-item list-group-item-action">Certificados</a>
                <a href="#" class="list-group-item list-group-item-action active">Equipos</a>
                <a href="clientes.php" class="list-group-item list-group-item-action">Clientes</a>
                <a href="gestion-usuarios.php" class="list-group-item list-group-item-action">Gestión de Usuarios</a>
            </div>
        </div>

        <div class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Gestión de Equipos</h2>
                    <p class="text-muted">Administra el inventario de equipos de medición</p>
                </div>
                <button class="btn btn-blue" data-bs-toggle="modal" data-bs-target="#newEquipmentModal">
                    + Nuevo Equipo
                </button>
            </header>

            <div class="row g-2 mb-3 align-items-center">
                <div class="col-12 col-md-6">
                    <input id="searchInput" type="text" class="form-control" placeholder="Buscar por serie, marca o modelo...">
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <div class="d-inline-flex align-items-center gap-2">
                        <label for="clientSelect" class="form-label mb-0">Cliente:</label>
                        <select id="clientSelect" class="form-select" style="min-width: 260px"></select>
                    </div>
                </div>
            </div>

            <div class="card card-custom p-3">
                <div class="table-responsive">
                    <table class="table table-custom table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>Número de Serie</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Tipo</th>
                                <th>Cliente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="equipmentTbody">
                            <tr id="loadingRow"><td colspan="6" class="text-center text-muted py-4">Cargando…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'partials/modal-new-equipment.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        const api = {
            clients: (limit = 100, offset = 0) => `api/clients.php?action=list&limit=${limit}&offset=${offset}`,
            equipmentByClient: (clientId, limit = 50, offset = 0) => `api/equipment.php?action=listByClientId&client_id=${encodeURIComponent(clientId)}&limit=${limit}&offset=${offset}`,
            equipmentTypes: () => `api/equipment.php?action=listTypes`,
            createEquipment: () => `api/equipment.php?action=create`,
        };

        const state = {
            clients: [],
            clientMap: new Map(),
            currentClientId: null,
            equipment: [],
            filtered: [],
        };

        const els = {
            tbody: document.getElementById('equipmentTbody'),
            search: document.getElementById('searchInput'),
            clientSelect: document.getElementById('clientSelect'),
            eqModal: document.getElementById('newEquipmentModal'),
            eqSerial: null,
            eqBrand: null,
            eqModel: null,
            eqType: null,
            eqSaveBtn: null,
            eqFormError: null,
        };

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, s => ({
                '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'
            }[s]));
        }

        async function fetchJson(url) {
            const res = await fetch(url);
            let payload = null;
            try { payload = await res.json(); } catch { /* ignore */ }
            if (!res.ok) {
                const msg = payload?.message ? `${payload.message} (HTTP ${res.status})` : `HTTP ${res.status}`;
                const details = payload?.details?.error ? `\nDetalles: ${payload.details.error}` : '';
                throw new Error(msg + details);
            }
            if (!payload || payload.ok !== true) {
                const details = payload?.details?.error ? `\nDetalles: ${payload.details.error}` : '';
                throw new Error((payload?.message || 'Respuesta inválida') + details);
            }
            return payload.data || [];
        }

        async function loadClients() {
            const list = await fetchJson(api.clients());
            state.clients = list;
            state.clientMap = new Map(list.map(c => [c.id, c.name]));
            populateClientSelect(list);
        }

        async function loadEquipmentTypes() {
            const list = await fetchJson(api.equipmentTypes());
            const sel = els.eqType;
            sel.innerHTML = '';
            for (const t of list) {
                const opt = document.createElement('option');
                opt.value = String(t.id);
                opt.textContent = t.name;
                sel.appendChild(opt);
            }
            if (!list.length) {
                const opt = document.createElement('option');
                opt.textContent = 'No hay tipos definidos';
                opt.disabled = true; opt.selected = true;
                sel.appendChild(opt);
            }
        }

        function populateClientSelect(list, preselectId) {
            els.clientSelect.innerHTML = '';
            if (!Array.isArray(list) || list.length === 0) {
                const opt = document.createElement('option');
                opt.textContent = 'Sin clientes';
                opt.disabled = true; opt.selected = true;
                els.clientSelect.appendChild(opt);
                return;
            }
            for (const c of list) {
                const opt = document.createElement('option');
                opt.value = c.id; opt.textContent = c.name;
                if (preselectId && preselectId === c.id) opt.selected = true;
                els.clientSelect.appendChild(opt);
            }
        }

        async function loadEquipment(clientId) {
            state.currentClientId = clientId;
            showLoading();
            try {
                const rows = await fetchJson(api.equipmentByClient(clientId));
                state.equipment = rows;
                applyFilter();
            } catch (e) {
                showError(e.message || 'Error cargando equipos');
            }
        }

        function showLoading() {
            els.tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Cargando…</td></tr>';
        }

        function showError(msg) {
            els.tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${escapeHtml(msg)}</td></tr>`;
        }

        function applyFilter() {
            const q = (els.search.value || '').toLowerCase().trim();
            if (!q) {
                state.filtered = state.equipment;
            } else {
                state.filtered = state.equipment.filter(e => {
                    const sn = (e.serial_number || '').toLowerCase();
                    const brand = (e.brand || '').toLowerCase();
                    const model = (e.model || '').toLowerCase();
                    return sn.includes(q) || brand.includes(q) || model.includes(q);
                });
            }
            renderRows();
        }

        function renderRows() {
            if (!state.filtered || state.filtered.length === 0) {
                els.tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Sin resultados</td></tr>';
                return;
            }
            const rowsHtml = state.filtered.map(e => {
                const sn = escapeHtml(e.serial_number);
                const brand = escapeHtml(e.brand);
                const model = escapeHtml(e.model);
                const typeBadge = escapeHtml(e.equipment_type_id ?? '—');
                const clientName = escapeHtml(state.clientMap.get(e.owner_client_id) || '—');
                return `
                <tr>
                  <td><span class="badge bg-secondary">${sn || '—'}</span></td>
                  <td>${brand || '—'}</td>
                  <td>${model || '—'}</td>
                  <td><span class="badge bg-secondary">${typeBadge}</span></td>
                  <td>${clientName}</td>
                  <td>
                    <button class="btn btn-sm btn-light" disabled>Editar</button>
                  </td>
                </tr>`;
            }).join('');
            els.tbody.innerHTML = rowsHtml;
        }

        // Eventos
        els.clientSelect.addEventListener('change', () => {
            const id = els.clientSelect.value;
            if (id) {
                const url = new URL(window.location.href);
                url.searchParams.set('client_id', id);
                history.replaceState({}, '', url);
                loadEquipment(id);
            }
        });
        els.search.addEventListener('input', () => applyFilter());

        // Inicio
        (async function init() {
            try {
                const urlCid = new URLSearchParams(window.location.search).get('client_id');
                await loadClients();
                let cid = urlCid;
                if (!cid && state.clients.length > 0) {
                    cid = state.clients[0].id;
                    // Asegurar selección visual
                    for (const opt of els.clientSelect.options) { if (opt.value === cid) { opt.selected = true; break; } }
                }
                if (cid) await loadEquipment(cid);
                else showError('No hay clientes para cargar equipos');
            } catch (e) {
                showError(e.message || 'Error inicializando');
            }
        })();

        // Modal: al abrir, cachear elementos y cargar tipos
        document.addEventListener('shown.bs.modal', async (ev) => {
            if (ev.target?.id !== 'newEquipmentModal') return;
            els.eqSerial = document.getElementById('eqSerial');
            els.eqBrand = document.getElementById('eqBrand');
            els.eqModel = document.getElementById('eqModel');
            els.eqType = document.getElementById('eqType');
            els.eqSaveBtn = document.getElementById('eqSaveBtn');
            els.eqFormError = document.getElementById('eqFormError');
            els.eqFormError?.classList.add('d-none');
            try { await loadEquipmentTypes(); } catch (e) { if (els.eqFormError) { els.eqFormError.textContent = e.message; els.eqFormError.classList.remove('d-none'); } }
        });

        // Guardar equipo
        document.addEventListener('click', async (ev) => {
            if (!(ev.target instanceof HTMLElement)) return;
            if (ev.target.id !== 'eqSaveBtn') return;
            if (!state.currentClientId) return;
            const payload = {
                serial_number: els.eqSerial?.value?.trim() || '',
                brand: els.eqBrand?.value?.trim() || '',
                model: els.eqModel?.value?.trim() || '',
                equipment_type_id: parseInt(els.eqType?.value || '0', 10) || 0,
                owner_client_id: state.currentClientId,
            };
            if (!payload.serial_number || !payload.brand || !payload.model || !payload.equipment_type_id) {
                if (els.eqFormError) { els.eqFormError.textContent = 'Completa los campos requeridos.'; els.eqFormError.classList.remove('d-none'); }
                return;
            }
            try {
                const res = await fetch(api.createEquipment(), { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                const json = await res.json();
                if (!res.ok || json.ok !== true) throw new Error(json?.message || `HTTP ${res.status}`);
                // Cerrar modal y refrescar lista
                const modal = bootstrap.Modal.getInstance(els.eqModal) || new bootstrap.Modal(els.eqModal);
                modal.hide();
                await loadEquipment(state.currentClientId);
            } catch (e) {
                if (els.eqFormError) { els.eqFormError.textContent = e.message || 'Error al guardar'; els.eqFormError.classList.remove('d-none'); }
            }
        });
    })();
    </script>
</body>
</html>