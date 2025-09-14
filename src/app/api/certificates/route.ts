import { NextResponse } from 'next/server'
import PDFDocument from 'pdfkit'
import { supabaseServer } from '@/lib/supabase/server'

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
