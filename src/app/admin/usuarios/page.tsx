'use client'
import { useEffect, useState } from 'react'
import AdminUsersView from '../../features/admin_users/presentation/pages/admin_users/Admin_users_view'

export default function Page(){
  const [ready, setReady] = useState(false)
  useEffect(() => { setReady(true) }, [])
  if (!ready) return null
  return <AdminUsersView />
}
