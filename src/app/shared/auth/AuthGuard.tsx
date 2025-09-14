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
        router.replace(`/login?next=${next}`)
        return
      }
      setStatus('allowed')
    }
    check()
    return () => { mounted = false }
  }, [isPublic, pathname, router])

  if (isPublic) return <>{children}</>
  if (status === 'checking') return null
  return <>{children}</>
}
