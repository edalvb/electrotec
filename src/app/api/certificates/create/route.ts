import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'
import { CertificatePdfGenerator } from '@/lib/pdf/certificates'
import { z } from 'zod'

const labSchema = z
  .object({
    temperature: z.number().optional(),
    humidity: z.number().min(0).max(100).optional(),
    pressure: z.number().optional(),
    calibration: z.boolean().optional(),
    maintenance: z.boolean().optional()
  })
  .optional()
const resultsSchema = z.record(z.unknown()).or(z.array(z.unknown()))

const schema = z.object({
  equipment_id: z.string().uuid(),
  calibration_date: z.string(),
  next_calibration_date: z.string(),
  results: resultsSchema,
  lab_conditions: labSchema,
  technician_id: z.string().uuid()
})

export async function POST(req: Request) {
  try {
    const body = await req.json()
    const parsed = schema.safeParse(body)
    if (!parsed.success) return NextResponse.json({ error: 'validation', details: parsed.error.flatten() }, { status: 400 })
    const db = supabaseServer()
    // Cargar datos relacionados: equipo y tipo para validar resultados
    const { data: equipment } = await db
      .from('equipment')
      .select('id, serial_number, brand, model, owner_client_id, equipment_type_id')
      .eq('id', parsed.data.equipment_id)
      .single()

    const { data: type } = equipment
      ? await db.from('equipment_types').select('id, name').eq('id', equipment.equipment_type_id).single()
      : { data: null as { id: number; name: string } | null }

    const { data: client } = equipment?.owner_client_id
      ? await db.from('clients').select('id, name').eq('id', equipment.owner_client_id).single()
      : { data: null as { id: string; name: string } | null }

    // Validación dinámica tolerante a acentos y variantes del nombre
    const norm = (s: string) => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase()
    const tnameRaw = type?.name || ''
    const tnorm = norm(tnameRaw)
    const typeKey: 'estacion' | 'teodolito' | 'nivel' | 'desconocido' =
      tnorm.includes('estacion') ? 'estacion' : tnorm.includes('teodolito') ? 'teodolito' : tnorm.includes('nivel') ? 'nivel' : 'desconocido'

  const angleRow = z.object({ pattern: z.string(), obtained: z.string(), error: z.string() })
    const distRow = z.object({ control: z.number(), obtained: z.number(), delta: z.number().optional() })

    const sEst = z.object({
      angular_precision: z.string(),
      angular_measurements: z.array(angleRow).min(1),
      distance_precision: z.string(),
      prism_measurements: z.array(distRow).min(1),
      no_prism_measurements: z.array(distRow).min(1),
      meta: z.any().optional()
    })
    const sTeo = z.object({
      angular_precision: z.string(),
      angular_measurements: z.array(angleRow).min(1),
      meta: z.any().optional()
    })
    const sNivel = z.object({
      angular_measurements: z.array(angleRow).min(1),
      level_precision_mm: z.number(),
      level_error: z.string(),
      meta: z.any().optional()
    })

    const tryOrder = typeKey === 'estacion' ? [sEst, sTeo, sNivel] : typeKey === 'teodolito' ? [sTeo, sEst, sNivel] : typeKey === 'nivel' ? [sNivel, sTeo, sEst] : [sEst, sTeo, sNivel]

    // Normalización: si es "nivel" y llega en formato { level_rows: [...] }, mapear al esquema esperado
    let resultsNormalized: unknown = parsed.data.results
    if (typeKey === 'nivel') {
      const rIn = parsed.data.results as any
      if (rIn && Array.isArray(rIn.level_rows)) {
        const fmtDms = (x: any) => `${Number(x?.d ?? 0)}° ${Number(x?.m ?? 0)}' ${Number(x?.s ?? 0)}"`
        const angular_measurements = rIn.level_rows.map((row: any) => ({
          pattern: fmtDms(row?.pattern),
          obtained: fmtDms(row?.obtained),
          error: String(row?.error ?? '')
        }))
        const first = rIn.level_rows[0]
        const level_precision_mm = typeof first?.precision === 'number' ? first.precision : undefined
        const level_error = String(first?.error ?? (rIn.level_rows.map((x: any) => x?.error).filter(Boolean).join(' / ') || ''))
        resultsNormalized = {
          angular_measurements,
          ...(level_precision_mm != null ? { level_precision_mm } : {}),
          level_error,
          ...(rIn.meta ? { meta: rIn.meta } : {})
        }
      }
    }
    let ok = false
    let lastErr: any = null
    for (const sch of tryOrder) {
      const test = sch.safeParse(resultsNormalized)
      if (test.success) { ok = true; break }
      lastErr = test.error
    }
    if (!ok) {
      return NextResponse.json({ error: 'results_validation', details: lastErr?.flatten?.() || lastErr || 'invalid results' }, { status: 400 })
    }

    // Insertar certificado recién después de validar
    const number = `ET-${new Date().getFullYear()}-${Math.floor(Math.random() * 900000 + 100000)}`
    const { data: cert, error } = await db
      .from('certificates')
      .insert({
        certificate_number: number,
        equipment_id: parsed.data.equipment_id,
        technician_id: parsed.data.technician_id,
        calibration_date: parsed.data.calibration_date,
        next_calibration_date: parsed.data.next_calibration_date,
        results: resultsNormalized as any,
        lab_conditions: parsed.data.lab_conditions || null
      })
      .select('*')
      .single()
    if (error || !cert) {
      const msg = (error && (error.message || (error as { details?: string }).details || (error as { hint?: string }).hint)) || 'unknown'
      const rls = typeof msg === 'string' && msg.toLowerCase().includes('row-level security')
      return NextResponse.json({ error: 'create_failed', details: msg }, { status: rls ? 403 : 500 })
    }

    // Técnico (para la firma) — no bloqueante
    const { data: technician } = await db
      .from('user_profiles')
      .select('id, full_name, signature_image_url, role')
      .eq('id', cert.technician_id)
      .single()

    // Generar PDF usando el servicio reutilizable
    const generator = new CertificatePdfGenerator()
    const buffer = await generator.generatePdfBuffer({
      certificate_number: cert.certificate_number,
      equipment: {
        typeName: type?.name || null,
        brand: equipment?.brand || null,
        model: equipment?.model || null,
        serial_number: equipment?.serial_number || null
      },
      client: { name: client?.name || null },
      technician: technician ? { full_name: technician.full_name, signature_image_url: technician.signature_image_url } : null,
      results: resultsNormalized as any,
      lab: parsed.data.lab_conditions || null,
      calibration_date: parsed.data.calibration_date,
      next_calibration_date: parsed.data.next_calibration_date
    })

    const storagePath = `certificates/${cert.id}.pdf`
    const { error: upErr } = await db.storage.from('public').upload(storagePath, buffer, { upsert: true, contentType: 'application/pdf' })
    if (upErr) {
      const msg = upErr.message || (upErr as { details?: string }).details || 'unknown'
      const rls = typeof msg === 'string' && msg.toLowerCase().includes('row-level security')
      return NextResponse.json({ error: 'upload_failed', details: msg }, { status: rls ? 403 : 500 })
    }
    const { data: pub } = db.storage.from('public').getPublicUrl(storagePath)
    await db.from('certificates').update({ pdf_url: pub.publicUrl }).eq('id', cert.id)
    return NextResponse.json({ id: cert.id, certificate_number: cert.certificate_number, pdf_url: pub.publicUrl })
  } catch (e: unknown) {
    const message = e instanceof Error ? e.message : 'unknown'
    return NextResponse.json({ error: 'unexpected', details: message }, { status: 500 })
  }
}
