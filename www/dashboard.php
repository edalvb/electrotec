<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="assets/css/global.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'dashboard'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php
            $pageTitle = 'Dashboard';
            $pageSubtitle = 'Panel de control y estadísticas';
            $headerActionsHtml = '<div class="d-flex align-items-center gap-2"><span class="text-muted">Bienvenido</span><span class="badge badge-glass" data-dashboard="user-name">Cargando...</span><button class="btn btn-sm btn-danger" onclick="logout()">Cerrar Sesión</button></div>';
            include __DIR__ . '/partials/header.php';
            ?>

            <script>
            // Verificar autenticación al cargar
            (function() {
                const token = localStorage.getItem('token');
                const userStr = localStorage.getItem('user');
                
                if (!token || !userStr) {
                    window.location.href = 'login.php';
                    return;
                }
                
                const user = JSON.parse(userStr);
                
                // Verificar que sea admin
                if (user.tipo !== 'admin') {
                    window.location.href = 'clientes-certificados.php';
                    return;
                }
                
                // Mostrar username del usuario
                const userNameElement = document.querySelector('[data-dashboard="user-name"]');
                if (userNameElement) {
                    userNameElement.textContent = user.username;
                }
            })();

            function logout() {
                localStorage.clear();
                window.location.href = 'login.php';
            }
            </script>

            <div class="container-fluid px-4 pb-5">
                <!-- Sección de métricas principales con mejor jerarquía visual -->
                <div class="metrics-section mb-5">
                    <div class="row g-4">
                        <!-- Métricas principales más destacadas -->
                        <div class="col-12 col-lg-6 col-xl-3">
                            <div class="metric-card metric-card-primary" data-aos="fade-up" data-aos-delay="0">
                                <div class="metric-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value" data-metric="certificates-this-month">0</div>
                                    <div class="metric-label">Certificados emitidos</div>
                                    <div class="metric-subtitle">Este mes</div>
                                </div>
                                <div class="metric-trend">
                                    <i class="bi bi-arrow-up"></i>
                                    <span>12%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-lg-6 col-xl-3">
                            <div class="metric-card metric-card-success" data-aos="fade-up" data-aos-delay="100">
                                <div class="metric-icon">
                                    <i class="bi bi-check2-circle"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value" data-metric="equipment-compliant">0</div>
                                    <div class="metric-label">Equipos conformes</div>
                                    <div class="metric-subtitle">Certificado vigente</div>
                                </div>
                                <div class="metric-trend positive">
                                    <i class="bi bi-arrow-up"></i>
                                    <span>8%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-lg-6 col-xl-3">
                            <div class="metric-card metric-card-warning" data-aos="fade-up" data-aos-delay="200">
                                <div class="metric-icon">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value" data-metric="equipment-due-30">0</div>
                                    <div class="metric-label">Próximas calibraciones</div>
                                    <div class="metric-subtitle">Próximos 30 días</div>
                                </div>
                                <div class="metric-alert">
                                    <i class="bi bi-exclamation-circle"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-lg-6 col-xl-3">
                            <div class="metric-card metric-card-error" data-aos="fade-up" data-aos-delay="300">
                                <div class="metric-icon">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value" data-metric="equipment-overdue">0</div>
                                    <div class="metric-label">Equipos vencidos</div>
                                    <div class="metric-subtitle">Requieren acción inmediata</div>
                                </div>
                                <div class="metric-pulse"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métricas secundarias más compactas -->
                <div class="secondary-metrics-section mb-5">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="secondary-metric-card" data-aos="fade-up" data-aos-delay="400">
                                <div class="secondary-metric-icon">
                                    <i class="bi bi-filetype-pdf"></i>
                                </div>
                                <div class="secondary-metric-content">
                                    <div class="secondary-metric-value" data-metric="pdf-completion">0%</div>
                                    <div class="secondary-metric-label">PDF generados</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3">
                            <div class="secondary-metric-card" data-aos="fade-up" data-aos-delay="450">
                                <div class="secondary-metric-icon">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="secondary-metric-content">
                                    <div class="secondary-metric-value" data-metric="clients-active">0</div>
                                    <div class="secondary-metric-label">Clientes activos</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3">
                            <div class="secondary-metric-card" data-aos="fade-up" data-aos-delay="500">
                                <div class="secondary-metric-icon">
                                    <i class="bi bi-clipboard-x"></i>
                                </div>
                                <div class="secondary-metric-content">
                                    <div class="secondary-metric-value" data-metric="equipment-without-certificate">0</div>
                                    <div class="secondary-metric-label">Sin certificado</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6 col-md-3">
                            <div class="secondary-metric-card" data-aos="fade-up" data-aos-delay="550">
                                <div class="secondary-metric-icon">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                                <div class="secondary-metric-content">
                                    <div class="secondary-metric-value" data-metric="clients-new-this-month">0</div>
                                    <div class="secondary-metric-label">Clientes nuevos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de gráficos principales -->
                <div class="charts-section mb-5">
                    <div class="row g-4">
                        <div class="col-12 col-xl-8">
                            <div class="chart-card chart-card-primary" data-aos="fade-right" data-aos-delay="600">
                                <div class="chart-header">
                                    <div class="chart-title-group">
                                        <h3 class="chart-title">Certificados por mes</h3>
                                        <p class="chart-subtitle">Tendencia de certificaciones emitidas</p>
                                    </div>
                                    <div class="chart-controls">
                                        <select class="form-select-glass" id="months-range" aria-label="Período">
                                            <option value="6">6 meses</option>
                                            <option value="12" selected>12 meses</option>
                                            <option value="24">24 meses</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="chart-body">
                                    <div class="chart-container">
                                        <canvas id="chart-certificates"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-xl-4">
                            <div class="chart-card chart-card-secondary" data-aos="fade-left" data-aos-delay="700">
                                <div class="chart-header">
                                    <div class="chart-title-group">
                                        <h3 class="chart-title">Tipos de equipo</h3>
                                        <p class="chart-subtitle">Distribución del parque</p>
                                    </div>
                                </div>
                                <div class="chart-body">
                                    <div class="chart-container">
                                        <canvas id="chart-equipment"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de análisis avanzado -->
                <div class="analysis-section mb-5">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="chart-card chart-card-analysis" data-aos="fade-up" data-aos-delay="900">
                                <div class="chart-header">
                                    <div class="chart-title-group">
                                        <h3 class="chart-title">Tasa de fallos</h3>
                                        <p class="chart-subtitle">Análisis de conformidad</p>
                                    </div>
                                    <div class="chart-controls">
                                        <select class="form-select-glass" id="fail-range" aria-label="Período">
                                            <option value="6">6 meses</option>
                                            <option value="12" selected>12 meses</option>
                                            <option value="24">24 meses</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="chart-body">
                                    <div class="chart-container">
                                        <canvas id="chart-fail-rate"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de datos tabulares -->
                <div class="tables-section mb-5">
                    <div class="row g-4">
                        <div class="col-12 col-xl-6">
                            <div class="data-card" data-aos="fade-right" data-aos-delay="1000">
                                <div class="data-header">
                                    <div class="data-title-group">
                                        <h3 class="data-title">
                                            <i class="bi bi-pie-chart me-2"></i>
                                            Cobertura por cliente
                                        </h3>
                                        <p class="data-subtitle">Estado de certificaciones por empresa</p>
                                    </div>
                                    <div class="data-badge">
                                        <span class="badge badge-info">Ascendente</span>
                                    </div>
                                </div>
                                <div class="data-body">
                                    <div class="table-container">
                                        <table class="table-modern" id="table-coverage">
                                            <thead>
                                                <tr>
                                                    <th>Cliente</th>
                                                    <th class="text-center">Equipos</th>
                                                    <th class="text-center">Conformes</th>
                                                    <th class="text-center">Vencidos</th>
                                                    <th class="text-center">Cobertura</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-xl-6">
                            <div class="data-card data-card-warning" data-aos="fade-left" data-aos-delay="1100">
                                <div class="data-header">
                                    <div class="data-title-group">
                                        <h3 class="data-title">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            Ranking de riesgo
                                        </h3>
                                        <p class="data-subtitle">Clientes con mayor exposición</p>
                                    </div>
                                    <div class="data-controls">
                                        <select class="form-select-glass" id="risk-limit" aria-label="Límite ranking">
                                            <option value="5">Top 5</option>
                                            <option value="10" selected>Top 10</option>
                                            <option value="20">Top 20</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="data-body">
                                    <div class="table-container">
                                        <table class="table-modern" id="table-risk">
                                            <thead>
                                                <tr>
                                                    <th>Cliente</th>
                                                    <th class="text-center">Equipos vencidos</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de alertas y seguimiento -->
                <div class="alerts-section mb-5">
                    <div class="row g-4">
                        <div class="col-12 col-xl-6">
                            <div class="data-card data-card-urgent" data-aos="fade-right" data-aos-delay="1200">
                                <div class="data-header">
                                    <div class="data-title-group">
                                        <h3 class="data-title">
                                            <i class="bi bi-clock-history me-2"></i>
                                            Próximos vencimientos
                                        </h3>
                                        <p class="data-subtitle">Certificados que requieren renovación</p>
                                    </div>
                                    <div class="data-controls">
                                        <select class="form-select-glass" id="expiring-range" aria-label="Horizonte temporal">
                                            <option value="14">14 días</option>
                                            <option value="30" selected>30 días</option>
                                            <option value="60">60 días</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="data-body">
                                    <div class="table-container">
                                        <table class="table-modern table-urgent" id="table-expiring">
                                            <thead>
                                                <tr>
                                                    <th>Certificado</th>
                                                    <th>Equipo</th>
                                                    <th>Cliente</th>
                                                    <th class="text-center">Vencimiento</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12 col-xl-6">
                            <div class="data-card data-card-incomplete" data-aos="fade-left" data-aos-delay="1300">
                                <div class="data-header">
                                    <div class="data-title-group">
                                        <h3 class="data-title">
                                            <i class="bi bi-file-earmark-x me-2"></i>
                                            Certificados sin PDF
                                        </h3>
                                        <p class="data-subtitle">Documentación pendiente de generar</p>
                                    </div>
                                    <div class="data-badge">
                                        <span class="badge badge-warning">Incompleto</span>
                                    </div>
                                </div>
                                <div class="data-body">
                                    <div class="table-container">
                                        <table class="table-modern" id="table-missing-pdf">
                                            <thead>
                                                <tr>
                                                    <th>Certificado</th>
                                                    <th>Cliente</th>
                                                    <th>Equipo</th>
                                                    <th class="text-center">Calibración</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de equipos sin certificar -->
                <div class="orphaned-section">
                    <div class="row">
                        <div class="col-12">
                            <div class="data-card data-card-orphaned" data-aos="fade-up" data-aos-delay="1400">
                                <div class="data-header">
                                    <div class="data-title-group">
                                        <h3 class="data-title">
                                            <i class="bi bi-shield-slash me-2"></i>
                                            Equipos sin historial de certificados
                                        </h3>
                                        <p class="data-subtitle">Equipos que nunca han sido certificados</p>
                                    </div>
                                    <div class="data-stats">
                                        <div class="stat-item">
                                            <span class="stat-value" data-metric="equipment-without-count">0</span>
                                            <span class="stat-label">equipos</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="data-body">
                                    <div class="table-container">
                                        <table class="table-modern table-orphaned" id="table-equipment-without">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <i class="bi bi-gear me-1"></i>
                                                        Equipo
                                                    </th>
                                                    <th>
                                                        <i class="bi bi-building me-1"></i>
                                                        Marca
                                                    </th>
                                                    <th>
                                                        <i class="bi bi-tag me-1"></i>
                                                        Modelo
                                                    </th>
                                                    <th>
                                                        <i class="bi bi-collection me-1"></i>
                                                        Tipo
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
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
    <?php include_once 'partials/modal-invite-tech.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" integrity="sha384-pRVcJsteVEhDWUpAJZciV06Pq4jG3E77sGHTrOHJe8CWpF6ehBF38N5EJ2VNivX3" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/dashboard.js" defer></script>
    
    <script>
        // Inicializar animaciones AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });
        
        // Agregar efectos de hover interactivos
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar autenticación
            try {
                Auth.requireAuth();
            } catch (e) {
                return;
            }

            // Efecto hover en las tarjetas de métricas
            const metricCards = document.querySelectorAll('.metric-card, .secondary-metric-card');
            metricCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
            
            // Efecto hover en las tarjetas de gráficos
            const chartCards = document.querySelectorAll('.chart-card, .data-card');
            chartCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>