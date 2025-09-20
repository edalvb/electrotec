<?php
// header.php — Encabezado reutilizable para páginas internas
// Variables esperadas (opcionales):
// - $pageTitle (string): título principal
// - $pageSubtitle (string): subtítulo
// - $headerActionsHtml (string): HTML a la derecha del header (botones, badges, etc.)

$pageTitle = isset($pageTitle) ? (string)$pageTitle : '';
$pageSubtitle = isset($pageSubtitle) ? (string)$pageSubtitle : '';
$headerActionsHtml = isset($headerActionsHtml) ? (string)$headerActionsHtml : '';
?>

<header class="main-header glass d-flex justify-content-between align-items-center p-3 mb-4 rounded-lg shadow">
  <div>
    <h2 class="mb-1"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h2>
    <p class="subtitle m-0"><?= htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8') ?></p>
  </div>
  <div class="d-flex align-items-center gap-2">
    <?= $headerActionsHtml ?>
  </div>
</header>