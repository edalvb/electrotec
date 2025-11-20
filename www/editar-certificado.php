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
                                <label class="form-label">Cliente</label>
                                <input id="clientName" class="form-control" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Equipo</label>
                                <input id="equipmentName" class="form-control" readonly>
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
                            <!-- Estado del Equipo: Campo oculto (se mantiene el valor existente) -->
                            <input type="hidden" id="equipmentStatus" value="approved">
                            <!-- <div class="col-md-6">
                                <label class="form-label">Estado del Equipo</label>
                                <select id="equipmentStatus" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    <option value="approved">Aprobado</option>
                                    <option value="conditional">Aprobado con observaciones</option>
                                    <option value="rejected">Rechazado</option>
                                </select>
                            </div> -->
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
                                <thead id="theadResultados">
                                    <tr>
                                        <th id="thCol1">Valor de Patrón</th>
                                        <th id="thCol2">Valor Obtenido</th>
                                        <th id="thCol3">Precisión</th>
                                        <th id="thCol4">Error</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyResultados">
                                    <tr><td colspan="5" class="text-center text-muted">Sin filas</td></tr>
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

            <!-- Modal Resultados Generales -->
            <div class="modal fade" id="modalResultados" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Resultado</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formResultados">
                                <input type="hidden" id="resIndex">
                                
                                <!-- Campos para Vertical/Horizontal -->
                                <div id="fieldsVerticalHorizontal" class="d-none">
                                    <div class="mb-3" id="blockVH_Label">
                                        <label class="form-label">Etiqueta (Ángulo)</label>
                                        <input type="text" id="resLabel" class="form-control">
                                    </div>
                                    <div class="row g-2 mb-3" id="blockVH_Patron">
                                        <div class="col-12"><label class="form-label fw-bold">Valor Patrón</label></div>
                                        <div class="col-md-4"><input type="number" id="resPatronG" class="form-control" placeholder="Grados"></div>
                                        <div class="col-md-4"><input type="number" id="resPatronM" class="form-control" placeholder="Minutos"></div>
                                        <div class="col-md-4"><input type="number" id="resPatronS" class="form-control" placeholder="Segundos"></div>
                                        <div class="col-md-4"><input type="number" id="resPatronG_F" class="form-control" placeholder="Grados Final"></div>
                                        <div class="col-md-4"><input type="number" id="resPatronM_F" class="form-control" placeholder="Minutos Final"></div>
                                        <div class="col-md-4"><input type="number" id="resPatronS_F" class="form-control" placeholder="Segundos Final"></div>
                                    </div>
                                    <div class="row g-2 mb-3" id="blockVH_Inicial">
                                        <div class="col-12"><label class="form-label fw-bold">Valor Inicial (Obtenido)</label></div>
                                        <div class="col-md-4"><input type="number" id="resObtG" class="form-control" placeholder="Grados"></div>
                                        <div class="col-md-4"><input type="number" id="resObtM" class="form-control" placeholder="Minutos"></div>
                                        <div class="col-md-4"><input type="number" id="resObtS" class="form-control" placeholder="Segundos"></div>
                                    </div>
                                    <div class="row g-2 mb-3" id="blockVH_Final">
                                        <div class="col-12"><label class="form-label fw-bold">Valor Final (Obtenido)</label></div>
                                        <div class="col-md-4"><input type="number" id="resObtG_F" class="form-control" placeholder="Grados"></div>
                                        <div class="col-md-4"><input type="number" id="resObtM_F" class="form-control" placeholder="Minutos"></div>
                                        <div class="col-md-4"><input type="number" id="resObtS_F" class="form-control" placeholder="Segundos"></div>
                                    </div>
                                </div>

                                <!-- Campos para Normal (Angular/Lineal) -->
                                <div id="fieldsNormal">
                                    <div class="row g-2 mb-3" id="blockN_Patron">
                                        <div class="col-12"><label class="form-label fw-bold">Valor Patrón</label></div>
                                        <div class="col-md-4"><input type="number" id="resPatronG_N" class="form-control" placeholder="Grados"></div>
                                        <div class="col-md-4"><input type="number" id="resPatronM_N" class="form-control" placeholder="Minutos"></div>
                                        <div class="col-md-4"><input type="number" id="resPatronS_N" class="form-control" placeholder="Segundos"></div>
                                    </div>
                                    <div class="row g-2 mb-3" id="blockN_Obtenido">
                                        <div class="col-12"><label class="form-label fw-bold">Valor Obtenido</label></div>
                                        <div class="col-md-4"><input type="number" id="resObtG_N" class="form-control" placeholder="Grados"></div>
                                        <div class="col-md-4"><input type="number" id="resObtM_N" class="form-control" placeholder="Minutos"></div>
                                        <div class="col-md-4"><input type="number" id="resObtS_N" class="form-control" placeholder="Segundos"></div>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-md-6" id="block_Precision">
                                        <label class="form-label">Precisión</label>
                                        <input type="number" step="any" id="resPrecision" class="form-control">
                                    </div>
                                    <div class="col-md-6" id="block_Error">
                                        <label class="form-label">Error (segundos)</label>
                                        <input type="number" step="any" id="resError" class="form-control">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btnSaveResultado">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Resultados Distancia -->
            <div class="modal fade" id="modalDistancia" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Distancia</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formDistancia">
                                <input type="hidden" id="distIndex">
                                <input type="hidden" id="distConPrisma">
                                
                                <div class="mb-3" id="blockDist_Punto">
                                    <label class="form-label">Punto de Control (m)</label>
                                    <input type="number" step="0.001" id="distPuntoControl" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Distancia Obtenida (m)</label>
                                    <input type="number" step="0.001" id="distObtenida" class="form-control">
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label">Precisión Base (mm)</label>
                                        <input type="number" step="any" id="distPrecBase" class="form-control">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Precisión PPM</label>
                                        <input type="number" step="any" id="distPrecPPM" class="form-control">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Variación (m)</label>
                                    <input type="number" step="0.001" id="distVariacion" class="form-control">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" id="btnSaveDistancia">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>

            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/uppercase-inputs.js"></script>
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

        // Campos generales
        const certificateNumber = document.getElementById('certificateNumber');
        const clientName = document.getElementById('clientName');
        const equipmentName = document.getElementById('equipmentName');
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
        
        // Tablas
        const btnAddResultado = document.getElementById('btnAddResultado');
        const tbodyResultados = document.getElementById('tbodyResultados');
        const resultTableTitle = document.getElementById('resultTableTitle');
        const btnAddDistConPrisma = document.getElementById('btnAddDistConPrisma');
        const btnAddDistSinPrisma = document.getElementById('btnAddDistSinPrisma');
        const tbodyDistConPrisma = document.getElementById('tbodyDistConPrisma');
        const tbodyDistSinPrisma = document.getElementById('tbodyDistSinPrisma');

        // Modales
        const modalResultados = new bootstrap.Modal(document.getElementById('modalResultados'));
        const modalDistancia = new bootstrap.Modal(document.getElementById('modalDistancia'));
        
        // Elementos Modal Resultados
        const formResultados = document.getElementById('formResultados');
        const resIndex = document.getElementById('resIndex');
        const fieldsVerticalHorizontal = document.getElementById('fieldsVerticalHorizontal');
        const fieldsNormal = document.getElementById('fieldsNormal');
        
        // Bloques contenedores para visibilidad
        const blockVH_Label = document.getElementById('blockVH_Label');
        const blockVH_Patron = document.getElementById('blockVH_Patron');
        const blockVH_Inicial = document.getElementById('blockVH_Inicial');
        const blockVH_Final = document.getElementById('blockVH_Final');
        const blockN_Patron = document.getElementById('blockN_Patron');
        const blockN_Obtenido = document.getElementById('blockN_Obtenido');
        const block_Precision = document.getElementById('block_Precision');
        const block_Error = document.getElementById('block_Error');

        const resLabel = document.getElementById('resLabel');
        const resPatronG = document.getElementById('resPatronG');
        const resPatronM = document.getElementById('resPatronM');
        const resPatronS = document.getElementById('resPatronS');
        const resPatronG_F = document.getElementById('resPatronG_F');
        const resPatronM_F = document.getElementById('resPatronM_F');
        const resPatronS_F = document.getElementById('resPatronS_F');
        const resObtG = document.getElementById('resObtG');
        const resObtM = document.getElementById('resObtM');
        const resObtS = document.getElementById('resObtS');
        const resObtG_F = document.getElementById('resObtG_F');
        const resObtM_F = document.getElementById('resObtM_F');
        const resObtS_F = document.getElementById('resObtS_F');
        const resPatronG_N = document.getElementById('resPatronG_N');
        const resPatronM_N = document.getElementById('resPatronM_N');
        const resPatronS_N = document.getElementById('resPatronS_N');
        const resObtG_N = document.getElementById('resObtG_N');
        const resObtM_N = document.getElementById('resObtM_N');
        const resObtS_N = document.getElementById('resObtS_N');
        const resPrecision = document.getElementById('resPrecision');
        const resError = document.getElementById('resError');
        const btnSaveResultado = document.getElementById('btnSaveResultado');

        // Elementos Modal Distancia
        const formDistancia = document.getElementById('formDistancia');
        const distIndex = document.getElementById('distIndex');
        const distConPrisma = document.getElementById('distConPrisma');
        const distPuntoControl = document.getElementById('distPuntoControl');
        const blockDistPunto = document.getElementById('blockDist_Punto');
        const distObtenida = document.getElementById('distObtenida');
        const distPrecBase = document.getElementById('distPrecBase');
        const distPrecPPM = document.getElementById('distPrecPPM');
        const distVariacion = document.getElementById('distVariacion');
        const btnSaveDistancia = document.getElementById('btnSaveDistancia');

        const state = {
            currentPrecision: 'segundos',
            allowDistWithPrism: true,
            resultados: [],
            resultadosDist: [],
            equipmentType: '' // Para saber si es "Con prisma"
        };

        function clearAlerts(){ errorAlert.classList.add('d-none'); successAlert.classList.add('d-none'); }
        function setError(m){ errorAlert.textContent = m; errorAlert.classList.remove('d-none'); }
        function setSuccess(m){ successAlert.textContent = m; successAlert.classList.remove('d-none'); }

        function fmtDms(g,m,s){ const gg=Number(g)||0, mm=Number(m)||0, ss=Number(s)||0; return `${gg}° ${String(mm).padStart(2,'0')}' ${String(ss).padStart(2,'0')}"`; }

        // --- Lógica de Resultados Generales ---

        window.openModalResultados = (index = null) => {
            resIndex.value = index !== null ? index : -1;
            const isEdit = index !== null;
            const item = isEdit ? state.resultados[index] : {};
            
            // Resetear formulario
            formResultados.reset();
            
            // Mostrar campos según tipo
            const isVH = state.currentPrecision === 'vertical_horizontal';
            fieldsVerticalHorizontal.classList.toggle('d-none', !isVH);
            fieldsNormal.classList.toggle('d-none', isVH);

            // Llenar valores
            if (isVH) {
                resLabel.value = item.label_resultado || '';
                resPatronG.value = item.valor_patron_grados ?? '';
                resPatronM.value = item.valor_patron_minutos ?? '';
                resPatronS.value = item.valor_patron_segundos ?? '';
                resPatronG_F.value = item.valor_patron_grados_valfinal ?? '';
                resPatronM_F.value = item.valor_patron_minutos_valfinal ?? '';
                resPatronS_F.value = item.valor_patron_segundos_valfinal ?? '';
                resObtG.value = item.valor_obtenido_grados ?? '';
                resObtM.value = item.valor_obtenido_minutos ?? '';
                resObtS.value = item.valor_obtenido_segundos ?? '';
                resObtG_F.value = item.valor_obtenido_grados_valfinal ?? '';
                resObtM_F.value = item.valor_obtenido_minutos_valfinal ?? '';
                resObtS_F.value = item.valor_obtenido_segundos_valfinal ?? '';
            } else {
                resPatronG_N.value = item.valor_patron_grados ?? '';
                resPatronM_N.value = item.valor_patron_minutos ?? '';
                resPatronS_N.value = item.valor_patron_segundos ?? '';
                resObtG_N.value = item.valor_obtenido_grados ?? '';
                resObtM_N.value = item.valor_obtenido_minutos ?? '';
                resObtS_N.value = item.valor_obtenido_segundos ?? '';
            }
            resPrecision.value = item.precision ?? item.precision_val ?? '';
            resError.value = item.error_segundos ?? '';

            // Aplicar reglas de edición (SOLO si es edición, si es nuevo permitimos todo o aplicamos reglas por defecto?)
            // Asumiremos que al crear se permite todo, pero al editar se restringe.
            // O mejor, restringimos siempre según la regla.
            
            // Resetear visibilidad y estado disabled
            const allInputs = formResultados.querySelectorAll('input');
            allInputs.forEach(i => i.disabled = false);
            
            // Mostrar todos los bloques por defecto dentro de su contexto
            if (isVH) {
                blockVH_Label.classList.remove('d-none');
                blockVH_Patron.classList.remove('d-none');
                blockVH_Inicial.classList.remove('d-none');
                blockVH_Final.classList.remove('d-none');
                block_Precision.classList.remove('d-none');
                block_Error.classList.remove('d-none');
            } else {
                blockN_Patron.classList.remove('d-none');
                blockN_Obtenido.classList.remove('d-none');
                block_Precision.classList.remove('d-none');
                block_Error.classList.remove('d-none');
            }

            if (isEdit) {
                if (isVH) {
                    // Regla: Editar SOLAMENTE "valor inicial", "valor final" y "error".
                    // Ocultar lo que no se edita
                    blockVH_Label.classList.add('d-none');
                    blockVH_Patron.classList.add('d-none');
                    block_Precision.classList.add('d-none');
                    
                    // Asegurar que lo visible sea editable (ya lo reseteamos arriba, pero por claridad)
                    resObtG.disabled = false; resObtM.disabled = false; resObtS.disabled = false;
                    resObtG_F.disabled = false; resObtM_F.disabled = false; resObtS_F.disabled = false;
                    resError.disabled = false;
                } else {
                    // Regla: Editar SOLAMENTE precisión y error.
                    // Ocultar lo que no se edita
                    blockN_Patron.classList.add('d-none');
                    blockN_Obtenido.classList.add('d-none');
                    
                    // Asegurar que lo visible sea editable
                    resPrecision.disabled = false;
                    resError.disabled = false;
                }
            } else {
                // Si es NUEVO registro, mostramos todo para que puedan ingresar los datos.
                // No ocultamos nada.
            }

            modalResultados.show();
        };

        btnSaveResultado.addEventListener('click', () => {
            const idx = parseInt(resIndex.value);
            const isVH = state.currentPrecision === 'vertical_horizontal';
            
            const newItem = {
                tipo_resultado: state.currentPrecision,
                error_segundos: parseFloat(resError.value || 0),
                precision: parseFloat(resPrecision.value || 0)
            };

            if (isVH) {
                newItem.label_resultado = resLabel.value;
                newItem.valor_patron_grados = parseInt(resPatronG.value||0);
                newItem.valor_patron_minutos = parseInt(resPatronM.value||0);
                newItem.valor_patron_segundos = parseInt(resPatronS.value||0);
                newItem.valor_patron_grados_valfinal = parseInt(resPatronG_F.value||0);
                newItem.valor_patron_minutos_valfinal = parseInt(resPatronM_F.value||0);
                newItem.valor_patron_segundos_valfinal = parseInt(resPatronS_F.value||0);
                
                newItem.valor_obtenido_grados = parseInt(resObtG.value||0);
                newItem.valor_obtenido_minutos = parseInt(resObtM.value||0);
                newItem.valor_obtenido_segundos = parseInt(resObtS.value||0);
                newItem.valor_obtenido_grados_valfinal = parseInt(resObtG_F.value||0);
                newItem.valor_obtenido_minutos_valfinal = parseInt(resObtM_F.value||0);
                newItem.valor_obtenido_segundos_valfinal = parseInt(resObtS_F.value||0);
            } else {
                newItem.valor_patron_grados = parseInt(resPatronG_N.value||0);
                newItem.valor_patron_minutos = parseInt(resPatronM_N.value||0);
                newItem.valor_patron_segundos = parseInt(resPatronS_N.value||0);
                newItem.valor_obtenido_grados = parseInt(resObtG_N.value||0);
                newItem.valor_obtenido_minutos = parseInt(resObtM_N.value||0);
                newItem.valor_obtenido_segundos = parseInt(resObtS_N.value||0);
            }

            // Si es edición, preservar valores que no se editaron (por si acaso)
            if (idx >= 0) {
                state.resultados[idx] = { ...state.resultados[idx], ...newItem };
            } else {
                state.resultados.push(newItem);
            }
            renderResultados();
            modalResultados.hide();
        });

        window.deleteRow = (index) => {
            if (confirm('¿Estás seguro de eliminar esta fila?')) {
                state.resultados.splice(index, 1);
                renderResultados();
            }
        };

        function renderResultados(){
            const colspan = state.currentPrecision === 'vertical_horizontal' ? '6' : '5'; // +1 por acciones
            if (!state.resultados.length) { tbodyResultados.innerHTML = `<tr><td colspan="${colspan}" class="text-center text-muted">Sin filas</td></tr>`; return; }
            
            // Actualizar cabecera si es necesario (agregar columna acciones)
            const theadRow = document.querySelector('#theadResultados tr');
            if (!theadRow.querySelector('.th-actions')) {
                const th = document.createElement('th');
                th.className = 'th-actions';
                th.textContent = 'Acciones';
                theadRow.appendChild(th);
            }

            tbodyResultados.innerHTML = state.resultados.map((r, i) => {
                const tipo = r.tipo_resultado || state.currentPrecision;
                const label = r.label_resultado || '';
                let html = '';
                
                if (tipo === 'vertical_horizontal') {
                    const patronIni = fmtDms(r.valor_patron_grados, r.valor_patron_minutos, r.valor_patron_segundos);
                    const patronFin = fmtDms(r.valor_patron_grados_valfinal || 0, r.valor_patron_minutos_valfinal || 0, r.valor_patron_segundos_valfinal || 0);
                    const obtIni = fmtDms(r.valor_obtenido_grados, r.valor_obtenido_minutos, r.valor_obtenido_segundos);
                    const obtFin = fmtDms(r.valor_obtenido_grados_valfinal || 0, r.valor_obtenido_minutos_valfinal || 0, r.valor_obtenido_segundos_valfinal || 0);
                    const errStr = `${String(r.error_segundos||0).padStart(2,'0')}"`;
                    html = `<td><strong>${label || 'Sin etiqueta'}</strong></td><td>${patronIni}<br>${patronFin}</td><td>${obtIni}<br>${obtFin}</td><td>${obtIni}<br>${obtFin}</td><td>${errStr}</td>`;
                } else {
                    const precVal = (r.precision ?? r.precision_val ?? 0);
                    const precStr = tipo === 'lineal' ? `± ${String(Math.max(0, parseInt(precVal||0))).padStart(2,'0')} mm` : `± ${String(Math.max(0, parseInt(precVal||0))).padStart(2,'0')}"`;
                    html = `<td>${fmtDms(r.valor_patron_grados, r.valor_patron_minutos, r.valor_patron_segundos)}</td><td>${fmtDms(r.valor_obtenido_grados, r.valor_obtenido_minutos, r.valor_obtenido_segundos)}</td><td>${precStr}</td><td>${String(r.error_segundos||0).padStart(2,'0')}"</td>`;
                }
                
                return `<tr>
                    ${html}
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="openModalResultados(${i})"><i class="bi bi-pencil"></i> Editar</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRow(${i})"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
            }).join('');
        }

        // --- Lógica de Resultados Distancia ---

        window.openModalDistancia = (index = null, conPrisma = true) => {
            // Buscar el índice real en el array global state.resultadosDist
            // El index que recibimos aquí es relativo a la tabla filtrada (con/sin prisma)
            // Necesitamos mapear esto correctamente.
            // Mejor estrategia: pasar el objeto directamente o buscarlo por referencia es complicado.
            // Vamos a reconstruir los índices.
            
            let realIndex = -1;
            let item = {};
            
            if (index !== null) {
                // Filtrar para encontrar el elemento correcto
                const filtered = state.resultadosDist.map((r, i) => ({...r, originalIndex: i}))
                                     .filter(r => !!r.con_prisma === conPrisma);
                if (filtered[index]) {
                    realIndex = filtered[index].originalIndex;
                    item = state.resultadosDist[realIndex];
                }
            }

            distIndex.value = realIndex;
            distConPrisma.value = conPrisma ? '1' : '0';
            
            // Resetear
            formDistancia.reset();
            // Asegurarse que el bloque esté visible por defecto (nuevo/edición previa)
            if (blockDistPunto) blockDistPunto.classList.remove('d-none');
            
            // Llenar
            distPuntoControl.value = item.punto_control_metros ?? '';
            distObtenida.value = item.distancia_obtenida_metros ?? '';
            distPrecBase.value = item.precision_base_mm ?? '';
            distPrecPPM.value = item.precision_ppm ?? '';
            distVariacion.value = item.variacion_metros ?? '';

            // Reglas de edición
            // "Cuando el tipo de equipo es Con prisma, la tabla 'Con/Sin prisma' debe editarse solo los valores presición, variacion y distancia obtenida."
            // Asumimos que si estamos en la tabla "Con prisma" (conPrisma=true), aplica la regla.
            // O si el equipo es de tipo prisma.
            
            // Si es edición
            if (index !== null) {
                // Regla: En edición (tanto Con Prisma como Sin Prisma) NO mostrar/editar "Punto de Control"
                if (blockDistPunto) blockDistPunto.classList.add('d-none');
                distPuntoControl.disabled = true;
                distObtenida.disabled = false;
                distPrecBase.disabled = false;
                distPrecPPM.disabled = false;
                distVariacion.disabled = false;
            } else {
                // Nuevo registro: todo editable
                if (blockDistPunto) blockDistPunto.classList.remove('d-none');
                distPuntoControl.disabled = false;
                distObtenida.disabled = false;
                distPrecBase.disabled = false;
                distPrecPPM.disabled = false;
                distVariacion.disabled = false;
            }

            modalDistancia.show();
        };

        btnSaveDistancia.addEventListener('click', () => {
            const idx = parseInt(distIndex.value);
            const isConPrisma = distConPrisma.value === '1';
            
            const newItem = {
                punto_control_metros: parseFloat(distPuntoControl.value || 0),
                distancia_obtenida_metros: parseFloat(distObtenida.value || 0),
                variacion_metros: parseFloat(distVariacion.value || 0),
                precision_base_mm: parseFloat(distPrecBase.value || 0),
                precision_ppm: parseFloat(distPrecPPM.value || 0),
                con_prisma: isConPrisma
            };

            if (idx >= 0) {
                state.resultadosDist[idx] = { ...state.resultadosDist[idx], ...newItem };
            } else {
                state.resultadosDist.push(newItem);
            }
            renderDist();
            modalDistancia.hide();
        });

        window.deleteDistRow = (index, conPrisma) => {
            if (confirm('¿Estás seguro de eliminar esta fila?')) {
                // Encontrar índice real
                const filtered = state.resultadosDist.map((r, i) => ({...r, originalIndex: i}))
                                     .filter(r => !!r.con_prisma === conPrisma);
                if (filtered[index]) {
                    const realIndex = filtered[index].originalIndex;
                    state.resultadosDist.splice(realIndex, 1);
                    renderDist();
                }
            }
        };

        function renderDist(){
            const con = state.resultadosDist.filter(r => !!r.con_prisma);
            const sin = state.resultadosDist.filter(r => !r.con_prisma);
            
            // Headers con columna acciones
            const updateHeader = (tableId) => {
                const theadRow = document.querySelector(`#${tableId}`).previousElementSibling.querySelector('tr');
                if (!theadRow.querySelector('.th-actions')) {
                    const th = document.createElement('th');
                    th.className = 'th-actions';
                    th.textContent = 'Acciones';
                    theadRow.appendChild(th);
                }
            };
            // Esto asume estructura HTML fija, cuidado. El HTML original tiene thead dentro de table.
            // tbodyDistConPrisma está dentro de table.
            
            const renderRows = (rows, isCon) => {
                if (!rows.length) return '<tr><td colspan="5" class="text-center text-muted">Sin filas</td></tr>';
                return rows.map((r, i) => `<tr>
                    <td>${Number(r.punto_control_metros).toFixed(3)} m.</td>
                    <td>${Number(r.distancia_obtenida_metros).toFixed(3)} m.</td>
                    <td>${r.precision_base_mm} mm + ${r.precision_ppm} ppm</td>
                    <td>${Number(r.variacion_metros).toFixed(3)} m.</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="openModalDistancia(${i}, ${isCon})"><i class="bi bi-pencil"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDistRow(${i}, ${isCon})"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`).join('');
            };

            // Asegurar cabeceras
            const tableCon = tbodyDistConPrisma.closest('table');
            if (tableCon && !tableCon.querySelector('thead .th-actions')) {
                const th = document.createElement('th'); th.className = 'th-actions'; th.textContent = 'Acciones';
                tableCon.querySelector('thead tr').appendChild(th);
            }
            const tableSin = tbodyDistSinPrisma.closest('table');
            if (tableSin && !tableSin.querySelector('thead .th-actions')) {
                const th = document.createElement('th'); th.className = 'th-actions'; th.textContent = 'Acciones';
                tableSin.querySelector('thead tr').appendChild(th);
            }

            tbodyDistConPrisma.innerHTML = renderRows(con, true);
            tbodyDistSinPrisma.innerHTML = renderRows(sin, false);
        }

        btnAddResultado.addEventListener('click', () => openModalResultados(null));
        btnAddDistConPrisma.addEventListener('click', () => openModalDistancia(null, true));
        btnAddDistSinPrisma.addEventListener('click', () => openModalDistancia(null, false));

        async function load(){
            try {
                const res = await Auth.fetchWithAuth(API_FIND);
                if (!res || res.error) throw new Error(res?.message || 'No se pudo cargar');
                const data = res.data || res;
                certificateNumber.value = data.certificate_number || '';
                clientName.value = data.client_name || 'Cliente no especificado';
                equipmentName.value = data.equipment_name || data.equipment_code || 'Equipo no especificado';
                technicianName.value = data.technician_name || '';
                calibrationDate.value = (data.calibration_date || '').slice(0,10);
                nextCalibrationDate.value = (data.next_calibration_date || '').slice(0,10);
                const cond = data.lab_conditions || {};
                temperature.value = cond.temperatura_celsius ?? cond.temperature ?? '';
                humidity.value = cond.humedad_relativa_porc ?? cond.humidity ?? '';
                pressure.value = cond.presion_atm_mmhg ?? cond.pressure ?? '';
                
                let resultsJson = {};
                try {
                    if (typeof data.results === 'string' && data.results.trim() !== '') {
                        resultsJson = JSON.parse(data.results);
                    } else if (typeof data.results === 'object' && data.results !== null) {
                        resultsJson = data.results;
                    }
                } catch (_) { resultsJson = {}; }
                
                const st = (resultsJson && typeof resultsJson === 'object' ? (resultsJson.service_type || {}) : {});
                const toBool = (v) => v === true || v === 1 || v === '1' || v === 'true';
                isCalibration.checked = toBool(st.calibration);
                isMaintenance.checked = toBool(st.maintenance);
                observations.value = (resultsJson && typeof resultsJson.observations === 'string') ? resultsJson.observations : '';
                equipmentStatus.value = (resultsJson && typeof resultsJson.status === 'string') ? resultsJson.status : (data.status || '');
                
                state.resultados = Array.isArray(data.resultados) ? data.resultados : [];
                state.resultadosDist = Array.isArray(data.resultados_distancia) ? data.resultados_distancia : [];
                
                // Deducir precision
                const first = state.resultados[0] || {};
                const prec = first.tipo_resultado || 'segundos';
                state.currentPrecision = ['lineal', 'vertical_horizontal'].includes(prec) ? prec : 'segundos';
                
                const thCol1 = document.getElementById('thCol1');
                const thCol2 = document.getElementById('thCol2');
                const thCol3 = document.getElementById('thCol3');
                const thCol4 = document.getElementById('thCol4');
                
                if (state.currentPrecision === 'lineal') {
                    resultTableTitle.textContent = 'Resultados (precisión lineal en mm)';
                    thCol1.textContent = 'Valor de Patrón';
                    thCol2.textContent = 'Valor Obtenido';
                    thCol3.textContent = 'Precisión (mm)';
                    thCol4.textContent = 'Error';
                } else if (state.currentPrecision === 'vertical_horizontal') {
                    resultTableTitle.textContent = 'Resultados Vertical/Horizontal';
                    thCol1.textContent = 'Ángulo';
                    thCol2.textContent = 'Valor patrón';
                    thCol3.textContent = 'Valor inicial';
                    thCol4.textContent = 'Valor final';
                    // Columna Error se maneja en renderResultados
                } else {
                    resultTableTitle.textContent = 'Resultados (precisión angular en segundos)';
                    thCol1.textContent = 'Valor de Patrón';
                    thCol2.textContent = 'Valor Obtenido';
                    thCol3.textContent = 'Precisión';
                    thCol4.textContent = 'Error';
                }
                
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
                status: equipmentStatus.value || 'approved',
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
