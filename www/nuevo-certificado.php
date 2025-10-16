<!-- nuevo-certificado.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Nuevo Certificado</title>
    <link href="assets/css/global.css" rel="stylesheet">
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
                                <select id="equipmentSelect" class="form-select" required disabled>
                                    <option value="">Selecciona un cliente primero</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha de Calibración *</label>
                                <input id="calibrationDate" type="date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Próxima Calibración</label>
                                <input id="nextCalibrationDate" type="date" class="form-control">
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
                                <textarea id="observations" class="form-control" rows="4" placeholder="Ingresa observaciones sobre la calibración..."></textarea>
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
                                <input id="certificateNumber" type="text" class="form-control" placeholder="Ej: CERT-2024-001">
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
        const API_CLIENTS = 'api/clients.php?action=list&limit=200&offset=0';
        const API_EQUIPMENT = clientId => `api/equipment.php?action=list&client_id=${encodeURIComponent(clientId)}&limit=200&offset=0`;
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
                const response = await fetch(API_CLIENTS);
                const data = await response.json();
                
                if (!response.ok || !data.ok || !Array.isArray(data.data)) {
                    throw new Error('Error al cargar clientes');
                }

                clientSelect.innerHTML = '<option value="">Seleccione un cliente</option>';
                data.data.forEach(client => {
                    const option = document.createElement('option');
                    option.value = client.id;
                    option.textContent = client.name;
                    clientSelect.appendChild(option);
                });
            } catch (error) {
                setError('No se pudieron cargar los clientes: ' + error.message);
                clientSelect.innerHTML = '<option value="">Error al cargar clientes</option>';
            }
        }

        // Cargar equipos cuando se selecciona un cliente
        clientSelect.addEventListener('change', async (e) => {
            const clientId = e.target.value;
            equipmentSelect.innerHTML = '<option value="">Cargando equipos...</option>';
            equipmentSelect.disabled = true;

            if (!clientId) {
                equipmentSelect.innerHTML = '<option value="">Seleccione un cliente primero</option>';
                return;
            }

            try {
                const response = await fetch(API_EQUIPMENT(clientId));
                const data = await response.json();

                if (!response.ok || !data.ok || !Array.isArray(data.data)) {
                    throw new Error('Error al cargar equipos');
                }

                if (data.data.length === 0) {
                    equipmentSelect.innerHTML = '<option value="">No hay equipos disponibles</option>';
                    return;
                }

                equipmentSelect.innerHTML = '<option value="">Seleccione un equipo</option>';
                data.data.forEach(equipment => {
                    const option = document.createElement('option');
                    option.value = equipment.id;
                    option.textContent = `${equipment.name} - ${equipment.serial_number || 'S/N'}`;
                    equipmentSelect.appendChild(option);
                });
                equipmentSelect.disabled = false;
            } catch (error) {
                setError('No se pudieron cargar los equipos: ' + error.message);
                equipmentSelect.innerHTML = '<option value="">Error al cargar equipos</option>';
            }
        });

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
                calibration_date: calDate,
                next_calibration_date: nextCalibrationDate.value || null,
                certificate_number: certificateNumber.value.trim() || null,
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
                status: equipmentStatus.value || null
            };

            try {
                saveBtn.disabled = true;
                saveBtn.textContent = 'Creando certificado...';

                const response = await fetch(API_CREATE_CERTIFICATE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok || data.error) {
                    throw new Error(data.message || data.error || 'Error al crear el certificado');
                }

                setSuccess('Certificado creado exitosamente');
                
                // Limpiar el formulario
                form.reset();
                equipmentSelect.innerHTML = '<option value="">Seleccione un cliente primero</option>';
                equipmentSelect.disabled = true;

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

        // Cargar clientes al inicio
        await loadClients();
    });
    </script>
</body>
</html>
