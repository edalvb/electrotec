import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'

export async function GET(req: Request) {
  const { searchParams } = new URL(req.url)
  const q = (searchParams.get('q') || '').trim()
  if (!q || q.length < 2) return NextResponse.json({ items: [] })
  const db = supabaseServer()
  const { data: equipments } = await db
    .from('equipment')
    .select('id, serial_number, brand, model, owner_client_id, equipment_type_id')
    .ilike('serial_number', `%${q}%`)
    .limit(10)
  const clientIds = [...new Set((equipments || []).map(e => e.owner_client_id).filter(Boolean) as string[])]
  const typeIds = [...new Set((equipments || []).map(e => e.equipment_type_id).filter(Boolean) as number[])]
  const { data: clients } = clientIds.length ? await db.from('clients').select('id, name').in('id', clientIds) : { data: [] as { id: string; name: string }[] }
  const { data: types } = typeIds.length ? await db.from('equipment_types').select('id, name').in('id', typeIds) : { data: [] as { id: number; name: string }[] }
  const items = (equipments || []).map(e => ({
    id: e.id,
    serial_number: e.serial_number,
    brand: e.brand,
    model: e.model,
    client: clients?.find(c => c.id === e.owner_client_id) || null,
    equipment_type: types?.find(t => t.id === e.equipment_type_id) || null
  }))
  return NextResponse.json({ items })
}
