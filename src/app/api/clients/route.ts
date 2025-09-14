import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'
import { z } from 'zod'

const schema = z.object({ name: z.string().min(1), contact_details: z.record(z.any()).optional() })

export async function POST(req: Request) {
  const body = await req.json()
  const parsed = schema.safeParse(body)
  if (!parsed.success) return NextResponse.json({ error: 'validation', details: parsed.error.flatten() }, { status: 400 })
  const db = supabaseServer()
  const { data, error } = await db.from('clients').insert({ name: parsed.data.name, contact_details: parsed.data.contact_details || null }).select('id, name').single()
  if (error) {
    const rls = typeof error.message === 'string' && error.message.toLowerCase().includes('row-level security')
    const status = rls ? 403 : 500
    return NextResponse.json({ error: 'client_create_failed', details: error.message || error.details || null }, { status })
  }
  return NextResponse.json(data)
}

export async function GET(req: Request) {
  const { searchParams } = new URL(req.url)
  const q = (searchParams.get('q') || '').trim()
  const db = supabaseServer()
  let query = db.from('clients').select('id, name, contact_details').order('created_at', { ascending: false })
  if (q) query = query.ilike('name', `%${q}%`)
  const { data, error } = await query
  if (error) return NextResponse.json({ error: 'list_failed' }, { status: 500 })
  return NextResponse.json({ items: data || [] })
}
