<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4">ELECTROTEC</h3>
    <p class="text-center text-muted">Inicia sesión para continuar</p>

    <div>
        <div class="mb-3">
            <label for="username" class="form-label">Usuario</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Ingresa tu usuario" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Ingresa tu contraseña" required>
        </div>
        <div class="mb-4">
            <label for="user_type" class="form-label">Tipo de Usuario</label>
            <select class="form-select" id="user_type" name="user_type">
                <option value="cliente" selected>Cliente</option>
                <option value="tecnico">Técnico</option>
                <option value="gestor">Gestor</option>
            </select>
        </div>
        <div class="d-grid gap-2">
            <a href="dashboard.php" class="btn btn-blue">Iniciar Sesión (Provisional)</a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>