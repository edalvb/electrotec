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
                <!-- Sección de métricas principales -->
                <div class="metrics-section mb-5">
                    <div class="row g-4 justify-content-center">
                        <!-- Certificados emitidos -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="metric-card metric-card-primary" data-aos="fade-up" data-aos-delay="0">
                                <div class="metric-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="metric-content">
                                    <div class="metric-value" data-metric="certificates-this-month">0</div>
                                    <div class="metric-label">Certificados emitidos</div>
                                    <div class="metric-subtitle">Total en el sistema</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Equipos vencidos -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="metric-card metric-card-error" data-aos="fade-up" data-aos-delay="100">
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
            </div>

            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>

    <?php include_once 'partials/modal-new-certificate.html'; ?>
    <?php include_once 'partials/modal-new-client.html'; ?>
    <?php include_once 'partials/modal-invite-tech.html'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/dashboard.js?v=2.0" defer></script>
    
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