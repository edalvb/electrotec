A continuación tienes la versión de `screens.md` sincronizada con la estructura actual del proyecto (rutas en `src/app` y utilidades en `src/lib`). Mantengo el detalle UX/UI pero adapto las rutas, nombres de carpetas y acciones esperadas.

---

## Área Pública (Sin Autenticación)

### Vista Pública del Certificado

- URL: `/certificado/[id]` (ej: `/certificado/a1b2c3d4-e5f6...`). Está implementada en `src/app/certificado/[id]/page.tsx`.
- Propósito: Verificar visualmente y desde móvil la validez del certificado al escanear el QR. Mostrar resumen claro y acceso al PDF almacenado en Supabase Storage.
- Acceso: Público.
- Componentes principales:
  - Encabezado con logo (uso del layout global en `src/app/layout.tsx`).
  - Título: `Certificado de Calibración N° [certificate_number]`.
  - Panel de estado (VIGENTE / PRÓXIMO A VENCER / VENCIDO) calculado a partir de `next_calibration_date`.
  - Sección "Equipo Calibrado": tipo (desde `equipment_types`), marca, modelo y número de serie.
  - Sección "Validez": fecha de calibración y próxima calibración (formateadas).
  - Sección "Propietario": datos del cliente.
  - Botón principal: `VER CERTIFICADO COMPLETO (PDF)` que abre `pdf_url` en nueva pestaña (PDF generado por `src/lib/pdf/certificates/CertificatePdfGenerator.ts`).
  - Pie de página con leyenda: "Certificado emitido por ELECTROTEC CONSULTING S.A.C.".

---

## Área Privada (Requiere Autenticación)

La aplicación usa páginas protegidas y componentes de contexto para autenticación en `src/app/features/auth` y el guard `src/app/shared/auth/AuthGuard.tsx`.

### Layout principal (Shell)

- Implementación: `src/app/layout.tsx` con navegación persistente y estilos globales en `src/app/globals.css`.
- Componentes persistentes:
  - Sidebar con enlaces a: `Dashboard` (`/`), `Certificados` (`/certificados`), `Equipos` (`/equipos`), `Clientes` (`/clientes`) y, si el usuario tiene rol ADMIN, `Gestión de Usuarios` (`/admin/usuarios`).
  - Header con título de página y menú de usuario que muestra `user_profiles.full_name`.

---

### Autenticación y Onboarding

#### Inicio de sesión

- URL: `/login` (implementado en `src/app/login/page.tsx`).
- Acceso: usuarios no autenticados. Usuarios autenticados son redirigidos al `/`.
- Componentes: formulario con email, contraseña, opción "Recordarme" y mensajes de error claros.

#### Aceptar invitación

- URL: `/aceptar-invitacion` (implementado en `src/app/aceptar-invitacion/page.tsx`).
- Acceso: usuarios con token de invitación válido (Supabase). Formulario para establecer contraseña y activar cuenta; luego redirige al Dashboard.

---

## Módulo Principal

### Dashboard

- URL: `/` (implementado en `src/app/page.tsx`).
- Acceso: ADMIN y TECHNICIAN (controlado por RLS / lógica de frontend).
- Componentes:
  - Tarjetas KPI: certificados emitidos este mes, próximas calibraciones (30 días), etc.
  - Botón principal: `Generar Nuevo Certificado` que lleva a `/certificados/nuevo`.
  - Tabla con últimos certificados (los 5 más recientes).

---

## Certificados (CRUD)

### Lista de certificados

- URL: `/certificados` (implementado en `src/app/certificados/page.tsx`).
- Acceso: ADMIN (ve todo) y TECHNICIAN (vista filtrada por RLS).
- Componentes:
  - Encabezado con botón `[+] Nuevo Certificado`.
  - Barra de búsqueda y filtros (rango de fechas, tipo de equipo, técnico —este último visible solo para ADMIN).
  - Tabla paginada con columnas: N° Cert., Equipo (Marca/Modelo), N° Serie, Cliente, Fecha Calib., Próxima Calib., Técnico, Estado, Acciones (Ver PDF / Ver detalles / Generar QR).

### Crear/Editar certificado (Wizard)

- URL: `/certificados/nuevo` y `/certificados/[id]/editar` (nuevo en `src/app/certificados/nuevo/page.tsx` y edición en la estructura de `features/certificates/presentation/pages`).
- Acceso: ADMIN y TECHNICIAN.
- Flujo (3 pasos):
  1. Equipo y Cliente: búsqueda por número de serie (llama a API `src/app/api/equipment/route.ts` o `src/app/api/equipment/search/route.ts`) y autofill; opción para crear equipo/cliente en un modal.
  2. Datos de calibración: fecha, condiciones del laboratorio y sección de resultados dinámica según tipo de equipo. Los datos técnicos se guardan en un JSON estructurado.
  3. Revisión y generación: mostrar resumen, firma del técnico (desde `user_profiles.signature_image_url`) y `Confirmar y Generar Certificado`.
- Backend: las rutas API relevantes están en `src/app/api/certificates/` (crear, actualizar, obtener), y el generador de PDF está en `src/lib/pdf/certificates/CertificatePdfGenerator.ts`.

### Página de éxito

- URL: `/certificados/[id]/exito` (puede implementarse en el flujo post-creación bajo `src/app/certificados/`).
- Componentes: QR grande con botones `Descargar QR` y `Copiar enlace`, botón de `Descargar PDF del Certificado`, y opciones para `Generar otro` o `Volver a la lista`.

---

## Administración (Solo ADMIN)

### Gestión de usuarios

- URL: `/admin/usuarios` (implementado en `src/app/admin/usuarios/page.tsx`).
- Acceso: ADMIN.
- Componentes:
  - Tabla de usuarios con columnas: Nombre completo, Email, Rol, Estado, Acciones (Editar / Activar/Desactivar).
  - Modal para invitar o editar usuario: nombre, email, rol (ADMIN/TECHNICIAN) y opción para subir reemplazar firma digital.
  - Las acciones de invitación y gestión usan las rutas bajo `src/app/api/admin/users/route.ts` y `src/app/api/admin/users/[id]/route.ts`.

---

## Integraciones y utilidades

- Generación de PDFs: `src/lib/pdf/certificates/CertificatePdfGenerator.ts`.
- Cliente HTTP: `src/lib/http/axios.ts`.
- Supabase: configuración y helpers en `src/lib/supabase/client.ts`, `server.ts` y `rbac.ts`.
- Rutas API del proyecto: varias bajo `src/app/api/*` (certificates, equipment, clients, admin/users, public certificates, etc.).

---

## Notas y próximos pasos recomendados

- Revisar `src/app/features/*/presentation/pages` para alinear copy y estados mostrados en las vistas con la documentación.
- Añadir diagramas o imágenes (capturas de pantalla) en `docs/` cuando las vistas estén listas para mejorar la documentación UX.

---

Documento actualizado: adaptado a la estructura actual del repositorio y rutas implementadas.