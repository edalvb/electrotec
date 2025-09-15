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

const roleEnum = z.enum(['ADMIN','TECHNICIAN']).optional()
const inviteSchema = z.object({ full_name: z.string().min(2), email: z.string().email(), role: roleEnum })
const createSchema = z.object({ full_name: z.string().min(2), email: z.string().email(), password: z.string().min(8), role: roleEnum })

export async function POST(req: NextRequest){
  const authz = await requireRole(req, ['ADMIN'])
  if (!authz.ok) return json({ error: authz.message }, authz.status)
  const origin = req.nextUrl.origin
  const contentType = req.headers.get('content-type') || ''
  if (contentType.includes('multipart/form-data')){
    const form = await req.formData()
    const mode = String(form.get('mode') || 'invite')
    const full_name = String(form.get('full_name') || '')
    const email = String(form.get('email') || '')
    const role = form.get('role') ? String(form.get('role')) : undefined
    const service = supabaseServer()
    if (mode === 'create'){
      const password = String(form.get('password') || '')
      const parsedCreate = createSchema.safeParse({ full_name, email, password, role })
      if (!parsedCreate.success) return json({ error: 'Datos inválidos' }, 400)
      const { data: created, error: cErr } = await service.auth.admin.createUser({ email, password, user_metadata: { full_name }, email_confirm: true })
      if (cErr) return json({ error: cErr.message }, 400)
      const userId = created.user?.id
      if (!userId) return json({ error: 'No se pudo crear el usuario' }, 400)
      let signature_url: string | null = null
      const file = form.get('signature') as File | null
      if (file && file.size > 0){
        const array = new Uint8Array(await file.arrayBuffer())
        const path = `signatures/${userId}-${Date.now()}-${file.name}`
        const up = await service.storage.from('public').upload(path, array, { contentType: file.type, upsert: true })
        if (up.error) return json({ error: up.error.message }, 400)
        const { data: pub } = service.storage.from('public').getPublicUrl(path)
        signature_url = pub.publicUrl
      }
      const { error: upErr } = await service.from('user_profiles').upsert({ id: userId, full_name, signature_image_url: signature_url, role: (parsedCreate.data.role||'TECHNICIAN'), is_active: true }).eq('id', userId)
      if (upErr) return json({ error: upErr.message }, 400)
      return json({ ok: true, id: userId, created: true })
    }
    const parsed = inviteSchema.safeParse({ full_name, email, role })
    if (!parsed.success) return json({ error: 'Datos inválidos' }, 400)
    const file = form.get('signature') as File | null
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
    const { error: upErr } = await service.from('user_profiles').upsert({ id: userId, full_name, signature_image_url: signature_url, role: (parsed.data.role||'TECHNICIAN'), is_active: true }).eq('id', userId)
    if (upErr) return json({ error: upErr.message }, 400)
    return json({ ok: true, id: userId, signature_url })
  }
  const body = await req.json()
  if (body?.mode === 'create'){
    const parsedCreate = createSchema.safeParse(body)
    if (!parsedCreate.success) return json({ error: 'Datos inválidos' }, 400)
    const service = supabaseServer()
    const { data: created, error: cErr } = await service.auth.admin.createUser({ email: parsedCreate.data.email, password: parsedCreate.data.password, user_metadata: { full_name: parsedCreate.data.full_name }, email_confirm: true })
    if (cErr) return json({ error: cErr.message }, 400)
    const userId = created.user?.id
    if (userId){
      await service.from('user_profiles').upsert({ id: userId, full_name: parsedCreate.data.full_name, role: (parsedCreate.data.role||'TECHNICIAN'), is_active: true }).eq('id', userId)
    }
    return json({ ok: true, id: userId || null, created: true })
  }
  const parsed = inviteSchema.safeParse(body)
  if (!parsed.success) return json({ error: 'Datos inválidos' }, 400)
  const service = supabaseServer()
  const { data: invited, error } = await service.auth.admin.inviteUserByEmail(parsed.data.email, { redirectTo: `${origin}/aceptar-invitacion`, data: { full_name: parsed.data.full_name } })
  if (error) return json({ error: error.message }, 400)
  const userId = invited.user?.id
  if (userId){
    await service.from('user_profiles').upsert({ id: userId, full_name: parsed.data.full_name, role: (parsed.data.role||'TECHNICIAN'), is_active: true }).eq('id', userId)
  }
  return json({ ok: true, id: userId || null, invited: true })
}
