<!-- clientes.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Clientes</title>
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
                <a href="equipos.php" class="list-group-item list-group-item-action">Equipos</a>
                <a href="#" class="list-group-item list-group-item-action active">Clientes</a>
                <a href="gestion-usuarios.php" class="list-group-item list-group-item-action">Gestión de Usuarios</a>
            </div>
        </div>

        <div class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Gestión de Clientes</h2>
                    <p class="text-muted">Administra la información de tus clientes</p>
                </div>
                <button class="btn btn-blue" data-bs-toggle="modal" data-bs-target="#newClientModal">
                    + Nuevo Cliente
                </button>
            </header>

            <div class="mb-3">
                <input type="text" class="form-control" placeholder="Buscar clientes por nombre...">
            </div>

            <div class="card card-custom p-3">
                <div class="table-responsive">
                    <table class="table table-custom table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>Nombre del Cliente</th>
                                <th>Información de Contacto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Edward</td>
                                <td>RUC: 10781990791 / +51945077284</td>
                                <td>
                                    <button class="btn btn-sm btn-light">Editar</button>
                                    <button class="btn btn-sm btn-danger">Eliminar</button>
                                </td>
                            </tr>
                            <tr>
                                <td>Carlos</td>
                                <td>Sin información de contacto</td>
                                <td>
                                    <button class="btn btn-sm btn-light">Editar</button>
                                    <button class="btn btn-sm btn-danger">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'partials/modal-new-client.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>