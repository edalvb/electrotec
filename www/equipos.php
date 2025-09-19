<?php /* equipos.php (UI dinámica) */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Equipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
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
        };

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, s => ({
                '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;'
            }[s]));
        }

        async function fetchJson(url) {
            const res = await fetch(url);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            if (!data || data.ok !== true) throw new Error(data?.message || 'Respuesta inválida');
            return data.data || [];
        }

        async function loadClients() {
            const list = await fetchJson(api.clients());
            state.clients = list;
            state.clientMap = new Map(list.map(c => [c.id, c.name]));
            populateClientSelect(list);
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
    })();
    </script>
</body>
</html>