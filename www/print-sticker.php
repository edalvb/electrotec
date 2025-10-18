<?php
// Página simple para imprimir el QR/sticker a tamaño controlado.
require_once __DIR__ . '/bootstrap.php';

$id = (string)($_GET['id'] ?? '');
if ($id === '') {
    http_response_code(422);
    echo 'ID requerido';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Imprimir Sticker</title>
  <link href="assets/css/global.css" rel="stylesheet">
  <style>
    @media print {
      .no-print { display: none !important; }
      body { margin: 0; }
    }
    body { background: #f5f6f8; }
    .sheet {
      width: 100mm; /* ancho página para impresión */
      margin: 12mm auto;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 6px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.08);
      padding: 10mm 8mm;
    }
    .preview {
      display: flex; align-items: center; justify-content: center;
    }
    .preview img, .preview object { max-width: 100%; height: auto; }
    .toolbar { display:flex; justify-content: space-between; align-items:center; gap:12px; padding: 12px; }
  </style>
</head>
<body>
  <div class="toolbar no-print">
    <div>
      <a href="certificados.php" class="btn btn-secondary">Volver</a>
    </div>
    <div>
      <button class="btn btn-primary" onclick="window.print()">Imprimir</button>
    </div>
  </div>
  <div class="sheet">
    <div class="preview">
      <!-- Intentamos mostrar PNG si existe; el endpoint sirve SVG o PNG -->
      <object data="<?php echo 'api/certificates/sticker.php?id=' . urlencode($id); ?>" type="image/svg+xml" width="100%" height="auto">
        <img src="<?php echo 'api/certificates/sticker.php?id=' . urlencode($id); ?>" alt="Sticker" />
      </object>
    </div>
  </div>
  <script>
    // auto print si viene ?auto=1
    (function(){
      const params = new URLSearchParams(location.search);
      if (params.get('auto') === '1') {
        setTimeout(() => window.print(), 300);
      }
    })();
  </script>
</body>
</html>
