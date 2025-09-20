<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Iniciar Sesión</title>
    <link href="assets/css/global.css" rel="stylesheet">
</head>
<body>

<div class="login-card">
    <!-- Header con logo y marca -->
    <div class="text-center mb-4">
        <h1 class="hero-title mb-2">ELECTROTEC</h1>
        <p class="text-muted">Inicia sesión para continuar</p>
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
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            <a href="index.php" class="btn btn-secondary">Volver al Inicio</a>
        </div>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>