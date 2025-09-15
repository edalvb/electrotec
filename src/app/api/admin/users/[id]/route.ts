import { NextRequest } from 'next/server'
import { json, requireRole } from '@/lib/supabase/rbac'
import { supabaseServer } from '@/lib/supabase/server'
import { z } from 'zod'

const patchSchema = z.object({ role: z.enum(['ADMIN','TECHNICIAN']).optional(), is_active: z.boolean().optional(), full_name: z.string().min(2).optional() })

export async function PATCH(req: NextRequest, { params }: { params: { id: string } }){
  const authz = await requireRole(req, ['ADMIN'])
  if (!authz.ok) return json({ error: authz.message }, authz.status)
  const id = params.id
  const ct = req.headers.get('content-type') || ''
  const service = supabaseServer()
  // Perfil actual del usuario que será actualizado
  const { data: currentProfile, error: cpErr } = await service.from('user_profiles').select('id, role, is_active, deleted_at').eq('id', id).single()
  if (cpErr) return json({ error: cpErr.message }, 400)
  if (ct.includes('multipart/form-data')){
    const form = await req.formData()
    const data: any = {}
    if (form.get('role')) data.role = String(form.get('role'))
    if (form.get('is_active')) data.is_active = String(form.get('is_active')) === 'true'
    if (form.get('full_name')) data.full_name = String(form.get('full_name'))
    const parsed = patchSchema.safeParse(data)
    if (!parsed.success) return json({ error: 'Datos inválidos' }, 400)
    // Salvaguarda: no permitir desactivar o degradar al último ADMIN activo
    if (currentProfile.role === 'ADMIN'){
      const willDeactivate = typeof parsed.data.is_active === 'boolean' ? parsed.data.is_active === false : false
      const willDemote = parsed.data.role === 'TECHNICIAN'
      if (willDeactivate || willDemote){
        const { count, error: cntErr } = await service.from('user_profiles').select('id', { count: 'exact', head: true }).eq('role', 'ADMIN').eq('is_active', true).is('deleted_at', null).neq('id', id)
        if (cntErr) return json({ error: cntErr.message }, 400)
        const otherAdmins = count || 0
        if (otherAdmins === 0){
          return json({ error: 'No puedes dejar el sistema sin administradores.' }, 400)
        }
      }
    }
    const updateData: { full_name?: string; role?: 'ADMIN'|'TECHNICIAN'; is_active?: boolean; signature_image_url?: string | null } = { ...parsed.data }
    const file = form.get('signature') as File | null
    if (file && file.size > 0){
      const array = new Uint8Array(await file.arrayBuffer())
      const path = `signatures/${id}-${Date.now()}-${file.name}`
      const up = await service.storage.from('public').upload(path, array, { contentType: file.type, upsert: true })
      if (up.error) return json({ error: up.error.message }, 400)
      const { data: pub } = service.storage.from('public').getPublicUrl(path)
      updateData.signature_image_url = pub.publicUrl
    }
    const { error } = await service.from('user_profiles').update(updateData).eq('id', id)
    if (error) return json({ error: error.message }, 400)
    return json({ ok: true })
  }
  const body = await req.json()
  const parsed = patchSchema.safeParse(body)
  if (!parsed.success) return json({ error: 'Datos inválidos' }, 400)
  if (currentProfile.role === 'ADMIN'){
    const willDeactivate = typeof parsed.data.is_active === 'boolean' ? parsed.data.is_active === false : false
    const willDemote = parsed.data.role === 'TECHNICIAN'
    if (willDeactivate || willDemote){
      const { count, error: cntErr } = await service.from('user_profiles').select('id', { count: 'exact', head: true }).eq('role', 'ADMIN').eq('is_active', true).is('deleted_at', null).neq('id', id)
      if (cntErr) return json({ error: cntErr.message }, 400)
      const otherAdmins = count || 0
      if (otherAdmins === 0){
        return json({ error: 'No puedes dejar el sistema sin administradores.' }, 400)
      }
    }
  }
  const { error } = await service.from('user_profiles').update(parsed.data).eq('id', id)
  if (error) return json({ error: error.message }, 400)
  return json({ ok: true })
}

const deleteSchema = z.object({ hard: z.boolean().optional() })

export async function DELETE(req: NextRequest, { params }: { params: { id: string } }){
  const authz = await requireRole(req, ['ADMIN'])
  if (!authz.ok) return json({ error: authz.message }, authz.status)
  const id = params.id
  // evitar auto-eliminación del admin actual
  if (authz.user.id === id) return json({ error: 'No puedes eliminar tu propio usuario.' }, 400)
  const service = supabaseServer()
  const url = new URL(req.url)
  const hard = url.searchParams.get('hard') === 'true'
  // Bloquear eliminar al último ADMIN activo
  const { data: target, error: tErr } = await service.from('user_profiles').select('id, role, is_active, deleted_at').eq('id', id).single()
  if (tErr) return json({ error: tErr.message }, 400)
  if (target.role === 'ADMIN' && target.is_active && !target.deleted_at){
    const { count, error: cntErr } = await service.from('user_profiles').select('id', { count: 'exact', head: true }).eq('role', 'ADMIN').eq('is_active', true).is('deleted_at', null).neq('id', id)
    if (cntErr) return json({ error: cntErr.message }, 400)
    const otherAdmins = count || 0
    if (otherAdmins === 0){
      return json({ error: 'No puedes eliminar al último administrador activo.' }, 400)
    }
  }
  // si hay certificados asociados, forzar soft delete
  const { count, error: cntErr } = await service.from('certificates').select('id', { count: 'exact', head: true }).eq('technician_id', id)
  if (cntErr) return json({ error: cntErr.message }, 400)
  const hasDeps = (count || 0) > 0
  const doHard = hard && !hasDeps
  if (!doHard){
    // Soft delete: marcar inactivo y timestamp
    const { error } = await service.from('user_profiles').update({ is_active: false, deleted_at: new Date().toISOString() }).eq('id', id)
    if (error) return json({ error: error.message }, 400)
    return json({ ok: true, softDeleted: true })
  }
  // Hard delete: eliminar storage de firma si existe, borrar profile y usuario auth
  // 1) Traer perfil para obtener firma
  const { data: profile, error: pErr } = await service.from('user_profiles').select('signature_image_url').eq('id', id).single()
  if (pErr) return json({ error: pErr.message }, 400)
  // 2) Borrar objeto de storage si es de nuestro bucket público
  if (profile?.signature_image_url){
    try {
      const pub = profile.signature_image_url
      // Las URLs públicas tienen forma: {SUPABASE_URL}/storage/v1/object/public/{path}
      const idx = pub.indexOf('/storage/v1/object/public/')
      if (idx !== -1){
        const path = pub.substring(idx + '/storage/v1/object/public/'.length)
        // path incluye bucket_id al inicio
        const slash = path.indexOf('/')
        const bucket = slash > 0 ? path.substring(0, slash) : 'public'
        const objPath = slash > 0 ? path.substring(slash + 1) : path
        await service.storage.from(bucket).remove([objPath])
      }
    } catch { /* noop */ }
  }
  // 3) Borrar perfil primero (ON DELETE CASCADE en auth.users->user_profiles no aplica al revés)
  const { error: delProfErr } = await service.from('user_profiles').delete().eq('id', id)
  if (delProfErr) return json({ error: delProfErr.message }, 400)
  // 4) Borrar usuario en Auth
  const { error: delAuthErr } = await service.auth.admin.deleteUser(id)
  if (delAuthErr) return json({ error: delAuthErr.message }, 400)
  return json({ ok: true, hardDeleted: true })
}
