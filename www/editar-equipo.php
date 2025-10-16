<!-- editar-equipo.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Editar Equipo</title>
    <link href="assets/css/global.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'equipos'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Editar Equipo';
            $pageSubtitle = 'Actualiza la información del equipo seleccionado';
            $headerActionsHtml = '';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="card glass p-4 rounded-lg">
                <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
                <div id="successAlert" class="alert alert-success d-none" role="alert"></div>

                <form id="equipmentForm" novalidate>
                    <input id="eqId" type="hidden" />
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
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>

            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const API_FIND = 'api/equipment.php?action=find';
        const API_LIST_TYPES = 'api/equipment.php?action=listTypes';
        const API_UPDATE = (id) => `api/equipment.php?action=update&id=${encodeURIComponent(id)}`;

        const url = new URL(window.location.href);
        const id = url.searchParams.get('id') || '';

        const form = document.getElementById('equipmentForm');
        const saveBtn = document.getElementById('saveEquipmentBtn');
        const idInput = document.getElementById('eqId');
        const serialInput = document.getElementById('eqSerial');
        const brandInput = document.getElementById('eqBrand');
        const modelInput = document.getElementById('eqModel');
        const typeSelect = document.getElementById('eqType');
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');

        function setError(message) {
            errorAlert.textContent = message || 'Ocurrió un error al actualizar el equipo.';
            errorAlert.classList.remove('d-none');
            successAlert.classList.add('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function setSuccess(message) {
            successAlert.textContent = message || 'Equipo actualizado exitosamente.';
            successAlert.classList.remove('d-none');
            errorAlert.classList.add('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function clearAlerts() {
            errorAlert.classList.add('d-none');
            successAlert.classList.add('d-none');
        }

        async function loadTypes() {
            const res = await fetch(API_LIST_TYPES);
            const data = await res.json();
            if (!res.ok || data.ok !== true) throw new Error(data.message || 'No se pudieron cargar los tipos');
            const types = Array.isArray(data.data) ? data.data : [];
            typeSelect.innerHTML = '';
            for (const t of types) {
                const opt = document.createElement('option');
                opt.value = String(t.id ?? '');
                opt.textContent = String(t.name ?? '');
                typeSelect.appendChild(opt);
            }
        }

        async function loadEquipment() {
            if (!id) {
                setError('Falta el parámetro id en la URL.');
                return;
            }
            const res = await fetch(`${API_FIND}&id=${encodeURIComponent(id)}`);
            const data = await res.json();
            if (!res.ok || data.ok !== true) throw new Error(data.message || 'No se pudo cargar el equipo');
            const e = data.data || {};
            idInput.value = e.id || '';
            serialInput.value = e.serial_number || '';
            brandInput.value = e.brand || '';
            modelInput.value = e.model || '';
            typeSelect.value = String(e.equipment_type_id || '');
        }

        async function save(e) {
            e.preventDefault();
            clearAlerts();
            const payload = {
                id: idInput.value.trim(),
                serial_number: serialInput.value.trim(),
                brand: brandInput.value.trim(),
                model: modelInput.value.trim(),
                equipment_type_id: parseInt(typeSelect.value || '0', 10) || 0,
            };
            if (!payload.id || !payload.serial_number || !payload.brand || !payload.model || !payload.equipment_type_id) {
                setError('Completa los campos requeridos.');
                return;
            }
            try {
                saveBtn.disabled = true;
                saveBtn.textContent = 'Guardando...';
                const res = await fetch(API_UPDATE(payload.id), {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (!res.ok || data.ok !== true) throw new Error(data.message || 'Error al actualizar');
                setSuccess('Equipo actualizado exitosamente');
                setTimeout(() => {
                    window.location.href = 'equipos.php';
                }, 1500);
            } catch (err) {
                setError(err.message);
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Guardar Cambios';
            }
        }

        (async function init() {
            try {
                await loadTypes();
                await loadEquipment();
            } catch (err) {
                setError(err.message);
            }
        })();

        form.addEventListener('submit', save);
    });
    </script>
</body>
</html>
