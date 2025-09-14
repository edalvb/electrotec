'use client'
import { useEffect, useState } from 'react'
import { Button, Card, Flex, Heading, Text, TextField } from '@radix-ui/themes'
import Portal from '@/app/shared/ui/Portal'

type Item = { id:string; serial_number:string; brand:string; model:string; client: { id:string; name:string } | null; equipment_type: { id:number; name:string } | null }

export default function EquiposPage(){
  const [items, setItems] = useState<Item[]>([])
  const [q, setQ] = useState('')
  const [loading, setLoading] = useState(true)
  const [showCreate, setShowCreate] = useState(false)
  const [serial, setSerial] = useState('')
  const [brand, setBrand] = useState('')
  const [model, setModel] = useState('')
  const [types, setTypes] = useState<{ id:number; name:string }[]>([])
  const [equipmentTypeId, setEquipmentTypeId] = useState<number | ''>('' as any)
  const can = serial && brand && model && equipmentTypeId

  const load = async (query='') => {
    setLoading(true)
    const r = await fetch(`/api/equipment${query?`?q=${encodeURIComponent(query)}`:''}`)
    const j = await r.json()
    setItems(j.items||[])
    setLoading(false)
  }
  useEffect(() => { load() }, [])
  useEffect(() => { const h=setTimeout(()=>load(q),300); return ()=>clearTimeout(h) }, [q])

  const openCreate = async () => { setShowCreate(true); const r = await fetch('/api/equipment/types'); const j = await r.json(); setTypes(j.items||[]); if ((j.items||[]).length) setEquipmentTypeId(j.items[0].id) }

  return (
    <div className="p-6 space-y-4">
      <Flex justify="between" align="center">
        <Heading size="6" className="font-heading text-primary">Equipos</Heading>
        <Button className="btn-primary" onClick={openCreate}>Nuevo Equipo</Button>
      </Flex>
      <TextField.Root className="input-glass" value={q} onChange={e=>setQ(e.target.value)} placeholder="Buscar por serie/marca/modelo"/>
      <Card className="glass p-4">
        {loading ? <Text className="text-muted">Cargando…</Text> : (
          <div className="overflow-auto">
            <div className="min-w-[720px] grid grid-cols-12 gap-2 text-sm text-muted mb-2">
              <div className="col-span-3">Serie</div>
              <div className="col-span-3">Marca</div>
              <div className="col-span-3">Modelo</div>
              <div className="col-span-3">Tipo / Cliente</div>
            </div>
            {items.map(it => (
              <div key={it.id} className="min-w-[720px] grid grid-cols-12 gap-2 py-2 border-b border-white/10">
                <div className="col-span-3">{it.serial_number}</div>
                <div className="col-span-3">{it.brand}</div>
                <div className="col-span-3">{it.model}</div>
                <div className="col-span-3">{it.equipment_type?.name || '-'} / {it.client?.name || '-'}</div>
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
                <Heading size="5" className="font-heading text-primary">Nuevo Equipo</Heading>
                <TextField.Root className="input-glass" value={serial} onChange={e=>setSerial(e.target.value)} placeholder="Número de serie"/>
                <TextField.Root className="input-glass" value={brand} onChange={e=>setBrand(e.target.value)} placeholder="Marca"/>
                <TextField.Root className="input-glass" value={model} onChange={e=>setModel(e.target.value)} placeholder="Modelo"/>
                <div>
                  <Text className="text-muted">Tipo de equipo</Text>
                  <select className="mt-1 w-full input-glass" value={equipmentTypeId as any} onChange={e=>setEquipmentTypeId(Number(e.target.value))}>
                    {types.map(t => (<option key={t.id} value={t.id}>{t.name}</option>))}
                  </select>
                </div>
                <Flex justify="between" gap="3">
                  <Button className="btn-glass" onClick={()=>{ setShowCreate(false); setSerial(''); setBrand(''); setModel('') }}>Cancelar</Button>
                  <Button className="btn-primary" disabled={!can} onClick={async ()=>{ const r=await fetch('/api/equipment',{ method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ equipment:{ serial_number:serial, brand, model, equipment_type_id:Number(equipmentTypeId) } })}); if(r.ok){ setShowCreate(false); setSerial(''); setBrand(''); setModel(''); load(q) } }}>Guardar</Button>
                </Flex>
              </Flex>
            </Card>
          </div>
        </Portal>
      )}
    </div>
  )
}
