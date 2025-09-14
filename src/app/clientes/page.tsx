'use client'
import { useEffect, useState } from 'react'
import { Button, Card, Flex, Heading, Text, TextField } from '@radix-ui/themes'
import Portal from '@/app/shared/ui/Portal'

type Client = { id:string; name:string; contact_details?: Record<string, unknown> | null }

export default function ClientesPage(){
  const [items, setItems] = useState<Client[]>([])
  const [q, setQ] = useState('')
  const [loading, setLoading] = useState(true)
  const [showCreate, setShowCreate] = useState(false)
  const [name, setName] = useState('')

  const load = async (query='') => {
    setLoading(true)
    const r = await fetch(`/api/clients${query?`?q=${encodeURIComponent(query)}`:''}`)
    const j = await r.json()
    setItems(j.items||[])
    setLoading(false)
  }
  useEffect(() => { load() }, [])
  useEffect(() => { const h=setTimeout(()=>load(q),300); return ()=>clearTimeout(h) }, [q])

  return (
    <div className="p-6 space-y-4">
      <Flex justify="between" align="center">
        <Heading size="6" className="font-heading text-primary">Clientes</Heading>
        <Button className="btn-primary" onClick={()=>setShowCreate(true)}>Nuevo Cliente</Button>
      </Flex>
      <TextField.Root className="input-glass" value={q} onChange={e=>setQ(e.target.value)} placeholder="Buscar por nombre"/>
      <Card className="glass p-4">
        {loading ? <Text className="text-muted">Cargando…</Text> : (
          <div className="overflow-auto">
            <div className="min-w-[520px] grid grid-cols-12 gap-2 text-sm text-muted mb-2">
              <div className="col-span-6">Nombre</div>
              <div className="col-span-6">Contacto</div>
            </div>
            {items.map(it => (
              <div key={it.id} className="min-w-[520px] grid grid-cols-12 gap-2 py-2 border-b border-white/10">
                <div className="col-span-6">{it.name}</div>
                <div className="col-span-6">{it.contact_details ? JSON.stringify(it.contact_details) : '-'}</div>
              </div>
            ))}
          </div>
        )}
      </Card>

      {showCreate && (
        <Portal>
          <div className="fixed inset-0 bg-black/40 z-[9999] flex items-center justify-center p-4">
            <Card className="glass p-6 w-full max-w-md">
              <Flex direction="column" gap="3">
                <Heading size="5" className="font-heading text-primary">Nuevo Cliente</Heading>
                <TextField.Root className="input-glass" value={name} onChange={e=>setName(e.target.value)} placeholder="Nombre"/>
                <Flex justify="between" gap="3">
                  <Button className="btn-glass" onClick={()=>{ setShowCreate(false); setName('') }}>Cancelar</Button>
                  <Button className="btn-primary" disabled={!name} onClick={async()=>{ const r=await fetch('/api/clients',{ method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ name })}); if(r.ok){ setShowCreate(false); setName(''); load(q) } }}>Guardar</Button>
                </Flex>
              </Flex>
            </Card>
          </div>
        </Portal>
      )}
    </div>
  )
}
