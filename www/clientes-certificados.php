<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Mis Certificados</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'certificados'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Mis Certificados';
            $pageSubtitle = 'Consulta y descarga de tus certificados';
            $headerActionsHtml = '<button class="btn btn-danger" onclick="Auth.logout()">Cerrar Sesión</button>';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="container-fluid px-4 pb-5">
                <div class="card glass p-3 rounded-lg">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                        <div>
                            <div id="list-meta" class="text-muted small">Mostrando 0 certificados</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
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
                                    <th>FECHAS</th>
                                    <th>PDF</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody id="certificates-tbody">
                                <tr><td colspan="5" class="text-center text-muted">Cargando certificados…</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card glass p-3 rounded-lg mt-4">
                    <h5 class="mb-3">Mis datos de acceso</h5>
                    <div id="profile-alert" class="alert d-none" role="alert"></div>
                    <form id="profile-form" class="row g-3">
                        <div class="col-md-6">
                            <label for="pf-username" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="pf-username" required>
                        </div>
                        <div class="col-12">
                            <hr>
                            <h6 class="mb-2">Cambiar contraseña (opcional)</h6>
                        </div>
                        <div class="col-md-4">
                            <label for="pf-password" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="pf-password" placeholder="Mín. 8 caracteres">
                        </div>
                        <div class="col-md-4">
                            <label for="pf-password-confirm" class="form-label">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="pf-password-confirm">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">Guardar cambios</button>
                        </div>
                    </form>
                </div>

                <div class="card glass p-3 rounded-lg mt-4">
                    <h5 class="mb-3">Mis datos personales</h5>
                    <div id="client-alert" class="alert d-none" role="alert"></div>
                    <form id="client-form" class="row g-3">
                        <div class="col-md-6">
                            <label for="cf-nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="cf-nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cf-ruc" class="form-label">RUC</label>
                            <input type="text" class="form-control" id="cf-ruc" disabled>
                        </div>
                        <div class="col-md-4">
                            <label for="cf-dni" class="form-label">DNI</label>
                            <input type="text" class="form-control" id="cf-dni" maxlength="8">
                        </div>
                        <div class="col-md-4">
                            <label for="cf-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="cf-email">
                        </div>
                        <div class="col-md-4">
                            <label for="cf-celular" class="form-label">Celular</label>
                            <input type="text" class="form-control" id="cf-celular" maxlength="9">
                        </div>
                        <div class="col-12">
                            <label for="cf-direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="cf-direccion" rows="2"></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">Guardar datos</button>
                        </div>
                    </form>
                </div>

            </div>
            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>

    <script>
    (function () {
        // Requiere rol cliente
        try { Auth.requireAuth('client'); } catch(e) { return; }

        const els = {
            tbody: document.getElementById('certificates-tbody'),
            listMeta: document.getElementById('list-meta'),
            refreshBtn: document.getElementById('refresh-btn'),
            alert: document.getElementById('alert-placeholder'),
            pAlert: document.getElementById('profile-alert'),
            pf: document.getElementById('profile-form'),
            pfUsername: document.getElementById('pf-username'),
            pfPassword: document.getElementById('pf-password'),
            pfPassword2: document.getElementById('pf-password-confirm'),
            cf: document.getElementById('client-form'),
            cAlert: document.getElementById('client-alert'),
            cfNombre: document.getElementById('cf-nombre'),
            cfRuc: document.getElementById('cf-ruc'),
            cfDni: document.getElementById('cf-dni'),
            cfEmail: document.getElementById('cf-email'),
            cfCelular: document.getElementById('cf-celular'),
            cfDireccion: document.getElementById('cf-direccion')
        };

        function escapeHtml(str){
            return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
        }
        function showAlert(msg, type='warning'){
            els.alert.innerHTML = `<div class="alert alert-${type}" role="alert">${escapeHtml(msg)}</div>`;
        }
        function clearAlert(){ els.alert.innerHTML = ''; }
        function setLoading(){ els.tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Cargando…</td></tr>'; }
        function setEmpty(){ els.tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay certificados</td></tr>'; }

        function buildEquipmentCell(cert) {
            const serial = cert.equipment_serial_number || cert.equipment_serial || '';
            const brandModel = [cert.equipment_brand, cert.equipment_model].filter(Boolean).join(' ');
            if (serial) {
                const brandModelHtml = brandModel ? `<br><small class="text-muted">${escapeHtml(brandModel)}</small>` : '';
                return `${escapeHtml(serial)}${brandModelHtml}`;
            }
            return '<span class="text-muted">Equipo</span>';
        }
        function buildPdfCell(cert) {
            const id = encodeURIComponent(cert.id || '');
            const token = encodeURIComponent(Auth.getToken() || '');
            return `
                <div class="btn-group btn-group-sm" role="group">
                    <a class="btn btn-primary" href="api/certificates/pdf_fpdf.php?id=${id}&action=download&token=${token}" target="_blank" rel="noopener">Descargar</a>
                    <a class="btn btn-outline-secondary" href="api/certificates/pdf_fpdf.php?id=${id}&action=view&token=${token}" target="_blank" rel="noopener" title="Ver">Ver</a>
                </div>`;
        }

        function renderRows(items){
            if (!items.length) { setEmpty(); return; }
            els.tbody.innerHTML = items.map(cert => `
                <tr>
                    <td>${escapeHtml(cert.certificate_number || '(sin número)')}</td>
                    <td>${buildEquipmentCell(cert)}</td>
                    <td>
                        Cal. ${escapeHtml(cert.calibration_date || '')}<br>
                        <small class="text-muted">Próx. ${escapeHtml(cert.next_calibration_date || '')}</small>
                    </td>
                    <td>${buildPdfCell(cert)}</td>
                    <td>
                        <a class="btn btn-sm btn-outline-secondary" href="api/certificates/sticker.php?id=${encodeURIComponent(cert.id || '')}" target="_blank" rel="noopener" title="Sticker">Sticker</a>
                        <a class="btn btn-sm btn-outline-secondary" href="ver-certificado.php?id=${encodeURIComponent(cert.id || '')}" title="Detalles">Detalles</a>
                    </td>
                </tr>`).join('');
        }

        async function loadCertificates(){
            setLoading();
            clearAlert();
            try {
                // Para clientes usamos el endpoint por usuario de cliente (user_profile_id). En este sistema, usamos user.id simple.
                const url = `api/certificates.php?action=listForMe&limit=200&offset=0`;
                const payload = await Auth.fetchWithAuth(url);
                const items = Array.isArray(payload?.data) ? payload.data : (Array.isArray(payload) ? payload : []);
                renderRows(items);
                const noun = items.length === 1 ? 'certificado' : 'certificados';
                els.listMeta.textContent = `Mostrando ${items.length} ${noun}`;
                if (!items.length) showAlert('No se encontraron certificados.', 'info');
            } catch (err) {
                console.error(err);
                setEmpty();
                showAlert(err?.message || 'Error al cargar certificados', 'danger');
            }
        }

        function showProfileAlert(msg, type='success'){
            els.pAlert.className = `alert alert-${type}`;
            els.pAlert.textContent = msg;
            els.pAlert.classList.remove('d-none');
            setTimeout(() => els.pAlert.classList.add('d-none'), 4000);
        }

        function showClientAlert(msg, type='success'){
            els.cAlert.className = `alert alert-${type}`;
            els.cAlert.textContent = msg;
            els.cAlert.classList.remove('d-none');
            setTimeout(() => els.cAlert.classList.add('d-none'), 4000);
        }

        async function loadProfile(){
            try {
                // Mostrar username y, adicionalmente, cargar datos del cliente asociado
                const payload = await Auth.fetchWithAuth('api/auth.php');
                const user = payload?.data?.user || null;
                if (user) els.pfUsername.value = user.username || '';
            } catch (e) { console.warn(e); }
        }

        async function loadClient(){
            try {
                const resp = await Auth.fetchWithAuth('api/clients.php?action=me');
                const client = resp?.data || null;
                if (!client) return;
                els.cfNombre.value = client.nombre || '';
                els.cfRuc.value = client.ruc || '';
                els.cfDni.value = client.dni || '';
                els.cfEmail.value = client.email || '';
                els.cfCelular.value = client.celular || '';
                els.cfDireccion.value = client.direccion || '';
            } catch (e) { console.warn(e); }
        }

        els.pf.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            try {
                const body = { username: els.pfUsername.value.trim() };
                const p1 = els.pfPassword.value;
                const p2 = els.pfPassword2.value;
                if (p1 || p2) {
                    if (p1.length < 8) { showProfileAlert('La nueva contraseña debe tener al menos 8 caracteres', 'danger'); return; }
                    if (p1 !== p2) { showProfileAlert('Las contraseñas no coinciden', 'danger'); return; }
                    body.password = p1;
                    body.password_confirm = p2;
                }
                const resp = await Auth.fetchWithAuth('api/users.php?action=me', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                if (resp?.ok) {
                    // actualizar localStorage username
                    const user = JSON.parse(localStorage.getItem('user') || '{}');
                    user.username = body.username;
                    localStorage.setItem('user', JSON.stringify(user));
                    showProfileAlert('Perfil actualizado');
                } else {
                    showProfileAlert(resp?.message || 'No se pudo actualizar', 'danger');
                }
            } catch (e) {
                showProfileAlert(e?.message || 'Error de red', 'danger');
            }
        });

        els.refreshBtn.addEventListener('click', loadCertificates);

        els.cf.addEventListener('submit', async (ev) => {
            ev.preventDefault();
            try {
                const body = {
                    nombre: els.cfNombre.value.trim(),
                    ruc: (els.cfRuc.value || '').trim(),
                    dni: els.cfDni.value.trim() || null,
                    email: els.cfEmail.value.trim() || null,
                    celular: els.cfCelular.value.trim() || null,
                    direccion: els.cfDireccion.value.trim() || null,
                };
                const resp = await Auth.fetchWithAuth('api/clients.php?action=updateMe', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                if (resp?.ok) {
                    showClientAlert('Datos actualizados');
                } else {
                    showClientAlert(resp?.message || 'No se pudo actualizar', 'danger');
                }
            } catch (e) {
                showClientAlert(e?.message || 'Error de red', 'danger');
            }
        });

        // init
        loadCertificates();
        loadProfile();
        loadClient();
    })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
