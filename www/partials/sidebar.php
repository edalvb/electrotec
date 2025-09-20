<?php
// Partial de sidebar reutilizable con activación dinámica
// Uso: establecer $activePage = 'equipos' | 'dashboard' | 'clientes' | 'certificados' | 'gestion-usuarios' antes de incluir.
// Si no se establece, se infiere desde el nombre del script actual.

$activePage = $activePage ?? pathinfo(basename($_SERVER['PHP_SELF'] ?? ''), PATHINFO_FILENAME);
?>

<aside class="sidebar text-center glass rounded-lg shadow">
    <div class="brand my-4">
        <img src="assets/images/logo.png" alt="Electrotec" class="brand-logo mb-2">
        <div class="brand-title">ELECTROTEC</div>
        <div class="brand-subtitle text-muted">Sistema de certificados</div>
    </div>
    <nav class="list-group list-group-flush">
        <a href="dashboard.php" class="list-group-item list-group-item-action<?= $activePage === 'dashboard' ? ' active' : '' ?>">Dashboard</a>
        <a href="certificados.php" class="list-group-item list-group-item-action<?= $activePage === 'certificados' ? ' active' : '' ?>">Certificados</a>
        <a href="equipos.php" class="list-group-item list-group-item-action<?= $activePage === 'equipos' ? ' active' : '' ?>">Equipos</a>
        <a href="clientes.php" class="list-group-item list-group-item-action<?= $activePage === 'clientes' ? ' active' : '' ?>">Clientes</a>
        <a href="gestion-usuarios.php" class="list-group-item list-group-item-action<?= $activePage === 'gestion-usuarios' ? ' active' : '' ?>">Gestión de Usuarios</a>
    </nav>
</aside>
