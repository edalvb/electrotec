<!-- nuevo-cliente.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Nuevo Cliente</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'clientes'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Nuevo Cliente';
            $pageSubtitle = 'Completa la información para crear un nuevo cliente';
            $headerActionsHtml = '';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="card glass p-4 rounded-lg">
                <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
                <div id="successAlert" class="alert alert-success d-none" role="alert"></div>

                <form id="clientForm">
                    <div class="form-group">
                        <label class="form-label">Usuario asociado <span class="text-danger">*</span></label>
                        <select id="clientUserId" class="form-control" required>
                            <option value="">Seleccione un usuario...</option>
                        </select>
                        <small class="form-text text-muted">El usuario que tendrá acceso a este cliente</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nombre del cliente <span class="text-danger">*</span></label>
                        <input
                            id="clientName"
                            type="text"
                            class="form-control"
                            placeholder="Ingrese el nombre del cliente"
                            required
                        />
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">RUC <span class="text-danger">*</span></label>
                                <input
                                    id="clientRuc"
                                    type="text"
                                    class="form-control"
                                    placeholder="RUC del cliente (11 dígitos)"
                                    maxlength="11"
                                    pattern="[0-9]{11}"
                                    required
                                />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">DNI</label>
                                <input
                                    id="clientDni"
                                    type="text"
                                    class="form-control"
                                    placeholder="DNI del cliente"
                                    maxlength="8"
                                    pattern="[0-9]{8}"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input
                                    id="clientEmail"
                                    type="email"
                                    class="form-control"
                                    placeholder="correo@dominio.com"
                                />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Celular</label>
                                <input
                                    id="clientPhone"
                                    type="tel"
                                    class="form-control"
                                    placeholder="Número de celular"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Dirección</label>
                        <textarea
                            id="clientAddress"
                            class="form-control"
                            placeholder="Dirección completa del cliente"
                            rows="3"
                        ></textarea>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='clientes.php'">
                            Cancelar
                        </button>
                        <button id="saveClientBtn" type="submit" class="btn btn-primary">
                            Guardar Cliente
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

        const API_CREATE = 'api/clients.php?action=create';
        const API_USERS = 'api/users.php?action=list';
        const form = document.getElementById('clientForm');
        const saveBtn = document.getElementById('saveClientBtn');
        const userIdSelect = document.getElementById('clientUserId');
        const nameInput = document.getElementById('clientName');
        const rucInput = document.getElementById('clientRuc');
        const dniInput = document.getElementById('clientDni');
        const phoneInput = document.getElementById('clientPhone');
        const emailInput = document.getElementById('clientEmail');
        const addressInput = document.getElementById('clientAddress');
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');

        function setError(message) {
            errorAlert.textContent = message || 'Ocurrió un error al guardar el cliente.';
            errorAlert.classList.remove('d-none');
            successAlert.classList.add('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function setSuccess(message) {
            successAlert.textContent = message || 'Cliente guardado exitosamente.';
            successAlert.classList.remove('d-none');
            errorAlert.classList.add('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function clearAlerts() {
            errorAlert.classList.add('d-none');
            successAlert.classList.add('d-none');
        }

        // Cargar lista de usuarios de tipo 'client'
        async function loadUsers() {
            try {
                const data = await Auth.fetchWithAuth(API_USERS, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });

                if (data.ok && data.data) {
                    const users = data.data.filter(u => u.tipo === 'client');
                    users.forEach(user => {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = `${user.username} (ID: ${user.id})`;
                        userIdSelect.appendChild(option);
                    });
                }
            } catch (err) {
                console.error('Error al cargar usuarios:', err);
                setError('No se pudieron cargar los usuarios disponibles');
            }
        }

        async function saveClient(e) {
            e.preventDefault();
            clearAlerts();

            const userId = userIdSelect.value;
            const name = nameInput.value.trim();
            const ruc = rucInput.value.trim();

            if (!userId) {
                setError('Debe seleccionar un usuario.');
                return;
            }

            if (!name) {
                setError('El nombre del cliente es obligatorio.');
                return;
            }

            if (!ruc) {
                setError('El RUC es obligatorio.');
                return;
            }

            const payload = {
                user_id: parseInt(userId),
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

                const data = await Auth.fetchWithAuth(API_CREATE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (data.error) {
                    throw new Error(data.message || data.error || 'Error al guardar el cliente');
                }

                setSuccess('Cliente creado exitosamente');
                
                // Limpiar el formulario
                form.reset();

                // Redirigir después de 2 segundos
                setTimeout(() => {
                    window.location.href = 'clientes.php';
                }, 2000);

            } catch (err) {
                setError(err.message);
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Guardar Cliente';
            }
        }

        // Cargar usuarios al iniciar
        loadUsers();

        form.addEventListener('submit', saveClient);
    });
    </script>
</body>
</html>
