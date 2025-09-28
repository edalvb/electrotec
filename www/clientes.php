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
        <?php $activePage = 'clientes'; include __DIR__ . '/partials/sidebar.php'; ?>

        <div class="main-content flex-grow-1">
            <?php 
            $pageTitle = 'Gestión de Clientes';
            $pageSubtitle = 'Administra la información de tus clientes';
            $headerActionsHtml = '<button class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newClientModal"><span aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg></span>Nuevo Cliente</button>';
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
                                <th>Correo</th>
                                <th>Información de Contacto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="clientsTbody">
                            <tr id="loadingRow">
                                <td colspan="4" class="text-center">
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
    <?php include_once 'partials/modal-new-client.html'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const API_LIST = 'api/clients.php?action=list&limit=200&offset=0';
        const API_CREATE = 'api/clients.php?action=create';
        const API_UPDATE = id => `api/clients.php?action=update&id=${encodeURIComponent(id)}`;
        const API_DELETE = id => `api/clients.php?action=delete&id=${encodeURIComponent(id)}`;
        const searchInput = document.getElementById('searchInput');
        const tbody = document.getElementById('clientsTbody');
        const errorAlert = document.getElementById('errorAlert');
        const saveBtn = document.getElementById('saveClientBtn');
        const nameInput = document.getElementById('clientName');
        const rucInput = document.getElementById('clientRuc');
        const dniInput = document.getElementById('clientDni');
        const phoneInput = document.getElementById('clientPhone');
        const emailInput = document.getElementById('clientEmail');
        const modalElement = document.getElementById('newClientModal');
        const modalTitle = modalElement?.querySelector('.modal-title');
        const modal = modalElement ? bootstrap.Modal.getOrCreateInstance(modalElement) : null;
        let allClients = [];
        let modalMode = 'create';
        let editingClientId = null;

        function showLoading() {
            tbody.innerHTML = `
                <tr id="loadingRow">
                    <td colspan="4" class="text-center py-4">
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
                    obj = null;
                }
            }
            if (obj && typeof obj === 'object') {
                const parts = [];
                const ruc = obj.ruc || obj.RUC || obj.taxId || obj.tax_id;
                const dni = obj.dni || obj.DNI;
                const phone = obj.phone || obj.telefono || obj.celular || obj.mobile;
                const address = obj.address || obj.direccion;
                if (ruc) parts.push(`RUC: ${ruc}`);
                if (dni) parts.push(`DNI: ${dni}`);
                if (phone) parts.push(`${phone}`);
                if (address) parts.push(`${address}`);
                if (parts.length > 0) return parts.join(' / ');
            }
            if (typeof contact === 'string') {
                const txt = contact.trim();
                if (txt) return txt;
            }
            return null;
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
                        <td colspan="4" class="text-center text-muted">No se encontraron clientes.</td>
                    </tr>`;
                return;
            }
            const rows = clients.map(c => {
                const id = c.id || '';
                const name = c.name || '(Sin nombre)';
                const email = c.email || '';
                const contactText = parseContactDetails(c.contact_details) || 'Sin información de contacto';
                return `
                    <tr data-id="${id}">
                        <td>${escapeHtml(name)}</td>
                        <td>${escapeHtml(email)}</td>
                        <td>${escapeHtml(contactText)}</td>
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
                const res = await fetch(API_LIST, { headers: { Accept: 'application/json' } });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const json = await res.json();
                if (!json || json.ok !== true || !Array.isArray(json.data)) {
                    throw new Error('Respuesta inesperada del servidor.');
                }
                allClients = json.data;
                renderRows(allClients);
            } catch (error) {
                setError(error?.message || String(error));
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-muted">No fue posible cargar los clientes.</td>
                    </tr>`;
            }
        }

        function setModalMode(mode, client) {
            modalMode = mode;
            editingClientId = client?.id || null;
            if (modalTitle) {
                modalTitle.textContent = mode === 'edit' ? 'Editar Cliente' : 'Nuevo Cliente';
            }
            if (saveBtn) {
                saveBtn.textContent = mode === 'edit' ? 'Guardar cambios' : 'Guardar';
            }
            if (mode === 'edit' && client) {
                nameInput.value = client.name || '';
                emailInput.value = client.email || '';
                const contact = client.contact_details || {};
                rucInput.value = contact.ruc || '';
                dniInput.value = contact.dni || '';
                phoneInput.value = contact.phone || contact.telefono || contact.celular || '';
            } else {
                [nameInput, emailInput, rucInput, dniInput, phoneInput].forEach(input => {
                    if (input) input.value = '';
                });
            }
        }

        function collectContactDetails() {
            const contact = {};
            const ruc = rucInput?.value?.trim();
            const dni = dniInput?.value?.trim();
            const phone = phoneInput?.value?.trim();
            if (ruc) contact.ruc = ruc;
            if (dni) contact.dni = dni;
            if (phone) contact.phone = phone;
            return Object.keys(contact).length ? contact : null;
        }

        async function createClient(payload) {
            const res = await fetch(API_CREATE, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify(payload),
            });
            const json = await res.json().catch(() => null);
            if (!res.ok || !json?.ok) {
                const msg = json?.message || `Error al crear cliente (HTTP ${res.status})`;
                throw new Error(msg);
            }
        }

        async function updateClient(id, payload) {
            const res = await fetch(API_UPDATE(id), {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                body: JSON.stringify(payload),
            });
            const json = await res.json().catch(() => null);
            if (!res.ok || !json?.ok) {
                const msg = json?.message || `Error al actualizar cliente (HTTP ${res.status})`;
                throw new Error(msg);
            }
        }

        async function deleteClient(id) {
            const res = await fetch(API_DELETE(id), {
                method: 'DELETE',
                headers: { Accept: 'application/json' },
            });
            const json = await res.json().catch(() => null);
            if (!res.ok || !json?.ok) {
                const msg = json?.message || `Error al eliminar cliente (HTTP ${res.status})`;
                throw new Error(msg);
            }
        }

        async function handleSave() {
            if (!saveBtn) return;
            const name = nameInput?.value?.trim() || '';
            const email = emailInput?.value?.trim() || '';
            if (!name) {
                alert('El nombre es obligatorio');
                return;
            }
            if (!email) {
                alert('El correo es obligatorio');
                return;
            }
            const contactDetails = collectContactDetails();
            const payload = { name, email, contact_details: contactDetails };
            saveBtn.disabled = true;
            const originalText = saveBtn.textContent;
            saveBtn.textContent = 'Guardando...';
            try {
                if (modalMode === 'edit' && editingClientId) {
                    await updateClient(editingClientId, payload);
                } else {
                    await createClient(payload);
                }
                if (modal) modal.hide();
                await loadClients();
            } catch (error) {
                alert(error?.message || 'Operación no completada');
            } finally {
                saveBtn.disabled = false;
                saveBtn.textContent = originalText;
            }
        }

        searchInput.addEventListener('input', () => {
            const q = searchInput.value.trim().toLowerCase();
            if (!q) {
                renderRows(allClients);
                return;
            }
            const filtered = allClients.filter(c => {
                const name = (c.name || '').toLowerCase();
                const email = (c.email || '').toLowerCase();
                return name.includes(q) || email.includes(q);
            });
            renderRows(filtered);
        });

        if (saveBtn) {
            saveBtn.addEventListener('click', handleSave);
        }

        document.querySelectorAll('[data-bs-target="#newClientModal"]').forEach(trigger => {
            trigger.addEventListener('click', () => setModalMode('create'));
        });

        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', () => setModalMode('create'));
        }

        tbody.addEventListener('click', event => {
            const button = event.target instanceof HTMLElement ? event.target.closest('button[data-action]') : null;
            if (!button) return;
            const row = button.closest('tr');
            if (!row) return;
            const id = row.getAttribute('data-id') || '';
            if (!id) return;
            const client = allClients.find(item => item.id === id);
            const action = button.getAttribute('data-action');
            if (action === 'edit') {
                if (!client) {
                    alert('Cliente no encontrado');
                    return;
                }
                setModalMode('edit', client);
                if (modal) modal.show();
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