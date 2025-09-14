### **Sistema de Diseño: "Electrotec Glass UI"**

#### **Principio Fundamental: El Fondo**

El Glassmorphism no funciona sobre un fondo blanco simple. Necesita un fondo colorido y con formas para que el efecto de desenfoque (blur) sea visible y atractivo.

*   **Fondo de la Aplicación:** Se utilizará un gradiente radial o lineal suave que combine los colores de la marca, o una imagen de fondo abstracta y sutil (como un render 3D de circuitos o formas geométricas).
    *   **Ejemplo de Gradiente (CSS):** `background: radial-gradient(circle, #5C66CC, #2A2F6C);`

---

#### **1. Paleta de Colores (Adaptada para Transparencia)**

La clave es usar colores con transparencia (`rgba`) para los fondos de los elementos "de vidrio".

| Rol en UI           | Nombre         | RGBA / HEX                | Descripción                                                                          |
| :------------------ | :------------- | :------------------------ | :----------------------------------------------------------------------------------- |
| **Primario Sólido** | `primary-blue` | `#2A2F6C`                 | Para botones primarios, texto y acentos que necesitan un alto contraste. **NO es transparente.** |
| **Secundario**      | `secondary-blue`| `#5C66CC`                 | Para estados hover y acentos secundarios.                                            |
| **Superficie "Glass"** | `surface-glass`| `rgba(255, 255, 255, 0.25)` | **El color base para todos los paneles de vidrio.** Blanco con 25% de opacidad.          |
| **Borde "Glass"**     | `border-glass` | `rgba(255, 255, 255, 0.40)` | Borde blanco sutil para los paneles, que ayuda a "atrapar la luz".                 |
| **Texto Principal**   | `text-primary` | `#FFFFFF`                 | Blanco puro. Será el color de texto más común sobre los fondos oscuros/de vidrio.      |
| **Texto Secundario**  | `text-muted`   | `rgba(255, 255, 255, 0.70)` | Blanco con 70% de opacidad para etiquetas y texto menos importante.                  |

**Colores Semánticos (Para Íconos y Texto de Notificación)**

| Rol en UI  | Nombre    | HEX       | Uso                                                                    |
| :--------- | :-------- | :-------- | :--------------------------------------------------------------------- |
| **Éxito**    | `success` | `#10B981` | Verde vibrante para íconos de éxito y texto del estado "Vigente".      |
| **Alerta**   | `warning` | `#F59E0B` | Naranja para alertas y "Próximo a Vencer".                             |
| **Error**    | `error`   | `#EF4444` | Rojo para errores y "Vencido".                                         |

---

#### **2. Tipografía**

La legibilidad es aún más crucial en Glassmorphism. Mantenemos las fuentes pero añadimos un toque de sombra para mejorar el contraste sobre fondos potencialmente complejos.

*   **Fuente para Encabezados (Headings):** **Montserrat** (Bold/SemiBold)
    *   **Sombra de Texto:** `text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);` para que se separe ligeramente del fondo.
*   **Fuente para Cuerpo de Texto (Body):** **Inter** (Regular/Medium)
*   **Color:** `text-primary` (Blanco) por defecto.

---

#### **3. Estilo de Superficie "Glass"**

Esta es la sección más importante. Define cómo se ven los paneles de vidrio. Reemplaza las reglas de bordes y sombras anteriores.

*   **Desenfoque de Fondo (Backdrop Blur):**
    *   **CSS:** `backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);`
    *   Este es el efecto de "vidrio esmerilado". El valor `12px` puede ajustarse.
*   **Fondo Translúcido:**
    *   **CSS:** `background: var(--surface-glass);` // `rgba(255, 255, 255, 0.25)`
*   **Borde Sutil:**
    *   **CSS:** `border: 1px solid var(--border-glass);` // `rgba(255, 255, 255, 0.40)`
*   **Sombra Suave:**
    *   **CSS:** `box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);`
    *   Una sombra más difusa y coloreada (usando un azul oscuro) que da la sensación de que el panel flota sobre el fondo.
*   **Radio del Borde:**
    *   `rounded-lg` (16px) será el estándar para los paneles, para dar una apariencia suave y orgánica.

---

#### **4. Iconografía**

*   **Librería:** Heroicons o Feather Icons (estilo `Outline`).
*   **Color por Defecto:** `text-muted` (`rgba(255, 255, 255, 0.70)`).
*   **Color Activo/Hover:** `text-primary` (`#FFFFFF`).

---

### **Aplicación a Componentes de UI (Ejemplos Concretos)**

#### **Cards y Modales**

*   **Estilo:** Aplicarán el **Estilo de Superficie "Glass"** completo. Serán los elementos visuales más impactantes de la aplicación.
*   **Contenido:** El texto y los iconos dentro usarán los colores `text-primary` y `text-muted`.

#### **Botones**

1.  **Primario (Acción Principal):**
    *   **Estilo:** **Sólido y Opaco.** Esto es crucial para la accesibilidad y para que el CTA principal no se pierda.
    *   Fondo: `primary-blue`
    *   Texto: `text-primary` (Blanco)
    *   Sombra: Una sombra sutil para darle elevación.

2.  **Secundario (Acción Alternativa):**
    *   **Estilo:** **Glassmorphism.** Este sí será un panel de vidrio.
    *   Aplicará el `Estilo de Superficie "Glass"` pero con un `blur` menor (ej: `8px`) y menos `padding`.
    *   Texto: `text-primary` (Blanco)
    *   Hover: El fondo se vuelve ligeramente más opaco (ej: `rgba(255, 255, 255, 0.35)`).

#### **Campos de Formulario (Inputs)**

*   **Estilo:** Una versión más sutil del efecto para mantener la usabilidad.
*   Fondo: `rgba(0, 0, 0, 0.2)` (un fondo oscuro semitransparente para que el texto blanco tenga contraste).
*   Borde: 1px sólido `border-glass`.
*   Texto: `text-primary` (Blanco).
*   **Estado Focus:** El borde se ilumina con `secondary-blue`.
