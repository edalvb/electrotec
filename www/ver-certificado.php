<?php
require_once __DIR__ . '/bootstrap.php';

use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;

$id = (string)($_GET['id'] ?? '');
$error = '';
$cert = null;
if ($id === '') { $error = 'ID de certificado requerido.'; }
else {
    try {
        $pdo = (new PdoFactory(new Config()))->create();
  $stmt = $pdo->prepare('SELECT c.*, cl.nombre AS client_name FROM certificates c LEFT JOIN clients cl ON cl.id = c.client_id WHERE c.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $cert = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        if (!$cert) { $error = 'Certificado no encontrado.'; }
    } catch (Throwable $e) {
        $error = 'Error al cargar el certificado: '.$e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ver Certificado</title>
  <link href="assets/css/global.css" rel="stylesheet">
  <style>
    .container { max-width: 960px; margin: 90px auto 40px; padding: 0 16px; }
    .card { background: #fff; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); padding: 20px; }
    .actions { display: flex; gap: 12px; flex-wrap: wrap; }
  </style>
  <link rel="icon" type="image/x-icon" href="./assets/images/favicon.ico">
  <link rel="icon" type="image/png" sizes="32x32" href="./assets/images/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon-16x16.png">
  <nav class="navbar glass" style="position: fixed; top: 0; left: 0; right: 0; z-index: 100; padding: .6rem 0;">
    <div class="container" style="margin: 0 auto;">
      <div class="d-flex justify-content-between align-items-center">
        <div class="brand"><div class="brand-logo"><img src="assets/images/logo.png" alt="Logo" style="width:32px;height:32px;"></div><div><div class="brand-title">ELECTROTEC</div><div class="brand-subtitle">Certificados</div></div></div>
        <div class="d-flex align-items-center" style="gap: 1rem;">
          <a class="nav-link" href="index.php#contacto">Contacto</a>
          <a class="btn btn-primary btn-sm" href="login.php">Ingresar</a>
        </div>
      </div>
    </div>
  </nav>
</head>
<body>
  <div class="container">
    <h1 class="mb-3">Ver Certificado</h1>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($cert): ?>
      <div class="card">
        <h3 class="mb-2">Certificado N° <?php echo htmlspecialchars($cert['certificate_number'] ?? ''); ?></h3>
        <p class="text-muted">Cliente: <strong><?php echo htmlspecialchars($cert['client_name'] ?? ''); ?></strong></p>
        <div class="actions">
          <a class="btn btn-primary" href="<?php echo sprintf('api/certificates/pdf_fpdf.php?id=%s&action=view', urlencode($id)); ?>" target="_blank">Ver PDF</a>
          <a class="btn btn-secondary" href="<?php echo sprintf('api/certificates/pdf_fpdf.php?id=%s', urlencode($id)); ?>">Descargar PDF</a>
          <a class="btn btn-outline" href="<?php echo sprintf('api/certificates/sticker.php?id=%s', urlencode($id)); ?>" target="_blank">Ver Sticker</a>
        </div>
      </div>
    <?php endif; ?>
  </div>
  <script src="assets/js/app.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
