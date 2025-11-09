<?php /* tipos-equipo.php (Módulo independiente de gestión de tipos) */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELECTROTEC | Tipos de equipo</title>
    <link href="assets/css/global.css" rel="stylesheet">
    <script src="assets/js/auth.js"></script>
    <style>
        .table thead th { white-space: nowrap; }
    </style>
    </head>
<body>
<div class="d-flex">
    <?php $activePage = 'tipos-equipo'; include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="main-content flex-grow-1">
        <?php 
        $pageTitle = 'Tipos de equipo';
        $pageSubtitle = 'Gestiona los tipos de equipos y su configuración';
        $headerActionsHtml = '';
        include __DIR__ . '/partials/header.php';
        ?>

        <section class="card glass p-3 mb-3 rounded-lg">
            <div id="typeError" class="alert alert-danger d-none" role="alert"></div>
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="newTypeName">Nombre del tipo</label>
                    <input id="newTypeName" type="text" class="form-control" placeholder="Ej: Estación Total" autocomplete="off" />
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label d-block">Resultado (precisión)</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="newTypePrecision" id="newTypePrecisionSeg" value="segundos" checked>
                            <label class="form-check-label" for="newTypePrecisionSeg">Segundos</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="newTypePrecision" id="newTypePrecisionLin" value="lineal">
                            <label class="form-check-label" for="newTypePrecisionLin">Lineal</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="newTypePrecision" id="newTypePrecisionVH" value="vertical_horizontal">
                            <label class="form-check-label" for="newTypePrecisionVH">Vert./Horiz.</label>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label d-block" for="newTypeConPrisma">Distancia con prisma</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="newTypeConPrisma">
                        <label class="form-check-label" for="newTypeConPrisma">Permitir</label>
                    </div>
                </div>
                <div class="col-12 col-md-3 text-md-end">
                    <button id="addTypeBtn" type="button" class="btn btn-primary w-100">Crear tipo</button>
                </div>
            </div>
        </section>

        <section class="card glass p-3 rounded-lg">
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th style="width: 30%">Tipo</th>
                            <th style="width: 25%">Precisión</th>
                            <th style="width: 15%" class="text-center">Con prisma</th>
                            <th style="width: 10%" class="text-center">Uso</th>
                            <th style="width: 20%" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="typeTableBody">
                        <tr><td colspan="5" class="text-center text-muted py-4">Cargando…</td></tr>
                    </tbody>
                </table>
            </div>
            <p id="typeMeta" class="text-muted small mb-0"></p>
        </section>

        <?php include __DIR__ . '/partials/footer.php'; ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/uppercase-inputs.js"></script>
<script>
(function() {
    // Auth requerida
    try { Auth.requireAuth('admin'); } catch(e) { return; }

    const api = {
        list: () => 'api/equipment.php?action=listTypes',
        create: () => 'api/equipment.php?action=createType',
        update: (id) => `api/equipment.php?action=updateType&id=${encodeURIComponent(id)}`,
        remove: (id) => `api/equipment.php?action=deleteType&id=${encodeURIComponent(id)}`,
    };

    const els = {
        error: document.getElementById('typeError'),
        table: document.getElementById('typeTableBody'),
        meta: document.getElementById('typeMeta'),
        addBtn: document.getElementById('addTypeBtn'),
        name: document.getElementById('newTypeName'),
        conPrisma: document.getElementById('newTypeConPrisma'),
        precisionRadios: () => Array.from(document.querySelectorAll('input[name="newTypePrecision"]')),
    };

    const state = { types: [] };

    function setError(msg) {
        if (!els.error) return;
        if (!msg) { els.error.textContent=''; els.error.classList.add('d-none'); return; }
        els.error.textContent = msg;
        els.error.classList.remove('d-none');
    }

    async function sendJson(url, { method='POST', body=null }={}) {
        const opts = { method };
        if (body !== null) { opts.headers = { 'Content-Type': 'application/json' }; opts.body = JSON.stringify(body); }
        return await Auth.fetchWithAuth(url, opts);
    }

    async function fetchList() {
        const resp = await Auth.fetchWithAuth(api.list());
        const list = resp && resp.ok && Array.isArray(resp.data) ? resp.data : [];
        state.types = list.map(t => ({
            id: Number(t?.id ?? 0),
            name: String(t?.name ?? ''),
            equipment_count: Number(t?.equipment_count ?? 0),
            resultado_precision: String(t?.resultado_precision ?? 'segundos'),
            resultado_conprisma: !!t?.resultado_conprisma,
        }));
        render();
    }

    function render() {
        if (!els.table) return;
        if (!state.types.length) {
            els.table.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">Sin tipos registrados</td></tr>';
        } else {
            els.table.innerHTML = state.types.map(t => {
                const count = Number(t.equipment_count || 0);
                const delDisabled = count>0 ? ' disabled title="No se puede eliminar: hay equipos asociados"' : '';
                return `
                <tr data-id="${t.id}">
                    <td><input class="form-control form-control-sm type-name" type="text" value="${escapeHtml(t.name)}"></td>
                    <td>
                        <div class="d-flex gap-2">
                            <div class="form-check">
                                <input class="form-check-input type-precision" type="radio" name="precision-${t.id}" id="prec-seg-${t.id}" value="segundos" ${t.resultado_precision==='segundos'?'checked':''}>
                                <label class="form-check-label" for="prec-seg-${t.id}">Segundos</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input type-precision" type="radio" name="precision-${t.id}" id="prec-lin-${t.id}" value="lineal" ${t.resultado_precision==='lineal'?'checked':''}>
                                <label class="form-check-label" for="prec-lin-${t.id}">Lineal</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input type-precision" type="radio" name="precision-${t.id}" id="prec-vh-${t.id}" value="vertical_horizontal" ${t.resultado_precision==='vertical_horizontal'?'checked':''}>
                                <label class="form-check-label" for="prec-vh-${t.id}">Vert./Horiz.</label>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="form-check form-switch d-inline-block">
                            <input class="form-check-input type-conprisma" type="checkbox" id="conprisma-${t.id}" ${t.resultado_conprisma ? 'checked' : ''}>
                        </div>
                    </td>
                    <td class="text-center"><span class="badge badge-glass">${count}</span></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-primary" data-action="save">Guardar</button>
                            <button type="button" class="btn btn-outline-danger" data-action="delete"${delDisabled}>Eliminar</button>
                        </div>
                    </td>
                </tr>`;
            }).join('');
        }
        if (els.meta) {
            const total = state.types.length;
            const inUse = state.types.filter(t=> (t.equipment_count||0)>0).length;
            const label = total===1?'tipo':'tipos';
            els.meta.textContent = `${total} ${label} registrados • ${inUse} en uso`;
        }
    }

    function escapeHtml(str) { return String(str ?? '').replace(/[&<>"']/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;' }[s])); }

    document.addEventListener('click', async (ev) => {
        // Crear tipo
        if (ev.target && ev.target === els.addBtn) {
            const name = els.name?.value?.trim() || '';
            if (!name) { setError('El nombre es obligatorio.'); return; }
            const rp = (function(){ const r=els.precisionRadios(); const c=r.find(x=>x.checked); return c?c.value:'segundos';})();
            const rcp = !!(els.conPrisma && els.conPrisma.checked);
            setError('');
            els.addBtn.disabled = true;
            try {
                await sendJson(api.create(), { method:'POST', body:{ name, resultado_precision: rp, resultado_conprisma: rcp } });
                if (els.name) els.name.value='';
                if (els.conPrisma) els.conPrisma.checked=false;
                els.precisionRadios().forEach(x=>{ if (x.value==='segundos') x.checked=true; });
                await fetchList();
            } catch (e) {
                setError(e.message || 'Error al crear el tipo.');
            } finally {
                els.addBtn.disabled = false;
            }
            return;
        }

        // Acciones por fila
        const actBtn = ev.target && ev.target.closest('[data-action]');
        if (actBtn) {
            const action = actBtn.getAttribute('data-action');
            const row = actBtn.closest('tr[data-id]');
            const id = row ? parseInt(row.getAttribute('data-id')||'0',10) : 0;
            if (!row || !id) return;

            if (action === 'save') {
                const nameEl = row.querySelector('.type-name');
                const name = nameEl && nameEl.value ? String(nameEl.value).trim() : '';
                if (!name) { setError('El nombre es obligatorio.'); return; }
                const rpEl = row.querySelector('input.type-precision:checked');
                const rp = rpEl ? rpEl.value : 'segundos';
                const rcpEl = row.querySelector('input.type-conprisma');
                const rcp = rcpEl ? !!rcpEl.checked : false;
                actBtn.setAttribute('disabled','true');
                try {
                    await sendJson(api.update(id), { method:'PUT', body:{ id, name, resultado_precision: rp, resultado_conprisma: rcp } });
                    setError('');
                    await fetchList();
                } catch (e) {
                    setError(e.message || 'Error al actualizar el tipo.');
                } finally {
                    actBtn.removeAttribute('disabled');
                }
                return;
            }

            if (action === 'delete') {
                if (actBtn.hasAttribute('disabled')) return;
                const ok = window.confirm('¿Eliminar este tipo? Esta acción no se puede deshacer.');
                if (!ok) return;
                actBtn.setAttribute('disabled','true');
                try {
                    await sendJson(api.remove(id), { method:'DELETE' });
                    setError('');
                    await fetchList();
                } catch (e) {
                    setError(e.message || 'Error al eliminar el tipo.');
                } finally {
                    actBtn.removeAttribute('disabled');
                }
                return;
            }
        }
    });

    // init
    fetchList().catch(() => {
        setError('No se pudieron cargar los tipos.');
        if (els.table) els.table.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-4">Error al cargar</td></tr>';
    });
})();
</script>
</body>
</html>
