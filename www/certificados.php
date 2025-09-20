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
        <div class="sidebar">
            <div class="brand">
                <div class="brand-logo">E</div>
                <div>
                    <div class="brand-title">ELECTROTEC</div>
                    <div class="brand-subtitle">Sistema de certificados</div>
                </div>
            </div>
            <nav class="nav">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="#" class="nav-item active">Certificados</a>
                <a href="equipos.php" class="nav-item">Equipos</a>
                <a href="clientes.php" class="nav-item">Clientes</a>
                <a href="gestion-usuarios.php" class="nav-item">Gestión de Usuarios</a>
            </nav>
        </div>

        <div class="main-content flex-grow-1">
            <header class="main-header">
                <div>
                    <h2>Certificados</h2>
                    <p class="subtitle">Listado de certificados de calibración</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newCertificateModal">
                    + Añadir certificado
                </button>
            </header>

            <div class="card glass">
                <div id="alert-placeholder"></div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NÚMERO</th>
                                <th>EQUIPO</th>
                                <th>FECHAS</th>
                                <th>PDF</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody id="certificates-tbody">
                            <tr id="loading-row"><td colspan="5" class="text-center text-muted">Cargando certificados…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'partials/modal-new-certificate.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        const tbody = document.getElementById('certificates-tbody');
        const alertBox = document.getElementById('alert-placeholder');

        function showAlert(message, type = 'warning') {
            alertBox.innerHTML = `
                <div class="alert alert-${type}" role="alert">
                    ${escapeHtml(message)}
                    <button type="button" class="btn btn-sm btn-outline" onclick="this.parentElement.remove()" aria-label="Close">×</button>
                </div>`;
        }

        function escapeHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function formatDateYmd(ymd) {
            if (!ymd) return '';
            // Esperado: YYYY-MM-DD
            const m = String(ymd).match(/^(\d{4})-(\d{2})-(\d{2})$/);
            if (!m) return escapeHtml(ymd);
            const [_, y, mm, dd] = m;
            return `${parseInt(dd, 10)}/${parseInt(mm, 10)}/${y}`;
        }

        async function fetchJson(url) {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            let payload = null;
            try { payload = await res.json(); } catch (_) {}
            if (!res.ok) {
                let msg = payload && payload.message ? payload.message : `HTTP ${res.status}`;
                if (payload && payload.details && payload.details.error) {
                    msg += ` — ${payload.details.error}`;
                }
                throw new Error(msg);
            }
            if (!payload || payload.ok !== true) {
                let msg = payload && payload.message ? payload.message : 'Respuesta inválida de API';
                if (payload && payload.details && payload.details.error) {
                    msg += ` — ${payload.details.error}`;
                }
                throw new Error(msg);
            }
            return payload.data;
        }

        function buildCertRow(cert, equipmentMap) {
            const eq = equipmentMap.get(cert.equipment_id);
            const eqTitle = eq ? `${eq.serial_number || '(sin SN)'}<br><small class="text-muted">${escapeHtml((eq.brand || '') + (eq.model ? ' ' + eq.model : ''))}</small>`
                               : `<span class="text-muted">(equipo desconocido)</span>`;
            const pdfCell = cert.pdf_url ?
                `<a class="btn btn-sm btn-primary" href="${escapeHtml(cert.pdf_url)}" target="_blank" rel="noopener">Descargar</a>` :
                `<span class="text-muted">No disponible</span>`;

            return `
                <tr>
                    <td>${escapeHtml(cert.certificate_number || '(sin número)')}</td>
                    <td>${eq ? escapeHtml(eq.serial_number || '(sin SN)') + '<br><small class=\"text-muted\">' + escapeHtml((eq.brand || '') + (eq.model ? ' ' + eq.model : '')) + '</small>' : eqTitle}</td>
                    <td>
                        Cal. ${formatDateYmd(cert.calibration_date)}<br>
                        <small class="text-muted">Próx. ${formatDateYmd(cert.next_calibration_date)}</small>
                    </td>
                    <td>${pdfCell}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" disabled>Editar</button>
                        <button class="btn btn-sm btn-secondary" disabled>Ver QR</button>
                        <button class="btn btn-sm btn-outline" disabled>Eliminar</button>
                    </td>
                </tr>`;
        }

        async function loadData() {
            const params = new URLSearchParams(window.location.search);
            const clientId = params.get('client_id');
            const userProfileId = params.get('user_profile_id');

            if (!clientId && !userProfileId) {
                tbody.innerHTML = '';
                showAlert('Agrega ?client_id=<UUID> o ?user_profile_id=<UUID> a la URL para cargar certificados.', 'info');
                return;
            }

            try {
                // 1) Obtener certificados según el contexto
                const certUrl = clientId
                    ? `api/certificates.php?action=listByClientId&client_id=${encodeURIComponent(clientId)}&limit=100&offset=0`
                    : `api/certificates.php?action=listForClientUser&user_profile_id=${encodeURIComponent(userProfileId)}&limit=100&offset=0`;

                const certificates = await fetchJson(certUrl);

                // 2) Obtener equipos para poder mostrar SN/Marca/Modelo
                const equipmentMap = new Map();
                if (clientId) {
                    const eqUrl = `api/equipment.php?action=listByClientId&client_id=${encodeURIComponent(clientId)}&limit=100&offset=0`;
                    const equipment = await fetchJson(eqUrl);
                    for (const e of equipment) equipmentMap.set(e.id, e);
                } else {
                    // user_profile_id: detectar client_id(s) desde certificados y traer equipos por cada cliente
                    const clientIds = Array.from(new Set(certificates.map(c => c.client_id).filter(Boolean)));
                    for (const cid of clientIds) {
                        try {
                            const eqUrl = `api/equipment.php?action=listByClientId&client_id=${encodeURIComponent(cid)}&limit=200&offset=0`;
                            const equipment = await fetchJson(eqUrl);
                            for (const e of equipment) equipmentMap.set(e.id, e);
                        } catch (err) {
                            // Continuar aunque falle cargar equipos de un cliente
                            console.warn('No se pudieron cargar equipos para client_id', cid, err);
                        }
                    }
                }

                // 3) Renderizar
                if (!certificates || certificates.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay certificados</td></tr>';
                    return;
                }

                const rows = certificates.map(c => buildCertRow(c, equipmentMap)).join('');
                tbody.innerHTML = rows;
            } catch (err) {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error al cargar certificados</td></tr>';
                showAlert(err.message || 'Error al cargar certificados', 'danger');
            }
        }

        loadData();
    })();
    </script>
</body>
</html>