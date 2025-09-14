import type { Metadata } from 'next'
import '@radix-ui/themes/styles.css'
import './globals.css'
import { Theme } from '@radix-ui/themes'
import { Inter, Outfit } from 'next/font/google'
import AuthGuard from './shared/auth/AuthGuard'

const inter = Inter({ subsets: ['latin'], variable: '--font-sans' })
const outfit = Outfit({ subsets: ['latin'], variable: '--font-heading' })

export const metadata: Metadata = { title: 'Electrotec', description: 'Gestión de certificados' }

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="es">
      <body className={`min-h-screen bg-app text-white antialiased ${inter.variable} ${outfit.variable} font-sans`}>
        <Theme accentColor="indigo" grayColor="slate" radius="large" appearance="dark">
          <AuthGuard>{children}</AuthGuard>
        </Theme>
      </body>
    </html>
  )
}
