<!-- certificados.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Certificados</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'certificados'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Certificados';
            $pageSubtitle = 'Listado de certificados de calibración';
            $headerActionsHtml = '<a href="nuevo-certificado.php" class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2"><span aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>Nuevo Certificado</a>';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="card glass p-3 rounded-lg">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                    <div>
                        <div id="list-meta" class="text-muted small">Mostrando 0 certificados</div>
                        <div id="context-info" class="text-muted small"></div>
                    </div>
                    <div id="pagination-controls" class="d-flex align-items-center gap-2">
                        <span id="page-indicator" class="text-muted small">Página 1</span>
                        <div class="btn-group" role="group" aria-label="Paginación">
                            <button id="prev-btn" class="btn btn-secondary btn-sm" type="button" disabled>Anterior</button>
                            <button id="next-btn" class="btn btn-secondary btn-sm" type="button" disabled>Siguiente</button>
                        </div>
                        <button id="refresh-btn" class="btn btn-outline-secondary btn-sm" type="button" aria-label="Refrescar">Refrescar</button>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function () {
        // Verificar autenticación (requiere rol admin)
        try {
            Auth.requireAuth('admin');
        } catch (e) {
            return; // Ya redirige automáticamente
        }

        const PAGE_SIZE = 50;

        const els = {
            tbody: document.getElementById('certificates-tbody'),
            alertBox: document.getElementById('alert-placeholder'),
            listMeta: document.getElementById('list-meta'),
            contextInfo: document.getElementById('context-info'),
            pageIndicator: document.getElementById('page-indicator'),
            prevBtn: document.getElementById('prev-btn'),
            nextBtn: document.getElementById('next-btn'),
            refreshBtn: document.getElementById('refresh-btn'),
            paginationControls: document.getElementById('pagination-controls'),
        };

        const state = {
            certificates: [],
            limit: PAGE_SIZE,
            offset: 0,
            total: 0,
            totalKnown: false,
            mode: 'all',
            identifiers: {
                clientId: null,
                userProfileId: null,
            },
            clientNameCache: new Map(),
            clientsLoaded: false,
            currentClientName: null,
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
            const msg = message ? escapeHtml(message) : 'No hay certificados registrados';
            els.tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">${msg}</td></tr>`;
        }

        function setErrorRow(message) {
            if (!els.tbody) return;
            const msg = message ? escapeHtml(message) : 'Error al cargar certificados';
            els.tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${msg}</td></tr>`;
        }

        function buildEquipmentCell(cert) {
            const serial = cert.equipment_serial_number || cert.equipment_serial || '';
            const brandModel = [cert.equipment_brand, cert.equipment_model].filter(Boolean).join(' ');
            if (serial) {
                const brandModelHtml = brandModel ? `<br><small class="text-muted">${escapeHtml(brandModel)}</small>` : '';
                return `${escapeHtml(serial)}${brandModelHtml}`;
            }
            if (cert.equipment_id) {
                return `<span class="text-muted">Equipo ${escapeHtml(cert.equipment_id)}</span>`;
            }
            return '<span class="text-muted">Equipo no identificado</span>';
        }

        function buildPdfCell(cert) {
            if (cert.pdf_url) {
                return `<a class="btn btn-sm btn-primary" href="${escapeHtml(cert.pdf_url)}" target="_blank" rel="noopener">Descargar</a>`;
            }
            const id = encodeURIComponent(cert.id || '');
            const token = encodeURIComponent(localStorage.getItem('token') || '');
            return `
                <div class="btn-group btn-group-sm" role="group">
                    <a class="btn btn-primary" href="api/certificates/pdf_fpdf.php?id=${id}&action=download&token=${token}" target="_blank" rel="noopener">Descargar</a>
                    <a class="btn btn-outline-secondary" href="api/certificates/pdf_fpdf.php?id=${id}&action=view&token=${token}" target="_blank" rel="noopener" title="Ver">Ver</a>
                </div>`;
        }

        function renderRows() {
            if (!els.tbody) return;
            if (!state.certificates.length) {
                setEmptyRow('No hay certificados registrados');
                return;
            }

            const rows = state.certificates.map((cert) => {
                const clientName = cert.client_name
                    || state.clientNameCache.get(cert.client_id)
                    || state.currentClientName
                    || cert.client_id
                    || '(sin cliente)';

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
                            <a class="btn btn-sm btn-secondary" href="editar-certificado.php?id=${encodeURIComponent(cert.id || '')}">Editar</a>
                            <button class="btn btn-sm btn-secondary" disabled>Ver QR</button>
                            <button class="btn btn-sm btn-outline" disabled>Eliminar</button>
                        </td>
                    </tr>`;
            }).join('');

            els.tbody.innerHTML = rows;
        }

        function currentPage() {
            return Math.floor(state.offset / state.limit) + 1;
        }

        function updateContextInfo() {
            if (!els.contextInfo) return;
            if (state.mode === 'all') {
                els.contextInfo.textContent = 'Vista de administrador: todos los certificados (50 por página).';
            } else if (state.mode === 'client') {
                const label = state.currentClientName || state.identifiers.clientId || '';
                els.contextInfo.textContent = label
                    ? `Certificados del cliente: ${label}`
                    : 'Certificados por cliente.';
            } else {
                const label = state.identifiers.userProfileId || '';
                els.contextInfo.textContent = label
                    ? `Certificados asociados al usuario: ${label}`
                    : 'Certificados del usuario registrado.';
            }
        }

        function updatePagination() {
            const count = state.certificates.length;
            if (state.mode === 'all') {
                const start = count === 0 ? 0 : state.offset + 1;
                const end = count === 0 ? 0 : state.offset + count;
                if (els.listMeta) {
                    if (count === 0 && state.total === 0) {
                        els.listMeta.textContent = 'No hay certificados registrados.';
                    } else if (state.totalKnown) {
                        const noun = state.total === 1 ? 'certificado' : 'certificados';
                        els.listMeta.textContent = `Mostrando ${start}–${end} de ${state.total} ${noun}`;
                    } else {
                        els.listMeta.textContent = `Mostrando ${start}–${end} certificados`;
                    }
                }

                if (els.paginationControls) {
                    els.paginationControls.classList.remove('d-none');
                }

                if (els.pageIndicator) {
                    const page = currentPage();
                    if (state.totalKnown) {
                        const totalPages = Math.max(1, Math.ceil(state.total / state.limit));
                        els.pageIndicator.textContent = `Página ${page} de ${totalPages}`;
                    } else {
                        els.pageIndicator.textContent = `Página ${page}`;
                    }
                }

                if (els.prevBtn) {
                    els.prevBtn.disabled = state.offset === 0;
                }

                if (els.nextBtn) {
                    const hasNext = state.totalKnown
                        ? (state.offset + state.limit) < state.total
                        : count === state.limit;
                    els.nextBtn.disabled = !hasNext;
                }
            } else {
                if (els.listMeta) {
                    if (count === 0) {
                        els.listMeta.textContent = 'No hay certificados registrados.';
                    } else {
                        const noun = count === 1 ? 'certificado' : 'certificados';
                        els.listMeta.textContent = `Mostrando ${count} ${noun}`;
                    }
                }
                if (els.paginationControls) {
                    els.paginationControls.classList.add('d-none');
                }
                if (els.pageIndicator) {
                    els.pageIndicator.textContent = '';
                }
            }

            updateContextInfo();
        }

        async function fetchJson(url) {
            return await Auth.fetchWithAuth(url);
        }

        // Extrae la carga útil desde respuestas del estilo { ok: true, data: ... }
        function unwrap(payload) {
            if (payload && typeof payload === 'object' && 'data' in payload) {
                return payload.data;
            }
            return payload;
        }

        async function resolveClientName(clientId) {
            if (!clientId) return null;
            if (state.clientNameCache.has(clientId)) {
                return state.clientNameCache.get(clientId);
            }
            if (state.clientsLoaded) {
                return null;
            }
            try {
                const raw = await fetchJson('api/clients.php?action=list&limit=500&offset=0');
                const clients = unwrap(raw);
                if (Array.isArray(clients)) {
                    for (const client of clients) {
                        if (client?.id) {
                            state.clientNameCache.set(client.id, client.name || client.id);
                        }
                    }
                }
            } catch (err) {
                console.warn('No se pudo cargar el catálogo de clientes', err);
            } finally {
                state.clientsLoaded = true;
            }
            return state.clientNameCache.get(clientId) ?? null;
        }

        async function loadAllCertificates() {
            setLoadingRow();
            clearAlert();
            try {
                const url = `api/certificates.php?action=listAll&limit=${state.limit}&offset=${state.offset}`;
                const raw = await fetchJson(url);
                const data = unwrap(raw);
                const items = Array.isArray(data?.items) ? data.items : (Array.isArray(data) ? data : []);
                state.certificates = items;
                const pagination = data?.pagination ?? {};
                const totalCandidate = Number(pagination.total);
                state.totalKnown = Number.isFinite(totalCandidate) && totalCandidate >= 0;
                state.total = state.totalKnown ? totalCandidate : state.offset + items.length;
                state.currentClientName = null;

                for (const item of items) {
                    if (item?.client_id && item?.client_name) {
                        state.clientNameCache.set(item.client_id, item.client_name);
                    }
                }

                renderRows();
                updatePagination();
                updateUrlState();

                if (!items.length) {
                    showAlert('No se encontraron certificados.', 'info');
                }
            } catch (err) {
                console.error(err);
                state.certificates = [];
                state.total = 0;
                state.totalKnown = true;
                setErrorRow('Error al cargar certificados');
                updatePagination();
                showAlert(err?.message || 'Error al cargar certificados', 'danger');
            }
        }

        async function loadCertificatesForClient(clientId) {
            if (!clientId) {
                state.certificates = [];
                state.total = 0;
                state.totalKnown = true;
                state.currentClientName = null;
                setEmptyRow('client_id es requerido para esta vista');
                updatePagination();
                showAlert('client_id es requerido para esta vista.', 'danger');
                return;
            }

            state.offset = 0;
            setLoadingRow();
            clearAlert();
            try {
                const url = `api/certificates.php?action=listByClientId&client_id=${encodeURIComponent(clientId)}&limit=${state.limit}&offset=0`;
                const raw = await fetchJson(url);
                const data = unwrap(raw);
                const items = Array.isArray(data) ? data : [];
                state.certificates = items;
                state.total = items.length;
                state.totalKnown = true;
                state.currentClientName = await resolveClientName(clientId) || null;
                if (state.currentClientName) {
                    state.clientNameCache.set(clientId, state.currentClientName);
                }

                renderRows();
                updatePagination();
                updateUrlState();

                if (!items.length) {
                    showAlert('Este cliente no tiene certificados registrados.', 'info');
                }
            } catch (err) {
                console.error(err);
                state.certificates = [];
                state.total = 0;
                state.totalKnown = true;
                state.currentClientName = null;
                setErrorRow('Error al cargar certificados');
                updatePagination();
                showAlert(err?.message || 'Error al cargar certificados', 'danger');
            }
        }

        async function loadCertificatesForUser(userProfileId) {
            if (!userProfileId) {
                state.certificates = [];
                state.total = 0;
                state.totalKnown = true;
                state.currentClientName = null;
                setEmptyRow('user_profile_id es requerido para esta vista');
                updatePagination();
                showAlert('user_profile_id es requerido para esta vista.', 'danger');
                return;
            }

            state.offset = 0;
            setLoadingRow();
            clearAlert();
            try {
                const url = `api/certificates.php?action=listForClientUser&user_profile_id=${encodeURIComponent(userProfileId)}&limit=${state.limit}&offset=0`;
                const raw = await fetchJson(url);
                const data = unwrap(raw);
                const items = Array.isArray(data) ? data : [];
                state.certificates = items;
                state.total = items.length;
                state.totalKnown = true;
                state.currentClientName = null;

                const clientIds = Array.from(new Set(items.map((c) => c.client_id).filter(Boolean)));
                if (clientIds.length) {
                    await resolveClientName(clientIds[0]);
                }

                renderRows();
                updatePagination();
                updateUrlState();

                if (!items.length) {
                    showAlert('No se encontraron certificados para este usuario.', 'info');
                }
            } catch (err) {
                console.error(err);
                state.certificates = [];
                state.total = 0;
                state.totalKnown = true;
                state.currentClientName = null;
                setErrorRow('Error al cargar certificados');
                updatePagination();
                showAlert(err?.message || 'Error al cargar certificados', 'danger');
            }
        }

        function updateUrlState() {
            const url = new URL(window.location.href);
            if (state.mode === 'all') {
                url.searchParams.delete('client_id');
                url.searchParams.delete('user_profile_id');
                if (state.offset > 0) {
                    url.searchParams.set('page', String(currentPage()));
                } else {
                    url.searchParams.delete('page');
                }
            } else if (state.mode === 'client') {
                if (state.identifiers.clientId) {
                    url.searchParams.set('client_id', state.identifiers.clientId);
                }
                url.searchParams.delete('user_profile_id');
                url.searchParams.delete('page');
            } else {
                if (state.identifiers.userProfileId) {
                    url.searchParams.set('user_profile_id', state.identifiers.userProfileId);
                }
                url.searchParams.delete('client_id');
                url.searchParams.delete('page');
            }
            history.replaceState({}, '', url);
        }

        function initContext() {
            const params = new URLSearchParams(window.location.search);
            const clientId = (params.get('client_id') || '').trim() || null;
            const userProfileId = (params.get('user_profile_id') || '').trim() || null;
            const pageParam = parseInt(params.get('page') || '1', 10);

            if (userProfileId) {
                state.mode = 'user';
                state.identifiers.userProfileId = userProfileId;
            } else if (clientId) {
                state.mode = 'client';
                state.identifiers.clientId = clientId;
            } else {
                state.mode = 'all';
                if (!Number.isNaN(pageParam) && pageParam > 1) {
                    state.offset = (pageParam - 1) * state.limit;
                }
            }

            state.currentClientName = null;
        }

        function attachEventListeners() {
            if (els.prevBtn) {
                els.prevBtn.addEventListener('click', () => {
                    if (state.mode !== 'all') return;
                    if (state.offset === 0) return;
                    state.offset = Math.max(0, state.offset - state.limit);
                    loadAllCertificates();
                });
            }

            if (els.nextBtn) {
                els.nextBtn.addEventListener('click', () => {
                    if (state.mode !== 'all') return;
                    const hasNext = state.totalKnown
                        ? (state.offset + state.limit) < state.total
                        : state.certificates.length === state.limit;
                    if (!hasNext) return;
                    state.offset += state.limit;
                    loadAllCertificates();
                });
            }

            if (els.refreshBtn) {
                els.refreshBtn.addEventListener('click', () => {
                    if (state.mode === 'all') {
                        loadAllCertificates();
                    } else if (state.mode === 'client') {
                        loadCertificatesForClient(state.identifiers.clientId);
                    } else {
                        loadCertificatesForUser(state.identifiers.userProfileId);
                    }
                });
            }
        }

        async function init() {
            initContext();
            attachEventListeners();
            updatePagination();

            if (state.mode === 'all') {
                await loadAllCertificates();
            } else if (state.mode === 'client') {
                await loadCertificatesForClient(state.identifiers.clientId);
            } else {
                await loadCertificatesForUser(state.identifiers.userProfileId);
            }
        }

        init().catch((err) => {
            console.error('Error inicializando certificados', err);
            state.certificates = [];
            setErrorRow('No se pudo inicializar la vista');
            updatePagination();
            showAlert(err?.message || 'No se pudo inicializar la vista', 'danger');
        });
    })();
    </script>
</body>
</html>