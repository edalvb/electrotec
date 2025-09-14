import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'

export async function GET(_: Request, ctx: { params: Promise<{ id: string }> }) {
  const { id } = await ctx.params
  const db = supabaseServer()
  const { data: cert, error } = await db.from('certificates').select('*').eq('id', id).single()
  if (error || !cert) return NextResponse.json({ error: 'not_found' }, { status: 404 })
  const { data: equipment } = await db.from('equipment').select('*').eq('id', cert.equipment_id).single()
  let client = null
  if (equipment?.owner_client_id) {
  const { data: c } = await db.from('clients').select('*').eq('id', equipment.owner_client_id).single()
    client = c
  }
  const { data: type } = equipment ? await db.from('equipment_types').select('*').eq('id', equipment.equipment_type_id).single() : { data: null }
  const { data: tech } = await db.from('user_profiles').select('*').eq('id', cert.technician_id).single()
  return NextResponse.json({
    id: cert.id,
    certificate_number: cert.certificate_number,
    calibration_date: cert.calibration_date,
    next_calibration_date: cert.next_calibration_date,
    pdf_url: cert.pdf_url,
    equipment: equipment ? { brand: equipment.brand, model: equipment.model, serial_number: equipment.serial_number } : null,
    equipment_type: type ? { name: type.name } : null,
    client: client ? { name: client.name } : null,
    technician: tech ? { full_name: tech.full_name, signature_image_url: tech.signature_image_url } : null
  })
}
