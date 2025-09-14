import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'
import { z } from 'zod'

const schema = z.object({
  client: z.object({ name: z.string().min(1), contact_details: z.record(z.any()).optional() }).optional(),
  equipment: z.object({
    serial_number: z.string().min(1),
    brand: z.string().min(1),
    model: z.string().min(1),
    equipment_type_id: z.number().int().positive()
  })
})

export async function POST(req: Request) {
  const body = await req.json()
  const parsed = schema.safeParse(body)
  if (!parsed.success) return NextResponse.json({ error: 'validation', details: parsed.error.flatten() }, { status: 400 })
  const db = supabaseServer()
  let clientId: string | null = null
  if (parsed.data.client) {
    const { data: c, error: ce } = await db.from('clients').insert({ name: parsed.data.client.name, contact_details: parsed.data.client.contact_details || null }).select('id').single()
    if (ce) return NextResponse.json({ error: 'client_create_failed' }, { status: 500 })
    clientId = c.id
  }
  const payload = { ...parsed.data.equipment, owner_client_id: clientId }
  const { data: e, error: ee } = await db.from('equipment').insert(payload).select('*').single()
  if (ee) return NextResponse.json({ error: 'equipment_create_failed' }, { status: 500 })
  const { data: type } = await db.from('equipment_types').select('id, name').eq('id', e.equipment_type_id).single()
  const client = clientId ? await db.from('clients').select('id, name').eq('id', clientId).single() : { data: null as { id: string; name: string } | null }
  return NextResponse.json({
    id: e.id,
    serial_number: e.serial_number,
    brand: e.brand,
    model: e.model,
    client: client.data,
    equipment_type: type
  })
}
