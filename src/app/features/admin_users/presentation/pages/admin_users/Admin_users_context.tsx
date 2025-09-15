'use client'
import { createContext, useContext } from 'react'
import { AdminUsersController } from './Admin_users_controller'
import { AdminUsersStore } from './Admin_users_store'

type Ctx = { controller: AdminUsersController; store: AdminUsersStore }
const AdminUsersContext = createContext<Ctx | null>(null)
export function useAdminUsers(){ const v = useContext(AdminUsersContext); if (!v) throw new Error('missing AdminUsersContext'); return v }
export function AdminUsersProvider({ children, controller, store }: { children: React.ReactNode; controller: AdminUsersController; store: AdminUsersStore }){
  return <AdminUsersContext.Provider value={{ controller, store }}>{children}</AdminUsersContext.Provider>
}
