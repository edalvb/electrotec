<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Iniciar Sesión</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <style>
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            display: none;
        }
        .error-message.show {
            display: block;
        }
    </style>
</head>
<body>

<div class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="card glass p-4 rounded-lg shadow" style="min-width: 340px; max-width: 420px; width: 100%;">
        <!-- Branding -->
        <div class="text-center mb-4">
            <img src="assets/images/logo.png" alt="Electrotec" class="brand-logo mb-2">
            <div class="brand-title">ELECTROTEC</div>
            <div class="brand-subtitle text-muted">Sistema de certificados</div>
        </div>

        <!-- Mensaje de error -->
        <div id="errorMessage" class="error-message"></div>

        <!-- Formulario de login -->
        <form id="loginForm">
            <div class="form-group mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu nombre de usuario" required>
            </div>
            
            <div class="form-group mb-4">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" id="btnLogin">
                    <span id="btnText">Iniciar Sesión</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
                <a href="index.php" class="btn btn-secondary">Volver al Inicio</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');
    const btnLogin = document.getElementById('btnLogin');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');
    
    // Ocultar mensaje de error anterior
    errorMessage.classList.remove('show');
    
    // Deshabilitar botón y mostrar spinner
    btnLogin.disabled = true;
    btnText.classList.add('d-none');
    btnSpinner.classList.remove('d-none');
    
    try {
        const response = await fetch('api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ username, password })
        });
        
        const data = await response.json();
        
        if (response.ok && data.ok) {
            // Guardar token en localStorage
            localStorage.setItem('token', data.data.token);
            localStorage.setItem('user', JSON.stringify(data.data.user));
            
            // Redirigir según tipo de usuario
            if (data.data.user.tipo === 'admin') {
                window.location.href = 'dashboard.php';
            } else {
                window.location.href = 'cliente.php';
            }
        } else {
            // Mostrar mensaje de error
            errorMessage.textContent = data.message || 'Error al iniciar sesión';
            errorMessage.classList.add('show');
            
            // Habilitar botón y ocultar spinner
            btnLogin.disabled = false;
            btnText.classList.remove('d-none');
            btnSpinner.classList.add('d-none');
        }
    } catch (error) {
        console.error('Error:', error);
        errorMessage.textContent = 'Error de conexión. Por favor, intente nuevamente.';
        errorMessage.classList.add('show');
        
        // Habilitar botón y ocultar spinner
        btnLogin.disabled = false;
        btnText.classList.remove('d-none');
        btnSpinner.classList.add('d-none');
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
