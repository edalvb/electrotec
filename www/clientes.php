<!-- clientes.html -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Clientes</title>
    <link href="assets/css/global.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar">
            <div class="brand">
                <div class="brand-logo">E</div>
                <div>
                    <div class="brand-title">ELECTROTEC</div>
                    <div class="brand-subtitle">Sistema de certificados</div>
                </div>
            </div>
            <nav class="nav">
                <a href="dashboard.php" class="nav-item">Dashboard</a>
                <a href="certificados.php" class="nav-item">Certificados</a>
                <a href="equipos.php" class="nav-item">Equipos</a>
                <a href="#" class="nav-item active">Clientes</a>
                <a href="gestion-usuarios.php" class="nav-item">Gestión de Usuarios</a>
            </nav>
        </div>

        <div class="main-content flex-grow-1">
            <header class="main-header">
                <div>
                    <h2>Gestión de Clientes</h2>
                    <p class="text-muted">Administra la información de tus clientes</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newClientModal">
                    + Nuevo Cliente
                </button>
            </header>

            <div class="form-group">
                <input id="searchInput" type="text" class="form-control" placeholder="Buscar clientes por nombre...">
            </div>

            <div class="glass card">
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
                                <td colspan="3" class="text-center">
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
                        <td colspan="3" class="text-center text-muted">No se encontraron clientes.</td>
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
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-secondary" data-action="edit" disabled title="Pendiente">
                                    Editar
                                </button>
                                <button class="btn btn-sm btn-outline" data-action="delete" disabled title="Pendiente">
                                    Eliminar
                                </button>
                            </div>
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
                        <td colspan="3" class="text-center text-muted">No fue posible cargar los clientes.</td>
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