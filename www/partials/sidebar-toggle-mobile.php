<!-- Botón para abrir sidebar en móviles -->
<button class="sidebar-mobile-toggle d-lg-none" 
        onclick="ElectrotecSidebar.getInstance().toggleSidebar()" 
        aria-label="Abrir menú de navegación" 
        title="Abrir menú">
    <svg class="hamburger-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
</button>

<!-- Overlay para cerrar sidebar en móvil -->
<div class="sidebar-overlay"></div>