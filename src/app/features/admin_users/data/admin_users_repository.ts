import { http } from '@/lib/http/axios'
import { supabaseBrowser } from '@/lib/supabase/client'

export class AdminUsersRepository {
  private async authHeaders(){ const { data } = await supabaseBrowser().auth.getSession(); const t = data.session?.access_token; return t ? { Authorization: `Bearer ${t}` } : {} }
  async list(){ const r = await http.get('/api/admin/users', { headers: await this.authHeaders() }); return r.data }
  async invite(input: { full_name: string; email: string; signature?: File | null; role?: 'ADMIN'|'TECHNICIAN' }){
    if (input.signature){
      const f = new FormData()
      f.append('full_name', input.full_name)
      f.append('email', input.email)
      if (input.role) f.append('role', input.role)
      f.append('signature', input.signature)
      const r = await http.post('/api/admin/users', f, { headers: await this.authHeaders() })
      return r.data
    }
    const r = await http.post('/api/admin/users', { full_name: input.full_name, email: input.email, role: input.role }, { headers: await this.authHeaders() })
    return r.data
  }
  async create(input: { full_name: string; email: string; password: string; signature?: File | null; role?: 'ADMIN'|'TECHNICIAN' }){
    if (input.signature){
      const f = new FormData()
      f.append('mode', 'create')
      f.append('full_name', input.full_name)
      f.append('email', input.email)
      f.append('password', input.password)
      if (input.role) f.append('role', input.role)
      f.append('signature', input.signature)
      const r = await http.post('/api/admin/users', f, { headers: await this.authHeaders() })
      return r.data
    }
    const r = await http.post('/api/admin/users', { mode: 'create', full_name: input.full_name, email: input.email, password: input.password, role: input.role }, { headers: await this.authHeaders() })
    return r.data
  }
  async update(id: string, input: { role?: 'ADMIN'|'TECHNICIAN'; is_active?: boolean; full_name?: string; signature?: File | null }){
    if (input.signature){
      const f = new FormData()
      if (input.role) f.append('role', input.role)
      if (typeof input.is_active === 'boolean') f.append('is_active', String(input.is_active))
      if (input.full_name) f.append('full_name', input.full_name)
      f.append('signature', input.signature)
      const r = await http.patch(`/api/admin/users/${id}`, f, { headers: await this.authHeaders() })
      return r.data
    }
    const r = await http.patch(`/api/admin/users/${id}`, input, { headers: await this.authHeaders() })
    return r.data
  }
}
