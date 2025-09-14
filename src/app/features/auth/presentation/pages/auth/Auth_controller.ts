'use client'
import { AuthStore } from './Auth_store'
import { useAuthState } from './auth_states'
import { loginSchema } from './validators'

export class AuthController {
  private static _instance: AuthController
  private store: AuthStore | null = null
  static instance() { if (!this._instance) this._instance = new AuthController(); return this._instance }
  async initialize(store: AuthStore) { this.store = store }
  handleLogin = async () => {
    const { email, password, setIsLoading, setError } = useAuthState.getState()
    setError(null)
    const parsed = loginSchema.safeParse({ email, password })
    if (!parsed.success) { setError('Datos inválidos'); return }
    setIsLoading(true)
    const res = await this.store!.signIn(email, password)
    if (res.ok) {
      useAuthState.getState().reset()
      const params = new URLSearchParams(window.location.search)
      const next = params.get('next') || '/'
      window.location.href = next
    } else {
      setError(res.message || 'Error de autenticación')
    }
    setIsLoading(false)
  }
}
