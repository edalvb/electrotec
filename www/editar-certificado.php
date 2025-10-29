<!-- editar-certificado.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Editar Certificado</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
    <style>
        .skeleton { background: linear-gradient(90deg, #eee, #f5f5f5, #eee); animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { background-position: 0 0; } 100% { background-position: 200% 0; } }
    </style>
    </head>
<body>
    <div class="d-flex">
        <?php $activePage = 'certificados'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Editar Certificado';
            $pageSubtitle = 'Actualiza fechas, resultados y condiciones ambientales';
            $headerActionsHtml = '';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="card glass p-4 rounded-lg">
                <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
                <div id="successAlert" class="alert alert-success d-none" role="alert"></div>

                <form id="certificateForm" class="d-none">
                    <div class="card glass-subtle p-4 mb-4">
                        <h6 class="mb-3 fw-bold">Datos generales</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Número</label>
                                <input id="certificateNumber" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Técnico calibrador</label>
                                <input id="technicianName" class="form-control" readonly>
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
                                    <input id="isCalibration" class="form-check-input" type="checkbox">
                                    <label class="form-check-label" for="isCalibration">Calibración</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="isMaintenance" class="form-check-input" type="checkbox">
                                    <label class="form-check-label" for="isMaintenance">Mantenimiento</label>
                                </div>
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
                            <div class="col-12">
                                <label class="form-label">Observaciones</label>
                                <textarea id="observations" class="form-control" rows="3" placeholder="Ingresa observaciones..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card glass-subtle p-4 mb-4">
                        <h6 class="mb-3 fw-bold">Resultados</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0" id="resultTableTitle">Resultados</h6>
                            <button id="btnAddResultado" type="button" class="btn btn-sm btn-primary">Agregar resultado</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle w-100">
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

                        <div id="distSections" class="mt-4 d-none">
                            <h6 class="fw-bold">Resultados de Distancia</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">Medición con Prisma</span>
                                        <button id="btnAddDistConPrisma" type="button" class="btn btn-sm btn-outline-primary">Agregar más</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle w-100">
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
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">Medición sin Prisma</span>
                                        <button id="btnAddDistSinPrisma" type="button" class="btn btn-sm btn-outline-primary">Agregar más</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped align-middle w-100">
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

                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                            Cancelar
                        </button>
                        <button id="saveBtn" type="submit" class="btn btn-primary">
                            Guardar Cambios
                        </button>
                    </div>
                </form>

                <div id="loadingState" class="skeleton" style="height: 160px;"></div>
            </div>

            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', async () => {
        try { Auth.requireAuth('admin'); } catch (e) { return; }

        const params = new URLSearchParams(window.location.search);
        const certId = params.get('id');
        if (!certId) {
            alert('Falta el parámetro id');
            window.location.href = 'certificados.php';
            return;
        }

        const API_FIND = `api/certificates.php?action=find&id=${encodeURIComponent(certId)}`;
        const API_UPDATE = `api/certificates.php?action=update&id=${encodeURIComponent(certId)}`;

        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');
        const form = document.getElementById('certificateForm');
        const loading = document.getElementById('loadingState');

        const certificateNumber = document.getElementById('certificateNumber');
        const technicianName = document.getElementById('technicianName');
        const calibrationDate = document.getElementById('calibrationDate');
        const nextCalibrationDate = document.getElementById('nextCalibrationDate');
        const temperature = document.getElementById('temperature');
        const humidity = document.getElementById('humidity');
        const pressure = document.getElementById('pressure');
        const isCalibration = document.getElementById('isCalibration');
        const isMaintenance = document.getElementById('isMaintenance');
        const observations = document.getElementById('observations');
        const equipmentStatus = document.getElementById('equipmentStatus');
        const btnAddResultado = document.getElementById('btnAddResultado');
        const tbodyResultados = document.getElementById('tbodyResultados');
        const thPrecision = document.getElementById('thPrecision');
        const resultTableTitle = document.getElementById('resultTableTitle');
        const btnAddDistConPrisma = document.getElementById('btnAddDistConPrisma');
        const btnAddDistSinPrisma = document.getElementById('btnAddDistSinPrisma');
        const tbodyDistConPrisma = document.getElementById('tbodyDistConPrisma');
        const tbodyDistSinPrisma = document.getElementById('tbodyDistSinPrisma');

        const state = {
            currentPrecision: 'segundos',
            allowDistWithPrism: true,
            resultados: [],
            resultadosDist: [],
        };

        function clearAlerts(){ errorAlert.classList.add('d-none'); successAlert.classList.add('d-none'); }
        function setError(m){ errorAlert.textContent = m; errorAlert.classList.remove('d-none'); }
        function setSuccess(m){ successAlert.textContent = m; successAlert.classList.remove('d-none'); }

        function fmtDms(g,m,s){ const gg=Number(g)||0, mm=Number(m)||0, ss=Number(s)||0; return `${gg}° ${String(mm).padStart(2,'0')}' ${String(ss).padStart(2,'0')}"`; }

        function renderResultados(){
            if (!state.resultados.length) { tbodyResultados.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>'; return; }
            tbodyResultados.innerHTML = state.resultados.map(r => {
                const precVal = (r.precision ?? r.precision_val ?? 0);
                const precStr = state.currentPrecision === 'lineal' ? `± ${String(Math.max(0, parseInt(precVal||0))).padStart(2,'0')} mm` : `± ${String(Math.max(0, parseInt(precVal||0))).padStart(2,'0')}"`;
                return `<tr><td>${fmtDms(r.valor_patron_grados, r.valor_patron_minutos, r.valor_patron_segundos)}</td><td>${fmtDms(r.valor_obtenido_grados, r.valor_obtenido_minutos, r.valor_obtenido_segundos)}</td><td>${precStr}</td><td>${String(r.error_segundos||0).padStart(2,'0')}"</td></tr>`;
            }).join('');
        }
        function renderDist(){
            const con = state.resultadosDist.filter(r => !!r.con_prisma);
            const sin = state.resultadosDist.filter(r => !r.con_prisma);
            tbodyDistConPrisma.innerHTML = con.length ? con.map(r => `<tr><td>${Number(r.punto_control_metros).toFixed(3)} m.</td><td>${Number(r.distancia_obtenida_metros).toFixed(3)} m.</td><td>${r.precision_base_mm} mm + ${r.precision_ppm} ppm</td><td>${Number(r.variacion_metros).toFixed(3)} m.</td></tr>`).join('') : '<tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>';
            tbodyDistSinPrisma.innerHTML = sin.length ? sin.map(r => `<tr><td>${Number(r.punto_control_metros).toFixed(3)} m.</td><td>${Number(r.distancia_obtenida_metros).toFixed(3)} m.</td><td>${r.precision_base_mm} mm + ${r.precision_ppm} ppm</td><td>${Number(r.variacion_metros).toFixed(3)} m.</td></tr>`).join('') : '<tr><td colspan="4" class="text-center text-muted">Sin filas</td></tr>';
        }

        btnAddResultado.addEventListener('click', () => {
            const pg = prompt('Valor de Patrón - Grados (entero):', '0'); if (pg===null) return;
            const pm = prompt('Valor de Patrón - Minutos (0-59):', '0'); if (pm===null) return;
            const ps = prompt('Valor de Patrón - Segundos (0-59):', '0'); if (ps===null) return;
            const og = prompt('Valor Obtenido - Grados (entero):', '0'); if (og===null) return;
            const om = prompt('Valor Obtenido - Minutos (0-59):', '0'); if (om===null) return;
            const os = prompt('Valor Obtenido - Segundos (0-59):', '0'); if (os===null) return;
            const prec = prompt('Precisión (mm o ")', '2'); if (prec===null) return;
            const err = prompt('Error (en segundos):', '0'); if (err===null) return;
            state.resultados.push({
                tipo_resultado: state.currentPrecision,
                valor_patron_grados: parseInt(pg||'0',10), valor_patron_minutos: parseInt(pm||'0',10), valor_patron_segundos: parseInt(ps||'0',10),
                valor_obtenido_grados: parseInt(og||'0',10), valor_obtenido_minutos: parseInt(om||'0',10), valor_obtenido_segundos: parseInt(os||'0',10),
                precision: parseInt(prec||'0',10), error_segundos: parseInt(err||'0',10)
            });
            renderResultados();
        });
        function promptDist(conPrisma){
            const pcm = prompt('Punto de Control (en metros):', '0.000'); if (pcm===null) return;
            const dom = prompt('Distancia Obtenida (en metros):', '0.000'); if (dom===null) return;
            const vm = prompt('Variación (en metros):', '0.000'); if (vm===null) return;
            const pb = prompt('Precisión Base (en mm):', '2'); if (pb===null) return;
            const pp = prompt('Precisión PPM:', '2'); if (pp===null) return;
            state.resultadosDist.push({ punto_control_metros: parseFloat(pcm||'0'), distancia_obtenida_metros: parseFloat(dom||'0'), variacion_metros: parseFloat(vm||'0'), precision_base_mm: parseInt(pb||'0',10), precision_ppm: parseInt(pp||'0',10), con_prisma: !!conPrisma });
            renderDist();
        }
        btnAddDistConPrisma.addEventListener('click',()=>promptDist(true));
        btnAddDistSinPrisma.addEventListener('click',()=>promptDist(false));

        async function load(){
            try {
                const res = await Auth.fetchWithAuth(API_FIND);
                if (!res || res.error) throw new Error(res?.message || 'No se pudo cargar');
                const data = res.data || res;
                certificateNumber.value = data.certificate_number || '';
                technicianName.value = data.technician_name || '';
                calibrationDate.value = (data.calibration_date || '').slice(0,10);
                nextCalibrationDate.value = (data.next_calibration_date || '').slice(0,10);
                const cond = data.lab_conditions || {};
                temperature.value = cond.temperatura_celsius ?? cond.temperature ?? '';
                humidity.value = cond.humedad_relativa_porc ?? cond.humidity ?? '';
                pressure.value = cond.presion_atm_mmhg ?? cond.pressure ?? '';
                // Parsear results (puede venir como string JSON desde DB)
                let resultsJson = {};
                try {
                    if (typeof data.results === 'string' && data.results.trim() !== '') {
                        resultsJson = JSON.parse(data.results);
                    } else if (typeof data.results === 'object' && data.results !== null) {
                        resultsJson = data.results;
                    }
                } catch (_) {
                    resultsJson = {};
                }
                const st = (resultsJson && typeof resultsJson === 'object' ? (resultsJson.service_type || {}) : {});
                const toBool = (v) => v === true || v === 1 || v === '1' || v === 'true';
                isCalibration.checked = toBool(st.calibration);
                isMaintenance.checked = toBool(st.maintenance);
                observations.value = (resultsJson && typeof resultsJson.observations === 'string') ? resultsJson.observations : '';
                equipmentStatus.value = (resultsJson && typeof resultsJson.status === 'string') ? resultsJson.status : (data.status || '');
                state.resultados = Array.isArray(data.resultados) ? data.resultados : [];
                state.resultadosDist = Array.isArray(data.resultados_distancia) ? data.resultados_distancia : [];
                // Deducir precision por primera fila
                const first = state.resultados[0] || {};
                state.currentPrecision = (first.tipo_resultado === 'lineal') ? 'lineal' : 'segundos';
                thPrecision.textContent = state.currentPrecision === 'lineal' ? 'Precisión (mm)' : 'Precisión';
                resultTableTitle.textContent = state.currentPrecision === 'lineal' ? 'Resultados (precisión lineal en mm)' : 'Resultados (precisión angular en segundos)';
                // Si hay distancia, mostrar secciones
                document.getElementById('distSections').classList.toggle('d-none', !(state.resultadosDist && state.resultadosDist.length));
                renderResultados();
                renderDist();
                loading.classList.add('d-none');
                form.classList.remove('d-none');
            } catch (e) {
                loading.classList.add('d-none');
                setError(e.message);
            }
        }

        form.addEventListener('submit', async (ev)=>{
            ev.preventDefault();
            clearAlerts();
            const payload = {
                calibration_date: calibrationDate.value,
                next_calibration_date: nextCalibrationDate.value,
                environmental_conditions: {
                    temperature: temperature.value ? parseFloat(temperature.value) : null,
                    humidity: humidity.value ? parseFloat(humidity.value) : null,
                    pressure: pressure.value ? parseFloat(pressure.value) : null,
                },
                observations: observations.value.trim() || null,
                status: equipmentStatus.value || null,
                service_type: { calibration: isCalibration.checked, maintenance: isMaintenance.checked },
                resultados: state.resultados,
                resultados_distancia: state.resultadosDist,
            };
            try {
                const res = await Auth.fetchWithAuth(API_UPDATE, { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                if (res && res.error) throw new Error(res.message || 'No se pudo actualizar');
                setSuccess('Cambios guardados');
                setTimeout(()=>{ window.location.href = 'certificados.php'; }, 1200);
            } catch (e) {
                setError(e.message);
            }
        });

        await load();
    });
    </script>
</body>
</html>
