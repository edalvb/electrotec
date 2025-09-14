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
  const pageParam = searchParams.get('page')
  const pageSizeParam = searchParams.get('pageSize')
  const page = pageParam ? Math.max(parseInt(pageParam || '1', 10) || 1, 1) : null
  const pageSize = pageSizeParam ? Math.min(Math.max(parseInt(pageSizeParam || '10', 10) || 10, 1), 100) : null
  const db = supabaseServer()
  let query = db.from('clients').select('id, name, contact_details', { count: 'exact', head: false }).order('created_at', { ascending: false })
  if (q) query = query.ilike('name', `%${q}%`)
  let start: number | undefined
  let end: number | undefined
  if (page && pageSize) {
    start = (page - 1) * pageSize
    end = start + pageSize - 1
    query = query.range(start, end)
  }
  const { data, error, count } = await query
  if (error) return NextResponse.json({ error: 'list_failed' }, { status: 500 })
  if (page && pageSize) {
    const total = count || 0
    const totalPages = Math.max(Math.ceil(total / pageSize), 1)
    return NextResponse.json({ items: data || [], pagination: { page, pageSize, total, totalPages } })
  }
  return NextResponse.json({ items: data || [] })
}
