<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Portal de Cliente</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <style>
        .profile-section {
            max-width: 800px;
            margin: 0 auto;
        }
        .profile-header {
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        .profile-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .info-row {
            display: flex;
            padding: 1rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            width: 150px;
            flex-shrink: 0;
        }
        .info-value {
            color: #333;
            flex: 1;
        }
        .btn-edit {
            margin-top: 1rem;
        }
        .edit-form {
            display: none;
        }
        .edit-form.active {
            display: block;
        }
        .view-mode.hidden {
            display: none;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include 'partials/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <!-- Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Mi Perfil</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-sm btn-danger" onclick="logout()">
                        Cerrar Sesión
                    </button>
                </div>
            </div>

            <!-- Mensaje de éxito -->
            <div id="successMessage" class="alert alert-success d-none" role="alert">
                Perfil actualizado exitosamente
            </div>

            <!-- Mensaje de error -->
            <div id="errorMessage" class="alert alert-danger d-none" role="alert"></div>

            <div class="profile-section">
                <!-- Profile Header -->
                <div class="profile-header">
                    <h2 id="profileName">Cargando...</h2>
                    <p class="mb-0 opacity-75">Cliente</p>
                </div>

                <!-- Profile Card - View Mode -->
                <div class="profile-card view-mode" id="viewMode">
                    <div class="info-row">
                        <div class="info-label">Nombre:</div>
                        <div class="info-value" id="viewNombre">-</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">RUC:</div>
                        <div class="info-value" id="viewRuc">-</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">DNI:</div>
                        <div class="info-value" id="viewDni">-</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email:</div>
                        <div class="info-value" id="viewEmail">-</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Celular:</div>
                        <div class="info-value" id="viewCelular">-</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Dirección:</div>
                        <div class="info-value" id="viewDireccion">-</div>
                    </div>
                    
                    <button type="button" class="btn btn-primary btn-edit" onclick="enableEdit()">
                        Editar Perfil
                    </button>
                </div>

                <!-- Profile Card - Edit Mode -->
                <div class="profile-card edit-form" id="editMode">
                    <form id="editForm">
                        <div class="mb-3">
                            <label for="editNombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="editNombre" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editRuc" class="form-label">RUC (No editable)</label>
                            <input type="text" class="form-control" id="editRuc" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editDni" class="form-label">DNI</label>
                            <input type="text" class="form-control" id="editDni" maxlength="8" pattern="[0-9]{8}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail">
                        </div>
                        
                        <div class="mb-3">
                            <label for="editCelular" class="form-label">Celular</label>
                            <input type="text" class="form-control" id="editCelular" maxlength="9" pattern="[0-9]{9}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="editDireccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="editDireccion" rows="3"></textarea>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Cambiar Contraseña (opcional)</h5>
                        
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Contraseña Actual</label>
                            <input type="password" class="form-control" id="currentPassword">
                            <small class="text-muted">Déjalo vacío si no deseas cambiar tu contraseña</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">Nueva Contraseña</label>
                            <input type="password" class="form-control" id="newPassword">
                            <small class="text-muted">Mínimo 8 caracteres, incluye mayúsculas, minúsculas, números y símbolos</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="confirmPassword">
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                Guardar Cambios
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="cancelEdit()">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
let userData = null;

// Verificar autenticación
function checkAuth() {
    const token = localStorage.getItem('token');
    const userStr = localStorage.getItem('user');
    
    if (!token || !userStr) {
        window.location.href = 'login.php';
        return;
    }
    
    const user = JSON.parse(userStr);
    
    // Verificar que sea cliente
    if (user.tipo !== 'cliente') {
        window.location.href = 'dashboard.php';
        return;
    }
    
    return token;
}

// Cargar datos del perfil
async function loadProfile() {
    const token = checkAuth();
    if (!token) return;
    
    try {
        const response = await fetch('api/users.php?action=me', {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.ok) {
            userData = data.data;
            displayProfile(userData);
        } else {
            if (response.status === 401) {
                localStorage.clear();
                window.location.href = 'login.php';
            } else {
                showError(data.message || 'Error al cargar el perfil');
            }
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error de conexión');
    }
}

// Mostrar datos del perfil
function displayProfile(user) {
    document.getElementById('profileName').textContent = user.nombre;
    document.getElementById('viewNombre').textContent = user.nombre;
    document.getElementById('viewRuc').textContent = user.ruc;
    document.getElementById('viewDni').textContent = user.dni || '-';
    document.getElementById('viewEmail').textContent = user.email || '-';
    document.getElementById('viewCelular').textContent = user.celular || '-';
    document.getElementById('viewDireccion').textContent = user.direccion || '-';
}

// Habilitar modo edición
function enableEdit() {
    document.getElementById('editNombre').value = userData.nombre;
    document.getElementById('editRuc').value = userData.ruc;
    document.getElementById('editDni').value = userData.dni || '';
    document.getElementById('editEmail').value = userData.email || '';
    document.getElementById('editCelular').value = userData.celular || '';
    document.getElementById('editDireccion').value = userData.direccion || '';
    
    // Limpiar campos de contraseña
    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
    
    document.getElementById('viewMode').classList.add('hidden');
    document.getElementById('editMode').classList.add('active');
}

// Cancelar edición
function cancelEdit() {
    document.getElementById('viewMode').classList.remove('hidden');
    document.getElementById('editMode').classList.remove('active');
    hideMessages();
}

// Guardar cambios
document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    hideMessages();
    
    const token = checkAuth();
    if (!token) return;
    
    // Validar contraseñas si se están cambiando
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (currentPassword || newPassword || confirmPassword) {
        if (!currentPassword) {
            showError('Debes ingresar tu contraseña actual para cambiarla');
            return;
        }
        if (!newPassword) {
            showError('Debes ingresar una nueva contraseña');
            return;
        }
        if (newPassword !== confirmPassword) {
            showError('Las contraseñas no coinciden');
            return;
        }
    }
    
    const updateData = {
        nombre: document.getElementById('editNombre').value,
        dni: document.getElementById('editDni').value || null,
        email: document.getElementById('editEmail').value || null,
        celular: document.getElementById('editCelular').value || null,
        direccion: document.getElementById('editDireccion').value || null
    };
    
    // Agregar contraseña si se está cambiando
    if (currentPassword && newPassword) {
        updateData.current_password = currentPassword;
        updateData.password = newPassword;
    }
    
    try {
        const response = await fetch('api/users.php?action=me', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(updateData)
        });
        
        const data = await response.json();
        
        if (response.ok && data.ok) {
            // Actualizar datos locales
            userData = data.data;
            
            // Actualizar localStorage
            const userLocal = JSON.parse(localStorage.getItem('user'));
            userLocal.nombre = userData.nombre;
            localStorage.setItem('user', JSON.stringify(userLocal));
            
            // Actualizar vista
            displayProfile(userData);
            
            // Volver a modo vista
            cancelEdit();
            
            // Mostrar mensaje de éxito
            showSuccess();
        } else {
            showError(data.message || 'Error al actualizar el perfil');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error de conexión');
    }
});

// Cerrar sesión
function logout() {
    localStorage.clear();
    window.location.href = 'login.php';
}

// Mostrar mensaje de éxito
function showSuccess() {
    const msg = document.getElementById('successMessage');
    msg.classList.remove('d-none');
    setTimeout(() => msg.classList.add('d-none'), 5000);
    window.scrollTo(0, 0);
}

// Mostrar mensaje de error
function showError(message) {
    const msg = document.getElementById('errorMessage');
    msg.textContent = message;
    msg.classList.remove('d-none');
    window.scrollTo(0, 0);
}

// Ocultar mensajes
function hideMessages() {
    document.getElementById('successMessage').classList.add('d-none');
    document.getElementById('errorMessage').classList.add('d-none');
}

// Cargar perfil al iniciar
loadProfile();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
