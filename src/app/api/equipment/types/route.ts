import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'

export async function GET() {
  const db = supabaseServer()
  const { data, error } = await db.from('equipment_types').select('id, name').order('name', { ascending: true })
  if (error) return NextResponse.json({ error: 'list_failed' }, { status: 500 })
  return NextResponse.json({ items: data })
}
