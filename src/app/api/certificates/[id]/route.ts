import { NextResponse } from 'next/server'
import PDFDocument from 'pdfkit'
import { supabaseServer } from '@/lib/supabase/server'

type PatchBody = Partial<{
  calibration_date: string
  next_calibration_date: string
  results: unknown
  lab_conditions: { temperature?: number; humidity?: number; pressure?: number; calibration?: boolean; maintenance?: boolean } | null
}>

async function generatePdfBuffer(cert: { id: string; certificate_number: string; calibration_date: string; next_calibration_date: string; results?: unknown }){
  const doc = new PDFDocument({ size: 'A4', margin: 48 })
  const chunks: Uint8Array[] = []
  doc.fontSize(20).text('ELECTROTEC CONSULTING S.A.C.')
  doc.moveDown()
  doc.fontSize(16).text(`Certificado de Calibración N° ${cert.certificate_number}`)
  doc.moveDown()
  doc.fontSize(12).text(`Fecha de Calibración: ${cert.calibration_date}`)
  doc.text(`Próxima Calibración: ${cert.next_calibration_date}`)
  if (cert.results) {
    doc.moveDown()
    doc.text('Resultados:')
    doc.text(JSON.stringify(cert.results))
  }
  const buffer = await new Promise<Buffer>((resolve) => {
    doc.on('data', (c: Uint8Array) => chunks.push(c))
    doc.on('end', () => resolve(Buffer.concat(chunks)))
    doc.end()
  })
  return buffer
}

export async function GET(_: Request, ctx: { params: Promise<{ id: string }> }){
  try {
    const { id } = await ctx.params
    const db = supabaseServer()
    const { data: cert, error } = await db.from('certificates').select('*').eq('id', id).single()
    if (error || !cert) return NextResponse.json({ error: 'not_found' }, { status: 404 })
    return NextResponse.json(cert)
  } catch (e: unknown) {
    const message = e instanceof Error ? e.message : 'unknown'
    return NextResponse.json({ error: 'unexpected', details: message }, { status: 500 })
  }
}

export async function PATCH(req: Request, ctx: { params: Promise<{ id: string }> }){
  try {
    const { id } = await ctx.params
    const body = (await req.json()) as PatchBody

    const updates: Record<string, unknown> = {}
    if (typeof body.calibration_date === 'string') updates.calibration_date = body.calibration_date
    if (typeof body.next_calibration_date === 'string') updates.next_calibration_date = body.next_calibration_date
    if (typeof body.results !== 'undefined') updates.results = body.results
    if (typeof body.lab_conditions !== 'undefined') updates.lab_conditions = body.lab_conditions

    const db = supabaseServer()

    if (Object.keys(updates).length > 0) {
      const { error: updErr } = await db.from('certificates').update(updates).eq('id', id)
      if (updErr) {
        const msg = updErr.message || (updErr as { details?: string }).details || 'unknown'
        const rls = typeof msg === 'string' && msg.toLowerCase().includes('row-level security')
        return NextResponse.json({ error: 'update_failed', details: msg }, { status: rls ? 403 : 500 })
      }
    }

    // Fetch the updated certificate
    const { data: cert, error: getErr } = await db.from('certificates').select('*').eq('id', id).single()
    if (getErr || !cert) return NextResponse.json({ error: 'not_found' }, { status: 404 })

    // Regenerate and replace PDF (upsert true)
    const buffer = await generatePdfBuffer({
      id: cert.id,
      certificate_number: cert.certificate_number,
      calibration_date: cert.calibration_date,
      next_calibration_date: cert.next_calibration_date,
      results: cert.results
    })
    const path = `certificates/${id}.pdf`
    const { error: upErr } = await db.storage.from('public').upload(path, buffer, { upsert: true, contentType: 'application/pdf' })
    if (upErr) {
      const msg = upErr.message || (upErr as { details?: string }).details || 'unknown'
      const rls = typeof msg === 'string' && msg.toLowerCase().includes('row-level security')
      return NextResponse.json({ error: 'upload_failed', details: msg }, { status: rls ? 403 : 500 })
    }
    const { data: pub } = db.storage.from('public').getPublicUrl(path)
    await db.from('certificates').update({ pdf_url: pub.publicUrl }).eq('id', id)

    return NextResponse.json({
      id: cert.id,
      certificate_number: cert.certificate_number,
      calibration_date: cert.calibration_date,
      next_calibration_date: cert.next_calibration_date,
      pdf_url: pub.publicUrl
    })
  } catch (e: unknown) {
    const message = e instanceof Error ? e.message : 'unknown'
    return NextResponse.json({ error: 'unexpected', details: message }, { status: 500 })
  }
}

export async function DELETE(_: Request, ctx: { params: Promise<{ id: string }> }){
  try {
    const { id } = await ctx.params
    const db = supabaseServer()

    // Remove PDF file (ignore if not exists)
    const path = `certificates/${id}.pdf`
    await db.storage.from('public').remove([path])

    // Delete DB record
    const { error } = await db.from('certificates').delete().eq('id', id)
    if (error) {
      const msg = error.message || (error as { details?: string }).details || 'unknown'
      const rls = typeof msg === 'string' && msg.toLowerCase().includes('row-level security')
      return NextResponse.json({ error: 'delete_failed', details: msg }, { status: rls ? 403 : 500 })
    }

    return NextResponse.json({ ok: true })
  } catch (e: unknown) {
    const message = e instanceof Error ? e.message : 'unknown'
    return NextResponse.json({ error: 'unexpected', details: message }, { status: 500 })
  }
}
