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
    <div class="container" style="padding-top: 24px; padding-bottom: 40px;">
        <h1 class="mb-3">Mis Certificados</h1>
        <!-- Controles bajo el título -->
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProfile">Editar datos de acceso</button>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalClient">Editar datos personales</button>
            <button class="btn btn-outline-danger ms-auto" onclick="Auth.logout()">Cerrar sesión</button>
        </div>

            <div class="container-fluid px-0 pb-5">
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

                                <!-- Modales -->
                                <div class="modal fade" id="modalProfile" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar datos de acceso</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div id="profile-alert" class="alert d-none" role="alert"></div>
                                                <form id="profile-form" class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="pf-username" class="form-label">Usuario</label>
                                                        <input type="text" class="form-control" id="pf-username" required>
                                                    </div>
                                                    <div class="col-12"><hr><h6 class="mb-2">Cambiar contraseña (opcional)</h6></div>
                                                    <div class="col-md-6">
                                                        <label for="pf-password" class="form-label">Nueva contraseña</label>
                                                        <input type="password" class="form-control" id="pf-password" placeholder="Mín. 8 caracteres">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="pf-password-confirm" class="form-label">Confirmar contraseña</label>
                                                        <input type="password" class="form-control" id="pf-password-confirm">
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="modalClient" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar datos personales</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body">
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
                                    </div>
                                </div>

                <!-- Detalle del certificado (vista web) -->
                <div id="details-card" class="card glass p-3 rounded-lg mt-4 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0">Detalle del Certificado</h5>
                        <button class="btn btn-sm btn-outline-secondary" id="btnCloseDetails">Cerrar</button>
                    </div>
                    <div id="details-body">
                        <!-- contenido dinámico -->
                    </div>
                </div>

            </div>
            <?php include __DIR__ . '/partials/footer.php'; ?>
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
                        <button class="btn btn-sm btn-outline-secondary" data-action="details" data-id="${encodeURIComponent(cert.id || '')}">Detalles</button>
                    </td>
                </tr>`).join('');
        }

        function renderDetails(cert){
            // Mostrar todos los datos relevantes del PDF en HTML
            const equip = {
                type: cert.equipment_type_name || cert.equipment_type || '',
                brand: cert.equipment_brand || '',
                model: cert.equipment_model || '',
                serial: cert.equipment_serial_number || cert.equipment_serial || ''
            };
            const html = `
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="glass p-3 rounded">
                            <div class="text-muted small">Certificado</div>
                            <div class="fs-5 fw-semibold">N° ${escapeHtml(cert.certificate_number || '')}</div>
                            <div>Calibración: <strong>${escapeHtml(cert.calibration_date || '')}</strong></div>
                            <div>Próxima: <strong>${escapeHtml(cert.next_calibration_date || '')}</strong></div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="glass p-3 rounded">
                            <div class="text-muted small">Equipo</div>
                            <div>Tipo: <strong>${escapeHtml(equip.type)}</strong></div>
                            <div>Marca: <strong>${escapeHtml(equip.brand)}</strong></div>
                            <div>Modelo: <strong>${escapeHtml(equip.model)}</strong></div>
                            <div>Serie: <strong>${escapeHtml(equip.serial)}</strong></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="glass p-3 rounded">
                            <div class="text-muted small mb-2">Resultados</div>
                            <div id="results-html">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-3">
                                        <thead>
                                            <tr>
                                                <th>Valor patrón</th>
                                                <th>Valor obtenido</th>
                                                <th>Precisión</th>
                                                <th>Error (seg)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyRes"></tbody>
                                    </table>
                                </div>
                                <div id="distSections" class="row g-3">
                                    <div class="col-12">
                                        <h6 class="mb-2">Medición con prisma</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                <tr>
                                                    <th>Punto control</th>
                                                    <th>Dist. obtenida</th>
                                                    <th>Precisión</th>
                                                    <th>Variación</th>
                                                </tr>
                                                </thead>
                                                <tbody id="tbodyDistCon"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <h6 class="mb-2">Medición sin prisma</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead>
                                                <tr>
                                                    <th>Punto control</th>
                                                    <th>Dist. obtenida</th>
                                                    <th>Precisión</th>
                                                    <th>Variación</th>
                                                </tr>
                                                </thead>
                                                <tbody id="tbodyDistSin"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="glass p-3 rounded">
                            <div class="text-muted small">Condiciones Ambientales</div>
                            <div id="lab-html">(si existen, se mostrarán aquí)</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="glass p-3 rounded">
                            <div class="text-muted small">Técnico</div>
                            <div id="tech-html">(si existe, se mostrará aquí)</div>
                        </div>
                    </div>
                </div>`;
            document.getElementById('details-body').innerHTML = html;
            document.getElementById('details-card').classList.remove('d-none');
        }

        async function loadDetails(certId){
            try {
                const raw = await Auth.fetchWithAuth(`api/certificates.php?action=get&id=${encodeURIComponent(certId)}`);
                const cert = raw?.data || raw;
                if (!cert) return;
                renderDetails(cert);
                // Cargar condiciones, resultados, técnico similares al PDF
                const [cond, res, dist] = await Promise.all([
                    Auth.fetchWithAuth(`api/certificates.php?action=getConditions&id=${encodeURIComponent(certId)}`).catch(()=>null),
                    Auth.fetchWithAuth(`api/certificates.php?action=getResults&id=${encodeURIComponent(certId)}`).catch(()=>null),
                    Auth.fetchWithAuth(`api/certificates.php?action=getDistanceResults&id=${encodeURIComponent(certId)}`).catch(()=>null),
                ]);
                const lab = cond?.data || cond || null;
                const resultados = Array.isArray(res?.data) ? res.data : (Array.isArray(res) ? res : []);
                const distRes = Array.isArray(dist?.data) ? dist.data : (Array.isArray(dist) ? dist : []);
                // Render básicos
                const labEl = document.getElementById('lab-html');
                if (labEl) {
                    const t = lab?.temperatura_celsius ?? lab?.temperature;
                    const h = lab?.humedad_relativa_porc ?? lab?.humidity;
                    const p = lab?.presion_atm_mmhg ?? lab?.pressure;
                    labEl.innerHTML = `Temperatura: <strong>${escapeHtml(String(t ?? '-'))}°</strong><br>
                        Humedad: <strong>${escapeHtml(String(h ?? '-'))}%</strong><br>
                        Presión atm: <strong>${escapeHtml(String(p ?? '-'))} mmHg</strong>`;
                }
                // Tabla de resultados angulares/lineales
                const tbodyRes = document.getElementById('tbodyRes');
                if (tbodyRes) {
                    if (!resultados.length) {
                        tbodyRes.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Sin resultados</td></tr>';
                    } else {
                        const fmtDms = (g,m,s)=>`${Number(g||0)}° ${String(Number(m||0)).padStart(2,'0')}' ${String(Number(s||0)).padStart(2,'0')}"`;
                        tbodyRes.innerHTML = resultados.map(r => {
                            const patron = fmtDms(r.valor_patron_grados, r.valor_patron_minutos, r.valor_patron_segundos);
                            const obtenido = fmtDms(r.valor_obtenido_grados, r.valor_obtenido_minutos, r.valor_obtenido_segundos);
                            const tipo = String(r.tipo_resultado||'segundos');
                            const precVal = r.precision ?? r.precision_val ?? 0;
                            const precStr = (tipo === 'lineal') ? `± ${Math.max(0, Math.round(Number(precVal)||0))} mm` : `± ${Math.max(0, parseInt(precVal||0))}"`;
                            const errStr = `${String(r.error_segundos||0).padStart(2,'0')}"`;
                            return `<tr>
                                <td>${escapeHtml(patron)}</td>
                                <td>${escapeHtml(obtenido)}</td>
                                <td>${escapeHtml(precStr)}</td>
                                <td>${escapeHtml(errStr)}</td>
                            </tr>`;
                        }).join('');
                    }
                }
                // Tablas de distancia
                const tbodyCon = document.getElementById('tbodyDistCon');
                const tbodySin = document.getElementById('tbodyDistSin');
                if (tbodyCon && tbodySin) {
                    const con = distRes.filter(r => !!r.con_prisma);
                    const sin = distRes.filter(r => !r.con_prisma);
                    const fmtPrec = (r)=>`${r.precision_base_mm} mm + ${r.precision_ppm} ppm`;
                    const rowDist = (r)=> `<tr>
                        <td>${Number(r.punto_control_metros||0).toFixed(3)} m.</td>
                        <td>${Number(r.distancia_obtenida_metros||0).toFixed(3)} m.</td>
                        <td>${fmtPrec(r)}</td>
                        <td>${Number(r.variacion_metros||0).toFixed(3)} m.</td>
                    </tr>`;
                    tbodyCon.innerHTML = con.length ? con.map(rowDist).join('') : '<tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>';
                    tbodySin.innerHTML = sin.length ? sin.map(rowDist).join('') : '<tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>';
                }
                // Técnico
                const techEl = document.getElementById('tech-html');
                if (techEl) {
                    const techName = cert.technician_name || cert.technician?.full_name || cert.technician?.nombre_completo || '';
                    const techCargo = cert.technician?.cargo || '';
                    if (techName) {
                        techEl.innerHTML = `<div>Nombre: <strong>${escapeHtml(techName)}</strong></div>` +
                            (techCargo ? `<div>Cargo: <strong>${escapeHtml(techCargo)}</strong></div>` : '');
                    } else {
                        techEl.textContent = 'No asignado';
                    }
                }
            } catch (e) {
                console.warn('No se pudo cargar detalle', e);
            }
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

        if (els.pf) els.pf.addEventListener('submit', async (ev) => {
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
        document.addEventListener('click', (ev) => {
            const btn = ev.target.closest('[data-action="details"]');
            if (btn) {
                const id = btn.getAttribute('data-id');
                if (id) loadDetails(id);
            }
        });
        const btnCloseDetails = document.getElementById('btnCloseDetails');
        if (btnCloseDetails) btnCloseDetails.addEventListener('click', () => {
            document.getElementById('details-card').classList.add('d-none');
            document.getElementById('details-body').innerHTML = '';
        });

        if (els.cf) els.cf.addEventListener('submit', async (ev) => {
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
