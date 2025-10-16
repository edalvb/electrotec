<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Electrotec</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/global.css" rel="stylesheet">
</head>
<body>
    <div class="app-layout">
        <?php
        $activePage = 'dashboard';
        include 'partials/sidebar.php';
        ?>
        
        <main class="main-content">
            <div class="main-header">
                <div>
                    <h2>Dashboard</h2>
                    <p class="subtitle">Panel de control principal</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card glass-card">
                        <div class="card-body">
                            <h5 class="card-title">Certificados Activos</h5>
                            <p class="card-text display-6">24</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card glass-card">
                        <div class="card-body">
                            <h5 class="card-title">Equipos Registrados</h5>
                            <p class="card-text display-6">156</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card glass-card">
                        <div class="card-body">
                            <h5 class="card-title">Clientes</h5>
                            <p class="card-text display-6">48</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 col-lg-3">
                    <div class="card glass-card">
                        <div class="card-body">
                            <h5 class="card-title">Usuarios</h5>
                            <p class="card-text display-6">12</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4 mt-4">
                <div class="col-lg-8">
                    <div class="card glass-card">
                        <div class="card-header">
                            <h5 class="mb-0">Certificados Recientes</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Lista de certificados emitidos recientemente...</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card glass-card">
                        <div class="card-header">
                            <h5 class="mb-0">Actividad</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Actividad reciente del sistema...</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php include 'partials/sidebar-toggle-mobile.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/sidebar.js"></script>
</body>
</html>