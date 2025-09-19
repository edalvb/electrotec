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
                <input id="searchInput" type="text" class="form-control" placeholder="Buscar clientes por nombre...">
            </div>

            <div class="card card-custom p-3">
                <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
                <div class="table-responsive">
                    <table class="table table-custom table-borderless table-hover">
                        <thead>
                            <tr>
                                <th>Nombre del Cliente</th>
                                <th>Información de Contacto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="clientsTbody">
                            <tr id="loadingRow">
                                <td colspan="3" class="text-center py-4">
                                    <div class="spinner-border text-primary me-2" role="status" aria-hidden="true"></div>
                                    Cargando clientes...
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
    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const API_URL = 'api/clients.php?action=list&limit=200&offset=0';
    const API_CREATE = 'api/clients.php?action=create';
        const searchInput = document.getElementById('searchInput');
        const tbody = document.getElementById('clientsTbody');
        const errorAlert = document.getElementById('errorAlert');

        /** @type {Array<any>} */
        let allClients = [];

        function showLoading() {
            tbody.innerHTML = `
                <tr id="loadingRow">
                    <td colspan="3" class="text-center py-4">
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

        function isLikelyJsonString(value) {
            return typeof value === 'string' && value.trim().length > 0 && (value.trim().startsWith('{') || value.trim().startsWith('['));
        }

        function parseContactDetails(contact) {
            if (!contact) return null;

            let obj = null;
            if (typeof contact === 'object') {
                obj = contact;
            } else if (isLikelyJsonString(contact)) {
                try {
                    obj = JSON.parse(contact);
                } catch (e) {
                    // Not JSON, fallthrough to string handling
                }
            }

            if (obj && typeof obj === 'object') {
                const parts = [];
                const ruc = obj.ruc || obj.RUC || obj.taxId || obj.tax_id;
                const phone = obj.phone || obj.telefono || obj.celular || obj.mobile;
                const email = obj.email || obj.correo;
                const address = obj.address || obj.direccion;
                if (ruc) parts.push(`RUC: ${ruc}`);
                if (phone) parts.push(`${phone}`);
                if (email) parts.push(`${email}`);
                if (address) parts.push(`${address}`);
                if (parts.length > 0) return parts.join(' / ');
            }

            if (typeof contact === 'string') {
                const txt = contact.trim();
                if (txt) return txt;
            }

            return null;
        }

        function renderRows(clients) {
            clearError();
            if (!clients || clients.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No se encontraron clientes.</td>
                    </tr>`;
                return;
            }

            const rows = clients.map(c => {
                const name = c.name || '(Sin nombre)';
                const contactText = parseContactDetails(c.contact_details) || 'Sin información de contacto';
                const id = c.id || '';
                return `
                    <tr data-id="${id}">
                        <td>${escapeHtml(name)}</td>
                        <td>${escapeHtml(contactText)}</td>
                        <td>
                            <button class="btn btn-sm btn-light" data-action="edit" disabled title="Pendiente">
                                Editar
                            </button>
                            <button class="btn btn-sm btn-danger" data-action="delete" disabled title="Pendiente">
                                Eliminar
                            </button>
                        </td>
                    </tr>`;
            }).join('');
            tbody.innerHTML = rows;
        }

        function escapeHtml(str) {
            return String(str)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        async function loadClients() {
            showLoading();
            try {
                const res = await fetch(API_URL, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const json = await res.json();
                if (!json || json.ok !== true || !Array.isArray(json.data)) {
                    throw new Error('Respuesta inesperada del servidor.');
                }
                allClients = json.data;
                renderRows(allClients);
            } catch (err) {
                setError(err?.message || String(err));
                tbody.innerHTML = `
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No fue posible cargar los clientes.</td>
                    </tr>`;
            }
        }

        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            if (!q) {
                renderRows(allClients);
                return;
            }
            const filtered = allClients.filter(c => (c.name || '').toLowerCase().includes(q));
            renderRows(filtered);
        });

        // Guardar nuevo cliente
        const saveBtn = document.getElementById('saveClientBtn');
        const nameInput = document.getElementById('clientName');
        const rucInput = document.getElementById('clientRuc');
        const dniInput = document.getElementById('clientDni');
        const phoneInput = document.getElementById('clientPhone');
        const emailInput = document.getElementById('clientEmail');
        const newClientModalEl = document.getElementById('newClientModal');
        const newClientModal = newClientModalEl ? bootstrap.Modal.getOrCreateInstance(newClientModalEl) : null;

        async function saveClient() {
            if (!nameInput || !saveBtn) return;
            const name = nameInput.value.trim();
            if (!name) {
                alert('El nombre es obligatorio');
                return;
            }
            saveBtn.disabled = true;
            saveBtn.textContent = 'Guardando...';
            try {
                const contact = {
                    ruc: rucInput?.value?.trim() || undefined,
                    dni: dniInput?.value?.trim() || undefined,
                    phone: phoneInput?.value?.trim() || undefined,
                    email: emailInput?.value?.trim() || undefined,
                };
                // eliminar claves undefined
                Object.keys(contact).forEach(k => contact[k] === undefined && delete contact[k]);

                const res = await fetch(API_CREATE, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ name, contact_details: Object.keys(contact).length ? contact : null })
                });
                const json = await res.json().catch(() => null);
                if (!res.ok || !json?.ok) {
                    const msg = json?.message || `Error al crear cliente (HTTP ${res.status})`;
                    throw new Error(msg);
                }
                // Cerrar modal y limpiar
                if (newClientModal) newClientModal.hide();
                [nameInput, rucInput, dniInput, phoneInput, emailInput].forEach(i => i && (i.value = ''));
                // Refrescar lista
                await loadClients();
            } catch (e) {
                alert(e?.message || 'No se pudo guardar el cliente');
            } finally {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Guardar';
                }
            }
        }

        if (saveBtn) saveBtn.addEventListener('click', saveClient);

        loadClients();
    });
    </script>
</body>
</html>