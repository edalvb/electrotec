<!-- dashboard.html -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/global.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar glass text-center">
            <div class="brand">
                <div class="brand-title">ELECTROTEC</div>
                <div class="brand-subtitle">Sistema de certificados</div>
            </div>
            <div class="nav">
                <a href="#" class="nav-item active">
                    <i class="bi bi-house"></i>
                    Dashboard
                </a>
                <a href="certificados.php" class="nav-item">
                    <i class="bi bi-file-earmark-text"></i>
                    Certificados
                </a>
                <a href="equipos.php" class="nav-item">
                    <i class="bi bi-gear"></i>
                    Equipos
                </a>
                <a href="clientes.php" class="nav-item">
                    <i class="bi bi-people"></i>
                    Clientes
                </a>
                <a href="gestion-usuarios.php" class="nav-item">
                    <i class="bi bi-person-gear"></i>
                    Gestión de Usuarios
                </a>
            </div>
        </div>

        <div class="main-content flex-grow-1">
            <header class="main-header">
                <div>
                    <h2>Dashboard</h2>
                    <p class="subtitle">Panel de control y estadísticas</p>
                </div>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-2">Bienvenido</span>
                    <span class="badge badge-glass">Edward Vasquez</span>
                </div>
            </header>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card glass feature-card">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon me-3">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0 text-primary">Certificados emitidos</h5>
                                <p class="text-muted mb-0">Este mes</p>
                            </div>
                            <span class="badge badge-success">1</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card glass feature-card">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon me-3">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-0 text-primary">Próximas calibraciones</h5>
                                <p class="text-muted mb-0">Siguientes 30 días</p>
                            </div>
                            <span class="badge badge-warning">1</span>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-5 text-primary">Acciones rápidas</h3>
            <div class="row g-4 mt-2">
                <div class="col-md-6 col-lg-4">
                    <button class="btn btn-primary w-100 py-3" data-bs-toggle="modal" data-bs-target="#newCertificateModal">
                        <i class="bi bi-file-earmark-plus me-2"></i>
                        <div>
                            <strong>Nuevo certificado</strong><br>
                            <small>Generar certificado</small>
                        </div>
                    </button>
                </div>
                <div class="col-md-6 col-lg-4">
                    <button class="btn btn-secondary glass w-100 py-3" data-bs-toggle="modal" data-bs-target="#newClientModal">
                        <i class="bi bi-person-plus me-2"></i>
                        <div>
                            <strong>Crear cliente</strong><br>
                            <small>Añadir nuevo cliente</small>
                        </div>
                    </button>
                </div>
                <div class="col-md-6 col-lg-4">
                    <button class="btn btn-secondary glass w-100 py-3" data-bs-toggle="modal" data-bs-target="#newEquipmentModal">
                        <i class="bi bi-plus-circle me-2"></i>
                        <div>
                            <strong>Crear equipo</strong><br>
                            <small>Registrar equipo</small>
                        </div>
                    </button>
                </div>
                <div class="col-md-6 col-lg-4 mt-4">
                    <button class="btn btn-secondary glass w-100 py-3" data-bs-toggle="modal" data-bs-target="#inviteTechModal">
                        <i class="bi bi-people me-2"></i>
                        <div>
                            <strong>Gestión de usuarios</strong><br>
                            <small>Invitar y administrar</small>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <?php include_once 'partials/modal-new-certificate.html'; ?>
    <?php include_once 'partials/modal-new-client.html'; ?>
    <?php include_once 'partials/modal-new-equipment.html'; ?>
    <?php include_once 'partials/modal-invite-tech.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>