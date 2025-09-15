import { NextRequest } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'
import { json, requireRole } from '@/lib/supabase/rbac'
import { z } from 'zod'

export async function GET(req: NextRequest){
  const authz = await requireRole(req, ['ADMIN'])
  if (!authz.ok) return json({ error: authz.message }, authz.status)
  const sb = supabaseServer()
  const { data, error } = await sb.from('user_profiles').select('*').order('created_at', { ascending: false })
  if (error) return json({ error: error.message }, 500)
  return json({ items: data })
}

const inviteSchema = z.object({ full_name: z.string().min(2), email: z.string().email() })

export async function POST(req: NextRequest){
  const authz = await requireRole(req, ['ADMIN'])
  if (!authz.ok) return json({ error: authz.message }, authz.status)
  const origin = req.nextUrl.origin
  const contentType = req.headers.get('content-type') || ''
  if (contentType.includes('multipart/form-data')){
    const form = await req.formData()
    const full_name = String(form.get('full_name') || '')
    const email = String(form.get('email') || '')
    const parsed = inviteSchema.safeParse({ full_name, email })
    if (!parsed.success) return json({ error: 'Datos inválidos' }, 400)
    const file = form.get('signature') as File | null
    const service = supabaseServer()
    const { data: invited, error: invErr } = await service.auth.admin.inviteUserByEmail(email, { redirectTo: `${origin}/aceptar-invitacion`, data: { full_name } })
    if (invErr) return json({ error: invErr.message }, 400)
    const userId = invited.user?.id
    if (!userId) return json({ error: 'No se pudo invitar' }, 400)
    let signature_url: string | null = null
    if (file && file.size > 0){
      const array = new Uint8Array(await file.arrayBuffer())
      const path = `signatures/${userId}-${Date.now()}-${file.name}`
      const up = await service.storage.from('public').upload(path, array, { contentType: file.type, upsert: true })
      if (up.error) return json({ error: up.error.message }, 400)
      const { data: pub } = service.storage.from('public').getPublicUrl(path)
      signature_url = pub.publicUrl
    }
    const { error: upErr } = await service.from('user_profiles').upsert({ id: userId, full_name, signature_image_url: signature_url, role: 'TECHNICIAN', is_active: true }).eq('id', userId)
    if (upErr) return json({ error: upErr.message }, 400)
    return json({ ok: true, id: userId, signature_url })
  }
  const body = await req.json()
  const parsed = inviteSchema.safeParse(body)
  if (!parsed.success) return json({ error: 'Datos inválidos' }, 400)
  const service = supabaseServer()
  const { data: invited, error } = await service.auth.admin.inviteUserByEmail(parsed.data.email, { redirectTo: `${origin}/aceptar-invitacion`, data: { full_name: parsed.data.full_name } })
  if (error) return json({ error: error.message }, 400)
  const userId = invited.user?.id
  if (userId){
    await service.from('user_profiles').upsert({ id: userId, full_name: parsed.data.full_name, role: 'TECHNICIAN', is_active: true }).eq('id', userId)
  }
  return json({ ok: true, id: userId || null })
}
