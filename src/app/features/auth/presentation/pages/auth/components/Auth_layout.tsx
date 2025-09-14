"use client"
import { useAuthContext } from '../Auth_context'
import { useAuthState } from '../auth_states'
import { ModernButton, ModernInput } from '@/app/shared/ui'

export default function AuthLayout() {
  const { controller } = useAuthContext()
  const { email, password, isLoading, error, setEmail, setPassword } = useAuthState()

  return (
    <div className="min-h-screen grid place-items-center p-6 bg-gradient-to-br from-blue-950 via-slate-900 to-slate-950">
      <div className="w-full max-w-md glass rounded-2xl border border-white/15 shadow-2xl overflow-hidden">
        <div className="p-6 sm:p-8">
          <div className="mb-6">
            <h1 className="text-2xl font-semibold bg-gradient-to-r from-blue-300 via-indigo-300 to-purple-300 bg-clip-text text-transparent">
              Iniciar sesión
            </h1>
            <p className="text-sm text-white/60 mt-1">Accede al panel de gestión de certificados.</p>
          </div>

          <div className="space-y-4">
            <ModernInput
              label="Correo electrónico"
              type="email"
              placeholder="tu@correo.com"
              value={email}
              onChange={e => setEmail(e.target.value)}
              disabled={isLoading}
            />

            <ModernInput
              label="Contraseña"
              type="password"
              placeholder="••••••••"
              value={password}
              onChange={e => setPassword(e.target.value)}
              disabled={isLoading}
            />

            {error && (
              <div className="text-red-400 text-sm" role="alert">{error}</div>
            )}

            <ModernButton
              variant="primary"
              className="w-full"
              loading={isLoading}
              disabled={isLoading || !email || !password}
              onClick={controller.handleLogin}
            >
              Iniciar sesión
            </ModernButton>
          </div>
        </div>
      </div>
    </div>
  )
}
