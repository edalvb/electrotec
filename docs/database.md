### **Análisis de los Cambios y Justificación**

1.  **Fuente Única de Verdad (Single Source of Truth):** El sistema de autenticación de Supabase (`auth.users`) será la única fuente de verdad para la *identidad* de un usuario (su ID, email, etc.). Nuestra tabla `user_profiles` solo contendrá los metadatos adicionales que nuestra aplicación necesita (nombre completo, rol, firma).

2.  **Integridad de Datos Garantizada:** Al vincular `user_profiles.id` directamente con `auth.users.id` mediante una clave foránea, garantizamos que:
    *   No puede existir un perfil sin un usuario de autenticación correspondiente.
    *   Si un usuario se elimina de Supabase Auth, su perfil se puede eliminar automáticamente en cascada (`ON DELETE CASCADE`), evitando datos "huérfanos" y manteniendo la base de datos limpia.

3.  **Gestión de Roles Explícita:** En lugar de asumir que todos los usuarios son técnicos, introduciremos una columna `role`. Esto es fundamental para la seguridad y la escalabilidad. Usaremos un tipo `ENUM` de PostgreSQL para asegurar que los roles solo puedan tener valores predefinidos (ej: 'ADMIN', 'TECHNICIAN'), evitando errores de tipeo.

4.  **Flexibilidad:** Un administrador ahora puede ser un usuario en el sistema sin necesidad de tener una firma, ya que el campo `signature_image_url` puede ser nulo. Esto refleja mejor la realidad.

---

### **Estructura de la Base de Datos Revisada y Mejorada (PostgreSQL)**

Aquí está el DDL (Data Definition Language) actualizado.

```sql
-- Creación de un tipo de dato personalizado para los roles de usuario.
-- Esto asegura la consistencia y previene errores.
CREATE TYPE public.user_role AS ENUM ('ADMIN', 'TECHNICIAN');


-- 1. Tabla de Perfiles de Usuario (REEMPLAZA la antigua tabla `technicians`)
-- Esta tabla extiende la tabla `auth.users` de Supabase con metadatos específicos de la aplicación.
CREATE TABLE public.user_profiles (
    -- La Clave Primaria es el mismo UUID del usuario en `auth.users`.
    -- Esto crea un vínculo directo y robusto.
    id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
    
    full_name TEXT NOT NULL,
    
    -- La firma es específica del rol de técnico, por lo que puede ser nula para otros roles (ej. un ADMIN puro).
    signature_image_url TEXT,
    
    -- Columna para definir los permisos del usuario dentro de la aplicación.
    role user_role NOT NULL DEFAULT 'TECHNICIAN',
    
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Habilitar Row Level Security (RLS) en la tabla de perfiles.
-- Es una buena práctica de seguridad para controlar quién puede ver/modificar qué perfil.
ALTER TABLE public.user_profiles ENABLE ROW LEVEL SECURITY;


-- 2. Tabla de Clientes (Sin cambios)
CREATE TABLE public.clients (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name TEXT NOT NULL,
    contact_details JSONB,
    created_at TIMESTAMPTZ DEFAULT NOW()
);


-- 3. Tabla de Tipos de Equipo (Sin cambios)
CREATE TABLE public.equipment_types (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);


-- 4. Tabla de Equipos (Sin cambios)
CREATE TABLE public.equipment (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    serial_number TEXT NOT NULL UNIQUE,
    brand TEXT NOT NULL,
    model TEXT NOT NULL,
    owner_client_id UUID REFERENCES public.clients(id) ON DELETE SET NULL,
    equipment_type_id INT REFERENCES public.equipment_types(id) ON DELETE RESTRICT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);


-- 5. Tabla de Certificados (ACTUALIZADA para referenciar `user_profiles`)
CREATE TABLE public.certificates (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    certificate_number TEXT NOT NULL UNIQUE,
    
    equipment_id UUID NOT NULL REFERENCES public.equipment(id) ON DELETE RESTRICT,
    
    -- CAMBIO CLAVE: La columna `technician_id` ahora referencia la tabla `user_profiles`.
    -- El nombre de la columna sigue siendo descriptivo del rol que cumple el usuario en este contexto.
    technician_id UUID NOT NULL REFERENCES public.user_profiles(id) ON DELETE RESTRICT,
    
    calibration_date DATE NOT NULL,
    next_calibration_date DATE NOT NULL,
    
    results JSONB NOT NULL,
    lab_conditions JSONB,
    
    pdf_url TEXT,
    
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Índices (Sin cambios, siguen siendo relevantes)
CREATE INDEX idx_equipment_serial_number ON equipment(serial_number);
CREATE INDEX idx_certificates_equipment_id ON certificates(equipment_id);
CREATE INDEX idx_certificates_number ON certificates(certificate_number);

```

### **Resumen de los Beneficios de la Nueva Estructura**

*   **Seguridad Mejorada:** La gestión de usuarios ahora se basa en el robusto sistema de `auth` de Supabase. El uso de roles y RLS (Row Level Security) permitirá definir políticas de acceso muy granulares (ej: "un técnico solo puede ver los certificados que él mismo emitió").
*   **Consistencia de Datos:** El enlace `FOREIGN KEY` con `ON DELETE CASCADE` entre `auth.users` y `user_profiles` es una garantía de que la base de datos se mantendrá íntegra y sin datos basura a lo largo del tiempo.
*   **Escalabilidad:** Añadir nuevos roles en el futuro (ej: 'SUPERVISOR', 'FINANZAS') será tan simple como añadir un nuevo valor al `ENUM` `user_role` y definir sus permisos, sin necesidad de reestructurar las tablas.
*   **Claridad del Modelo:** El modelo de datos ahora refleja de forma explícita y clara la relación entre un "usuario autenticado" y su "perfil de aplicación", que es un patrón de diseño estándar y muy fácil de entender para futuros desarrolladores.

Esta estructura revisada es la base perfecta para construir una aplicación segura, profesional y mantenible sobre Next.js y Supabase.