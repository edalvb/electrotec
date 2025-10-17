<!-- nuevo-certificado.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Nuevo Certificado</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'certificados'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Nuevo Certificado';
            $pageSubtitle = 'Completa los datos y resultados del certificado de calibración';
            $headerActionsHtml = '';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="card glass p-4 rounded-lg">
                <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
                <div id="successAlert" class="alert alert-success d-none" role="alert"></div>

                <form id="certificateForm">
                    <!-- Datos generales -->
                    <div class="card glass-subtle p-4 mb-4">
                        <h6 class="mb-3 fw-bold">Datos generales</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cliente *</label>
                                <select id="clientSelect" class="form-select" required>
                                    <option value="">Cargando clientes...</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Equipo *</label>
                                <select id="equipmentSelect" class="form-select" required>
                                    <option value="">Cargando equipos...</option>
                                </select>
                                <small class="text-muted">Lista de todos los equipos disponibles</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Calibración *</label>
                                <input id="calibrationDate" type="date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Próxima Calibración *</label>
                                <input id="nextCalibrationDate" type="date" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Temperatura (°C)</label>
                                <input id="temperature" type="number" step="0.1" class="form-control" placeholder="20.0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Humedad (%)</label>
                                <input id="humidity" type="number" step="0.1" class="form-control" placeholder="50.0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Presión (mmHg)</label>
                                <input id="pressure" type="number" step="0.1" class="form-control" placeholder="760.0">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-check-inline">
                                    <input id="isCalibration" class="form-check-input" type="checkbox" checked>
                                    <label class="form-check-label" for="isCalibration">Calibración</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="isMaintenance" class="form-check-input" type="checkbox">
                                    <label class="form-check-label" for="isMaintenance">Mantenimiento</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resultados -->
                    <div class="card glass-subtle p-4 mb-4">
                        <h6 class="mb-3 fw-bold">Resultados</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Observaciones</label>
                                <textarea id="observations" class="form-control" rows="3" placeholder="Ingresa observaciones sobre la calibración..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estado del Equipo</label>
                                <select id="equipmentStatus" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    <option value="approved">Aprobado</option>
                                    <option value="conditional">Aprobado con observaciones</option>
                                    <option value="rejected">Rechazado</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Número de Certificado</label>
                                <input id="certificateNumber" type="text" class="form-control" placeholder="Se genera automáticamente (AAAA-####)" readonly>
                                <small class="text-muted">Se asigna automáticamente al crear: año-número correlativo.</small>
                            </div>
                        </div>

                        <!-- Tabla de resultados angulares/lineales -->
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0" id="resultTableTitle">Resultados (según equipo)</h6>
                                <button id="btnAddResultado" type="button" class="btn btn-sm btn-primary">Agregar resultado</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Valor de Patrón</th>
                                            <th>Valor Obtenido</th>
                                            <th id="thPrecision">Precisión</th>
                                            <th>Error</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyResultados">
                                        <tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Secciones de resultados de distancia (mostradas si el equipo soporta con/sin prisma) -->
                        <div id="distSections" class="mt-4 d-none">
                            <h6 class="fw-bold">Resultados de Distancia</h6>
                            <div class="row g-3">
                                <div class="col-12 col-lg-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">Medición con Prisma</span>
                                        <button id="btnAddDistConPrisma" type="button" class="btn btn-sm btn-outline-primary">Agregar más</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Punto de Control</th>
                                                    <th>Distancia Obtenida</th>
                                                    <th>Precisión</th>
                                                    <th>Variación</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyDistConPrisma">
                                                <tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">Medición sin Prisma</span>
                                        <button id="btnAddDistSinPrisma" type="button" class="btn btn-sm btn-outline-primary">Agregar más</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Punto de Control</th>
                                                    <th>Distancia Obtenida</th>
                                                    <th>Precisión</th>
                                                    <th>Variación</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyDistSinPrisma">
                                                <tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='certificados.php'">
                            Cancelar
                        </button>
                        <button id="saveCertificateBtn" type="submit" class="btn btn-primary">
                            Crear Certificado
                        </button>
                    </div>
                </form>
            </div>

            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', async () => {
        // Verificar autenticación
        try {
            Auth.requireAuth('admin');
        } catch (e) {
            return;
        }

        const API_CLIENTS = 'api/clients.php?action=list&limit=200&offset=0';
        const API_EQUIPMENT = 'api/equipment.php?action=list&limit=200&offset=0';
        const API_CREATE_CERTIFICATE = 'api/certificates.php?action=create';

        const form = document.getElementById('certificateForm');
        const saveBtn = document.getElementById('saveCertificateBtn');
        const clientSelect = document.getElementById('clientSelect');
        const equipmentSelect = document.getElementById('equipmentSelect');
        const calibrationDate = document.getElementById('calibrationDate');
        const nextCalibrationDate = document.getElementById('nextCalibrationDate');
        const temperature = document.getElementById('temperature');
        const humidity = document.getElementById('humidity');
        const pressure = document.getElementById('pressure');
        const isCalibration = document.getElementById('isCalibration');
        const isMaintenance = document.getElementById('isMaintenance');
        const observations = document.getElementById('observations');
        const equipmentStatus = document.getElementById('equipmentStatus');
    const certificateNumber = document.getElementById('certificateNumber');
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');

        // UI resultados
        const btnAddResultado = document.getElementById('btnAddResultado');
        const tbodyResultados = document.getElementById('tbodyResultados');
        const resultTableTitle = document.getElementById('resultTableTitle');
        const thPrecision = document.getElementById('thPrecision');
        const distSections = document.getElementById('distSections');
        const btnAddDistConPrisma = document.getElementById('btnAddDistConPrisma');
        const btnAddDistSinPrisma = document.getElementById('btnAddDistSinPrisma');
        const tbodyDistConPrisma = document.getElementById('tbodyDistConPrisma');
        const tbodyDistSinPrisma = document.getElementById('tbodyDistSinPrisma');

        const state = {
            equipments: [],
            equipmentMap: {},
            resultados: [], // angulares/lineales
            resultadosDist: [], // distancia
            currentPrecision: 'segundos',
            allowDistWithPrism: false,
        };

        function setError(message) {
            errorAlert.textContent = message || 'Ocurrió un error.';
            errorAlert.classList.remove('d-none');
            successAlert.classList.add('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function setSuccess(message) {
            successAlert.textContent = message || 'Certificado creado exitosamente.';
            successAlert.classList.remove('d-none');
            errorAlert.classList.add('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function clearAlerts() {
            errorAlert.classList.add('d-none');
            successAlert.classList.add('d-none');
        }

        // Cargar clientes
        async function loadClients() {
            try {
                const data = await Auth.fetchWithAuth(API_CLIENTS);
                
                if (!data.ok || !Array.isArray(data.data)) {
                    throw new Error('Error al cargar clientes');
                }

                clientSelect.innerHTML = '<option value="">Seleccione un cliente</option>';
                data.data.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    // La API devuelve el campo 'nombre' (no 'name')
                    option.textContent = client.nombre || client.name || 'Cliente sin nombre';
                    clientSelect.appendChild(option);
                });
            } catch (error) {
                setError('No se pudieron cargar los clientes: ' + error.message);
                clientSelect.innerHTML = '<option value="">Error al cargar clientes</option>';
            }
        }

        // Cargar todos los equipos disponibles (independiente del cliente)
        async function loadEquipment() {
            try {
                const data = await Auth.fetchWithAuth(API_EQUIPMENT);

                if (!data.ok || !Array.isArray(data.data)) {
                    throw new Error('Error al cargar equipos');
                }

                if (data.data.length === 0) {
                    equipmentSelect.innerHTML = '<option value="">No hay equipos disponibles</option>';
                    return;
                }

                equipmentSelect.innerHTML = '<option value="">Seleccione un equipo</option>';
                state.equipments = data.data;
                state.equipmentMap = Object.fromEntries((data.data || []).map(e => [e.id, e]));
                data.data.forEach(equipment => {
                    const option = document.createElement('option');
                    option.value = equipment.id;
                    const brand = equipment.brand || '';
                    const model = equipment.model || '';
                    const sn = equipment.serial_number || 'S/N';
                    option.textContent = `${brand} ${model} - S/N: ${sn}`.trim();
                    equipmentSelect.appendChild(option);
                });
            } catch (error) {
                setError('No se pudieron cargar los equipos: ' + error.message);
                equipmentSelect.innerHTML = '<option value="">Error al cargar equipos</option>';
            }
        }

        function fmtDms(g, m, s) {
            const gg = Number(g)||0; const mm = Number(m)||0; const ss = Number(s)||0;
            return `${gg}° ${String(mm).padStart(2,'0')}' ${String(ss).padStart(2,'0')}"`;
        }

        function renderResultados() {
            if (!state.resultados.length) {
                tbodyResultados.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>';
                return;
            }
            tbodyResultados.innerHTML = state.resultados.map(r => {
                const patron = fmtDms(r.valor_patron_grados, r.valor_patron_minutos, r.valor_patron_segundos);
                const obtenido = fmtDms(r.valor_obtenido_grados, r.valor_obtenido_minutos, r.valor_obtenido_segundos);
                const precStr = state.currentPrecision === 'lineal' ? `± ${Number(r.precision_val||0).toFixed(1)} mm` : `± ${String(r.precision_val||0).padStart(2,'0')}"`;
                const errStr = `${String(r.error_segundos||0).padStart(2,'0')}"`;
                return `<tr><td>${patron}</td><td>${obtenido}</td><td>${precStr}</td><td>${errStr}</td></tr>`;
            }).join('');
        }

        function renderDistTables() {
            const con = state.resultadosDist.filter(r => !!r.con_prisma);
            const sin = state.resultadosDist.filter(r => !r.con_prisma);
            tbodyDistConPrisma.innerHTML = con.length ? con.map(r => {
                const prec = `${r.precision_base_mm} mm + ${r.precision_ppm} ppm`;
                return `<tr><td>${Number(r.punto_control_metros).toFixed(3)} m.</td><td>${Number(r.distancia_obtenida_metros).toFixed(3)} m.</td><td>${prec}</td><td>${Number(r.variacion_metros).toFixed(3)} m.</td></tr>`;
            }).join('') : '<tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>';
            tbodyDistSinPrisma.innerHTML = sin.length ? sin.map(r => {
                const prec = `${r.precision_base_mm} mm + ${r.precision_ppm} ppm`;
                return `<tr><td>${Number(r.punto_control_metros).toFixed(3)} m.</td><td>${Number(r.distancia_obtenida_metros).toFixed(3)} m.</td><td>${prec}</td><td>${Number(r.variacion_metros).toFixed(3)} m.</td></tr>`;
            }).join('') : '<tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>';
        }

        function syncUiWithEquipment() {
            const eq = state.equipmentMap[equipmentSelect.value];
            state.currentPrecision = (eq && eq.resultado_precision === 'lineal') ? 'lineal' : 'segundos';
            state.allowDistWithPrism = !!(eq && eq.resultado_conprisma);
            resultTableTitle.textContent = state.currentPrecision === 'lineal' ? 'Resultados (precisión lineal en mm)' : 'Resultados (precisión angular en segundos)';
            thPrecision.textContent = state.currentPrecision === 'lineal' ? 'Precisión (mm)' : 'Precisión';
            distSections.classList.toggle('d-none', !state.allowDistWithPrism);
        }

        equipmentSelect.addEventListener('change', () => {
            syncUiWithEquipment();
        });

        // Modales simples mediante prompt; se puede mejorar con un modal Bootstrap
        btnAddResultado.addEventListener('click', () => {
            // Solicitar datos mínimos
            const pg = prompt('Valor de Patrón - Grados (entero):', '0');
            if (pg === null) return;
            const pm = prompt('Valor de Patrón - Minutos (0-59):', '0');
            if (pm === null) return;
            const ps = prompt('Valor de Patrón - Segundos (0-59):', '0');
            if (ps === null) return;
            const og = prompt('Valor Obtenido - Grados (entero):', '0');
            if (og === null) return;
            const om = prompt('Valor Obtenido - Minutos (0-59):', '0');
            if (om === null) return;
            const os = prompt('Valor Obtenido - Segundos (0-59):', '0');
            if (os === null) return;
            const prec = prompt(state.currentPrecision === 'lineal' ? 'Precisión (en mm, decimal):' : 'Precisión (en segundos ")', state.currentPrecision === 'lineal' ? '2.0' : '2');
            if (prec === null) return;
            const err = prompt('Error (en segundos):', '0');
            if (err === null) return;

            state.resultados.push({
                tipo_resultado: state.currentPrecision,
                valor_patron_grados: parseInt(pg||'0',10),
                valor_patron_minutos: parseInt(pm||'0',10),
                valor_patron_segundos: parseInt(ps||'0',10),
                valor_obtenido_grados: parseInt(og||'0',10),
                valor_obtenido_minutos: parseInt(om||'0',10),
                valor_obtenido_segundos: parseInt(os||'0',10),
                precision_val: parseFloat(prec||'0'),
                error_segundos: parseInt(err||'0',10)
            });
            renderResultados();
        });

        function promptDist(conPrisma) {
            const pcm = prompt('Punto de Control (en metros):', '0.000'); if (pcm === null) return;
            const dom = prompt('Distancia Obtenida (en metros):', '0.000'); if (dom === null) return;
            const vm = prompt('Variación (en metros):', '0.000'); if (vm === null) return;
            const pb = prompt('Precisión Base (en mm):', '2'); if (pb === null) return;
            const pp = prompt('Precisión PPM:', '2'); if (pp === null) return;
            state.resultadosDist.push({
                punto_control_metros: parseFloat(pcm||'0'),
                distancia_obtenida_metros: parseFloat(dom||'0'),
                variacion_metros: parseFloat(vm||'0'),
                precision_base_mm: parseInt(pb||'0',10),
                precision_ppm: parseInt(pp||'0',10),
                con_prisma: !!conPrisma,
            });
            renderDistTables();
        }
        btnAddDistConPrisma.addEventListener('click', () => promptDist(true));
        btnAddDistSinPrisma.addEventListener('click', () => promptDist(false));

        // Manejar el envío del formulario
        async function handleSubmit(e) {
            e.preventDefault();
            clearAlerts();

            const clientId = clientSelect.value;
            const equipmentId = equipmentSelect.value;
            const calDate = calibrationDate.value;

            if (!clientId || !equipmentId || !calDate) {
                setError('Por favor completa todos los campos obligatorios (*)');
                return;
            }

            // Construir el payload
            const payload = {
                equipment_id: equipmentId,
                client_id: clientId,
                calibration_date: calDate,
                next_calibration_date: nextCalibrationDate.value || null,
                // certificate_number: el backend lo genera automáticamente
                environmental_conditions: {
                    temperature: temperature.value ? parseFloat(temperature.value) : null,
                    humidity: humidity.value ? parseFloat(humidity.value) : null,
                    pressure: pressure.value ? parseFloat(pressure.value) : null
                },
                service_type: {
                    calibration: isCalibration.checked,
                    maintenance: isMaintenance.checked
                },
                observations: observations.value.trim() || null,
                status: equipmentStatus.value || null,
                resultados: state.resultados,
                resultados_distancia: state.resultadosDist,
            };

            try {
                saveBtn.disabled = true;
                saveBtn.textContent = 'Creando certificado...';

                const data = await Auth.fetchWithAuth(API_CREATE_CERTIFICATE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (data.error) {
                    throw new Error(data.message || data.error || 'Error al crear el certificado');
                }

                setSuccess('Certificado creado exitosamente');
                
                // Limpiar el formulario (manteniendo lista de equipos cargada)
                form.reset();
                equipmentSelect.selectedIndex = 0;

                // Redirigir después de 2 segundos
                setTimeout(() => {
                    window.location.href = 'certificados.php';
                }, 2000);

            } catch (err) {
                setError(err.message);
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Crear Certificado';
            }
        }

        form.addEventListener('submit', handleSubmit);

        // Establecer fecha de hoy como predeterminada
        const today = new Date().toISOString().split('T')[0];
        calibrationDate.value = today;

        // Cargar clientes y equipos al inicio
        await Promise.all([loadClients(), loadEquipment()]);
        syncUiWithEquipment();
        renderResultados();
        renderDistTables();
    });
    </script>
</body>
</html>
