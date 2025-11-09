<!-- nuevo-equipo.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Nuevo Equipo</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'equipos'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Nuevo Equipo';
            $pageSubtitle = 'Completa la información para registrar un nuevo equipo';
            $headerActionsHtml = '';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="card glass p-4 rounded-lg">
                <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
                <div id="successAlert" class="alert alert-success d-none" role="alert"></div>

                <form id="equipmentForm" novalidate>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="eqSerial">Número de serie</label>
                            <input id="eqSerial" type="text" class="form-control" placeholder="Número de serie del equipo" required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="eqBrand">Marca</label>
                            <input id="eqBrand" type="text" class="form-control" placeholder="Marca del equipo" required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="eqModel">Modelo</label>
                            <input id="eqModel" type="text" class="form-control" placeholder="Modelo del equipo" required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="eqType">Tipo de equipo</label>
                            <select id="eqType" class="form-select" required></select>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='equipos.php'">
                            Cancelar
                        </button>
                        <button id="saveEquipmentBtn" type="submit" class="btn btn-primary">
                            Guardar Equipo
                        </button>
                    </div>
                </form>
            </div>

            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/uppercase-inputs.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Verificar autenticación
        try {
            Auth.requireAuth('admin');
        } catch (e) {
            return;
        }

        const API_CREATE = 'api/equipment.php?action=create';
        const API_LIST_TYPES = 'api/equipment.php?action=listTypes';

        const form = document.getElementById('equipmentForm');
        const saveBtn = document.getElementById('saveEquipmentBtn');
        const serialInput = document.getElementById('eqSerial');
        const brandInput = document.getElementById('eqBrand');
        const modelInput = document.getElementById('eqModel');
    const typeSelect = document.getElementById('eqType');
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');

        function setError(message) {
            errorAlert.textContent = message || 'Ocurrió un error al guardar el equipo.';
            errorAlert.classList.remove('d-none');
            successAlert.classList.add('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function setSuccess(message) {
            successAlert.textContent = message || 'Equipo guardado exitosamente.';
            successAlert.classList.remove('d-none');
            errorAlert.classList.add('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function clearAlerts() {
            errorAlert.classList.add('d-none');
            successAlert.classList.add('d-none');
        }

        async function loadTypes() {
            try {
                const data = await Auth.fetchWithAuth(API_LIST_TYPES);
                if (data.ok !== true) {
                    throw new Error(data.message || 'No se pudieron cargar los tipos de equipo');
                }
                const types = Array.isArray(data.data) ? data.data : [];
                typeSelect.innerHTML = '';
                for (const t of types) {
                    const opt = document.createElement('option');
                    opt.value = String(t.id ?? '');
                    opt.textContent = String(t.name ?? '');
                    typeSelect.appendChild(opt);
                }
                if (typeSelect.options.length === 0) {
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'No hay tipos disponibles';
                    typeSelect.appendChild(opt);
                }
            } catch (e) {
                setError(e.message || 'Error al cargar tipos de equipo');
            }
        }

        async function saveEquipment(e) {
            e.preventDefault();
            clearAlerts();

            const payload = {
                serial_number: serialInput.value.trim(),
                brand: brandInput.value.trim(),
                model: modelInput.value.trim(),
                equipment_type_id: parseInt(typeSelect.value || '0', 10) || 0,
            };

            if (!payload.serial_number || !payload.brand || !payload.model || !payload.equipment_type_id) {
                setError('Completa los campos requeridos.');
                return;
            }

            try {
                saveBtn.disabled = true;
                saveBtn.textContent = 'Guardando...';

                const data = await Auth.fetchWithAuth(API_CREATE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (data.ok !== true) {
                    throw new Error(data.message || 'Error al guardar el equipo');
                }

                setSuccess('Equipo creado exitosamente');
                form.reset();

                setTimeout(() => {
                    window.location.href = 'equipos.php';
                }, 1500);

            } catch (err) {
                setError(err.message);
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Guardar Equipo';
            }
        }

        form.addEventListener('submit', saveEquipment);
        loadTypes();
    });
    </script>
</body>
</html>
