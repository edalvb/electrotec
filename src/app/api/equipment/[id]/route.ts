import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'
import { z } from 'zod'

const updateSchema = z
  .object({
    serial_number: z.string().min(1).optional(),
    brand: z.string().min(1).optional(),
    model: z.string().min(1).optional(),
    equipment_type_id: z.number().int().positive().optional(),
    owner_client_id: z.string().uuid().nullable().optional()
  })
  .refine((data) => Object.keys(data).length > 0, {
    message: 'No fields provided for update'
  })

export async function PATCH(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params
  if (!id || typeof id !== 'string') {
    return NextResponse.json({ error: 'invalid_id' }, { status: 400 })
  }

  const body = await req.json().catch(() => null)
  const parsed = updateSchema.safeParse(body || {})
  if (!parsed.success) {
    return NextResponse.json({ error: 'validation', details: parsed.error.flatten() }, { status: 400 })
  }

  const db = supabaseServer()
  const { data: updated, error } = await db
    .from('equipment')
    .update(parsed.data)
    .eq('id', id)
    .select('*')
    .single()

  if (error) {
    const rls = typeof error.message === 'string' && error.message.toLowerCase().includes('row-level security')
    const status = error.code === '23505' ? 409 : error.code === '23503' ? 400 : rls ? 403 : 500
    return NextResponse.json({ error: 'equipment_update_failed', details: error.message || error.details || null }, { status })
  }

  const { data: type } = await db
    .from('equipment_types')
    .select('id, name')
    .eq('id', updated.equipment_type_id)
    .single()

  const client = updated.owner_client_id
    ? await db.from('clients').select('id, name').eq('id', updated.owner_client_id).single()
    : { data: null as { id: string; name: string } | null }

  return NextResponse.json({
    id: updated.id,
    serial_number: updated.serial_number,
    brand: updated.brand,
    model: updated.model,
    client: client.data,
    equipment_type: type
  })
}

export async function DELETE(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params
  if (!id || typeof id !== 'string') {
    return NextResponse.json({ error: 'invalid_id' }, { status: 400 })
  }

  const db = supabaseServer()

  // Validar si existen certificados vinculados a este equipo
  const { count, error: countErr } = await db
    .from('certificates')
    .select('id', { count: 'exact', head: true })
    .eq('equipment_id', id)

  if (countErr) {
    const rls = typeof countErr.message === 'string' && countErr.message.toLowerCase().includes('row-level security')
    const status = rls ? 403 : 500
    return NextResponse.json({ error: 'certificates_check_failed', details: countErr.message || countErr.details || null }, { status })
  }

  if ((count || 0) > 0) {
    return NextResponse.json({ error: 'has_linked_certificates', count }, { status: 409 })
  }

  // Proceder a eliminar el equipo
  const { error: delErr } = await db
    .from('equipment')
    .delete()
    .eq('id', id)

  if (delErr) {
    const rls = typeof delErr.message === 'string' && delErr.message.toLowerCase().includes('row-level security')
    const status = delErr.code === '23503' ? 409 : rls ? 403 : 500
    return NextResponse.json({ error: 'equipment_delete_failed', details: delErr.message || delErr.details || null }, { status })
  }

  return NextResponse.json({ ok: true })
}
