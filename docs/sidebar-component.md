# Sidebar Component - Electrotec Glass UI (Responsive + Collapsible)

## Descripción

Componente de sidebar mejorado que implementa el sistema de diseño "Electrotec Glass UI" con efectos de glassmorphism, navegación intuitiva, soporte responsive completo y funcionalidad de colapsar/expandir en pantallas grandes.

## Características Principales

### 🎨 Diseño Visual

- **Glassmorphism**: Efectos de vidrio esmerilado con `backdrop-filter: blur()`
- **Iconografía**: Iconos SVG inline optimizados para cada sección
- **Animaciones**: Transiciones suaves y animaciones de entrada escalonadas
- **Tipografía**: Uso de las fuentes del sistema (Montserrat + Inter)
- **Título ajustado**: El título "ELECTROTEC" ahora se ajusta correctamente sin desbordarse

### 🚀 Funcionalidades

- **Navegación dinámica**: Detección automática de la página activa
- **Estados visuales**: Hover, active y focus states mejorados
- **Responsive design**: Adaptación completa para móviles y tablets
- **Accesibilidad**: Soporte completo para navegación por teclado y lectores de pantalla
- **Colapsable**: En pantallas grandes (≥1024px) se puede minimizar para mostrar solo iconos
- **Persistencia**: Recuerda el estado colapsado/expandido usando localStorage

### 📱 Responsive Features

- **Desktop (≥1024px)**:
  - Sidebar fijo de 260px de ancho
  - Botón de toggle para colapsar a 80px (solo iconos)
  - Tooltips en estado colapsado
  - Estado persistente entre sesiones
- **Tablet (768px-1023px)**:
  - Sidebar más compacto (220px)
  - No se puede colapsar (siempre expandido)
  - Iconos redimensionados
- **Mobile (<768px)**:
  - Sidebar deslizable con overlay
  - Botón hamburguesa para abrir
  - Navegación táctil optimizada

## Estructura del Componente

### PHP Structure

```php
<?php
$activePage = $activePage ?? pathinfo(basename($_SERVER['PHP_SELF'] ?? ''), PATHINFO_FILENAME);

$navItems = [
    'dashboard' => ['url' => 'dashboard.php', 'label' => 'Dashboard', 'icon' => '...'],
    // ... más elementos
];
?>

<aside class="sidebar glass rounded-lg shadow" role="navigation">
    <header class="brand text-center">...</header>
    <nav class="nav mt-4" role="menu">...</nav>
    <footer class="sidebar-footer mt-auto pt-4">...</footer>
</aside>
```

### Clases CSS Principales

```css
.sidebar                    /* Container principal con glass effect */
/* Container principal con glass effect */
.brand                      /* Área de marca y logo */
.brand-logo                 /* Logo con efectos hover */
.brand-title               /* Título principal */
.brand-subtitle            /* Subtítulo descriptivo */
.nav-item                  /* Enlaces de navegación */
.nav-icon-wrapper          /* Container de iconos */
.nav-label                 /* Etiquetas de texto */
.sidebar-footer; /* Área inferior con estado */
```

## Implementación

### 1. Estructura HTML del Layout

```html
<div class="app-layout">
  <?php
  $activePage = 'dashboard'; // Opcional: definir página activa
  include 'partials/sidebar.php';
  ?>
  
  <main class="main-content">
    <!-- Contenido de la página -->
  </main>
</div>

<!-- Para móviles: incluir botón de toggle -->
<?php include 'partials/sidebar-toggle-mobile.php'; ?>
```

### 2. Incluir Estilos CSS

Los estilos están integrados en `assets/css/global.css` en la sección "SIDEBAR ENHANCEMENTS".

### 3. Incluir JavaScript (Requerido)

```html
<script src="assets/js/sidebar.js"></script>
```

### 4. Uso Básico

```php
<?php
// En cualquier página, establecer la página activa
$activePage = 'certificados'; // dashboard, certificados, equipos, clientes, gestion-usuarios
include 'partials/sidebar.php';
?>
```

### 5. Control Programático

```javascript
// Obtener instancia del sidebar
const sidebar = ElectrotecSidebar.getInstance();

// Toggle colapsar/expandir (solo desktop)
sidebar.toggleCollapse();

// Abrir/cerrar en móvil
sidebar.toggleSidebar();

// Escuchar cambios de estado
window.addEventListener('sidebarToggle', (e) => {
  console.log('Sidebar collapsed:', e.detail.collapsed);
});
```

## Personalización

### Añadir Nuevos Elementos de Navegación

```php
$navItems['nueva-seccion'] = [
    'url' => 'nueva-seccion.php',
    'label' => 'Nueva Sección',
    'icon' => '<svg>...</svg>' // Icono SVG
];
```

### Modificar Colores

Editar las variables CSS en `:root`:

```css
:root {
  --primary-blue: #2a2f6c;
  --secondary-blue: #5c66cc;
  --surface-glass: rgba(255, 255, 255, 0.25);
  /* ... */
}
```

### Ajustar Animaciones

```css
.nav-item {
  transition-duration: 0.3s; /* Cambiar velocidad */
  transform: translateX(8px); /* Cambiar distancia de hover */
}
```

## Accesibilidad

### Características Implementadas

- ✅ Navegación por teclado (Tab, Arrow keys, Enter, Space)
- ✅ Atributos ARIA apropiados (`role`, `aria-current`, `aria-label`)
- ✅ Estados de focus visibles
- ✅ Soporte para `prefers-reduced-motion`
- ✅ Texto alternativo para iconos

### Navegación por Teclado

- **Tab**: Navegar entre elementos
- **Arrow Up/Down**: Moverse entre enlaces de navegación
- **Enter/Space**: Activar enlace
- **Escape**: Cerrar sidebar en móvil

## Compatibilidad

### Navegadores Soportados

- ✅ Chrome 88+
- ✅ Firefox 94+
- ✅ Safari 14+
- ✅ Edge 88+

### Características CSS Modernas

- `backdrop-filter` (con fallback `-webkit-backdrop-filter`)
- CSS Grid y Flexbox
- CSS Custom Properties (variables)
- `clamp()` para responsive typography

## Optimización de Rendimiento

### Técnicas Aplicadas

- **Lazy Loading**: Animaciones solo cuando es necesario
- **Hardware Acceleration**: `transform` en lugar de `left/top`
- **Debounced Resize**: Eventos de redimensionado optimizados
- **CSS Containment**: Aislamiento de estilos para mejor rendimiento

## Mantenimiento

### Añadir Nuevos Iconos

1. Obtener SVG de [Heroicons](https://heroicons.com/) o [Feather Icons](https://feathericons.com/)
2. Optimizar con SVGO
3. Añadir al array `$navItems`
4. Verificar contraste y legibilidad

### Actualizar Estilos

1. Modificar variables CSS en lugar de valores hardcoded
2. Probar en todos los breakpoints responsive
3. Verificar accesibilidad con herramientas como axe-DevTools
4. Validar compatibilidad cross-browser

## Troubleshooting

### Problemas Comunes

#### Sidebar no se muestra correctamente

- Verificar que `global.css` esté incluido
- Confirmar que las variables CSS estén definidas
- Revisar la estructura HTML

#### Efectos glass no funcionan

- Verificar soporte de `backdrop-filter` en el navegador
- Confirmar que el fondo de la página no sea blanco sólido
- Revisar que los valores de `blur()` sean apropiados

#### Navegación móvil no funciona

- Incluir `sidebar.js`
- Verificar que no haya conflictos con otros scripts
- Revisar la consola para errores JavaScript

#### El sidebar no se colapsa en desktop

- Verificar que se esté usando el botón de toggle correcto
- Confirmar que no se esté en modo móvil/tablet
- Revisar la consola para errores JavaScript

## Futuras Mejoras

### Roadmap

- [ ] Soporte para temas dinámicos (claro/oscuro)
- [ ] Sidebar colapsible en desktop
- [ ] Indicadores de notificación en elementos de navegación
- [ ] Búsqueda rápida dentro del sidebar
- [ ] Personalización de usuario (reordenar elementos)

---

**Versión**: 1.0  
**Última actualización**: Septiembre 2025  
**Sistema de Diseño**: Electrotec Glass UI
