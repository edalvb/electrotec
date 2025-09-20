<!-- dashboard.html -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Dashboard</title>
    <link href="assets/css/global.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar text-center">
            <h5 class="my-4">ELECTROTEC<br><small class="text-muted">Sistema de certificados</small></h5>
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action active">Dashboard</a>
                <a href="certificados.php" class="list-group-item list-group-item-action">Certificados</a>
                <a href="equipos.php" class="list-group-item list-group-item-action">Equipos</a>
                <a href="clientes.php" class="list-group-item list-group-item-action">Clientes</a>
                <a href="gestion-usuarios.php" class="list-group-item list-group-item-action">Gestión de Usuarios</a>
            </div>
        </div>

        <div class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Dashboard</h2>
                    <p class="text-muted">Panel de control y estadísticas</p>
                </div>
                <div>
                    <span>Bienvenido</span>
                    <span class="badge bg-primary">Edward Vasquez</span>
                </div>
            </header>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card card-custom p-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-file-earmark-text me-3"></i>
                            <div>
                                <h5 class="mb-0">Certificados emitidos</h5>
                                <p class="text-muted">Este mes</p>
                            </div>
                            <span class="badge bg-success ms-auto">1</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card card-custom p-3">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-check me-3"></i>
                            <div>
                                <h5 class="mb-0">Próximas calibraciones</h5>
                                <p class="text-muted">Siguientes 30 días</p>
                            </div>
                            <span class="badge bg-warning ms-auto">1</span>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-5">Acciones rápidas</h3>
            <div class="row g-4 mt-2">
                <div class="col-md-6 col-lg-4">
                    <button class="btn w-100 py-3 btn-blue" data-bs-toggle="modal" data-bs-target="#newCertificateModal">
                        + Nuevo certificado<br><small>Generar certificado</small>
                    </button>
                </div>
                <div class="col-md-6 col-lg-4">
                    <button class="btn w-100 py-3 btn-blue" data-bs-toggle="modal" data-bs-target="#newClientModal">
                        + Crear cliente<br><small>Añadir nuevo cliente</small>
                    </button>
                </div>
                <div class="col-md-6 col-lg-4">
                    <button class="btn w-100 py-3 btn-blue" data-bs-toggle="modal" data-bs-target="#newEquipmentModal">
                        + Crear equipo<br><small>Registrar equipo</small>
                    </button>
                </div>
                <div class="col-md-6 col-lg-4 mt-4">
                    <button class="btn w-100 py-3 btn-blue" data-bs-toggle="modal" data-bs-target="#inviteTechModal">
                        Gestión de usuarios<br><small>Invitar y administrar</small>
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