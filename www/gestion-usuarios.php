<!-- gestion-usuarios.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Gestión de Usuarios</title>
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
                <a href="clientes.php" class="list-group-item list-group-item-action">Clientes</a>
                <a href="#" class="list-group-item list-group-item-action active">Gestión de Usuarios</a>
            </div>
        </div>

        <div class="main-content flex-grow-1">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Gestión de Usuarios</h2>
                    <p class="text-muted">Administración de roles y accesos</p>
                </div>
                <button class="btn btn-blue" data-bs-toggle="modal" data-bs-target="#inviteTechModal">
                    Invitar técnico
                </button>
            </header>

            <div class="mb-3">
                <input type="text" class="form-control" placeholder="Buscar por nombre o ID">
            </div>

            <div class="card card-custom p-3">
                <div class="table-responsive">
                    <table class="table table-custom table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    Edward II<br>
                                    <small class="text-muted">c61f3a4e-9daa-4a8b-8e9e-2fb9d6061b02</small>
                                </td>
                                <td><span class="badge bg-primary">TÉCNICO</span></td>
                                <td><span class="badge bg-success">Activo</span></td>
                                <td>
                                    <button class="btn btn-sm btn-light">Editar</button>
                                    <button class="btn btn-sm btn-danger">Eliminar</button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Edward Vasquez<br>
                                    <small class="text-muted">b7df3ea7-1aa3-4382-8bde-190d941d8fca</small>
                                </td>
                                <td><span class="badge bg-warning">ADMIN</span></td>
                                <td><span class="badge bg-success">Activo</span></td>
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
    <?php include_once 'partials/modal-invite-tech.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>