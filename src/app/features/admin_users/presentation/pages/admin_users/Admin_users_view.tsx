'use client'
import { useEffect, useState } from 'react'
import { AdminUsersController } from './Admin_users_controller'
import { AdminUsersStore } from './Admin_users_store'
import { AdminUsersProvider } from './Admin_users_context'
import AdminUsersLayout from './components/Admin_users_layout'

export default function AdminUsersView(){
  const [controller] = useState(() => AdminUsersController.instance())
  const [store] = useState(() => new AdminUsersStore())
  const [loading, setLoading] = useState(true)
  useEffect(() => { (async () => { await controller.initialize(store); setLoading(false) })() }, [controller, store])
  if (loading) return null
  return (
    <AdminUsersProvider controller={controller} store={store}>
      <AdminUsersLayout />
    </AdminUsersProvider>
  )
}
