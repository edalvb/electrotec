<?php
// Partial de sidebar reutilizable con activación dinámica
// Uso: establecer $activePage = 'equipos' | 'dashboard' | 'clientes' | 'certificados' | 'gestion-usuarios' antes de incluir.
// Si no se establece, se infiere desde el nombre del script actual.

$activePage = $activePage ?? pathinfo(basename($_SERVER['PHP_SELF'] ?? ''), PATHINFO_FILENAME);

// Configuración de elementos de navegación con iconos
$navItems = [
    'dashboard' => [
        'url' => 'dashboard.php',
        'label' => 'Dashboard',
        'icon' => '<svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'
    ],
    'certificados' => [
        'url' => 'certificados.php',
        'label' => 'Certificados',
        'icon' => '<svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14,2 14,8 20,8"/><path d="m10 12-2 2 2 2"/><path d="m14 12 2 2-2 2"/></svg>'
    ],
    'equipos' => [
        'url' => 'equipos.php',
        'label' => 'Equipos',
        'icon' => '<svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>'
    ],
    'clientes' => [
        'url' => 'clientes.php',
        'label' => 'Clientes',
        'icon' => '<svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m22 21-3-3m0-10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/></svg>'
    ],
    'gestion-usuarios' => [
        'url' => 'gestion-usuarios.php',
        'label' => 'Gestión de Usuarios',
        'icon' => '<svg class="nav-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m22 11 2 2v3h-2m0-5-2-2v3h2"/></svg>'
    ]
];
?>

<aside class="sidebar glass rounded-lg shadow" role="navigation" aria-label="Navegación principal">
    <!-- Área de marca -->
    <header class="brand text-center">
        <div class="brand-logo-container mb-3">
            <img src="assets/images/logo.png" alt="Logo Electrotec" class="brand-logo">
        </div>
        <h1 class="brand-title">ELECTROTEC</h1>
        <p class="brand-subtitle text-muted">Sistema de certificados</p>
    </header>

    <!-- Navegación principal -->
    <nav class="nav mt-4" role="menu">
        <?php foreach ($navItems as $page => $item): ?>
            <a href="<?= htmlspecialchars($item['url']) ?>" 
               class="nav-item<?= $activePage === $page ? ' active' : '' ?>"
               role="menuitem"
               aria-current="<?= $activePage === $page ? 'page' : 'false' ?>"
               title="<?= htmlspecialchars($item['label']) ?>">
                <span class="nav-icon-wrapper">
                    <?= $item['icon'] ?>
                </span>
                <span class="nav-label"><?= htmlspecialchars($item['label']) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Indicador de estado (opcional) -->
    <footer class="sidebar-footer mt-auto pt-4">
        <div class="status-indicator glass-subtle rounded p-2 text-center">
            <small class="text-muted">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="l9 12 2 2 4-4"/>
                </svg>
                Sistema activo
            </small>
        </div>
    </footer>
</aside>
