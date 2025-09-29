/**
 * Sidebar Enhancement Script
 * Electrotec Glass UI System
 *
 * Funcionalidades:
 * - Navegación mejorada con indicadores visuales
 * - Soporte responsive para móviles
 * - Animaciones suaves y feedback táctil
 * - Accesibilidad mejorada
 */

class ElectrotecSidebar {
  constructor() {
    this.sidebar = null;
    this.overlay = null;
    this.navItems = [];
    this.isMobile = window.innerWidth <= 768;
    this.isCollapsed = false;
    this.collapseBreakpoint = 1024;

    this.init();
    this.bindEvents();
  }

  init() {
    this.sidebar = document.querySelector(".sidebar");
    if (!this.sidebar) return;

    this.navItems = Array.from(this.sidebar.querySelectorAll(".nav-item"));

    // Agregar índices para animaciones escalonadas
    this.navItems.forEach((item, index) => {
      item.style.setProperty("--item-index", index);
    });

    // Crear overlay para móviles si no existe
    if (this.isMobile && !document.querySelector(".sidebar-overlay")) {
      this.createMobileOverlay();
    }

    // Mejorar la accesibilidad
    this.enhanceAccessibility();
  }

  createMobileOverlay() {
    this.overlay = document.createElement("div");
    this.overlay.className = "sidebar-overlay";
    this.overlay.setAttribute("aria-hidden", "true");
    document.body.appendChild(this.overlay);

    // Click en overlay cierra el sidebar
    this.overlay.addEventListener("click", () => this.closeSidebar());
  }

  enhanceAccessibility() {
    // Mejorar navegación por teclado
    this.navItems.forEach((item, index) => {
      item.setAttribute("tabindex", "0");

      item.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          item.click();
        } else if (e.key === "ArrowDown") {
          e.preventDefault();
          this.focusNextItem(index);
        } else if (e.key === "ArrowUp") {
          e.preventDefault();
          this.focusPrevItem(index);
        }
      });
    });
  }

  focusNextItem(currentIndex) {
    const nextIndex = (currentIndex + 1) % this.navItems.length;
    this.navItems[nextIndex].focus();
  }

  focusPrevItem(currentIndex) {
    const prevIndex =
      currentIndex === 0 ? this.navItems.length - 1 : currentIndex - 1;
    this.navItems[prevIndex].focus();
  }

  openSidebar() {
    if (!this.isMobile) return;

    this.sidebar.classList.add("show");
    if (this.overlay) {
      this.overlay.classList.add("show");
    }

    // Prevenir scroll del body
    document.body.style.overflow = "hidden";

    // Focus en el primer elemento de navegación
    if (this.navItems.length > 0) {
      setTimeout(() => this.navItems[0].focus(), 300);
    }
  }

  closeSidebar() {
    if (!this.isMobile) return;

    this.sidebar.classList.remove("show");
    if (this.overlay) {
      this.overlay.classList.remove("show");
    }

    // Restaurar scroll del body
    document.body.style.overflow = "";
  }

  toggleSidebar() {
    if (this.sidebar.classList.contains("show")) {
      this.closeSidebar();
    } else {
      this.openSidebar();
    }
  }

  // Añadir efectos de ripple en click
  addRippleEffect(item, e) {
    const ripple = document.createElement("span");
    const rect = item.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;

    ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
            z-index: 1;
        `;

    // Añadir keyframes si no existen
    if (!document.querySelector("#ripple-keyframes")) {
      const style = document.createElement("style");
      style.id = "ripple-keyframes";
      style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
      document.head.appendChild(style);
    }

    item.style.position = "relative";
    item.style.overflow = "hidden";
    item.appendChild(ripple);

    // Remover el ripple después de la animación
    setTimeout(() => {
      if (ripple.parentNode) {
        ripple.parentNode.removeChild(ripple);
      }
    }, 600);
  }

  toggleCollapse() {
    if (this.isMobile) return; // No colapsar en móvil
    
    this.isCollapsed = !this.isCollapsed;
    this.sidebar.setAttribute('data-collapsed', this.isCollapsed.toString());
    
    // Guardar preferencia en localStorage
    localStorage.setItem('sidebar-collapsed', this.isCollapsed.toString());
    
    // Dispatch evento personalizado para que otros componentes puedan reaccionar
    window.dispatchEvent(new CustomEvent('sidebarToggle', {
      detail: { collapsed: this.isCollapsed }
    }));
  }

  loadSavedState() {
    // Cargar estado guardado del localStorage
    const savedState = localStorage.getItem('sidebar-collapsed');
    if (savedState === 'true' && !this.isMobile) {
      this.isCollapsed = true;
      this.sidebar.setAttribute('data-collapsed', 'true');
    }
  }

  handleResize() {
    const wasMobile = this.isMobile;
    this.isMobile = window.innerWidth <= this.collapseBreakpoint;

    if (wasMobile !== this.isMobile) {
      if (this.isMobile) {
        // Cambiando a móvil: descolapsar y cerrar sidebar
        this.isCollapsed = false;
        this.sidebar.setAttribute('data-collapsed', 'false');
        this.closeSidebar();
        document.body.style.overflow = "";
        
        if (!this.overlay) {
          this.createMobileOverlay();
        }
      } else {
        // Cambiando a desktop: restaurar estado guardado
        this.loadSavedState();
        if (this.overlay) {
          this.overlay.remove();
          this.overlay = null;
        }
      }
    }
  }

  bindEvents() {
    // Cargar estado guardado al inicializar
    this.loadSavedState();

    // Eventos de navegación mejorados
    this.navItems.forEach((item) => {
      // Efecto ripple en click
      item.addEventListener("click", (e) => {
        this.addRippleEffect(item, e);

        // Cerrar sidebar en móvil después de navegar
        if (this.isMobile) {
          setTimeout(() => this.closeSidebar(), 150);
        }
      });

      // Mejoras de hover
      item.addEventListener("mouseenter", () => {
        if (!this.isMobile) {
          item.style.setProperty("--hover-scale", "1.02");
        }
      });

      item.addEventListener("mouseleave", () => {
        if (!this.isMobile) {
          item.style.removeProperty("--hover-scale");
        }
      });
    });

    // Eventos de redimensionado
    window.addEventListener("resize", () => {
      this.handleResize();
    });

    // Escape key cierra el sidebar en móvil
    document.addEventListener("keydown", (e) => {
      if (
        e.key === "Escape" &&
        this.isMobile &&
        this.sidebar.classList.contains("show")
      ) {
        this.closeSidebar();
      }
    });
  }

  // Método público para abrir/cerrar desde fuera
  static getInstance() {
    if (!window.electrotecSidebar) {
      window.electrotecSidebar = new ElectrotecSidebar();
    }
    return window.electrotecSidebar;
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  ElectrotecSidebar.getInstance();
});

// Exponer globalmente para uso externo
window.ElectrotecSidebar = ElectrotecSidebar;
