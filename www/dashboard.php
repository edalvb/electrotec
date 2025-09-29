<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="assets/css/global.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'dashboard'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php
            $pageTitle = 'Dashboard';
            $pageSubtitle = 'Panel de control y estadísticas';
            $headerActionsHtml = '<div class="d-flex align-items-center gap-2"><span class="text-muted">Bienvenido</span><span class="badge badge-glass" data-dashboard="user-name">Edward Vasquez</span></div>';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="container-fluid px-4 pb-5">
                <div class="row g-4 dashboard-metrics">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card glass feature-card h-100">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 text-primary">Certificados emitidos</h5>
                                    <p class="text-muted mb-0">Mes actual</p>
                                </div>
                                <span class="badge badge-success" data-metric="certificates-this-month">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card glass feature-card h-100">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-check2-circle"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 text-primary">Equipos conformes</h5>
                                    <p class="text-muted mb-0">Con certificado vigente</p>
                                </div>
                                <span class="badge badge-success" data-metric="equipment-compliant">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card glass feature-card h-100">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 text-primary">Próximas calibraciones</h5>
                                    <p class="text-muted mb-0">30 días</p>
                                </div>
                                <span class="badge badge-warning" data-metric="equipment-due-30">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card glass feature-card h-100">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 text-primary">Equipos vencidos</h5>
                                    <p class="text-muted mb-0">Requieren acción</p>
                                </div>
                                <span class="badge badge-error" data-metric="equipment-overdue">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card glass feature-card h-100">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-filetype-pdf"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 text-primary">PDF generados</h5>
                                    <p class="text-muted mb-0">Cobertura</p>
                                </div>
                                <span class="badge badge-info" data-metric="pdf-completion">0%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card glass feature-card h-100">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 text-primary">Clientes activos</h5>
                                    <p class="text-muted mb-0">Últimos 12 meses</p>
                                </div>
                                <span class="badge badge-success" data-metric="clients-active">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card glass feature-card h-100">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-clipboard-x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 text-primary">Sin certificado</h5>
                                    <p class="text-muted mb-0">Equipos asignados</p>
                                </div>
                                <span class="badge badge-glass" data-metric="equipment-without-certificate">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card glass feature-card h-100">
                            <div class="d-flex align-items-center">
                                <div class="feature-icon me-3">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-0 text-primary">Clientes nuevos</h5>
                                    <p class="text-muted mb-0">Mes actual</p>
                                </div>
                                <span class="badge badge-info" data-metric="clients-new-this-month">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-12 col-xl-8">
                        <div class="card glass h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Certificados por mes</h5>
                                <select class="form-select w-auto" id="months-range" aria-label="Meses">
                                    <option value="6">6 meses</option>
                                    <option value="12" selected>12 meses</option>
                                    <option value="24">24 meses</option>
                                </select>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-certificates"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4">
                        <div class="card glass h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Distribución por tipo de equipo</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-equipment"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-12 col-xl-6">
                        <div class="card glass h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Productividad por técnico</h5>
                                <small class="text-muted">Últimos 12 meses</small>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-productivity"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card glass h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Tasa de fallos</h5>
                                <select class="form-select w-auto" id="fail-range" aria-label="Meses fallos">
                                    <option value="6">6 meses</option>
                                    <option value="12" selected>12 meses</option>
                                    <option value="24">24 meses</option>
                                </select>
                            </div>
                            <div class="card-body">
                                <canvas id="chart-fail-rate"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-12 col-xl-6">
                        <div class="card glass h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Cobertura por cliente</h5>
                                <small class="text-muted">Ordenado ascendente</small>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-sm table-glass" id="table-coverage">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th class="text-end">Equipos</th>
                                            <th class="text-end">Conformes</th>
                                            <th class="text-end">Vencidos</th>
                                            <th class="text-end">Cobertura</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card glass h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Ranking de riesgo</h5>
                                <select class="form-select w-auto" id="risk-limit" aria-label="Clientes riesgo">
                                    <option value="5">Top 5</option>
                                    <option value="10" selected>Top 10</option>
                                    <option value="20">Top 20</option>
                                </select>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-sm table-glass" id="table-risk">
                                    <thead>
                                        <tr>
                                            <th>Cliente</th>
                                            <th class="text-end">Equipos vencidos</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-12 col-xl-6">
                        <div class="card glass h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Próximos vencimientos</h5>
                                <select class="form-select w-auto" id="expiring-range" aria-label="Días proximos">
                                    <option value="14">14 días</option>
                                    <option value="30" selected>30 días</option>
                                    <option value="60">60 días</option>
                                </select>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-sm table-glass" id="table-expiring">
                                    <thead>
                                        <tr>
                                            <th>Certificado</th>
                                            <th>Equipo</th>
                                            <th>Cliente</th>
                                            <th class="text-end">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card glass h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Certificados sin PDF</h5>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-sm table-glass" id="table-missing-pdf">
                                    <thead>
                                        <tr>
                                            <th>Certificado</th>
                                            <th>Cliente</th>
                                            <th>Equipo</th>
                                            <th class="text-end">Calibración</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-12">
                        <div class="card glass">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Equipos sin historial de certificados</h5>
                                <span class="text-muted" data-metric="equipment-without-count">0 equipos</span>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-sm table-glass" id="table-equipment-without">
                                    <thead>
                                        <tr>
                                            <th>Equipo</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Tipo</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" integrity="sha384-pRVcJsteVEhDWUpAJZciV06Pq4jG3E77sGHTrOHJe8CWpF6ehBF38N5EJ2VNivX3" crossorigin="anonymous"></script>
    <script src="assets/js/dashboard.js" defer></script>
</body>
</html>