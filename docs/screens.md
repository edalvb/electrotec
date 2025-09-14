¡Absolutamente! Aquí tienes una estructura de pantallas extremadamente detallada, pensada desde la perspectiva de la experiencia de usuario (UX) y la interfaz de usuario (UI), y alineada con la arquitectura de Next.js y Supabase que hemos definido.

---

### **Área Pública (Sin Autenticación)**

#### **Pantalla 1.1: Vista Pública del Certificado**

*   **URL:** `/certificado/[id]` (Ej: `/certificado/a1b2c3d4-e5f6...`)
*   **Propósito:** Proveer una verificación instantánea, profesional y móvil-amigable de un certificado al escanear el código QR. Es la cara pública de la validez del trabajo de ELECTROTEC.
*   **Acceso:** Público.
*   **Componentes Detallados:**
    *   **Encabezado:** Logo de ELECTROTEC CONSULTING S.A.C., grande y claro.
    *   **Título Principal:** `Certificado de Calibración N° [certificate_number]`.
    *   **Panel de Estado (El elemento más importante):**
        *   Un recuadro prominente con un fondo de color y un ícono.
        *   **Lógica de Estado:**
            *   **Verde (`VIGENTE`):** Si `next_calibration_date` es una fecha futura. Ícono: ✅.
            *   **Naranja (`PRÓXIMO A VENCER`):** Si `next_calibration_date` está dentro de los próximos 30 días. Ícono: ⚠️.
            *   **Rojo (`VENCIDO`):** Si `next_calibration_date` es una fecha pasada. Ícono: ❌.
    *   **Sección "Equipo Calibrado":**
        *   **Tipo de Equipo:** (Ej: "Estación Total") - Obtenido de `equipment_types.name`.
        *   **Identificación:** `[equipment.brand] [equipment.model]` (Ej: "LEICA TS-06 ULTRA 2"").
        *   **Número de Serie:** `[equipment.serial_number]`.
    *   **Sección "Validez":**
        *   **Fecha de Calibración:** `[calibration_date]` formateada (Ej: "20 de Agosto de 2025").
        *   **Fecha de Próxima Calibración:** `[next_calibration_date]` formateada (Ej: "20 de Febrero de 2026").
    *   **Sección "Propietario":**
        *   **Otorgado a:** `[client.name]`.
    *   **Botón de Acción Principal (Call to Action):**
        *   Un botón grande que ocupa todo el ancho, con un ícono de PDF.
        *   **Texto:** `VER CERTIFICADO COMPLETO (PDF)`.
        *   **Acción:** Al hacer clic, abre el `pdf_url` (almacenado en Supabase Storage) en una nueva pestaña del navegador.
    *   **Pie de Página:** "Certificado emitido por ELECTROTEC CONSULTING S.A.C." con un enlace al sitio web principal.

---

### **Área Privada (Requiere Autenticación)**

#### **Pantalla 2.0: Layout Principal de la Aplicación (Shell)**

*   **Propósito:** Proveer una estructura y navegación consistentes a través de toda el área privada.
*   **Componentes Persistentes:**
    *   **Barra de Navegación Lateral (Sidebar):**
        *   Logo de ELECTROTEC en la parte superior.
        *   Enlaces de navegación con íconos:
            *   `Dashboard` (Inicio)
            *   `Certificados`
            *   `Equipos`
            *   `Clientes`
            *   **`Gestión de Usuarios` (Visible solo si `user_profiles.role === 'ADMIN'`)**
    *   **Barra Superior (Header):**
        *   Título de la página actual (Ej: "Dashboard" o "Lista de Certificados").
        *   A la derecha, un **Menú de Usuario**:
            *   Muestra el nombre: "Hola, `[user_profiles.full_name]`".
            *   Al hacer clic, se abre un dropdown con opciones: "Mi Perfil" y "Cerrar Sesión".

---

#### **Pantallas de Autenticación y Onboarding**

#### **Pantalla 2.1: Inicio de Sesión**

*   **URL:** `/login`
*   **Acceso:** Solo para usuarios no autenticados. Si un usuario logueado intenta acceder, se le redirige a `/`.
*   **Componentes:** Un formulario centrado en una página limpia con el logo.
    *   Campo de texto: `Correo Electrónico` (con validación de formato).
    *   Campo de texto: `Contraseña` (tipo password).
    *   Checkbox: `Recordarme`.
    *   Botón de envío: `Ingresar`.
    *   Mensajes de error claros (Ej: "Correo o contraseña incorrectos").
    *   Enlace: `¿Olvidaste tu contraseña?`.

#### **Pantalla 2.2: Aceptar Invitación y Establecer Contraseña**

*   **URL:** `/aceptar-invitacion`
*   **Acceso:** Solo para usuarios con un token de invitación válido en la URL (proporcionado por Supabase en el correo).
*   **Componentes:** Un formulario simple y directo.
    *   Título: "Bienvenido a ELECTROTEC. Crea tu contraseña para activar tu cuenta."
    *   Campo de texto: `Nueva Contraseña` (con indicador de fortaleza).
    *   Campo de texto: `Confirmar Nueva Contraseña` (con validación de coincidencia).
    *   Botón: `Activar Cuenta y Acceder`.
    *   **Flujo:** Al enviar, actualiza la contraseña del usuario en Supabase Auth y lo redirige automáticamente al Dashboard (`/`).

---

#### **Pantallas del Módulo Principal**

#### **Pantalla 2.3: Dashboard (Página de Inicio)**

*   **URL:** `/`
*   **Acceso:** `ADMIN`, `TECHNICIAN`.
*   **Componentes:**
    *   **Encabezado de Bienvenida:** `¡Buen día, [user_profiles.full_name]!`
    *   **Tarjetas de Acción Rápida (KPIs / Stats):**
        *   **"Certificados Emitidos este Mes":** Un número grande.
        *   **"Próximas Calibraciones (30 días)":** Un número que al hacer clic lleva a la lista de certificados pre-filtrada.
    *   **Botones de Acción Principales:**
        *   Botón grande y verde: `[+] Generar Nuevo Certificado`.
    *   **Tabla de Actividad Reciente:**
        *   Título: "Últimos Certificados Generados".
        *   Muestra los 5 certificados más recientes creados en el sistema.
        *   Columnas: N°, Equipo, Cliente, Fecha.

---

#### **Pantallas del Módulo de Certificados (CRUD)**

#### **Pantalla 2.4: Lista de Certificados**

*   **URL:** `/certificados`
*   **Acceso:** `ADMIN` (ve todo), `TECHNICIAN` (solo ve los que él emitió, implementado con RLS).
*   **Componentes:**
    *   **Encabezado:** Título "Certificados" y botón `[+] Nuevo Certificado`.
    *   **Barra de Filtros y Búsqueda Avanzada:**
        *   Campo de búsqueda principal: `Buscar por N° de Serie, N° de Certificado o Cliente...`
        *   Filtro por `Rango de Fechas`.
        *   Filtro por `Tipo de Equipo` (Dropdown).
        *   **(Solo ADMIN):** Filtro por `Técnico` (Dropdown).
    *   **Tabla de Datos Paginada:**
        *   **Columnas:** `N° Cert.`, `Equipo (Marca y Modelo)`, `N° Serie`, `Cliente`, `Fecha Calib.`, `Próxima Calib.`, `Técnico`, `Estado` (con la misma pastilla de color que la vista pública), `Acciones`.
        *   **Columna "Acciones":** Íconos para `Ver PDF`, `Ver Detalles`, `Generar QR`.

#### **Pantalla 2.5: Formulario de Creación/Edición de Certificado (Wizard)**

*   **URL:** `/certificados/nuevo` o `/certificados/[id]/editar`
*   **Acceso:** `ADMIN`, `TECHNICIAN`.
*   **Componentes (Asistente en 3 Pasos):**
    *   **Paso 1: Equipo y Cliente**
        *   Campo de texto auto-completado: `Buscar equipo por Número de Serie`.
        *   **Interacción:** Al escribir, busca en `equipment`. Al seleccionar un equipo, los campos de marca, modelo y cliente se rellenan automáticamente.
        *   **Si no existe:** Un botón `¿Equipo nuevo? Registrar aquí` abre un **modal** para crear el equipo y su cliente sin salir del flujo.
    *   **Paso 2: Datos de Calibración**
        *   **Campos Generales:** `Fecha de Calibración` (datepicker), `Próxima Fecha` (datepicker), `Condiciones del Laboratorio` (Temperatura, Humedad, Presión).
        *   **Sección de "Resultados" DINÁMICA:**
            *   **Si es Estación Total:** Se renderizan los campos para "Precisión Angular", tablas para "Medición con Prisma" (con botones para añadir/quitar filas de puntos de control) y "Medición sin Prisma".
            *   **Si es Teodolito:** Se renderizan campos para `Precisión` y `Error`.
            *   **Si es Nivel:** Se renderizan campos para `Precisión (mm)` y `Error`.
            *   Cada campo debe corresponder a una clave en el objeto `JSONB` que se guardará.
    *   **Paso 3: Revisión y Generación**
        *   Muestra un resumen de toda la información ingresada en formato de solo lectura.
        *   Muestra el nombre del técnico (`[user_profiles.full_name]`) y una imagen de su firma (`[user_profiles.signature_image_url]`).
        *   **Botón Final:** `Confirmar y Generar Certificado`.
        *   **Acción de Backend:** Al hacer clic, se realiza la llamada a la API que guarda los datos, genera el PDF, lo sube a Storage y actualiza el registro con la `pdf_url`.

#### **Pantalla 2.6: Página de Éxito y Herramientas del Certificado**

*   **URL:** `/certificados/[id]/exito`
*   **Acceso:** El usuario que acaba de crear el certificado.
*   **Componentes:**
    *   Mensaje de éxito grande: `¡Certificado N° [certificate_number] generado exitosamente!`
    *   **Panel de Código QR:**
        *   Una imagen grande del QR.
        *   Botones: `Descargar QR (PNG)` y `Copiar enlace`.
    *   **Panel de Documento:**
        *   Botón: `Descargar PDF del Certificado`.
    *   **Botones de Siguiente Acción:**
        *   `Generar otro certificado`.
        *   `Volver a la lista de certificados`.

---

#### **Pantallas de Administración (Solo ADMIN)**

#### **Pantalla 2.7: Gestión de Usuarios**

*   **URL:** `/admin/usuarios`
*   **Acceso:** Solo `ADMIN`.
*   **Componentes:**
    *   **Encabezado:** Título "Gestión de Usuarios" y botón `[+] Invitar Nuevo Usuario`.
    *   **Tabla de Usuarios:**
        *   **Columnas:** `Nombre Completo`, `Email`, `Rol`, `Estado` (Activo/Inactivo), `Acciones`.
        *   **Acciones:** Íconos para `Editar` y `Desactivar/Activar` usuario.
    *   **Modal de Invitación/Edición de Usuario:**
        *   Campo `Nombre Completo`.
        *   Campo `Email`.
        *   Dropdown `Rol` (`ADMIN`, `TECHNICIAN`).
        *   Campo para subir/reemplazar `Firma Digital` (imagen).
        *   Botón: `Enviar Invitación` o `Guardar Cambios`.