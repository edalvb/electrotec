import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'
import { z } from 'zod'

const updateSchema = z
  .object({
    name: z.string().min(1).optional(),
    contact_details: z.record(z.any()).nullable().optional()
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
  const { data, error } = await db
    .from('clients')
    .update({
      ...(parsed.data.name !== undefined ? { name: parsed.data.name } : {}),
      ...(parsed.data.contact_details !== undefined ? { contact_details: parsed.data.contact_details } : {})
    })
    .eq('id', id)
    .select('id, name, contact_details')
    .single()

  if (error) {
    const rls = typeof error.message === 'string' && error.message.toLowerCase().includes('row-level security')
    const status = rls ? 403 : 500
    return NextResponse.json({ error: 'client_update_failed', details: error.message || error.details || null }, { status })
  }

  return NextResponse.json(data)
}

export async function DELETE(
  _req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params
  if (!id || typeof id !== 'string') {
    return NextResponse.json({ error: 'invalid_id' }, { status: 400 })
  }

  const db = supabaseServer()
  const { error } = await db.from('clients').delete().eq('id', id)

  if (error) {
    const rls = typeof error.message === 'string' && error.message.toLowerCase().includes('row-level security')
    const status = rls ? 403 : 500
    return NextResponse.json({ error: 'client_delete_failed', details: error.message || error.details || null }, { status })
  }

  return new NextResponse(null, { status: 204 })
}
