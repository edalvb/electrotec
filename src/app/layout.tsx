import type { Metadata } from 'next'
import '@radix-ui/themes/styles.css'
import './globals.css'
import { Theme } from '@radix-ui/themes'

export const metadata: Metadata = { title: 'Electrotec', description: 'Gestión de certificados' }

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="es">
      <body className="min-h-screen bg-app text-white antialiased">
        <Theme accentColor="indigo" grayColor="slate" radius="large" appearance="dark">
          {children}
        </Theme>
      </body>
    </html>
  )
}
