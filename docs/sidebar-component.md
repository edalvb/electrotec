# Sidebar Component - Electrotec Glass UI

## Descripción

Componente de sidebar mejorado que implementa el sistema de diseño "Electrotec Glass UI" con efectos de glassmorphism, navegación intuitiva y soporte responsive completo.

## Características Principales

### 🎨 Diseño Visual

- **Glassmorphism**: Efectos de vidrio esmerilado con `backdrop-filter: blur()`
- **Iconografía**: Iconos SVG inline optimizados para cada sección
- **Animaciones**: Transiciones suaves y animaciones de entrada escalonadas
- **Tipografía**: Uso de las fuentes del sistema (Montserrat + Inter)

### 🚀 Funcionalidades

- **Navegación dinámica**: Detección automática de la página activa
- **Estados visuales**: Hover, active y focus states mejorados
- **Responsive design**: Adaptación completa para móviles y tablets
- **Accesibilidad**: Soporte completo para navegación por teclado y lectores de pantalla

### 📱 Responsive Features

- **Desktop**: Sidebar fijo con efectos de hover mejorados
- **Tablet**: Sidebar más compacto con iconos redimensionados
- **Mobile**: Sidebar deslizable con overlay y navegación táctil

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

### 1. Incluir el Componente

```php
<?php
$activePage = 'dashboard'; // Opcional: definir página activa
include 'partials/sidebar.php';
?>
```

### 2. Incluir Estilos CSS

Los estilos están integrados en `assets/css/global.css` en la sección "SIDEBAR ENHANCEMENTS".

### 3. Incluir JavaScript (Opcional)

```html
<script src="assets/js/sidebar.js"></script>
```

### 4. Para Móviles - Botón Toggle (Opcional)

```html
<button
  class="sidebar-toggle d-md-none"
  onclick="ElectrotecSidebar.getInstance().toggleSidebar()"
>
  <svg
    width="24"
    height="24"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
  >
    <line x1="3" y1="6" x2="21" y2="6"></line>
    <line x1="3" y1="12" x2="21" y2="12"></line>
    <line x1="3" y1="18" x2="21" y2="18"></line>
  </svg>
</button>
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

**Sidebar no se muestra correctamente**

- Verificar que `global.css` esté incluido
- Confirmar que las variables CSS estén definidas
- Revisar la estructura HTML

**Efectos glass no funcionan**

- Verificar soporte de `backdrop-filter` en el navegador
- Confirmar que el fondo de la página no sea blanco sólido
- Revisar que los valores de `blur()` sean apropiados

**Navegación móvil no funciona**

- Incluir `sidebar.js`
- Verificar que no haya conflictos con otros scripts
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
