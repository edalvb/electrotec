<!-- certificados.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Certificados</title>
    <link href="assets/css/global.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'certificados'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Certificados';
            $pageSubtitle = 'Listado de certificados de calibración';
            $headerActionsHtml = '<button class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newCertificateModal"><span aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>Nuevo Certificado</button>';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="card glass p-3 rounded-lg">
                <div class="row g-3 align-items-center mb-3">
                    <div class="col-12 col-md-7" id="client-filter-group">
                        <label for="client-select" class="form-label mb-1">Cliente</label>
                        <div class="d-flex gap-2">
                            <select id="client-select" class="form-select" aria-label="Seleccionar cliente" style="min-width: 240px"></select>
                            <button id="refresh-btn" class="btn btn-secondary" type="button">Refrescar</button>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 text-md-end">
                        <div class="text-muted small">
                            <span id="list-meta">Mostrando 0 certificados</span>
                            <span class="mx-2 d-none d-md-inline">•</span>
                            Cliente actual: <span id="current-client-name" class="badge badge-glass">—</span>
                        </div>
                    </div>
                </div>
                <div id="alert-placeholder"></div>
                <div class="table-responsive">
                    <table class="table table-custom table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>NÚMERO</th>
                                <th>EQUIPO</th>
                                <th>CLIENTE</th>
                                <th>FECHAS</th>
                                <th>PDF</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody id="certificates-tbody">
                            <tr id="loading-row"><td colspan="6" class="text-center text-muted">Cargando certificados…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>
    <?php include_once 'partials/modal-new-certificate.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function () {
        const els = {
            tbody: document.getElementById('certificates-tbody'),
            alertBox: document.getElementById('alert-placeholder'),
            clientSelect: document.getElementById('client-select'),
            clientFilterGroup: document.getElementById('client-filter-group'),
            listMeta: document.getElementById('list-meta'),
            currentClientName: document.getElementById('current-client-name'),
            refreshBtn: document.getElementById('refresh-btn'),
        };

        const state = {
            certificates: [],
            equipmentMap: new Map(),
            clients: [],
            clientMap: new Map(),
            currentClientId: null,
            context: {
                clientId: null,
                userProfileId: null,
            },
        };

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function clearAlert() {
            if (els.alertBox) {
                els.alertBox.innerHTML = '';
            }
        }

        function showAlert(message, type = 'warning') {
            if (!els.alertBox) return;
            els.alertBox.innerHTML = `
                <div class="alert alert-${type}" role="alert">
                    ${escapeHtml(message)}
                    <button type="button" class="btn btn-sm btn-outline" aria-label="Cerrar" onclick="this.parentElement.remove()">×</button>
                </div>`;
        }

        function formatDateYmd(ymd) {
            if (!ymd) return '';
            const m = String(ymd).match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (!m) return escapeHtml(ymd);
            const [, y, mm, dd] = m;
            return `${parseInt(dd, 10)}/${parseInt(mm, 10)}/${y}`;
        }

        function setLoadingRow() {
            if (!els.tbody) return;
            els.tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Cargando certificados…</td></tr>';
        }

        function setEmptyRow(message) {
            if (!els.tbody) return;
            const msg = message ? escapeHtml(message) : 'No hay certificados disponibles';
            els.tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">${msg}</td></tr>`;
        }

        function setErrorRow(message) {
            if (!els.tbody) return;
            const msg = message ? escapeHtml(message) : 'Error al cargar certificados';
            els.tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${msg}</td></tr>`;
        }

        function updateMeta(total, clientLabel) {
            if (els.listMeta) {
                const normalizedTotal = Number.isFinite(total) ? total : 0;
                const noun = normalizedTotal === 1 ? 'certificado' : 'certificados';
                els.listMeta.textContent = `Mostrando ${normalizedTotal} ${noun}`;
            }
            if (els.currentClientName) {
                els.currentClientName.textContent = clientLabel ? String(clientLabel) : '—';
            }
        }

        function buildEquipmentCell(cert) {
            const eq = state.equipmentMap.get(cert.equipment_id);
            if (eq) {
                const serial = escapeHtml(eq.serial_number || '(sin SN)');
                const brandModel = [eq.brand, eq.model].filter(Boolean).join(' ');
                const brandModelHtml = brandModel ? `<br><small class="text-muted">${escapeHtml(brandModel)}</small>` : '';
                return serial + brandModelHtml;
            }

            const fallbackSerial = cert.equipment_serial_number || cert.equipment_serial || cert.equipment_serialnumber || '';
            if (fallbackSerial) {
                const brandModel = [cert.equipment_brand, cert.equipment_model].filter(Boolean).join(' ');
                const brandModelHtml = brandModel ? `<br><small class="text-muted">${escapeHtml(brandModel)}</small>` : '';
                return `${escapeHtml(fallbackSerial)}${brandModelHtml}`;
            }

            return '<span class="text-muted">Equipo no identificado</span>';
        }

        function buildPdfCell(cert) {
            if (cert.pdf_url) {
                return `<a class="btn btn-sm btn-primary" href="${escapeHtml(cert.pdf_url)}" target="_blank" rel="noopener">Descargar</a>`;
            }
            return '<span class="text-muted">No disponible</span>';
        }

        function renderRows() {
            if (!els.tbody) return;
            if (!state.certificates.length) {
                setEmptyRow('No hay certificados para mostrar');
                return;
            }

            const rows = state.certificates.map((cert) => {
                const clientName = state.clientMap.get(cert.client_id) || cert.client_name || '(sin cliente)';
                return `
                    <tr>
                        <td>${escapeHtml(cert.certificate_number || '(sin número)')}</td>
                        <td>${buildEquipmentCell(cert)}</td>
                        <td>${escapeHtml(clientName)}</td>
                        <td>
                            Cal. ${formatDateYmd(cert.calibration_date)}<br>
                            <small class="text-muted">Próx. ${formatDateYmd(cert.next_calibration_date)}</small>
                        </td>
                        <td>${buildPdfCell(cert)}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary" disabled>Editar</button>
                            <button class="btn btn-sm btn-secondary" disabled>Ver QR</button>
                            <button class="btn btn-sm btn-outline" disabled>Eliminar</button>
                        </td>
                    </tr>`;
            }).join('');

            els.tbody.innerHTML = rows;
        }

        async function fetchJson(url) {
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            let payload = null;
            try {
                payload = await res.json();
            } catch (_) {
                // ignorar parse fallido, se maneja abajo
            }
            if (!res.ok) {
                let msg = payload?.message ? payload.message : `HTTP ${res.status}`;
                if (payload?.details?.error) {
                    msg += ` — ${payload.details.error}`;
                }
                throw new Error(msg);
            }
            if (!payload || payload.ok !== true) {
                let msg = payload?.message || 'Respuesta inválida de API';
                if (payload?.details?.error) {
                    msg += ` — ${payload.details.error}`;
                }
                throw new Error(msg);
            }
            return payload.data ?? [];
        }

        async function loadClients() {
            try {
                const url = 'api/clients.php?action=list&limit=200&offset=0';
                const clients = await fetchJson(url);
                state.clients = Array.isArray(clients) ? clients : [];
                state.clientMap = new Map(state.clients.map((c) => [c.id, c.name]));
                populateClientSelect(state.clients, state.context.clientId);
            } catch (err) {
                console.error('No se pudieron cargar clientes', err);
                showAlert(err?.message || 'No se pudieron cargar los clientes', 'danger');
                state.clients = [];
                state.clientMap = new Map();
                populateClientSelect([], null);
            }
        }

        function populateClientSelect(list, selectedId) {
            if (!els.clientSelect) return;
            els.clientSelect.innerHTML = '';

            if (!Array.isArray(list) || list.length === 0) {
                const opt = document.createElement('option');
                opt.textContent = 'Sin clientes disponibles';
                opt.disabled = true;
                opt.selected = true;
                els.clientSelect.appendChild(opt);
                els.clientSelect.disabled = true;
                return;
            }

            els.clientSelect.disabled = false;
            for (const client of list) {
                const opt = document.createElement('option');
                opt.value = client.id;
                opt.textContent = client.name || '(sin nombre)';
                if (selectedId && selectedId === client.id) {
                    opt.selected = true;
                }
                els.clientSelect.appendChild(opt);
            }
        }

        async function loadEquipmentForClient(clientId) {
            if (!clientId) return;
            const url = `api/equipment.php?action=listByClientId&client_id=${encodeURIComponent(clientId)}&limit=200&offset=0`;
            const equipment = await fetchJson(url);
            for (const e of equipment) {
                if (!e || typeof e !== 'object') continue;
                state.equipmentMap.set(e.id, e);
            }
        }

        function updateUrlParam(key, value) {
            const url = new URL(window.location.href);
            if (value) {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
            if (key === 'client_id') {
                url.searchParams.delete('user_profile_id');
            } else if (key === 'user_profile_id') {
                url.searchParams.delete('client_id');
            }
            history.replaceState({}, '', url);
        }

        async function loadCertificatesForClient(clientId) {
            if (!clientId) {
                setEmptyRow('Selecciona un cliente para ver certificados');
                updateMeta(0, '—');
                return;
            }

            state.currentClientId = clientId;
            setLoadingRow();
            clearAlert();
            try {
                const certUrl = `api/certificates.php?action=listByClientId&client_id=${encodeURIComponent(clientId)}&limit=200&offset=0`;
                const certificates = await fetchJson(certUrl);
                state.certificates = Array.isArray(certificates) ? certificates : [];
                state.equipmentMap = new Map();
                await loadEquipmentForClient(clientId);

                const clientLabel = state.clientMap.get(clientId) || '(sin cliente)';
                updateMeta(state.certificates.length, clientLabel);
                renderRows();
                if (!state.certificates.length) {
                    showAlert('Este cliente aún no tiene certificados registrados.', 'info');
                }
            } catch (err) {
                console.error(err);
                setErrorRow('Error al cargar certificados');
                showAlert(err?.message || 'Error al cargar certificados', 'danger');
            }
        }

        async function loadCertificatesForUser(userProfileId) {
            if (!userProfileId) {
                showAlert('user_profile_id es requerido para esta vista.', 'danger');
                setEmptyRow('Sin contexto de usuario');
                return;
            }

            state.currentClientId = null;
            setLoadingRow();
            clearAlert();

            try {
                const certUrl = `api/certificates.php?action=listForClientUser&user_profile_id=${encodeURIComponent(userProfileId)}&limit=200&offset=0`;
                const certificates = await fetchJson(certUrl);
                state.certificates = Array.isArray(certificates) ? certificates : [];

                state.equipmentMap = new Map();
                const clientIds = Array.from(new Set(state.certificates.map((c) => c.client_id).filter(Boolean)));
                for (const cid of clientIds) {
                    try {
                        await loadEquipmentForClient(cid);
                    } catch (err) {
                        console.warn('No se pudieron cargar equipos para client_id', cid, err);
                    }
                }

                const label = clientIds.length === 0
                    ? '—'
                    : clientIds.length === 1
                        ? (state.clientMap.get(clientIds[0]) || clientIds[0])
                        : 'Varios clientes';

                updateMeta(state.certificates.length, label);
                renderRows();
                if (!state.certificates.length) {
                    showAlert('No se encontraron certificados para este usuario.', 'info');
                }
            } catch (err) {
                console.error(err);
                setErrorRow('Error al cargar certificados');
                showAlert(err?.message || 'Error al cargar certificados', 'danger');
            }
        }

        function initEventListeners(isUserContext) {
            if (els.clientSelect && !isUserContext) {
                els.clientSelect.addEventListener('change', () => {
                    const clientId = els.clientSelect.value;
                    state.context.clientId = clientId || null;
                    updateUrlParam('client_id', clientId || null);
                    loadCertificatesForClient(clientId);
                });
            }

            if (els.refreshBtn) {
                els.refreshBtn.addEventListener('click', () => {
                    if (isUserContext) {
                        loadCertificatesForUser(state.context.userProfileId);
                    } else {
                        loadCertificatesForClient(state.currentClientId || state.context.clientId);
                    }
                });
            }
        }

        function initContext() {
            const params = new URLSearchParams(window.location.search);
            state.context.clientId = (params.get('client_id') || '').trim() || null;
            state.context.userProfileId = (params.get('user_profile_id') || '').trim() || null;
            const isUserContext = Boolean(state.context.userProfileId);

            if (isUserContext && els.clientFilterGroup) {
                els.clientFilterGroup.classList.add('d-none');
            }
            if (isUserContext && els.clientSelect) {
                els.clientSelect.disabled = true;
            }

            return isUserContext;
        }

        async function init() {
            const isUserContext = initContext();
            initEventListeners(isUserContext);

            await loadClients();

            if (isUserContext) {
                await loadCertificatesForUser(state.context.userProfileId);
                updateUrlParam('user_profile_id', state.context.userProfileId);
                return;
            }

            let clientId = state.context.clientId;
            if (clientId && !state.clientMap.has(clientId)) {
                clientId = null;
            }
            if (!clientId && state.clients.length > 0) {
                clientId = state.clients[0].id;
                if (els.clientSelect) {
                    els.clientSelect.value = clientId;
                }
                updateUrlParam('client_id', clientId);
            } else if (clientId && els.clientSelect) {
                els.clientSelect.value = clientId;
            }

            if (!clientId) {
                setEmptyRow('No hay clientes disponibles');
                showAlert('No existen clientes registrados para mostrar certificados.', 'info');
                updateMeta(0, '—');
                return;
            }

            await loadCertificatesForClient(clientId);
        }

        init().catch((err) => {
            console.error('Error inicializando certificados', err);
            setErrorRow('No se pudo inicializar la vista');
            showAlert(err?.message || 'No se pudo inicializar la vista', 'danger');
        });
    })();
    </script>
</body>
</html>