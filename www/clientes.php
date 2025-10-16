<!-- clientes.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Clientes</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
</head>
<body>
    <div class="d-flex">
        <?php $activePage = 'clientes'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Gestión de Clientes';
            $pageSubtitle = 'Administra la información de tus clientes';
            $headerActionsHtml = '<a href="nuevo-cliente.php" class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2"><span aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>Nuevo Cliente</a>';
            include __DIR__ . '/partials/header.php';
            ?>

            <div class="form-group">
                <input id="searchInput" type="text" class="form-control" placeholder="Buscar clientes por nombre...">
            </div>

            <div class="card glass p-3 rounded-lg">
                <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
                <div class="table-responsive">
                    <table class="table table-custom table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>Nombre del Cliente</th>
                                <th>RUC</th>
                                <th>DNI</th>
                                <th>Email</th>
                                <th>Celular</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="clientsTbody">
                            <tr id="loadingRow">
                                <td colspan="7" class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="spinner-border text-primary me-2" role="status" aria-hidden="true"></div>
                                        Cargando clientes...
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php include __DIR__ . '/partials/footer.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Verificar autenticación
        try {
            Auth.requireAuth('admin');
        } catch (e) {
            return;
        }

        const API_LIST = 'api/clients.php?action=list&limit=200&offset=0';
        const API_UPDATE = id => `api/clients.php?action=update&id=${encodeURIComponent(id)}`;
        const API_DELETE = id => `api/clients.php?action=delete&id=${encodeURIComponent(id)}`;
        const searchInput = document.getElementById('searchInput');
        const tbody = document.getElementById('clientsTbody');
        const errorAlert = document.getElementById('errorAlert');
        let allClients = [];

        function showLoading() {
            tbody.innerHTML = `
                <tr id="loadingRow">
                    <td colspan="7" class="text-center py-4">
                        <div class="spinner-border text-primary me-2" role="status" aria-hidden="true"></div>
                        Cargando clientes...
                    </td>
                </tr>`;
        }

        function setError(message) {
            errorAlert.textContent = message || 'Ocurrió un error al cargar los clientes.';
            errorAlert.classList.remove('d-none');
        }

        function clearError() {
            errorAlert.classList.add('d-none');
            errorAlert.textContent = '';
        }

        function escapeHtml(str) {
            return String(str)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        function renderRows(clients) {
            clearError();
            if (!clients || clients.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted">No se encontraron clientes.</td>
                    </tr>`;
                return;
            }
            const rows = clients.map(c => {
                const id = c.id || '';
                const nombre = c.nombre || '(Sin nombre)';
                const ruc = c.ruc || '-';
                const dni = c.dni || '-';
                const email = c.email || '-';
                const celular = c.celular || '-';
                const direccion = c.direccion || '-';
                return `
                    <tr data-id="${id}">
                        <td>${escapeHtml(nombre)}</td>
                        <td>${escapeHtml(ruc)}</td>
                        <td>${escapeHtml(dni)}</td>
                        <td>${escapeHtml(email)}</td>
                        <td>${escapeHtml(celular)}</td>
                        <td title="${escapeHtml(direccion)}">${escapeHtml(direccion.length > 30 ? direccion.substring(0, 30) + '...' : direccion)}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-secondary" data-action="edit">Editar</button>
                                <button class="btn btn-sm btn-outline-danger" data-action="delete">Eliminar</button>
                            </div>
                        </td>
                    </tr>`;
            }).join('');
            tbody.innerHTML = rows;
        }

        async function loadClients() {
            showLoading();
            try {
                const json = await Auth.fetchWithAuth(API_LIST);
                if (!json || json.ok !== true || !Array.isArray(json.data)) {
                    throw new Error('Respuesta inesperada del servidor.');
                }
                allClients = json.data;
                renderRows(allClients);
            } catch (error) {
                setError(error?.message || String(error));
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="text-center text-muted">No fue posible cargar los clientes.</td>
                    </tr>`;
            }
        }

        async function deleteClient(id) {
            const json = await Auth.fetchWithAuth(API_DELETE(id), { method: 'DELETE' });
            if (!json?.ok) {
                const msg = json?.message || 'Error al eliminar cliente';
                throw new Error(msg);
            }
        }

        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            if (!q) {
                renderRows(allClients);
                return;
            }
            const filtered = allClients.filter(c => {
                const nombre = (c.nombre || '').toLowerCase();
                const ruc = (c.ruc || '').toLowerCase();
                const email = (c.email || '').toLowerCase();
                return nombre.includes(q) || ruc.includes(q) || email.includes(q);
            });
            renderRows(filtered);
        });

        tbody.addEventListener('click', event => {
            const button = event.target instanceof HTMLElement ? event.target.closest('button[data-action]') : null;
            if (!button) return;
            const row = button.closest('tr');
            if (!row) return;
            const id = row.getAttribute('data-id') || '';
            if (!id) return;
            const action = button.getAttribute('data-action');
            
            if (action === 'edit') {
                // Redirigir a la página de edición (por implementar)
                window.location.href = `editar-cliente.php?id=${encodeURIComponent(id)}`;
            }
            if (action === 'delete') {
                if (!confirm('¿Deseas eliminar este cliente?')) return;
                deleteClient(id)
                    .then(loadClients)
                    .catch(error => alert(error?.message || 'No se pudo eliminar el cliente'));
            }
        });

        loadClients();
    });
    </script>
</body>
</html>