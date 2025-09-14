import { NextResponse, type NextRequest } from 'next/server'

// Cookie que usaremos solo como flag de sesión (no es un mecanismo de seguridad)
const AUTH_COOKIE = 'et_auth'

function isPublicPath(pathname: string) {
  if (pathname.startsWith('/login')) return true
  if (pathname.startsWith('/certificado/')) return true
  return false
}

export function middleware(req: NextRequest) {
  const { pathname } = req.nextUrl

  // Permitir rutas públicas sin chequear
  if (isPublicPath(pathname)) return NextResponse.next()

  // Si no hay flag de auth, redirigir a login
  const authFlag = req.cookies.get(AUTH_COOKIE)?.value
  if (!authFlag) {
    const url = req.nextUrl.clone()
    url.pathname = '/login'
    url.searchParams.set('next', req.nextUrl.pathname + req.nextUrl.search)
    return NextResponse.redirect(url)
  }

  return NextResponse.next()
}

// Excluir API y assets estáticos del middleware
export const config = {
  matcher: [
    // Aplica a todo menos API, rutas internas y estáticos
    '/((?!api|_next|_document|_app|_error|favicon.ico|robots.txt|sitemap.xml|.*\.(png|jpg|jpeg|gif|svg|webp|ico)$).*)',
  ],
}
