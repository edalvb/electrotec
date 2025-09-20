<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Iniciar Sesión</title>
    <link href="assets/css/global.css" rel="stylesheet">
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

        <!-- Formulario de login -->
        <form action="dashboard.php" method="POST">
            <div class="form-group mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu usuario" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
            </div>
            
            <div class="form-group mb-4">
                <label for="user_type" class="form-label">Tipo de Usuario</label>
                <select class="form-select" id="user_type" name="user_type" required>
                    <option value="">Selecciona un tipo</option>
                    <option value="cliente">Cliente</option>
                    <option value="tecnico">Técnico</option>
                    <option value="gestor">Gestor</option>
                </select>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Iniciar Sesión</button>
                <a href="index.php" class="btn btn-secondary">Volver al Inicio</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>