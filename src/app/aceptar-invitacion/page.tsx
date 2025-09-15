'use client'
import { useEffect, useState, type ChangeEvent } from 'react'
import { supabaseBrowser } from '@/lib/supabase/client'
import { ModernButton, ModernCard, ModernInput } from '@/app/shared/ui'

export default function AcceptInvitePage(){
  const [code, setCode] = useState<string | null>(null)
  const [password, setPassword] = useState('')
  const [confirm, setConfirm] = useState('')
  const [status, setStatus] = useState<'checking'|'ready'|'done'|'error'>('checking')
  const [message, setMessage] = useState('')

  useEffect(() => {
    const url = new URL(window.location.href)
    const c = url.searchParams.get('code')
    const type = url.searchParams.get('type')
    if (type !== 'signup' && type !== 'invite') { setStatus('error'); setMessage('Enlace inválido'); return }
    setCode(c)
    setStatus('ready')
  }, [])

  const handleSetPassword = async () => {
    if (!code) return
    if (!password || password !== confirm) { setMessage('Las contraseñas no coinciden'); return }
    setMessage('')
    const sb = supabaseBrowser()
    const ex = await sb.auth.exchangeCodeForSession(code)
    if (ex.error) { setStatus('error'); setMessage(ex.error.message); return }
    const up = await sb.auth.updateUser({ password })
    if (up.error) { setStatus('error'); setMessage(up.error.message); return }
    setStatus('done')
  }

  if (status === 'checking') return null
  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-indigo-950 flex items-center justify-center p-6">
      <ModernCard className="p-8 w-full max-w-md">
        {status !== 'done' ? (
          <div className="space-y-4">
            <h1 className="text-2xl font-semibold text-white">Activar cuenta</h1>
            {!!message && <div className="text-red-300 text-sm">{message}</div>}
            <ModernInput type="password" placeholder="Nueva contraseña" value={password} onChange={(e: ChangeEvent<HTMLInputElement>) => setPassword(e.target.value)} />
            <ModernInput type="password" placeholder="Confirmar contraseña" value={confirm} onChange={(e: ChangeEvent<HTMLInputElement>) => setConfirm(e.target.value)} />
            <ModernButton variant="primary" onClick={handleSetPassword}>Guardar contraseña</ModernButton>
          </div>
        ) : (
          <div className="space-y-4 text-center">
            <h1 className="text-2xl font-semibold text-white">Cuenta activada</h1>
            <p className="text-white/70">Ya puedes iniciar sesión</p>
            <a href="/login" className="inline-block"><ModernButton variant="primary">Ir a login</ModernButton></a>
          </div>
        )}
      </ModernCard>
    </div>
  )
}
