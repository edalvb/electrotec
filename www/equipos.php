<!-- equipos.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Equipos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar text-center">
            <h5 class="my-4">ELECTROTEC<br><small class="text-muted">Sistema de certificados</small></h5>
            <div class="list-group list-group-flush">
                <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                <a href="certificados.php" class="list-group-item list-group-item-action">Certificados</a>
                <a href="#" class="list-group-item list-group-item-action active">Equipos</a>
                <a href="clientes.php" class="list-group-item list-group-item-action">Clientes</a>
                <a href="gestion-usuarios.php" class="list-group-item list-group-item-action">Gestión de Usuarios</a>
            </div>
        </div>

        <div class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Gestión de Equipos</h2>
                    <p class="text-muted">Administra el inventario de equipos de medición</p>
                </div>
                <button class="btn btn-blue" data-bs-toggle="modal" data-bs-target="#newEquipmentModal">
                    + Nuevo Equipo
                </button>
            </header>

            <div class="mb-3">
                <input type="text" class="form-control" placeholder="Buscar por serie, marca o modelo...">
            </div>

            <div class="card card-custom p-3">
                <div class="table-responsive">
                    <table class="table table-custom table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>Número de Serie</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Tipo</th>
                                <th>Cliente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-secondary">SN-DEMO-001</span></td>
                                <td>LEICA</td>
                                <td>TS-06 ULTRA 2</td>
                                <td><span class="badge bg-secondary">Nivel</span></td>
                                <td>Edward</td>
                                <td>
                                    <button class="btn btn-sm btn-light">Editar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'partials/modal-new-equipment.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>