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
  if (ct.includes('multipart/form-data')){
    const form = await req.formData()
    const data: any = {}
    if (form.get('role')) data.role = String(form.get('role'))
    if (form.get('is_active')) data.is_active = String(form.get('is_active')) === 'true'
    if (form.get('full_name')) data.full_name = String(form.get('full_name'))
    const parsed = patchSchema.safeParse(data)
    if (!parsed.success) return json({ error: 'Datos inválidos' }, 400)
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
  const { error } = await service.from('user_profiles').update(parsed.data).eq('id', id)
  if (error) return json({ error: error.message }, 400)
  return json({ ok: true })
}
