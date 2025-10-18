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
                                <label class="form-label">Técnico calibrador *</label>
                                <select id="technicianSelect" class="form-select" required>
                                    <option value="">Cargando técnicos...</option>
                                </select>
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
                        <div id="resultsSection" class="mt-4 d-none">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0" id="resultTableTitle">Resultados (según equipo)</h6>
                                <button id="btnAddResultado" type="button" class="btn btn-sm btn-primary" disabled>Agregar resultado</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle w-100">
                                    <thead>
                                        <tr>
                                            <th>Valor de Patrón</th>
                                            <th>Valor Obtenido</th>
                                            <th id="thPrecision">Precisión</th>
                                            <th>Error</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyResultados">
                                        <tr><td colspan="5" class="text-center text-muted">Sin filas</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Secciones de resultados de distancia (mostradas si el equipo soporta con/sin prisma) -->
                        <div id="distSections" class="mt-4 d-none">
                            <h6 class="fw-bold">Resultados de Distancia</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">Medición con Prisma</span>
                                        <button id="btnAddDistConPrisma" type="button" class="btn btn-sm btn-outline-primary" disabled>Agregar más</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle w-100">
                                            <thead>
                                                <tr>
                                                    <th>Punto de Control</th>
                                                    <th>Distancia Obtenida</th>
                                                    <th>Precisión</th>
                                                    <th>Variación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyDistConPrisma">
                                                <tr><td colspan="5" class="text-center text-muted">Sin filas</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">Medición sin Prisma</span>
                                        <button id="btnAddDistSinPrisma" type="button" class="btn btn-sm btn-outline-primary" disabled>Agregar más</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle w-100">
                                            <thead>
                                                <tr>
                                                    <th>Punto de Control</th>
                                                    <th>Distancia Obtenida</th>
                                                    <th>Precisión</th>
                                                    <th>Variación</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbodyDistSinPrisma">
                                                <tr><td colspan="5" class="text-center text-muted">Sin filas</td></tr>
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

        <!-- Modal: Resultado Angular/Lineal -->
        <div class="modal fade" id="modalResultado" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Resultado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formResultado">
                            <input type="hidden" id="resultadoIndex" value="-1">
                                            <div class="row g-3 needs-validation" novalidate>
                                <div class="col-12"><small class="text-muted">Valor de Patrón</small></div>
                                <div class="col-4">
                                    <label class="form-label">Grados</label>
                                    <input type="number" class="form-control" id="resPg" step="1" required>
                                                    <div class="invalid-feedback">Ingrese grados (número entero).</div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Minutos</label>
                                    <input type="number" class="form-control" id="resPm" min="0" max="59" step="1" required>
                                                    <div class="invalid-feedback">Minutos debe estar entre 0 y 59.</div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Segundos</label>
                                    <input type="number" class="form-control" id="resPs" min="0" max="59" step="1" required>
                                                    <div class="invalid-feedback">Segundos debe estar entre 0 y 59.</div>
                                </div>
                                <div class="col-12 mt-2"><small class="text-muted">Valor Obtenido</small></div>
                                <div class="col-4">
                                    <label class="form-label">Grados</label>
                                    <input type="number" class="form-control" id="resOg" step="1" required>
                                                    <div class="invalid-feedback">Ingrese grados (número entero).</div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Minutos</label>
                                    <input type="number" class="form-control" id="resOm" min="0" max="59" step="1" required>
                                                    <div class="invalid-feedback">Minutos debe estar entre 0 y 59.</div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label">Segundos</label>
                                    <input type="number" class="form-control" id="resOs" min="0" max="59" step="1" required>
                                                    <div class="invalid-feedback">Segundos debe estar entre 0 y 59.</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" id="lblPrecision">Precisión</label>
                                    <input type="number" class="form-control" id="resPrec" step="1" required>
                                    <small class="text-muted" id="helpPrecision">En segundos ("), o mm según equipo</small>
                                                    <div class="invalid-feedback">Ingrese un valor válido de precisión.</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Error (segundos)</label>
                                    <input type="number" class="form-control" id="resErr" step="1" min="0" required>
                                                    <div class="invalid-feedback">Ingrese un error en segundos (>= 0).</div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnGuardarResultado">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Resultado de Distancia -->
        <div class="modal fade" id="modalDistancia" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Resultado de Distancia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formDistancia">
                            <input type="hidden" id="distIndex" value="-1">
                                            <div class="row g-3 needs-validation" novalidate>
                                <div class="col-6">
                                    <label class="form-label">Punto de Control (m)</label>
                                    <input type="number" class="form-control" id="distPcm" step="0.001" required>
                                                    <div class="invalid-feedback">Ingrese el punto de control en metros.</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Distancia Obtenida (m)</label>
                                    <input type="number" class="form-control" id="distDom" step="0.001" required>
                                                    <div class="invalid-feedback">Ingrese la distancia obtenida en metros.</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Variación (m)</label>
                                    <input type="number" class="form-control" id="distVm" step="0.001" required>
                                                    <div class="invalid-feedback">Ingrese la variación en metros.</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Precisión Base (mm)</label>
                                    <input type="number" class="form-control" id="distPb" step="1" required>
                                                    <div class="invalid-feedback">Ingrese precisión base en mm.</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Precisión PPM</label>
                                    <input type="number" class="form-control" id="distPp" step="1" required>
                                                    <div class="invalid-feedback">Ingrese precisión en ppm.</div>
                                </div>
                                <div class="col-6 d-flex align-items-end">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="distConPrisma">
                                        <label class="form-check-label" for="distConPrisma">Con prisma</label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btnGuardarDistancia">Guardar</button>
                    </div>
                </div>
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
    const API_TECHNICIANS = 'api/technicians.php?action=list&limit=200&offset=0';

        const form = document.getElementById('certificateForm');
        const saveBtn = document.getElementById('saveCertificateBtn');
        const clientSelect = document.getElementById('clientSelect');
    const equipmentSelect = document.getElementById('equipmentSelect');
    const technicianSelect = document.getElementById('technicianSelect');
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
    const resultsSection = document.getElementById('resultsSection');
        const distSections = document.getElementById('distSections');
        const btnAddDistConPrisma = document.getElementById('btnAddDistConPrisma');
        const btnAddDistSinPrisma = document.getElementById('btnAddDistSinPrisma');
        const tbodyDistConPrisma = document.getElementById('tbodyDistConPrisma');
        const tbodyDistSinPrisma = document.getElementById('tbodyDistSinPrisma');
    // Estado de último equipo seleccionado (para bloqueo/migración)
    let lastEquipmentId = '';
    let lastPrecision = 'segundos';
    let lastAllowDist = false;

        // Modales
        const modalResultadoEl = document.getElementById('modalResultado');
        const modalDistanciaEl = document.getElementById('modalDistancia');
        const modalResultado = new bootstrap.Modal(modalResultadoEl);
        const modalDistancia = new bootstrap.Modal(modalDistanciaEl);
        // Campos modal Resultado
        const resIdx = document.getElementById('resultadoIndex');
        const resPg = document.getElementById('resPg');
        const resPm = document.getElementById('resPm');
        const resPs = document.getElementById('resPs');
        const resOg = document.getElementById('resOg');
        const resOm = document.getElementById('resOm');
        const resOs = document.getElementById('resOs');
        const resPrec = document.getElementById('resPrec');
        const resErr = document.getElementById('resErr');
        const lblPrecision = document.getElementById('lblPrecision');
        const helpPrecision = document.getElementById('helpPrecision');
        const btnGuardarResultado = document.getElementById('btnGuardarResultado');
        // Campos modal Distancia
        const distIdx = document.getElementById('distIndex');
        const distPcm = document.getElementById('distPcm');
        const distDom = document.getElementById('distDom');
        const distVm = document.getElementById('distVm');
        const distPb = document.getElementById('distPb');
        const distPp = document.getElementById('distPp');
        const distConPrisma = document.getElementById('distConPrisma');
        const btnGuardarDistancia = document.getElementById('btnGuardarDistancia');

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

        // Cargar técnicos
        async function loadTechnicians() {
            try {
                const data = await Auth.fetchWithAuth(API_TECHNICIANS);
                if (!data.ok || !Array.isArray(data.data)) throw new Error('Error al cargar técnicos');
                technicianSelect.innerHTML = '<option value="">Seleccione un técnico</option>';
                data.data.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.nombre_completo;
                    technicianSelect.appendChild(opt);
                });
            } catch (e) {
                setError('No se pudieron cargar los técnicos: ' + e.message);
                technicianSelect.innerHTML = '<option value="">Error al cargar técnicos</option>';
            }
        }

        function fmtDms(g, m, s) {
            const gg = Number(g)||0; const mm = Number(m)||0; const ss = Number(s)||0;
            return `${gg}° ${String(mm).padStart(2,'0')}' ${String(ss).padStart(2,'0')}"`;
        }

        function renderResultados() {
            if (!state.resultados.length) {
                tbodyResultados.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Sin filas</td></tr>';
                return;
            }
            tbodyResultados.innerHTML = state.resultados.map((r, idx) => {
                const patron = fmtDms(r.valor_patron_grados, r.valor_patron_minutos, r.valor_patron_segundos);
                const obtenido = fmtDms(r.valor_obtenido_grados, r.valor_obtenido_minutos, r.valor_obtenido_segundos);
                const precVal = (r.precision ?? r.precision_val ?? 0);
                const precStr = state.currentPrecision === 'lineal'
                    ? `± ${String(Math.max(0, Math.round(Number(precVal)||0))).padStart(2,'0')} mm`
                    : `± ${String(Math.max(0, parseInt(precVal||0))).padStart(2,'0')}"`;
                const errStr = `${String(r.error_segundos||0).padStart(2,'0')}"`;
                return `<tr>
                    <td>${patron}</td>
                    <td>${obtenido}</td>
                    <td>${precStr}</td>
                    <td>${errStr}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-secondary me-1" data-action="edit-res" data-index="${idx}">Editar</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-action="del-res" data-index="${idx}">Eliminar</button>
                    </td>
                </tr>`;
            }).join('');
        }

        function renderDistTables() {
            const con = state.resultadosDist.filter(r => !!r.con_prisma);
            const sin = state.resultadosDist.filter(r => !r.con_prisma);
            tbodyDistConPrisma.innerHTML = con.length ? con.map((r, idx) => {
                const prec = `${r.precision_base_mm} mm + ${r.precision_ppm} ppm`;
                return `<tr>
                    <td>${Number(r.punto_control_metros).toFixed(3)} m.</td>
                    <td>${Number(r.distancia_obtenida_metros).toFixed(3)} m.</td>
                    <td>${prec}</td>
                    <td>${Number(r.variacion_metros).toFixed(3)} m.</td>
                    <td>
                        <button type=\"button\" class=\"btn btn-sm btn-outline-secondary me-1\" data-action=\"edit-dist\" data-kind=\"con\" data-index=\"${idx}\">Editar</button>
                        <button type=\"button\" class=\"btn btn-sm btn-outline-danger\" data-action=\"del-dist\" data-kind=\"con\" data-index=\"${idx}\">Eliminar</button>
                    </td>
                </tr>`;
            }).join('') : '<tr><td colspan="5" class="text-center text-muted">Sin filas</td></tr>';
            tbodyDistSinPrisma.innerHTML = sin.length ? sin.map((r, idx) => {
                const prec = `${r.precision_base_mm} mm + ${r.precision_ppm} ppm`;
                return `<tr>
                    <td>${Number(r.punto_control_metros).toFixed(3)} m.</td>
                    <td>${Number(r.distancia_obtenida_metros).toFixed(3)} m.</td>
                    <td>${prec}</td>
                    <td>${Number(r.variacion_metros).toFixed(3)} m.</td>
                    <td>
                        <button type=\"button\" class=\"btn btn-sm btn-outline-secondary me-1\" data-action=\"edit-dist\" data-kind=\"sin\" data-index=\"${idx}\">Editar</button>
                        <button type=\"button\" class=\"btn btn-sm btn-outline-danger\" data-action=\"del-dist\" data-kind=\"sin\" data-index=\"${idx}\">Eliminar</button>
                    </td>
                </tr>`;
            }).join('') : '<tr><td colspan="5" class="text-center text-muted">Sin filas</td></tr>';
        }

        function syncUiWithEquipment() {
            const eq = state.equipmentMap[equipmentSelect.value];
            state.currentPrecision = (eq && eq.resultado_precision === 'lineal') ? 'lineal' : 'segundos';
            state.allowDistWithPrism = !!(eq && eq.resultado_conprisma);
            resultTableTitle.textContent = state.currentPrecision === 'lineal' ? 'Resultados (precisión lineal en mm)' : 'Resultados (precisión angular en segundos)';
            thPrecision.textContent = state.currentPrecision === 'lineal' ? 'Precisión (mm)' : 'Precisión';
            const hasEq = !!equipmentSelect.value;
            // Mostrar/ocultar secciones según selección de equipo
            resultsSection.classList.toggle('d-none', !hasEq);
            btnAddResultado.disabled = !hasEq;
            distSections.classList.toggle('d-none', !hasEq || !state.allowDistWithPrism);
            btnAddDistConPrisma.disabled = !hasEq || !state.allowDistWithPrism;
            btnAddDistSinPrisma.disabled = !hasEq || !state.allowDistWithPrism;
        }

        equipmentSelect.addEventListener('change', () => {
            const newEq = state.equipmentMap[equipmentSelect.value] || null;
            const newPrecision = (newEq && newEq.resultado_precision === 'lineal') ? 'lineal' : 'segundos';
            const newAllowDist = !!(newEq && newEq.resultado_conprisma);

            const hasResultados = state.resultados.length > 0;
            const hasDist = state.resultadosDist.length > 0;

            let needsConfirm = false;
            const parts = [];
            if (hasResultados && newPrecision !== lastPrecision) {
                parts.push('• El tipo de precisión cambiará y puede requerir adaptar los resultados.');
                needsConfirm = true;
            }
            if (hasDist && !newAllowDist && lastAllowDist) {
                parts.push('• El nuevo equipo no admite Resultados de Distancia (Con/Sin prisma).');
                needsConfirm = true;
            }

            if (needsConfirm) {
                const msg = 'Cambio de equipo:\n' + parts.join('\n') + '\n\n¿Deseas continuar?';
                if (!confirm(msg)) {
                    // Revertir selección
                    equipmentSelect.value = lastEquipmentId;
                    return;
                }

                // Migración/normalización si cambia precisión
                if (hasResultados && newPrecision !== lastPrecision) {
                    const clear = confirm('¿Vaciar los resultados actuales?\nAceptar: vaciar\nCancelar: mantener y ajustar tipo (no se convierten unidades).');
                    if (clear) {
                        state.resultados = [];
                    } else {
                        state.resultados = state.resultados.map(r => ({ ...r, tipo_resultado: newPrecision }));
                    }
                    renderResultados();
                }

                // Manejo de distancias si ya no están permitidas
                if (hasDist && !newAllowDist && lastAllowDist) {
                    const clearDist = confirm('¿Eliminar los resultados de distancia existentes?\nAceptar: eliminar\nCancelar: mantener (se ocultarán si no aplican).');
                    if (clearDist) {
                        state.resultadosDist = [];
                        renderDistTables();
                    }
                }
            }

            // Aplicar nuevo estado
            lastEquipmentId = equipmentSelect.value;
            lastPrecision = newPrecision;
            lastAllowDist = newAllowDist;
            syncUiWithEquipment();
            renderResultados();
            renderDistTables();
        });

        // Validadores auxiliares
        function clampMinuteSecond(input) {
            const v = Number(input.value);
            if (Number.isNaN(v) || v < 0 || v > 59) {
                input.setCustomValidity('Debe estar entre 0 y 59');
            } else {
                input.setCustomValidity('');
            }
        }
        function requireNumber(input) {
            const v = input.value;
            if (v === '' || Number.isNaN(Number(v))) {
                input.setCustomValidity('Campo requerido');
            } else {
                input.setCustomValidity('');
            }
        }

        // Abrir modal para nuevo resultado
        btnAddResultado.addEventListener('click', () => {
            resIdx.value = -1;
            resPg.value = 0; resPm.value = 0; resPs.value = 0;
            resOg.value = 0; resOm.value = 0; resOs.value = 0;
            resPrec.value = state.currentPrecision === 'lineal' ? 2 : 2;
            resErr.value = 0;
            lblPrecision.textContent = state.currentPrecision === 'lineal' ? 'Precisión (mm)' : 'Precisión (segundos)';
            helpPrecision.textContent = state.currentPrecision === 'lineal' ? 'En milímetros (mm)' : 'En segundos ( ")';
            document.getElementById('formResultado').classList.remove('was-validated');
            modalResultado.show();
        });

        // Guardar resultado (nuevo/edición)
        btnGuardarResultado.addEventListener('click', () => {
            const formR = document.getElementById('formResultado');
            // Validaciones explícitas
            [resPm, resPs, resOm, resOs].forEach(clampMinuteSecond);
            [resPg, resOg, resPrec, resErr].forEach(requireNumber);
            if (!formR.checkValidity()) {
                formR.classList.add('was-validated');
                return;
            }
            const obj = {
                tipo_resultado: state.currentPrecision,
                valor_patron_grados: parseInt(resPg.value||'0',10),
                valor_patron_minutos: parseInt(resPm.value||'0',10),
                valor_patron_segundos: parseInt(resPs.value||'0',10),
                valor_obtenido_grados: parseInt(resOg.value||'0',10),
                valor_obtenido_minutos: parseInt(resOm.value||'0',10),
                valor_obtenido_segundos: parseInt(resOs.value||'0',10),
                precision: parseInt(resPrec.value||'0',10),
                error_segundos: parseInt(resErr.value||'0',10)
            };
            const idx = parseInt(resIdx.value, 10);
            if (isNaN(idx) || idx < 0) { state.resultados.push(obj); } else { state.resultados[idx] = obj; }
            renderResultados();
            modalResultado.hide();
        });

        // Delegación de eventos para editar/eliminar resultado
        tbodyResultados.addEventListener('click', (ev) => {
            const btn = ev.target.closest('button');
            if (!btn) return;
            const action = btn.getAttribute('data-action');
            const idx = parseInt(btn.getAttribute('data-index'));
            if (action === 'edit-res') {
                const r = state.resultados[idx];
                resIdx.value = idx;
                resPg.value = r.valor_patron_grados; resPm.value = r.valor_patron_minutos; resPs.value = r.valor_patron_segundos;
                resOg.value = r.valor_obtenido_grados; resOm.value = r.valor_obtenido_minutos; resOs.value = r.valor_obtenido_segundos;
                resPrec.value = r.precision ?? r.precision_val ?? 0; resErr.value = r.error_segundos ?? 0;
                lblPrecision.textContent = state.currentPrecision === 'lineal' ? 'Precisión (mm)' : 'Precisión (segundos)';
                helpPrecision.textContent = state.currentPrecision === 'lineal' ? 'En milímetros (mm)' : 'En segundos ( ")';
                modalResultado.show();
            } else if (action === 'del-res') {
                if (confirm('¿Eliminar este resultado?')) {
                    state.resultados.splice(idx, 1);
                    renderResultados();
                }
            }
        });

        // Abrir modal distancia (según botón)
        function openDistModal(conPrisma, index = -1, item = null) {
            distIdx.value = index;
            distConPrisma.checked = !!conPrisma;
            distConPrisma.disabled = index >= 0; // en edición no cambiar tipo
            if (item) {
                distPcm.value = item.punto_control_metros; distDom.value = item.distancia_obtenida_metros; distVm.value = item.variacion_metros;
                distPb.value = item.precision_base_mm; distPp.value = item.precision_ppm;
            } else {
                distPcm.value = 0; distDom.value = 0; distVm.value = 0; distPb.value = 2; distPp.value = 2;
            }
            modalDistancia.show();
        }

    btnAddDistConPrisma.addEventListener('click', () => { document.getElementById('formDistancia').classList.remove('was-validated'); openDistModal(true); });
    btnAddDistSinPrisma.addEventListener('click', () => { document.getElementById('formDistancia').classList.remove('was-validated'); openDistModal(false); });

        btnGuardarDistancia.addEventListener('click', () => {
            const formD = document.getElementById('formDistancia');
            [distPcm, distDom, distVm, distPb, distPp].forEach(requireNumber);
            if (!formD.checkValidity()) {
                formD.classList.add('was-validated');
                return;
            }
            const obj = {
                punto_control_metros: parseFloat(distPcm.value||'0'),
                distancia_obtenida_metros: parseFloat(distDom.value||'0'),
                variacion_metros: parseFloat(distVm.value||'0'),
                precision_base_mm: parseInt(distPb.value||'0',10),
                precision_ppm: parseInt(distPp.value||'0',10),
                con_prisma: !!distConPrisma.checked,
            };
            const idx = parseInt(distIdx.value, 10);
            if (isNaN(idx) || idx < 0) {
                state.resultadosDist.push(obj);
            } else {
                // Encontrar el subconjunto según con/sin prisma para el índice relativo
                const list = obj.con_prisma ? state.resultadosDist.filter(r=>!!r.con_prisma) : state.resultadosDist.filter(r=>!r.con_prisma);
                const target = list[idx];
                const absIndex = state.resultadosDist.indexOf(target);
                if (absIndex >= 0) state.resultadosDist[absIndex] = obj;
            }
            renderDistTables();
            modalDistancia.hide();
        });

        // Delegación para editar/eliminar distancias
        function handleDistAction(ev, withPrism) {
            const btn = ev.target.closest('button'); if (!btn) return;
            const action = btn.getAttribute('data-action'); const relIdx = parseInt(btn.getAttribute('data-index'));
            const subset = state.resultadosDist.filter(r => !!r.con_prisma === withPrism);
            const item = subset[relIdx]; if (!item) return;
            const absIndex = state.resultadosDist.indexOf(item);
            if (action === 'edit-dist') {
                openDistModal(withPrism, relIdx, item);
            } else if (action === 'del-dist') {
                if (confirm('¿Eliminar este registro de distancia?')) {
                    if (absIndex >= 0) state.resultadosDist.splice(absIndex, 1);
                    renderDistTables();
                }
            }
        }
        tbodyDistConPrisma.addEventListener('click', (ev)=>handleDistAction(ev, true));
        tbodyDistSinPrisma.addEventListener('click', (ev)=>handleDistAction(ev, false));

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
                calibrator_id: technicianSelect.value ? Number(technicianSelect.value) : null,
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
    await Promise.all([loadClients(), loadEquipment(), loadTechnicians()]);
        syncUiWithEquipment();
        // Inicializar "último equipo" tras la primera sincronización
        lastEquipmentId = equipmentSelect.value || '';
        lastPrecision = state.currentPrecision;
        lastAllowDist = state.allowDistWithPrism;
        renderResultados();
        renderDistTables();
    });
    </script>
</body>
</html>
