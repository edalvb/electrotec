'use client'
import { useEffect, useMemo, useState } from 'react'
import { usePathname, useRouter } from 'next/navigation'
import { supabaseBrowser } from '@/lib/supabase/client'

type Props = { children: React.ReactNode }

function isPublicPath(pathname: string) {
  if (!pathname) return false
  // Rutas públicas (no requieren estar logueado)
  if (pathname === '/login' || pathname.startsWith('/login')) return true
  if (pathname === '/' ) return false // home es privada (dashboard)
  // Vista pública de certificados
  if (pathname.startsWith('/certificado/')) return true
  return false
}

export default function AuthGuard({ children }: Props) {
  const pathname = usePathname()
  const router = useRouter()
  const [status, setStatus] = useState<'checking' | 'allowed'>('checking')
  const AUTH_COOKIE = 'et_auth'

  const isPublic = useMemo(() => isPublicPath(pathname || ''), [pathname])

  useEffect(() => {
    let mounted = true
    async function check() {
      if (isPublic) { if (mounted) setStatus('allowed'); return }
      const client = supabaseBrowser()
      const { data } = await client.auth.getUser()
      if (!mounted) return
      if (!data.user) {
        const next = encodeURIComponent(pathname || '/')
        // limpiar cookie si existiera
        if (typeof document !== 'undefined') document.cookie = `${AUTH_COOKIE}=; Max-Age=0; Path=/; SameSite=Lax`
        router.replace(`/login?next=${next}`)
        return
      }
      // Verificar perfil activo (is_active y no soft-deleted)
      const { data: profileData } = await client.from('user_profiles').select('is_active, deleted_at').eq('id', data.user.id).single()
      if (profileData && (profileData.is_active === false || profileData.deleted_at)){
        if (typeof document !== 'undefined') document.cookie = `${AUTH_COOKIE}=; Max-Age=0; Path=/; SameSite=Lax`
        router.replace('/login')
        return
      }
      // setear cookie flag de sesión
      if (typeof document !== 'undefined') document.cookie = `${AUTH_COOKIE}=1; Path=/; SameSite=Lax`
      setStatus('allowed')
    }
    check()
    return () => { mounted = false }
  }, [isPublic, pathname, router])

  // Mantener cookie sincronizada con cambios de sesión (login/logout)
  useEffect(() => {
    const client = supabaseBrowser()
    const { data: sub } = client.auth.onAuthStateChange((_event, session) => {
      if (typeof document === 'undefined') return
      if (session?.user) {
        document.cookie = `${AUTH_COOKIE}=1; Path=/; SameSite=Lax`
      } else {
        document.cookie = `${AUTH_COOKIE}=; Max-Age=0; Path=/; SameSite=Lax`
      }
    })
    return () => { sub.subscription.unsubscribe() }
  }, [])

  if (isPublic) return <>{children}</>
  if (status === 'checking') return null
  return <>{children}</>
}
