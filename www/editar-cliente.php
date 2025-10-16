<!-- editar-cliente.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Editar Cliente</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'clientes'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Editar Cliente';
            $pageSubtitle = 'Modifica la información del cliente';
            $headerActionsHtml = '';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="card glass p-4 rounded-lg">
                <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
                <div id="successAlert" class="alert alert-success d-none" role="alert"></div>

                <form id="clientForm">
                    <div class="form-group">
                        <label class="form-label">Nombre del cliente <span class="text-danger">*</span></label>
                        <input id="clientName" type="text" class="form-control" required />
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">RUC <span class="text-danger">*</span></label>
                                <input id="clientRuc" type="text" class="form-control" maxlength="11" pattern="[0-9]{11}" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">DNI</label>
                                <input id="clientDni" type="text" class="form-control" maxlength="8" pattern="[0-9]{8}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input id="clientEmail" type="email" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Celular</label>
                                <input id="clientPhone" type="tel" class="form-control" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Dirección</label>
                        <textarea id="clientAddress" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='clientes.php'">
                            Cancelar
                        </button>
                        <button id="saveClientBtn" type="submit" class="btn btn-primary">
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
        // Verificar autenticación
        try {
            Auth.requireAuth('admin');
        } catch (e) {
            return;
        }

        const params = new URLSearchParams(window.location.search);
        const clientId = params.get('id');
        if (!clientId) {
            alert('Falta el parámetro id');
            window.location.href = 'clientes.php';
            return;
        }

        const API_GET = `api/clients.php?action=get&id=${encodeURIComponent(clientId)}`;
        const API_UPDATE = `api/clients.php?action=update&id=${encodeURIComponent(clientId)}`;

        const form = document.getElementById('clientForm');
        const saveBtn = document.getElementById('saveClientBtn');
        const nameInput = document.getElementById('clientName');
        const rucInput = document.getElementById('clientRuc');
        const dniInput = document.getElementById('clientDni');
        const phoneInput = document.getElementById('clientPhone');
        const emailInput = document.getElementById('clientEmail');
        const addressInput = document.getElementById('clientAddress');
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');

        function setError(message) {
            errorAlert.textContent = message || 'Ocurrió un error.';
            errorAlert.classList.remove('d-none');
            successAlert.classList.add('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function setSuccess(message) {
            successAlert.textContent = message || 'Cambios guardados.';
            successAlert.classList.remove('d-none');
            errorAlert.classList.add('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function clearAlerts() {
            errorAlert.classList.add('d-none');
            successAlert.classList.add('d-none');
        }

        async function loadClient() {
            try {
                const json = await Auth.fetchWithAuth(API_GET);
                if (!json || json.ok !== true || !json.data) {
                    throw new Error(json?.message || 'No se pudo cargar el cliente');
                }
                const c = json.data;
                nameInput.value = c.nombre || '';
                rucInput.value = c.ruc || '';
                dniInput.value = c.dni || '';
                phoneInput.value = c.celular || '';
                emailInput.value = c.email || '';
                addressInput.value = c.direccion || '';
            } catch (err) {
                setError(err.message);
            }
        }

        async function saveClient(e) {
            e.preventDefault();
            clearAlerts();

            const name = nameInput.value.trim();
            const ruc = rucInput.value.trim();

            if (!name) return setError('El nombre del cliente es obligatorio.');
            if (!ruc) return setError('El RUC es obligatorio.');

            const payload = {
                nombre: name,
                ruc: ruc,
                dni: dniInput.value.trim() || null,
                email: emailInput.value.trim() || null,
                celular: phoneInput.value.trim() || null,
                direccion: addressInput.value.trim() || null
            };

            try {
                saveBtn.disabled = true;
                saveBtn.textContent = 'Guardando...';
                const data = await Auth.fetchWithAuth(API_UPDATE, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                if (!data?.ok) throw new Error(data?.message || 'Error al guardar');
                setSuccess('Cliente actualizado exitosamente.');
                setTimeout(() => window.location.href = 'clientes.php', 1500);
            } catch (err) {
                setError(err.message);
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Guardar Cambios';
            }
        }

        form.addEventListener('submit', saveClient);
        loadClient();
    });
    </script>
</body>
</html>
