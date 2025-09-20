<!-- gestion-usuarios.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Gestión de Usuarios</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <style>
        /* Glassmorphism System Design Styles */
        :root {
            --primary-blue: #2A2F6C;
            --secondary-blue: #5C66CC;
            --surface-glass: rgba(255, 255, 255, 0.25);
            --border-glass: rgba(255, 255, 255, 0.40);
            --text-primary: #FFFFFF;
            --text-muted: rgba(255, 255, 255, 0.70);
            --success: #10B981;
            --warning: #F59E0B;
            --error: #EF4444;
        }
        
        body {
            background: radial-gradient(circle at 30% 20%, #5C66CC, #2A2F6C);
            min-height: 100vh;
            color: var(--text-primary);
        }
        
        /* Glass Surface Style */
        .glass-surface {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            background: var(--surface-glass);
            border: 1px solid var(--border-glass);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            border-radius: 16px;
        }
        
        .glass-surface-subtle {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid var(--border-glass);
            box-shadow: 0 4px 16px 0 rgba(31, 38, 135, 0.25);
            border-radius: 12px;
        }
        
        /* Typography with shadows */
        h1, h2, h3, h4, h5, h6 {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
            color: var(--text-primary);
        }
        
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        /* Sidebar Glass */
        .sidebar {
            background: none;
            border-right: none;
        }
        
        .sidebar-glass {
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.20);
            border-right: 1px solid var(--border-glass);
            box-shadow: 4px 0 16px 0 rgba(31, 38, 135, 0.30);
        }
        
        /* List group glass effect */
        .list-group-item {
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.20);
            color: var(--text-muted);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            margin-bottom: 4px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .list-group-item:hover {
            background: rgba(255, 255, 255, 0.25);
            color: var(--text-primary);
            transform: translateX(4px);
        }
        
        .list-group-item.active {
            background: var(--primary-blue);
            color: var(--text-primary);
            border-color: var(--secondary-blue);
            box-shadow: 0 4px 16px 0 rgba(42, 47, 108, 0.4);
        }
        
        /* Primary button - solid */
        .btn-primary-glass {
            background: var(--primary-blue);
            border: none;
            color: var(--text-primary);
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 4px 16px 0 rgba(42, 47, 108, 0.4);
            transition: all 0.3s ease;
        }
        
        .btn-primary-glass:hover {
            background: var(--secondary-blue);
            transform: translateY(-2px);
            box-shadow: 0 6px 24px 0 rgba(42, 47, 108, 0.6);
            color: var(--text-primary);
        }
        
        /* Secondary button - glass */
        .btn-secondary-glass {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid var(--border-glass);
            color: var(--text-primary);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-secondary-glass:hover {
            background: rgba(255, 255, 255, 0.35);
            color: var(--text-primary);
        }
        
        /* Input glass style */
        .form-control-glass {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-glass);
            color: var(--text-primary);
            border-radius: 12px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        .form-control-glass::placeholder {
            color: var(--text-muted);
        }
        
        .form-control-glass:focus {
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 0.2rem rgba(92, 102, 204, 0.25);
            background: rgba(0, 0, 0, 0.3);
            color: var(--text-primary);
        }
        
        /* Table glass styling */
        .table-glass {
            color: var(--text-primary);
            --bs-table-bg: transparent;
        }
        
        .table-glass th {
            border-bottom: 1px solid var(--border-glass);
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .table-glass td {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table-glass tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Badge adjustments */
        .badge {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        /* Status badges */
        .badge.bg-success {
            background-color: var(--success) !important;
        }
        
        .badge.bg-warning {
            background-color: var(--warning) !important;
        }
        
        .badge.bg-danger {
            background-color: var(--error) !important;
        }
        
        /* Modal Glass Styling */
        .modal-content {
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            background: var(--surface-glass);
            border: 1px solid var(--border-glass);
            box-shadow: 0 16px 64px 0 rgba(31, 38, 135, 0.5);
            border-radius: 20px;
            color: var(--text-primary);
        }
        
        .modal-header {
            border-bottom: 1px solid var(--border-glass);
        }
        
        .modal-footer {
            border-top: 1px solid var(--border-glass);
        }
        
        .modal-title {
            color: var(--text-primary);
            font-weight: 600;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        
        .form-label {
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .form-select {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-glass);
            color: var(--text-primary);
            border-radius: 12px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        
        .form-select:focus {
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 0.2rem rgba(92, 102, 204, 0.25);
            background: rgba(0, 0, 0, 0.3);
            color: var(--text-primary);
        }
        
        .form-check-input {
            background-color: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-glass);
        }
        
        .form-check-input:checked {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .form-check-label {
            color: var(--text-primary);
        }
        
        .btn-close {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            opacity: 0.8;
        }
        
        .btn-close:hover {
            background: rgba(255, 255, 255, 0.3);
            opacity: 1;
        }
        
        /* Override some Bootstrap modal defaults for glass effect */
        .modal-backdrop {
            background: rgba(42, 47, 108, 0.3);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar sidebar-glass text-center">
            <div class="p-4">
                <h5 class="my-4">ELECTROTEC<br><small class="text-muted">Sistema de certificados</small></h5>
                <div class="list-group list-group-flush">
                    <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="certificados.php" class="list-group-item list-group-item-action">Certificados</a>
                    <a href="equipos.php" class="list-group-item list-group-item-action">Equipos</a>
                    <a href="clientes.php" class="list-group-item list-group-item-action">Clientes</a>
                    <a href="#" class="list-group-item list-group-item-action active">Gestión de Usuarios</a>
                </div>
            </div>
        </div>

        <div class="main-content flex-grow-1 p-4">
            <header class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Gestión de Usuarios</h2>
                    <p class="text-muted">Administración de roles y accesos</p>
                </div>
                <button class="btn btn-primary-glass" data-bs-toggle="modal" data-bs-target="#inviteTechModal">
                    <i class="me-2">👤</i>Invitar técnico
                </button>
            </header>

            <div class="mb-4">
                <input type="text" class="form-control form-control-glass" placeholder="Buscar por nombre o ID">
            </div>

            <div class="glass-surface p-4">
                <div class="table-responsive">
                    <table class="table table-glass table-borderless table-hover" id="usersTable">
                        <thead>
                            <tr>
                                <th>👤 Usuario</th>
                                <th>🏷️ Rol</th>
                                <th>📊 Estado</th>
                                <th>⚙️ Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usersTbody">
                            <tr>
                                <td colspan="4" class="text-center text-muted">⏳ Cargando usuarios...</td>
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
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">📭 No hay usuarios</td></tr>';
                    return;
                }
                tbody.innerHTML = rows.map(u => `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3" style="width: 40px; height: 40px; background: var(--surface-glass); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                                    👤
                                </div>
                                <div>
                                    <div style="color: var(--text-primary); font-weight: 500;">${u.full_name || '(Sin nombre)'}</div>
                                    <small class="text-muted">${u.id}</small>
                                </div>
                            </div>
                        </td>
                        <td>${roleBadge(u.role)}</td>
                        <td>${statusBadge(!!u.is_active, u.deleted_at)}</td>
                        <td>
                            <button class="btn btn-sm btn-secondary-glass me-2" disabled>✏️ Editar</button>
                            <button class="btn btn-sm btn-secondary-glass" disabled style="border-color: var(--error); color: var(--error);">🗑️ Eliminar</button>
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
                    tbody.innerHTML = `<tr><td colspan="4" class="text-center" style="color: var(--error);">❌ Error cargando usuarios: ${err.message}</td></tr>`;
                });
        })();
    </script>
</body>
</html>