'use client'
import { AdminUsersStore } from './Admin_users_store'
import { useAdminUsersState } from './admin_users_states'
import { z } from 'zod'

export class AdminUsersController {
  private static _instance: AdminUsersController
  private store: AdminUsersStore | null = null
  static instance(){ if (!this._instance) this._instance = new AdminUsersController(); return this._instance }
  async initialize(store: AdminUsersStore){ this.store = store; await this.load() }
  load = async () => {
    try {
      useAdminUsersState.getState().setIsLoading(true)
      const r = await this.store!.list()
      useAdminUsersState.getState().setItems(r.items||[])
    } catch (e: any) {
      const status = e?.response?.status
      if (status === 401 || status === 403) { window.location.href = '/' } else { useAdminUsersState.getState().setError('No se pudo cargar usuarios') }
    } finally {
      useAdminUsersState.getState().setIsLoading(false)
    }
  }
  invite = async (input: { full_name: string; email: string; signature?: File | null }) => {
    const schema = z.object({ full_name: z.string().min(2), email: z.string().email() })
    const parsed = schema.safeParse(input)
    if (!parsed.success) { useAdminUsersState.getState().setError('Datos inválidos'); return }
    useAdminUsersState.getState().setError(null)
    await this.store!.invite(input)
    useAdminUsersState.getState().closeInvite()
    await this.load()
  }
  update = async (id: string, input: { role?: 'ADMIN'|'TECHNICIAN'; is_active?: boolean; full_name?: string; signature?: File | null }) => {
    await this.store!.update(id, input)
    useAdminUsersState.getState().closeEdit()
    await this.load()
  }
}
