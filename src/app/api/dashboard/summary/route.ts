import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'

export async function GET() {
  const now = new Date()
  const start = new Date(now.getFullYear(), now.getMonth(), 1)
  const endSoon = new Date()
  endSoon.setDate(endSoon.getDate() + 30)
  const db = supabaseServer()
  const { data: monthData } = await db.from('certificates').select('id').gte('created_at', start.toISOString())
  const { data: dueSoon } = await db.from('certificates').select('id').gte('next_calibration_date', new Date().toISOString()).lte('next_calibration_date', endSoon.toISOString())
  return NextResponse.json({ issuedThisMonth: monthData?.length || 0, next30Days: dueSoon?.length || 0 })
}
