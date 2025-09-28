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
        <?php $activePage = 'dashboard'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Dashboard';
            $pageSubtitle = 'Panel de control y estadísticas';
            $headerActionsHtml = '<span class="text-muted me-2">Bienvenido</span><span class="badge badge-glass">Edward Vasquez</span>';
            include __DIR__ . '/partials/header.php';
            ?>

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

            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>
    
    <?php include_once 'partials/modal-new-certificate.html'; ?>
    <?php include_once 'partials/modal-new-client.html'; ?>
    <?php include_once 'partials/modal-new-equipment.html'; ?>
    <?php include_once 'partials/modal-invite-tech.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>