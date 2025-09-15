import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'
import PDFDocument from 'pdfkit'
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
    const number = `ET-${new Date().getFullYear()}-${Math.floor(Math.random() * 900000 + 100000)}`
    const { data: cert, error } = await db
      .from('certificates')
      .insert({
        certificate_number: number,
        equipment_id: parsed.data.equipment_id,
        technician_id: parsed.data.technician_id,
        calibration_date: parsed.data.calibration_date,
        next_calibration_date: parsed.data.next_calibration_date,
        results: parsed.data.results,
        lab_conditions: parsed.data.lab_conditions || null
      })
      .select('*')
      .single()
    if (error || !cert) {
      const msg = (error && (error.message || (error as { details?: string }).details || (error as { hint?: string }).hint)) || 'unknown'
      const rls = typeof msg === 'string' && msg.toLowerCase().includes('row-level security')
      return NextResponse.json({ error: 'create_failed', details: msg }, { status: rls ? 403 : 500 })
    }

    const doc = new PDFDocument({ size: 'A4', margin: 48 })
    const chunks: Uint8Array[] = []
    doc.fontSize(20).text('ELECTROTEC CONSULTING S.A.C.')
    doc.moveDown()
    doc.fontSize(16).text(`Certificado de Calibración N° ${cert.certificate_number}`)
    doc.moveDown()
    doc.fontSize(12).text(`Fecha de Calibración: ${cert.calibration_date}`)
    doc.text(`Próxima Calibración: ${cert.next_calibration_date}`)
    doc.moveDown()
    doc.text('Resultados:')
    doc.text(JSON.stringify(parsed.data.results))
    const buffer = await new Promise<Buffer>((resolve) => {
      doc.on('data', (c: Uint8Array) => chunks.push(c))
      doc.on('end', () => resolve(Buffer.concat(chunks)))
      doc.end()
    })
    const path = `certificates/${cert.id}.pdf`
    const { error: upErr } = await db.storage.from('public').upload(path, buffer, { upsert: true, contentType: 'application/pdf' })
    if (upErr) {
      const msg = upErr.message || (upErr as { details?: string }).details || 'unknown'
      const rls = typeof msg === 'string' && msg.toLowerCase().includes('row-level security')
      return NextResponse.json({ error: 'upload_failed', details: msg }, { status: rls ? 403 : 500 })
    }
    const { data: pub } = db.storage.from('public').getPublicUrl(path)
    await db.from('certificates').update({ pdf_url: pub.publicUrl }).eq('id', cert.id)
    return NextResponse.json({ id: cert.id, certificate_number: cert.certificate_number, pdf_url: pub.publicUrl })
  } catch (e: unknown) {
    const message = e instanceof Error ? e.message : 'unknown'
    return NextResponse.json({ error: 'unexpected', details: message }, { status: 500 })
  }
}
