'use client'
import { useEffect, useState } from 'react'
import { AuthContext } from './Auth_context'
import { AuthController } from './Auth_controller'
import { AuthStore } from './Auth_store'
import AuthLayout from './components/Auth_layout'

function AuthLayoutFallback() {
  return (
    <div className="min-h-screen grid place-items-center p-8 bg-gradient-to-br from-blue-950 via-slate-900 to-slate-950">
      <div className="glass border border-white/20 rounded-xl p-6 text-white/80">
        <div className="font-semibold">Cargando módulo de autenticación…</div>
        <div className="text-sm opacity-70">Temporalmente usando un layout básico mientras se recompila el componente.</div>
      </div>
    </div>
  )
}

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
