<!-- gestion-usuarios.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Gestión de Usuarios</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
    <style>
        /* Glassmorphism System Design Styles */
        :root {
            --primary-blue: #029DE4;
            --secondary-blue: #03679A;
            --surface-glass: rgba(255, 255, 255, 0.25);
            --border-glass: rgba(255, 255, 255, 0.40);
            --text-primary: #FFFFFF;
            --text-muted: rgba(255, 255, 255, 0.70);
            --success: #10B981;
            --warning: #F59E0B;
            --error: #EF4444;
        }
        
        body {
            background: radial-gradient(circle at 30% 20%, #029DE4, #03679A);
            min-height: 100vh;
            color: var(--text-primary);
        }
        
        /* Glass Surface Style */
        .glass-surface {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            background: var(--surface-glass);
            border: 1px solid var(--border-glass);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border-radius: 16px;
        }
        
        .glass-surface-subtle {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid var(--border-glass);
            box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.25);
            border-radius: 12px;
        }
        
        /* Typography with shadows */
        h1, h2, h3, h4, h5, h6 {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
            color: var(--text-primary);
        }
        
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        /* Sidebar Glass */
        .sidebar {
            background: none;
            border-right: none;
        }
        
        .sidebar-glass {
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.20);
            border-right: 1px solid var(--border-glass);
            box-shadow: 4px 0 16px 0 rgba(31, 38, 135, 0.30);
        }
        
        /* List group glass effect */
        .list-group-item {
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.20);
            color: var(--text-muted);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            margin-bottom: 4px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .list-group-item:hover {
            background: rgba(255, 255, 255, 0.25);
            color: var(--text-primary);
            transform: translateX(4px);
        }
        
        .list-group-item.active {
            background: var(--primary-blue);
            color: var(--text-primary);
            border-color: var(--secondary-blue);
            box-shadow: 0 4px 16px 0 rgba(42, 47, 108, 0.4);
        }
        
        /* Primary button - solid */
        .btn-primary-glass {
            background: var(--primary-blue);
            border: none;
            color: var(--text-primary);
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 16px 0 rgba(42, 47, 108, 0.4);
            transition: all 0.3s ease;
        }
        
        .btn-primary-glass:hover {
            background: var(--secondary-blue);
            transform: translateY(-2px);
            box-shadow: 0 6px 24px 0 rgba(42, 47, 108, 0.6);
            color: var(--text-primary);
        }
        
        /* Secondary button - glass */
        .btn-secondary-glass {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid var(--border-glass);
            color: var(--text-primary);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-secondary-glass:hover {
            background: rgba(255, 255, 255, 0.35);
            color: var(--text-primary);
        }
        
        /* Input glass style */
        .form-control-glass {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-glass);
            color: var(--text-primary);
            border-radius: 12px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        .form-control-glass::placeholder {
            color: var(--text-muted);
        }
        
        .form-control-glass:focus {
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 0.2rem rgba(92, 102, 204, 0.25);
            background: rgba(0, 0, 0, 0.3);
            color: var(--text-primary);
        }
        
        /* Table glass styling */
        .table-glass {
            color: var(--text-primary);
            --bs-table-bg: transparent;
        }
        
        .table-glass th {
            border-bottom: 1px solid var(--border-glass);
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .table-glass td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table-glass tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Badge adjustments */
        .badge {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Status badges */
        .badge.bg-success {
            background-color: var(--success) !important;
        }
        
        .badge.bg-warning {
            background-color: var(--warning) !important;
        }
        
        .badge.bg-danger {
            background-color: var(--error) !important;
        }
        
        /* Modal Glass Styling */
        .modal-content {
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            background: var(--surface-glass);
            border: 1px solid var(--border-glass);
            box-shadow: 0 16px 64px 0 rgba(31, 38, 135, 0.5);
            border-radius: 20px;
            color: var(--text-primary);
        }
        
        .modal-header {
            border-bottom: 1px solid var(--border-glass);
        }
        
        .modal-footer {
            border-top: 1px solid var(--border-glass);
        }
        
        .modal-title {
            color: var(--text-primary);
            font-weight: 600;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        .form-label {
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .form-select {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-glass);
            color: var(--text-primary);
            border-radius: 12px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        .form-select:focus {
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 0.2rem rgba(92, 102, 204, 0.25);
            background: rgba(0, 0, 0, 0.3);
            color: var(--text-primary);
        }
        
        .form-check-input {
            background-color: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-glass);
        }
        
        .form-check-input:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .form-check-label {
            color: var(--text-primary);
        }
        
        .btn-close {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            opacity: 0.8;
        }
        
        .btn-close:hover {
            background: rgba(255, 255, 255, 0.3);
            opacity: 1;
        }
        
        /* Override some Bootstrap modal defaults for glass effect */
        .modal-backdrop {
            background: rgba(42, 47, 108, 0.3);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'gestion-usuarios'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1 p-4">
            <?php 
            $pageTitle = 'Gestión de Usuarios';
            $pageSubtitle = 'Administración de roles y accesos';
            $headerActionsHtml = '<button class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#inviteTechModal"><span aria-hidden="true">👤</span>Crear usuario</button>';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="mb-4">
                <input type="text" class="form-control form-control-glass" placeholder="Buscar por nombre o ID">
            </div>

            <div class="glass-surface p-4 rounded-lg">
                <div class="table-responsive">
                    <table class="table table-glass table-borderless table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th>👤 Usuario</th>
                                <th>🏷️ Rol</th>
                                <th>📊 Estado</th>
                                <th>⚙️ Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usersTbody">
                            <tr>
                                <td colspan="4" class="text-center text-muted">⏳ Cargando usuarios...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>
    <?php include_once 'partials/modal-invite-tech.html'; ?>
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-custom">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title">Editar usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editUserError" class="alert alert-danger d-none" role="alert" style="text-align: left;"></div>
                    <div class="mb-3">
                        <label class="form-label">Nombre de usuario <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editUsername" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select class="form-select" id="editUserTipo">
                            <option value="admin">Administrador</option>
                            <option value="client">Cliente</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" class="form-control" id="editUserPassword" placeholder="Dejar en blanco para mantener">
                        <small class="text-muted">Mínimo 8 caracteres. Déjalo vacío para conservar la contraseña actual.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="editUserPasswordConfirm" placeholder="Repite la nueva contraseña">
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-blue" id="btnSaveUser">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteUserModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-custom">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title">Eliminar usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="deleteUserError" class="alert alert-danger d-none" role="alert" style="text-align: left;"></div>
                    <p>¿Seguro que deseas eliminar el usuario <strong id="deleteUserName"></strong>?</p>
                    <p class="text-muted mb-0">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDeleteUser">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function(){
            // Verificar autenticación
            let currentUser;
            try {
                currentUser = Auth.requireAuth('admin');
            } catch (e) {
                return;
            }

            const tbody = document.getElementById('usersTbody');
            const API_LIST = `${location.origin}/api/users.php?action=list&limit=50&offset=0`;
            const API_CREATE = `${location.origin}/api/users.php`;
            const isAdmin = currentUser && currentUser.tipo === 'admin';

            const modal = document.getElementById('inviteTechModal');
            const modalError = document.getElementById('createUserError');
            const btnCreateUser = document.getElementById('btnCreateUser');
            const usernameInput = document.getElementById('newUsername');
            const passwordInput = document.getElementById('newPassword');
            const passwordConfirmInput = document.getElementById('newPasswordConfirm');

            const editModalEl = document.getElementById('editUserModal');
            const editError = document.getElementById('editUserError');
            const editUsernameInput = document.getElementById('editUsername');
            const editTipoSelect = document.getElementById('editUserTipo');
            const editPasswordInput = document.getElementById('editUserPassword');
            const editPasswordConfirmInput = document.getElementById('editUserPasswordConfirm');
            const btnSaveUser = document.getElementById('btnSaveUser');

            const deleteModalEl = document.getElementById('deleteUserModal');
            const deleteError = document.getElementById('deleteUserError');
            const deleteUserName = document.getElementById('deleteUserName');
            const btnConfirmDelete = document.getElementById('btnConfirmDeleteUser');

            let usersCache = [];
            let editingUser = null;
            let deletingUser = null;

            function setModalError(message) {
                if (!modalError) return;
                if (!message) {
                    modalError.classList.add('d-none');
                    modalError.innerHTML = '';
                    return;
                }
                modalError.classList.remove('d-none');
                modalError.innerHTML = message;
            }

            function clearModalInputs() {
                if (usernameInput) usernameInput.value = '';
                if (passwordInput) passwordInput.value = '';
                if (passwordConfirmInput) passwordConfirmInput.value = '';
                setModalError('');
            }

            function setEditError(message) {
                if (!editError) return;
                if (!message) {
                    editError.classList.add('d-none');
                    editError.textContent = '';
                    return;
                }
                editError.classList.remove('d-none');
                editError.textContent = message;
            }

            function setDeleteError(message) {
                if (!deleteError) return;
                if (!message) {
                    deleteError.classList.add('d-none');
                    deleteError.textContent = '';
                    return;
                }
                deleteError.classList.remove('d-none');
                deleteError.textContent = message;
            }

            if (modal) {
                modal.addEventListener('show.bs.modal', () => {
                    clearModalInputs();
                });
            }

            if (editModalEl) {
                editModalEl.addEventListener('hidden.bs.modal', () => {
                    editingUser = null;
                    if (editPasswordInput) editPasswordInput.value = '';
                    if (editPasswordConfirmInput) editPasswordConfirmInput.value = '';
                    setEditError('');
                });
            }

            if (deleteModalEl) {
                deleteModalEl.addEventListener('hidden.bs.modal', () => {
                    deletingUser = null;
                    setDeleteError('');
                    if (deleteUserName) deleteUserName.textContent = '';
                });
            }

            if (btnCreateUser) {
                btnCreateUser.addEventListener('click', async () => {
                    setModalError('');

                    const username = usernameInput?.value?.trim() || '';
                    const password = passwordInput?.value || '';
                    const passwordConfirm = passwordConfirmInput?.value || '';

                    if (!username) {
                        setModalError('El nombre de usuario es obligatorio');
                        return;
                    }

                    if (!password) {
                        setModalError('La contraseña es obligatoria');
                        return;
                    }

                    if (password.length < 8) {
                        setModalError('La contraseña debe tener al menos 8 caracteres');
                        return;
                    }

                    if (password !== passwordConfirm) {
                        setModalError('Las contraseñas no coinciden');
                        return;
                    }

                    try {
                        btnCreateUser.disabled = true;
                        btnCreateUser.textContent = 'Creando...';

                        const payload = {
                            username: username,
                            password: password,
                            password_confirm: passwordConfirm,
                            tipo: 'admin'
                        };

                        await Auth.fetchWithAuth(API_CREATE, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });

                        if (modal) {
                            const modalInstance = bootstrap.Modal.getInstance(modal);
                            if (modalInstance) modalInstance.hide();
                        }

                        loadUsers();
                    } catch (err) {
                        setModalError(err.message || 'Error al crear el usuario');
                    } finally {
                        btnCreateUser.disabled = false;
                        btnCreateUser.textContent = 'Crear usuario';
                    }
                });
            }

            function roleBadge(tipo) {
                const map = {
                    'admin': 'bg-warning',
                    'client': 'bg-info'
                };
                const cls = map[tipo] || 'bg-secondary';
                const label = tipo === 'admin' ? 'ADMIN' : 'CLIENTE';
                return `<span class="badge ${cls}">${label}</span>`;
            }

            function statusBadge() {
                return '<span class="badge bg-success">Activo</span>';
            }

            function attachActionHandlers() {
                const editButtons = tbody.querySelectorAll('.btn-edit-user');
                editButtons.forEach(btn => {
                    btn.addEventListener('click', (event) => {
                        const target = event.currentTarget;
                        const userId = Number(target.dataset.userId);
                        const user = usersCache.find(u => Number(u.id) === userId);
                        if (!user || !editModalEl) return;

                        editingUser = user;
                        if (editUsernameInput) editUsernameInput.value = user.username || '';
                        if (editTipoSelect) editTipoSelect.value = user.tipo || 'admin';
                        if (editPasswordInput) editPasswordInput.value = '';
                        if (editPasswordConfirmInput) editPasswordConfirmInput.value = '';
                        setEditError('');

                        const modalInstance = bootstrap.Modal.getOrCreateInstance(editModalEl);
                        modalInstance.show();
                    });
                });

                const deleteButtons = tbody.querySelectorAll('.btn-delete-user');
                deleteButtons.forEach(btn => {
                    if (btn.hasAttribute('disabled')) {
                        return;
                    }

                    btn.addEventListener('click', (event) => {
                        const target = event.currentTarget;
                        const userId = Number(target.dataset.userId);
                        const user = usersCache.find(u => Number(u.id) === userId);
                        if (!user || !deleteModalEl) return;

                        deletingUser = user;
                        if (deleteUserName) deleteUserName.textContent = user.username || '';
                        setDeleteError('');

                        const modalInstance = bootstrap.Modal.getOrCreateInstance(deleteModalEl);
                        modalInstance.show();
                    });
                });
            }

            function render(rows) {
                if (!rows || rows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">📭 No hay usuarios</td></tr>';
                    usersCache = [];
                    return;
                }

                usersCache = rows.map(u => ({ ...u }));

                tbody.innerHTML = rows.map(u => {
                    const isSelf = currentUser && Number(currentUser.id) === Number(u.id);
                    const actionsHtml = isAdmin
                        ? `<button class="btn btn-sm btn-secondary-glass me-2 btn-edit-user" data-user-id="${u.id}">✏️ Editar</button>
                           <button class="btn btn-sm btn-secondary-glass btn-delete-user" data-user-id="${u.id}" data-username="${u.username}" ${isSelf ? 'disabled title="No puedes eliminar tu propio usuario"' : ''} style="border-color: var(--error); color: var(--error);">🗑️ Eliminar</button>`
                        : '<span class="text-muted">Sin acciones</span>';

                    return `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3" style="width: 40px; height: 40px; background: var(--surface-glass); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                        👤
                                    </div>
                                    <div>
                                        <div style="color: var(--text-primary); font-weight: 500;">${u.username}</div>
                                    </div>
                                </div>
                            </td>
                            <td>${roleBadge(u.tipo)}</td>
                            <td>${statusBadge()}</td>
                            <td>${actionsHtml}</td>
                        </tr>
                    `;
                }).join('');

                attachActionHandlers();
            }

            function loadUsers() {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">⏳ Cargando usuarios...</td></tr>';
                Auth.fetchWithAuth(API_LIST)
                    .then(json => {
                        if (json && json.ok) {
                            render(json.data);
                        } else {
                            throw new Error(json?.message || 'Error desconocido');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        tbody.innerHTML = `<tr><td colspan="4" class="text-center" style="color: var(--error);">❌ Error cargando usuarios: ${err.message}</td></tr>`;
                    });
            }

            if (btnSaveUser && editModalEl) {
                btnSaveUser.addEventListener('click', async () => {
                    if (!editingUser) {
                        return;
                    }

                    setEditError('');

                    const username = editUsernameInput?.value?.trim() || '';
                    const tipo = editTipoSelect?.value || 'admin';
                    const password = editPasswordInput?.value || '';
                    const passwordConfirm = editPasswordConfirmInput?.value || '';

                    if (!username) {
                        setEditError('El nombre de usuario es obligatorio');
                        return;
                    }

                    if (password && password.length < 8) {
                        setEditError('La contraseña debe tener al menos 8 caracteres');
                        return;
                    }

                    if (password && password !== passwordConfirm) {
                        setEditError('Las contraseñas no coinciden');
                        return;
                    }

                    const payload = { username, tipo };
                    if (password) {
                        payload.password = password;
                        payload.password_confirm = passwordConfirm;
                    }

                    try {
                        btnSaveUser.disabled = true;
                        btnSaveUser.textContent = 'Guardando...';

                        await Auth.fetchWithAuth(`${location.origin}/api/users.php?id=${editingUser.id}`, {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(payload)
                        });

                        const modalInstance = bootstrap.Modal.getInstance(editModalEl);
                        if (modalInstance) modalInstance.hide();

                        loadUsers();
                    } catch (err) {
                        setEditError(err.message || 'Error al actualizar el usuario');
                    } finally {
                        btnSaveUser.disabled = false;
                        btnSaveUser.textContent = 'Guardar cambios';
                    }
                });
            }

            if (btnConfirmDelete && deleteModalEl) {
                btnConfirmDelete.addEventListener('click', async () => {
                    if (!deletingUser) {
                        return;
                    }

                    setDeleteError('');

                    try {
                        btnConfirmDelete.disabled = true;
                        btnConfirmDelete.textContent = 'Eliminando...';

                        await Auth.fetchWithAuth(`${location.origin}/api/users.php?id=${deletingUser.id}`, {
                            method: 'DELETE'
                        });

                        const modalInstance = bootstrap.Modal.getInstance(deleteModalEl);
                        if (modalInstance) modalInstance.hide();

                        loadUsers();
                    } catch (err) {
                        setDeleteError(err.message || 'Error al eliminar el usuario');
                    } finally {
                        btnConfirmDelete.disabled = false;
                        btnConfirmDelete.textContent = 'Eliminar';
                    }
                });
            }

            loadUsers();
        })();
    </script>
</body>
</html>