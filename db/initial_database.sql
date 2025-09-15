CREATE EXTENSION IF NOT EXISTS pgcrypto;

DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_type t
    JOIN pg_namespace n ON n.oid = t.typnamespace
    WHERE t.typname = 'user_role' AND n.nspname = 'public'
  ) THEN
    CREATE TYPE public.user_role AS ENUM ('ADMIN', 'TECHNICIAN');
  END IF;
END
$$;

CREATE TABLE IF NOT EXISTS public.user_profiles (
    id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
    full_name TEXT NOT NULL,
    signature_image_url TEXT,
    role user_role NOT NULL DEFAULT 'TECHNICIAN',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    deleted_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

ALTER TABLE public.user_profiles ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS user_profiles_select_self ON public.user_profiles;
CREATE POLICY user_profiles_select_self ON public.user_profiles FOR SELECT USING (auth.uid() = id);

CREATE TABLE IF NOT EXISTS public.clients (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name TEXT NOT NULL,
    contact_details JSONB,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS public.equipment_types (
    id SERIAL PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS public.equipment (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    serial_number TEXT NOT NULL UNIQUE,
    brand TEXT NOT NULL,
    model TEXT NOT NULL,
    owner_client_id UUID REFERENCES public.clients(id) ON DELETE SET NULL,
    equipment_type_id INT REFERENCES public.equipment_types(id) ON DELETE RESTRICT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS public.certificates (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    certificate_number TEXT NOT NULL UNIQUE,
    equipment_id UUID NOT NULL REFERENCES public.equipment(id) ON DELETE RESTRICT,
    technician_id UUID NOT NULL REFERENCES public.user_profiles(id) ON DELETE RESTRICT,
    calibration_date DATE NOT NULL,
    next_calibration_date DATE NOT NULL,
    results JSONB NOT NULL,
    lab_conditions JSONB,
    pdf_url TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

ALTER TABLE public.clients ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.equipment_types ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.equipment ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.certificates ENABLE ROW LEVEL SECURITY;
DROP POLICY IF EXISTS certificates_select_anon ON public.certificates;
CREATE POLICY certificates_select_anon ON public.certificates
  FOR SELECT TO anon USING (pdf_url IS NOT NULL);

DROP POLICY IF EXISTS equipment_select_anon ON public.equipment;
CREATE POLICY equipment_select_anon ON public.equipment
  FOR SELECT TO anon USING (
    EXISTS (
      SELECT 1 FROM public.certificates c
      WHERE c.equipment_id = public.equipment.id AND c.pdf_url IS NOT NULL
    )
  );

DROP POLICY IF EXISTS clients_select_anon ON public.clients;
CREATE POLICY clients_select_anon ON public.clients
  FOR SELECT TO anon USING (
    EXISTS (
      SELECT 1 FROM public.equipment e
      JOIN public.certificates c ON c.equipment_id = e.id AND c.pdf_url IS NOT NULL
      WHERE e.owner_client_id = public.clients.id
    )
  );

DROP POLICY IF EXISTS equipment_types_select_anon ON public.equipment_types;
CREATE POLICY equipment_types_select_anon ON public.equipment_types
  FOR SELECT TO anon USING (
    EXISTS (
      SELECT 1 FROM public.equipment e
      JOIN public.certificates c ON c.equipment_id = e.id AND c.pdf_url IS NOT NULL
      WHERE e.equipment_type_id = public.equipment_types.id
    )
  );

DROP POLICY IF EXISTS clients_select_auth ON public.clients;
CREATE POLICY clients_select_auth ON public.clients FOR SELECT TO authenticated USING (true);
DROP POLICY IF EXISTS equipment_types_select_auth ON public.equipment_types;
CREATE POLICY equipment_types_select_auth ON public.equipment_types FOR SELECT TO authenticated USING (true);
DROP POLICY IF EXISTS equipment_select_auth ON public.equipment;
CREATE POLICY equipment_select_auth ON public.equipment FOR SELECT TO authenticated USING (true);
DROP POLICY IF EXISTS certificates_select_auth ON public.certificates;
CREATE POLICY certificates_select_auth ON public.certificates FOR SELECT TO authenticated USING (true);

DROP POLICY IF EXISTS user_profiles_select_public_techs ON public.user_profiles;
CREATE POLICY user_profiles_select_public_techs ON public.user_profiles FOR SELECT TO anon USING (
  EXISTS (
    SELECT 1 FROM public.certificates c WHERE c.technician_id = public.user_profiles.id AND c.pdf_url IS NOT NULL
  )
);

CREATE INDEX IF NOT EXISTS idx_equipment_serial_number ON public.equipment(serial_number);
CREATE INDEX IF NOT EXISTS idx_certificates_equipment_id ON public.certificates(equipment_id);
CREATE INDEX IF NOT EXISTS idx_certificates_number ON public.certificates(certificate_number);
CREATE INDEX IF NOT EXISTS idx_user_profiles_deleted_at ON public.user_profiles(deleted_at);

CREATE OR REPLACE FUNCTION public.set_updated_at() RETURNS trigger LANGUAGE plpgsql AS $$
BEGIN
  NEW.updated_at = NOW();
  RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS set_timestamp_user_profiles ON public.user_profiles;
CREATE TRIGGER set_timestamp_user_profiles BEFORE UPDATE ON public.user_profiles FOR EACH ROW EXECUTE FUNCTION public.set_updated_at();

DROP TRIGGER IF EXISTS set_timestamp_certificates ON public.certificates;
CREATE TRIGGER set_timestamp_certificates BEFORE UPDATE ON public.certificates FOR EACH ROW EXECUTE FUNCTION public.set_updated_at();

INSERT INTO storage.buckets (id, name, public) VALUES ('public','public', true) ON CONFLICT (id) DO NOTHING;

DO $$
BEGIN
  IF EXISTS (
    SELECT 1
    FROM pg_class c
    JOIN pg_namespace n ON n.oid = c.relnamespace
    WHERE n.nspname = 'storage' AND c.relname = 'objects'
      AND pg_get_userbyid(c.relowner) = current_user
  ) THEN
    EXECUTE 'ALTER TABLE storage.objects ENABLE ROW LEVEL SECURITY';
    BEGIN
      EXECUTE 'DROP POLICY IF EXISTS storage_public_read ON storage.objects';
    EXCEPTION WHEN OTHERS THEN
      NULL;
    END;
    EXECUTE 'CREATE POLICY storage_public_read ON storage.objects FOR SELECT TO anon USING (bucket_id = ''public'')';
    BEGIN
      EXECUTE 'DROP POLICY IF EXISTS storage_public_read_auth ON storage.objects';
    EXCEPTION WHEN OTHERS THEN
      NULL;
    END;
    EXECUTE 'CREATE POLICY storage_public_read_auth ON storage.objects FOR SELECT TO authenticated USING (bucket_id = ''public'')';
  ELSE
    RAISE NOTICE 'Skipping storage.objects RLS/policies because current user is not the owner';
  END IF;
END
$$;

INSERT INTO public.equipment_types (name) VALUES ('Estación Total') ON CONFLICT (name) DO NOTHING;
INSERT INTO public.equipment_types (name) VALUES ('Teodolito') ON CONFLICT (name) DO NOTHING;
INSERT INTO public.equipment_types (name) VALUES ('Nivel') ON CONFLICT (name) DO NOTHING;

INSERT INTO public.clients (id, name, contact_details)
VALUES (gen_random_uuid(), 'Cliente Demo', '{"email":"demo@cliente.com","phone":"000-000-000"}')
ON CONFLICT (id) DO NOTHING;

WITH c AS (
  SELECT id FROM public.clients WHERE name = 'Cliente Demo' LIMIT 1
), t AS (
  SELECT id FROM public.equipment_types WHERE name = 'Estación Total' LIMIT 1
)
INSERT INTO public.equipment (id, serial_number, brand, model, owner_client_id, equipment_type_id)
SELECT gen_random_uuid(), 'SN-DEMO-001', 'LEICA', 'TS-06 ULTRA 2', c.id, t.id FROM c, t
ON CONFLICT (serial_number) DO NOTHING;
