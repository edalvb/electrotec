'use client'
import { useEffect, useState } from 'react'
import { AuthContext } from './Auth_context'
import { AuthController } from './Auth_controller'
import { AuthStore } from './Auth_store'
import AuthLayout from './components/Auth_layout'

export default function AuthView() {
  const [controller] = useState(() => AuthController.instance())
  const [store] = useState(() => new AuthStore())
  const [ready, setReady] = useState(false)
  useEffect(() => { controller.initialize(store).finally(() => setReady(true)) }, [controller, store])
  if (!ready) return null
  return (
    <AuthContext.Provider value={{ controller, store }}>
      <AuthLayout/>
    </AuthContext.Provider>
  )
}
