import { NextRequest } from 'next/server'
import { createClient } from '@supabase/supabase-js'

type Role = 'ADMIN' | 'TECHNICIAN'

export async function getServerSupabaseUser(req: NextRequest){
  const url = process.env.NEXT_PUBLIC_SUPABASE_URL as string
  const anon = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY as string
  const auth = req.headers.get('authorization') || ''
  const token = auth.startsWith('Bearer ') ? auth.substring(7) : undefined
  const client = createClient(url, anon, { global: { headers: token ? { Authorization: `Bearer ${token}` } : undefined } })
  const { data } = await client.auth.getUser()
  return { client, user: data.user || null }
}

export async function requireRole(req: NextRequest, roles: Role[]){
  const { client, user } = await getServerSupabaseUser(req)
  if (!user) return { ok: false as const, status: 401, message: 'Unauthorized', client, user: null as null, profile: null as null }
  const { data: profile } = await client.from('user_profiles').select('*').eq('id', user.id).single()
  if (!profile) return { ok: false as const, status: 403, message: 'Forbidden', client, user, profile: null }
  if (!roles.includes(profile.role as Role)) return { ok: false as const, status: 403, message: 'Forbidden', client, user, profile }
  if (profile.is_active === false || profile.deleted_at) return { ok: false as const, status: 403, message: 'Inactive or deleted user', client, user, profile }
  return { ok: true as const, client, user, profile }
}

export function json(data: unknown, init?: number | ResponseInit){
  const body = JSON.stringify(data)
  if (typeof init === 'number') return new Response(body, { status: init, headers: { 'content-type': 'application/json' } })
  return new Response(body, { ...(init||{}), headers: { 'content-type': 'application/json', ...(init?.headers||{}) } })
}
