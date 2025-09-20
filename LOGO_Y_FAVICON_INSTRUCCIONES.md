# ELECTROTEC - Especificaciones de Logo y Favicon

## 📍 Ubicaciones donde debes añadir los archivos

### 1. **Favicon (favicon.ico)**

- **Ubicación:** `c:\Users\edalv\programming\github\electrotec\electrotec02\www\favicon.ico`
- **Formato:** `.ico` (recomendado) o `.png`
- **Tamaños recomendados:** 16x16px, 32x32px, 48x48px
- **Descripción:** Se mostrará en la pestaña del navegador y en marcadores

### 2. **Logo Principal**

- **Ubicación:** `c:\Users\edalv\programming\github\electrotec\electrotec02\www\logo.svg`
- **Formato:** `.svg` (preferido) o `.png` alta resolución
- **Tamaño:** Escalable (SVG) o mínimo 200x200px (PNG)
- **Uso:** Logo principal en navbar y footer

### 3. **Favicon adicionales (opcional pero recomendado)**

```
c:\Users\edalv\programming\github\electrotec\electrotec02\www\favicon-16x16.png
c:\Users\edalv\programming\github\electrotec\electrotec02\www\favicon-32x32.png
```

## 🎨 Especificaciones de Diseño

### Logo Principal

- **Colores:** Usar la paleta del sistema de diseño
  - Primario: `#2A2F6C`
  - Secundario: `#5C66CC`
  - Texto: `#FFFFFF`
- **Estilo:** Moderno, limpio, relacionado con electricidad/tecnología
- **Elementos sugeridos:**
  - Rayo estilizado
  - Circuito eléctrico
  - Escudo o certificación
  - Letras "E" o "ET" estilizadas

### Favicon

- **Diseño:** Versión simplificada del logo principal
- **Colores:** Contrastar bien sobre fondos claros y oscuros
- **Elementos:** Símbolo reconocible de ELECTROTEC

## 🔧 Cómo Implementar

### Paso 1: Crear los archivos gráficos

1. Diseña el logo principal en formato SVG o PNG de alta resolución
2. Crea el favicon en formato ICO (puedes usar herramientas online como favicon.io)
3. Opcionalmente crea las versiones PNG del favicon

### Paso 2: Colocar los archivos en las ubicaciones especificadas

```
www/assets/images/
├── favicon.ico              ← AQUÍ el favicon principal
├── favicon-16x16.png        ← AQUÍ el favicon 16x16 (opcional)
├── favicon-32x32.png        ← AQUÍ el favicon 32x32 (opcional)
└── logo.svg                 ← AQUÍ el logo principal
```

### Paso 3: Actualizar el código

Una vez que tengas los archivos, deberás actualizar estos elementos en el código:

#### En `landing.php` (líneas 14-16):

```html
<!-- Cambiar esto: -->
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png" />
<link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png" />
```

#### En `landing.php` (líneas 33-38):

```html
<!-- Cambiar esto: -->
<div class="brand-logo">
  <!-- <img src="logo.svg" alt="ELECTROTEC Logo" style="width: 40px; height: 40px;"> -->
  <span style="font-size: 1.5rem; font-weight: 800;">E</span>
</div>
```

**Reemplazar por:**

```html
<div class="brand-logo">
  <img
    src="logo.svg"
    alt="ELECTROTEC Logo"
    style="width: 40px; height: 40px;"
  />
</div>
```

#### En `landing.php` (líneas 502-505):

```html
<!-- Cambiar esto también en el footer: -->
<div class="brand-logo">
  <!-- <img src="logo.svg" alt="ELECTROTEC Logo" style="width: 40px; height: 40px;"> -->
  <span style="font-size: 1.5rem; font-weight: 800;">E</span>
</div>
```

### Paso 4: Añadir favicon a otras páginas

Agrega estas líneas en el `<head>` de todas las demás páginas PHP:

```html
<link rel="icon" type="image/x-icon" href="favicon.ico" />
<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png" />
<link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png" />
```

## 📝 Archivos que necesitan favicon:

- `index.php`
- `login.php`
- `dashboard.php`
- `certificados.php`
- `clientes.php`
- `equipos.php`
- `gestion-usuarios.php`

## 🎯 Sugerencias de Diseño

### Para el Logo:

- **Concepto 1:** Letra "E" estilizada con un rayo atravesándola
- **Concepto 2:** Escudo con circuitos eléctricos internos
- **Concepto 3:** Ícono de certificado con elementos eléctricos
- **Concepto 4:** Combinación de rayo + engranaje (tecnología + electricidad)

### Herramientas Recomendadas:

- **Para crear SVG:** Adobe Illustrator, Inkscape (gratuito), Figma
- **Para crear Favicon:** favicon.io, realfavicongenerator.net
- **Para optimizar:** SVGOMG (para SVG), TinyPNG (para PNG)

## ✅ Lista de Verificación

- [ ] Logo principal creado y colocado en `www/assets/images/logo.svg`
- [ ] Favicon principal creado y colocado en `www/assets/images/favicon.ico`
- [ ] Favicons adicionales creados (opcional)
- [ ] Logo implementado en `landing.php` (navbar y footer)
- [ ] Favicon implementado en todas las páginas PHP
- [ ] Verificar que los archivos cargan correctamente en el navegador

Una vez que tengas los archivos gráficos listos, simplemente colócalos en las ubicaciones especificadas y actualiza el código como se indica arriba.
