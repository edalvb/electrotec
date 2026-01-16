<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ELECTROTEC | Técnicos</title>
  <link href="assets/css/global.css" rel="stylesheet">
  <script src="assets/js/auth.js"></script>
</head>
<body>
  <div class="d-flex">
    <?php $activePage = 'tecnicos'; include __DIR__ . '/partials/sidebar.php'; ?>
    <div class="main-content flex-grow-1 p-4">
      <?php 
      $pageTitle = 'Mantenedor de Técnicos';
      $pageSubtitle = 'Crea, edita y elimina técnicos';
      $headerActionsHtml = '<button class="btn btn-primary" id="btnOpenNew">Nuevo técnico</button>';
      include __DIR__ . '/partials/header.php';
      ?>

      <div class="card glass p-4 rounded-lg">
        <div class="table-responsive">
          <table class="table table-striped" id="tblTecnicos">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nombre completo</th>
                <th>Cargo</th>
                <th>Firma</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody><tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr></tbody>
          </table>
        </div>
      </div>

      <?php include __DIR__ . '/partials/footer.php'; ?>
    </div>
  </div>

  <div class="modal fade" id="techModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="techModalTitle">Nuevo técnico</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="techModalError" class="alert alert-danger d-none"></div>
          <div class="mb-3">
            <label class="form-label">Nombre completo *</label>
            <input type="text" class="form-control" id="inpNombre">
          </div>
          <div class="mb-3">
            <label class="form-label">Cargo</label>
            <input type="text" class="form-control" id="inpCargo">
          </div>
          <div class="mb-3">
            <label class="form-label">Firma (imagen)</label>
            <input type="file" class="form-control" id="inpFirmaFile" accept="image/*">
            <small class="text-muted">Se almacenará como Base64 en la base de datos.</small>
            <div class="mt-2">
              <img id="imgPreview" src="" alt="Vista previa" style="max-height:100px; display:none; border:1px solid #ddd; padding:4px; border-radius:6px; background:#fff;"/>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" id="btnSaveTech">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="deleteTechModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Eliminar técnico</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="deleteTechError" class="alert alert-danger d-none"></div>
          <p>¿Seguro que deseas eliminar al técnico <strong id="delTechName"></strong>?</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-danger" id="btnConfirmDeleteTech">Eliminar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/uppercase-inputs.js"></script>
  <script>
  (function(){
    // Auth admin
    try { Auth.requireAuth('admin'); } catch(e) { return; }

    const API = {
      list: 'api/technicians.php?action=list&limit=200&offset=0',
      create: 'api/technicians.php?action=create',
      update: id => `api/technicians.php?action=update&id=${id}`,
      delete: id => `api/technicians.php?action=delete&id=${id}`,
    };

    const tbody = document.querySelector('#tblTecnicos tbody');
    const btnOpenNew = document.getElementById('btnOpenNew');
    const techModalEl = document.getElementById('techModal');
    const techModalErr = document.getElementById('techModalError');
    const techModalTitle = document.getElementById('techModalTitle');
    const inpNombre = document.getElementById('inpNombre');
    const inpCargo = document.getElementById('inpCargo');
  const inpFirmaFile = document.getElementById('inpFirmaFile');
  const imgPreview = document.getElementById('imgPreview');
    const btnSaveTech = document.getElementById('btnSaveTech');
    const delModalEl = document.getElementById('deleteTechModal');
    const delErr = document.getElementById('deleteTechError');
    const delName = document.getElementById('delTechName');
    const btnConfirmDeleteTech = document.getElementById('btnConfirmDeleteTech');

    let editing = null; // {id, nombre_completo, cargo, path_firma}
    let deleting = null;

    function setErr(el, msg){ if(!el) return; if(!msg){el.classList.add('d-none'); el.textContent='';} else {el.classList.remove('d-none'); el.textContent = msg;} }

    function render(rows){
      if(!rows || rows.length===0){ tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Sin técnicos</td></tr>'; return; }
      tbody.innerHTML = rows.map(r => `
        <tr>
          <td>${r.id}</td>
          <td>${r.nombre_completo}</td>
          <td>${r.cargo ?? ''}</td>
          <td>${r.firma_base64 ? `<img src="${r.firma_base64}" alt="Firma" style="height:80px;border:1px solid #ddd;border-radius:4px;background:#fff;padding:2px;"/>` : (r.path_firma ? `<code>${r.path_firma}</code>` : '')}</td>
          <td>
            <button class="btn btn-sm btn-secondary btn-edit" data-id="${r.id}">Editar</button>
            <button class="btn btn-sm btn-danger btn-del" data-id="${r.id}" data-name="${r.nombre_completo}">Eliminar</button>
          </td>
        </tr>`).join('');
      attach();
    }

    function attach(){
      tbody.querySelectorAll('.btn-edit').forEach(btn => btn.addEventListener('click', e => {
        const id = Number(e.currentTarget.getAttribute('data-id'));
        const tr = e.currentTarget.closest('tr');
        const cells = tr.querySelectorAll('td');
        editing = { id, nombre_completo: cells[1].textContent, cargo: cells[2].textContent || null };
        techModalTitle.textContent = 'Editar técnico';
        inpNombre.value = editing.nombre_completo || '';
        inpCargo.value = editing.cargo || '';
        if (inpFirmaFile) inpFirmaFile.value = '';
        if (imgPreview) { imgPreview.style.display='none'; imgPreview.src=''; }
        setErr(techModalErr, '');
        bootstrap.Modal.getOrCreateInstance(techModalEl).show();
      }));

      tbody.querySelectorAll('.btn-del').forEach(btn => btn.addEventListener('click', e => {
        const id = Number(e.currentTarget.getAttribute('data-id'));
        const name = e.currentTarget.getAttribute('data-name');
        deleting = { id, name };
        delName.textContent = name || '';
        setErr(delErr, '');
        bootstrap.Modal.getOrCreateInstance(delModalEl).show();
      }));
    }

    async function load(){
      tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>';
      try{
        const json = await Auth.fetchWithAuth(API.list);
        if(!json.ok) throw new Error(json.message || 'Error al cargar');
        render(json.data);
      }catch(err){
        tbody.innerHTML = `<tr><td colspan="5" class="text-danger text-center">${err.message}</td></tr>`;
      }
    }

    btnOpenNew.addEventListener('click', () => {
      editing = null;
      techModalTitle.textContent = 'Nuevo técnico';
      inpNombre.value = ''; inpCargo.value=''; if (inpFirmaFile) inpFirmaFile.value=''; if (imgPreview){ imgPreview.style.display='none'; imgPreview.src=''; }
      setErr(techModalErr, '');
      bootstrap.Modal.getOrCreateInstance(techModalEl).show();
    });

    async function fileToDataUrl(file){
      return new Promise((resolve,reject)=>{
        const rdr = new FileReader();
        rdr.onload = () => resolve(rdr.result);
        rdr.onerror = reject;
        rdr.readAsDataURL(file);
      });
    }

    if (inpFirmaFile) {
      inpFirmaFile.addEventListener('change', async (e) => {
        const f = e.target.files && e.target.files[0];
        if (!f) { if (imgPreview){ imgPreview.style.display='none'; imgPreview.src=''; } return; }
        try{
          const dataUrl = await fileToDataUrl(f);
          if (imgPreview) { imgPreview.src = dataUrl; imgPreview.style.display='inline-block'; }
        }catch(err){ /* ignore */ }
      });
    }

    btnSaveTech.addEventListener('click', async () => {
      setErr(techModalErr, '');
      const nombre = inpNombre.value.trim();
      if(!nombre){ setErr(techModalErr, 'El nombre completo es obligatorio'); return; }
      const payload = { nombre_completo: nombre, cargo: inpCargo.value.trim()||null };
      const f = inpFirmaFile && inpFirmaFile.files && inpFirmaFile.files[0];
      if (f) {
        try {
          const dataUrl = await fileToDataUrl(f);
          payload.firma_base64 = dataUrl; // solo enviar si el usuario seleccionó archivo
        } catch (e) { /* ignore */ }
      } else {
        // Nota: si no se selecciona archivo y estamos editando, NO enviar firma_base64 para no sobrescribir.
        // En creación, omitir también está bien (creará sin firma).
      }
      try{
        if(editing){
          await Auth.fetchWithAuth(API.update(editing.id), { method: 'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
        } else {
          await Auth.fetchWithAuth(API.create, { method: 'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
        }
        bootstrap.Modal.getInstance(techModalEl).hide();
        load();
      }catch(err){ setErr(techModalErr, err.message || 'Error al guardar'); }
    });

    btnConfirmDeleteTech.addEventListener('click', async () => {
      if(!deleting) return;
      setErr(delErr, '');
      try{
        await Auth.fetchWithAuth(API.delete(deleting.id), { method: 'DELETE' });
        bootstrap.Modal.getInstance(delModalEl).hide();
        load();
      }catch(err){ setErr(delErr, err.message || 'Error al eliminar'); }
    });

    load();
  })();
  </script>
</body>
</html>
