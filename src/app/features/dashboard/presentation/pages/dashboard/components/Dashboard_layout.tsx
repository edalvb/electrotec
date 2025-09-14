'use client'
import { useState, useEffect } from 'react'
import { useDashboardState } from '../dashboard_states'
import { Button, Card, Flex, Heading, Separator, Text, TextField } from '@radix-ui/themes'
import Link from 'next/link'
import Portal from '@/app/shared/ui/Portal'

export default function DashboardLayout() {
  const { summary, profileName } = useDashboardState(s => s)
  const [showClient, setShowClient] = useState(false)
  const [showEquipment, setShowEquipment] = useState(false)
  const [clientName, setClientName] = useState('')
  const [creatingClient, setCreatingClient] = useState(false)
  const [serial, setSerial] = useState('')
  const [brand, setBrand] = useState('')
  const [model, setModel] = useState('')
  const [types, setTypes] = useState<{ id:number; name:string }[]>([])
  const [equipmentTypeId, setEquipmentTypeId] = useState<number | ''>('' as any)
  const [creatingEq, setCreatingEq] = useState(false)
  useEffect(() => { if (showEquipment) { (async () => { const r = await fetch('/api/equipment/types'); const j = await r.json(); setTypes(j.items||[]); if ((j.items||[]).length) setEquipmentTypeId(j.items[0].id) })() } }, [showEquipment])
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
          <Button className="btn-glass" onClick={() => setShowClient(true)}>Crear cliente</Button>
          <Button className="btn-glass" onClick={() => setShowEquipment(true)}>Crear equipo</Button>
        </Flex>

        {showClient && (
          <Portal>
            <div className="fixed inset-0 bg-black/40 z-[9999] flex items-center justify-center p-4">
              <Card className="glass p-6 w-full max-w-md">
                <Flex direction="column" gap="3">
                  <Heading size="5" className="font-heading text-primary">Nuevo Cliente</Heading>
                  <TextField.Root className="input-glass" value={clientName} onChange={e => setClientName(e.target.value)} placeholder="Nombre"/>
                  <Flex justify="between" gap="3">
                    <Button className="btn-glass" onClick={() => { setClientName(''); setShowClient(false) }}>Cancelar</Button>
                    <Button className="btn-primary" disabled={!clientName || creatingClient} onClick={async () => { try { setCreatingClient(true); const r = await fetch('/api/clients', { method: 'POST', headers: { 'Content-Type':'application/json' }, body: JSON.stringify({ name: clientName }) }); if (r.ok) { setClientName(''); setShowClient(false) } } finally { setCreatingClient(false) } }}>Guardar</Button>
                  </Flex>
                </Flex>
              </Card>
            </div>
          </Portal>
        )}

        {showEquipment && (
          <Portal>
            <div className="fixed inset-0 bg-black/40 z-[9999] flex items-center justify-center p-4">
              <Card className="glass p-6 w-full max-w-md">
                <Flex direction="column" gap="3">
                  <Heading size="5" className="font-heading text-primary">Nuevo Equipo</Heading>
                  <TextField.Root className="input-glass" value={serial} onChange={e => setSerial(e.target.value)} placeholder="Número de serie"/>
                  <TextField.Root className="input-glass" value={brand} onChange={e => setBrand(e.target.value)} placeholder="Marca"/>
                  <TextField.Root className="input-glass" value={model} onChange={e => setModel(e.target.value)} placeholder="Modelo"/>
                  <div>
                    <Text className="text-muted">Tipo de equipo</Text>
                    <select className="mt-1 w-full input-glass" value={equipmentTypeId as any} onChange={e => setEquipmentTypeId(Number(e.target.value))}>
                      {types.map(t => (<option key={t.id} value={t.id}>{t.name}</option>))}
                    </select>
                  </div>
                  <Flex justify="between" gap="3">
                    <Button className="btn-glass" onClick={() => { setSerial(''); setBrand(''); setModel(''); setShowEquipment(false) }}>Cancelar</Button>
                    <Button className="btn-primary" disabled={!serial || !brand || !model || !equipmentTypeId || creatingEq} onClick={async () => { try { setCreatingEq(true); const r = await fetch('/api/equipment', { method: 'POST', headers: { 'Content-Type':'application/json' }, body: JSON.stringify({ equipment: { serial_number: serial, brand, model, equipment_type_id: Number(equipmentTypeId) } }) }); if (r.ok) { setSerial(''); setBrand(''); setModel(''); setShowEquipment(false) } } finally { setCreatingEq(false) } }}>Guardar</Button>
                  </Flex>
                </Flex>
              </Card>
            </div>
          </Portal>
        )}
      </main>
    </div>
  )
}
