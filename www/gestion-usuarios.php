<!-- gestion-usuarios.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Gestión de Usuarios</title>
    <link href="assets/css/global.css" rel="stylesheet">
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
                    <table class="table table-custom table-borderless table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usersTbody">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Cargando usuarios...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'partials/modal-invite-tech.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function(){
            const tbody = document.getElementById('usersTbody');
            const API = `${location.origin}/api/users.php?action=list&limit=50&offset=0`;

            function roleBadge(role){
                const map = {
                    'SUPERADMIN': 'bg-danger',
                    'ADMIN': 'bg-warning',
                    'TECHNICIAN': 'bg-primary',
                    'CLIENT': 'bg-info'
                };
                const cls = map[role] || 'bg-secondary';
                return `<span class="badge ${cls}">${role}</span>`;
            }

            function statusBadge(isActive, deletedAt){
                if (deletedAt) return '<span class="badge bg-secondary">Eliminado</span>';
                return isActive ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';
            }

            function render(rows){
                if (!rows || rows.length === 0){
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay usuarios</td></tr>';
                    return;
                }
                tbody.innerHTML = rows.map(u => `
                    <tr>
                        <td>
                            ${u.full_name || '(Sin nombre)'}<br>
                            <small class="text-muted">${u.id}</small>
                        </td>
                        <td>${roleBadge(u.role)}</td>
                        <td>${statusBadge(!!u.is_active, u.deleted_at)}</td>
                        <td>
                            <button class="btn btn-sm btn-light" disabled>Editar</button>
                            <button class="btn btn-sm btn-danger" disabled>Eliminar</button>
                        </td>
                    </tr>
                `).join('');
            }

            fetch(API)
                .then(r => r.json())
                .then(json => {
                    if (json && json.ok) { render(json.data); }
                    else { throw new Error(json?.message || 'Error desconocido'); }
                })
                .catch(err => {
                    console.error(err);
                    tbody.innerHTML = `<tr><td colspan="4" class="text-danger text-center">Error cargando usuarios: ${err.message}</td></tr>`;
                });
        })();
    </script>
</body>
</html>