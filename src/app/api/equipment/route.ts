import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'
import { z } from 'zod'

const schema = z.object({
  client: z.object({ name: z.string().min(1), contact_details: z.record(z.any()).optional() }).optional(),
  equipment: z.object({
    serial_number: z.string().min(1),
    brand: z.string().min(1),
    model: z.string().min(1),
    equipment_type_id: z.number().int().positive(),
    owner_client_id: z.string().uuid().nullable().optional()
  })
})

export async function POST(req: Request) {
  const body = await req.json()
  const parsed = schema.safeParse(body)
  if (!parsed.success) return NextResponse.json({ error: 'validation', details: parsed.error.flatten() }, { status: 400 })
  const db = supabaseServer()
  let clientId: string | null = parsed.data.equipment.owner_client_id ?? null
  if (parsed.data.client) {
    const { data: c, error: ce } = await db.from('clients').insert({ name: parsed.data.client.name, contact_details: parsed.data.client.contact_details || null }).select('id').single()
    if (ce) {
      const rls = typeof ce.message === 'string' && ce.message.toLowerCase().includes('row-level security')
      const status = ce.code === '23505' ? 409 : rls ? 403 : 500
      return NextResponse.json({ error: 'client_create_failed', details: ce.message || ce.details || null }, { status })
    }
    clientId = c.id
  }
  const payload = { ...parsed.data.equipment, owner_client_id: clientId }
  const { data: e, error: ee } = await db.from('equipment').insert(payload).select('*').single()
  if (ee) {
    const rls = typeof ee.message === 'string' && ee.message.toLowerCase().includes('row-level security')
    const status = ee.code === '23505' ? 409 : ee.code === '23503' ? 400 : rls ? 403 : 500
    return NextResponse.json({ error: 'equipment_create_failed', details: ee.message || ee.details || null }, { status })
  }
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

export async function GET(req: Request) {
  const { searchParams } = new URL(req.url)
  const q = (searchParams.get('q') || '').trim()
  const db = supabaseServer()
  let query = db.from('equipment').select('id, serial_number, brand, model, owner_client_id, equipment_type_id').order('created_at', { ascending: false })
  if (q) query = query.or(`serial_number.ilike.%${q}%,brand.ilike.%${q}%,model.ilike.%${q}%`)
  const { data: equipments, error } = await query
  if (error) return NextResponse.json({ error: 'list_failed' }, { status: 500 })
  const clientIds = [...new Set((equipments || []).map(e => e.owner_client_id).filter(Boolean) as string[])]
  const typeIds = [...new Set((equipments || []).map(e => e.equipment_type_id).filter(Boolean) as number[])]
  const { data: clients } = clientIds.length ? await db.from('clients').select('id, name').in('id', clientIds) : { data: [] as { id: string; name: string }[] }
  const { data: types } = typeIds.length ? await db.from('equipment_types').select('id, name').in('id', typeIds) : { data: [] as { id: number; name: string }[] }
  // Calcular si cada equipo es eliminable (sin certificados vinculados)
  const equipmentIds = (equipments || []).map(e => e.id)
  let certSet = new Set<string>()
  if (equipmentIds.length) {
    const { data: certRefs, error: certErr } = await db
      .from('certificates')
      .select('equipment_id')
      .in('equipment_id', equipmentIds)
    if (!certErr) {
      certSet = new Set((certRefs || []).map(r => r.equipment_id as string))
    }
  }
  const items = (equipments || []).map(e => ({
    id: e.id,
    serial_number: e.serial_number,
    brand: e.brand,
    model: e.model,
    client: clients?.find(c => c.id === e.owner_client_id) || null,
    equipment_type: types?.find(t => t.id === e.equipment_type_id) || null,
    deletable: !certSet.has(e.id)
  }))
  return NextResponse.json({ items })
}
