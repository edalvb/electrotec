import { http } from '@/lib/http/axios'
import { supabaseBrowser } from '@/lib/supabase/client'

export class DashboardRepository {
  async getSummary(){ const r = await http.get('/api/dashboard/summary'); return r.data }
  async getProfile(){ const c = supabaseBrowser(); const { data } = await c.auth.getUser(); if (!data.user) return null; const { data: profile } = await c.from('user_profiles').select('*').eq('id', data.user.id).single(); return profile }
}
