import { NextResponse } from 'next/server'
import PDFDocument from 'pdfkit'
import { supabaseServer } from '@/lib/supabase/server'

export async function GET(req: Request) {
  const { searchParams } = new URL(req.url)
  const page = Math.max(parseInt(searchParams.get('page') || '1', 10) || 1, 1)
  const pageSize = Math.min(Math.max(parseInt(searchParams.get('pageSize') || '20', 10) || 20, 1), 100)
  const q = (searchParams.get('q') || '').trim().toLowerCase()

  const db = supabaseServer()
  // Fetch certificates page
  const from = (page - 1) * pageSize
  const to = from + pageSize - 1
  const { data: certs, error } = await db
    .from('certificates')
    .select('id, certificate_number, equipment_id, calibration_date, next_calibration_date, pdf_url, created_at')
    .order('created_at', { ascending: false })
    .range(from, to)

  if (error) return NextResponse.json({ error: 'list_failed' }, { status: 500 })

  const equipmentIds = [...new Set((certs || []).map(c => c.equipment_id))]
  const { data: equipments } = equipmentIds.length
    ? await db
        .from('equipment')
        .select('id, serial_number, brand, model')
        .in('id', equipmentIds)
    : { data: [] as { id: string; serial_number: string; brand: string; model: string }[] }

  const items = (certs || []).map(c => ({
    id: c.id,
    certificate_number: c.certificate_number,
    calibration_date: c.calibration_date,
    next_calibration_date: c.next_calibration_date,
    pdf_url: c.pdf_url,
    equipment: equipments?.find(e => e.id === c.equipment_id) || null
  }))

  // Optional client-side filtering by query across certificate number or equipment serial/model/brand
  const filtered = q
    ? items.filter(it => {
        const hay = `${it.certificate_number} ${it.equipment?.serial_number || ''} ${it.equipment?.brand || ''} ${it.equipment?.model || ''}`.toLowerCase()
        return hay.includes(q)
      })
    : items

  return NextResponse.json({ items: filtered, page, pageSize })
}

export async function POST(req: Request) {
  const body = await req.json()
  const id = body.id as string
  const db = supabaseServer()
  const { data: cert, error } = await db.from('certificates').select('*').eq('id', id).single()
  if (error || !cert) return NextResponse.json({ error: 'not_found' }, { status: 404 })
  const doc = new PDFDocument({ size: 'A4', margin: 48 })
  const chunks: Uint8Array[] = []
  doc.fontSize(18).text(`Certificado de Calibración N° ${cert.certificate_number}`)
  doc.moveDown()
  doc.fontSize(12).text(`Fecha de Calibración: ${cert.calibration_date}`)
  doc.text(`Próxima Calibración: ${cert.next_calibration_date}`)
  const buffer = await new Promise<Buffer>((resolve) => {
    doc.on('data', (c: Uint8Array) => chunks.push(c))
    doc.on('end', () => resolve(Buffer.concat(chunks)))
    doc.end()
  })
  const path = `certificates/${id}.pdf`
  const { error: upErr } = await db.storage.from('public').upload(path, buffer, { upsert: true, contentType: 'application/pdf' })
  if (upErr) return NextResponse.json({ error: 'upload_failed' }, { status: 500 })
  const { data: pub } = db.storage.from('public').getPublicUrl(path)
  await db.from('certificates').update({ pdf_url: pub.publicUrl }).eq('id', id)
  return NextResponse.json({ pdf_url: pub.publicUrl })
}
