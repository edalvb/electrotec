'use client'
import { useDashboardState } from '../dashboard_states'
import { Button, Card, Flex, Heading, Separator, Text } from '@radix-ui/themes'
import Link from 'next/link'

export default function DashboardLayout() {
  const { summary, profileName } = useDashboardState(s => s)
  return (
    <div className="min-h-screen grid md:grid-cols-[240px_1fr]">
      <aside className="p-4 glass hidden md:block">
        <Flex direction="column" gap="4">
          <Heading size="5">ELECTROTEC</Heading>
          <Separator size="4"/>
          <Flex direction="column" gap="2">
            <Link href="/"><Button className="btn-glass w-full" variant="soft">Dashboard</Button></Link>
            <Link href="/certificados"><Button className="btn-glass w-full" variant="soft">Certificados</Button></Link>
            <Link href="/equipos"><Button className="btn-glass w-full" variant="soft">Equipos</Button></Link>
            <Link href="/clientes"><Button className="btn-glass w-full" variant="soft">Clientes</Button></Link>
          </Flex>
        </Flex>
      </aside>
      <main className="p-6">
        <Flex justify="between" align="center" className="mb-6">
          <Heading size="6">Dashboard</Heading>
          <Text>Hola, {profileName || 'Usuario'}</Text>
        </Flex>
        <div className="grid md:grid-cols-2 gap-4">
          <Card className="glass p-4">
            <Heading size="4">Certificados emitidos este mes</Heading>
            <Text style={{ fontSize: 48 }}>{summary.issuedThisMonth}</Text>
          </Card>
          <Card className="glass p-4">
            <Heading size="4">Próximas calibraciones (30 días)</Heading>
            <Text style={{ fontSize: 48 }}>{summary.next30Days}</Text>
          </Card>
        </div>
        <Flex className="mt-6" gap="4">
          <Link href="/certificados/nuevo"><Button className="btn-primary">Generar nuevo certificado</Button></Link>
        </Flex>
      </main>
    </div>
  )
}
