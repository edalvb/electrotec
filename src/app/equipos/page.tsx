'use client'
import { useEffect, useState } from 'react'
import { Button, Card, Flex, Heading, Text } from '@radix-ui/themes'
import Portal from '@/app/shared/ui/Portal'
import Link from 'next/link'
import ModernInput from '@/app/shared/ui/ModernInput'

type Item = { id:string; serial_number:string; brand:string; model:string; client: { id:string; name:string } | null; equipment_type: { id:number; name:string } | null; deletable?: boolean }
type Client = { id:string; name:string }

export default function EquiposPage(){
  const [items, setItems] = useState<Item[]>([])
  const [q, setQ] = useState('')
  const [loading, setLoading] = useState(true)
  const [showCreate, setShowCreate] = useState(false)
  const [serial, setSerial] = useState('')
  const [brand, setBrand] = useState('')
  const [model, setModel] = useState('')
  const [types, setTypes] = useState<{ id:number; name:string }[]>([])
  const [equipmentTypeId, setEquipmentTypeId] = useState<number | ''>('')
  const [editingId, setEditingId] = useState<string | null>(null)
  const [editingClient, setEditingClient] = useState<Client | null>(null)
  const [clientPickerOpen, setClientPickerOpen] = useState(false)
  const [clients, setClients] = useState<Client[]>([])
  const [clientQ, setClientQ] = useState('')
  const [clientPage, setClientPage] = useState(1)
  const [clientTotalPages, setClientTotalPages] = useState(1)
  const pageSize = 10
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

  const openCreate = async () => { 
    setEditingId(null)
    setSerial('')
    setBrand('')
    setModel('')
    setEquipmentTypeId('')
    setEditingClient(null)
    setShowCreate(true); 
    const r = await fetch('/api/equipment/types'); 
    const j = await r.json(); 
    setTypes(j.items||[]); 
    if ((j.items||[]).length) setEquipmentTypeId(j.items[0].id) 
  }

  const openEdit = async (item: Item) => {
    setEditingId(item.id)
    setSerial(item.serial_number)
    setBrand(item.brand)
    setModel(item.model)
    setEditingClient(item.client ? { id: item.client.id, name: item.client.name } : null)
    setShowCreate(true)
    const r = await fetch('/api/equipment/types')
    const j = await r.json()
    setTypes(j.items||[])
    const currentTypeId = item.equipment_type?.id
    if (currentTypeId) setEquipmentTypeId(currentTypeId)
  }

  const loadClients = async (page=1, q='') => {
    const r = await fetch(`/api/clients?page=${page}&pageSize=${pageSize}${q?`&q=${encodeURIComponent(q)}`:''}`)
    const j = await r.json()
    setClients(j.items||[])
    setClientTotalPages(j.pagination?.totalPages || 1)
  }

  const getEquipmentIcon = (typeName?: string) => {
    switch(typeName?.toLowerCase()) {
      case 'nivel':
        return 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'
      case 'multímetro':
        return 'M13 10V3L4 14h7v7l9-11h-7z'
      default:
        return 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'
    }
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-indigo-950">
      <div className="container mx-auto p-8 max-w-7xl">
        {/* Header */}
        <div className="flex justify-between items-center mb-8">
          <div className="space-y-2">
            <div className="flex items-center gap-3">
              <Link href="/" className="text-white/60 hover:text-white transition-colors">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                </svg>
              </Link>
              <Heading size="7" className="font-heading bg-gradient-to-r from-purple-400 via-pink-400 to-red-400 bg-clip-text text-transparent">
                Gestión de Equipos
              </Heading>
            </div>
            <Text className="text-white/60 text-lg">Administra el inventario de equipos de medición</Text>
          </div>
          <Button 
            className="btn-primary px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 transition-all transform hover:scale-105"
            onClick={openCreate}
          >
            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Equipo
          </Button>
        </div>

        {/* Search */}
        <div className="mb-6 max-w-md">
          <ModernInput
            value={q}
            onChange={e=>setQ(e.target.value)}
            placeholder="Buscar por serie, marca o modelo..."
            icon={(
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            )}
          />
        </div>

        {/* Content */}
        <Card className="glass p-6 rounded-2xl border border-slate-800/60 bg-slate-900/40 backdrop-blur-xl shadow-2xl">
          {loading ? (
            <div className="flex items-center justify-center py-12">
              <div className="flex items-center gap-3">
                <div className="w-6 h-6 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <Text className="text-white/70">Cargando equipos...</Text>
              </div>
            </div>
          ) : items.length === 0 ? (
            <div className="text-center py-12">
              <div className="w-16 h-16 mx-auto bg-gradient-to-br from-purple-500/30 to-pink-500/30 rounded-full flex items-center justify-center mb-4">
                <svg className="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <Heading size="4" className="text-white/90 mb-2">No hay equipos registrados</Heading>
              <Text className="text-white/60 mb-4">Comienza registrando tu primer equipo</Text>
              <Button 
                className="btn-primary bg-gradient-to-r from-purple-600 to-pink-600"
                onClick={openCreate}
              >
                Registrar primer equipo
              </Button>
            </div>
          ) : (
            <div className="space-y-4">
              {/* Table Header */}
              <div className="hidden md:grid grid-cols-12 gap-4 pb-3 border-b border-slate-700/60">
                <div className="col-span-3">
                  <Text className="text-sm font-medium text-white/90">Número de Serie</Text>
                </div>
                <div className="col-span-2">
                  <Text className="text-sm font-medium text-white/90">Marca</Text>
                </div>
                <div className="col-span-2">
                  <Text className="text-sm font-medium text-white/90">Modelo</Text>
                </div>
                <div className="col-span-2">
                  <Text className="text-sm font-medium text-white/90">Tipo</Text>
                </div>
                <div className="col-span-2">
                  <Text className="text-sm font-medium text-white/90">Cliente</Text>
                </div>
                <div className="col-span-1">
                  <Text className="text-sm font-medium text-white/90">Acciones</Text>
                </div>
              </div>

              {/* Table Body */}
              <div className="space-y-2">
                {items.map(equipment => (
                  <div key={equipment.id} className="grid grid-cols-1 md:grid-cols-12 gap-4 p-4 rounded-xl bg-slate-900/50 hover:bg-slate-900/70 border border-slate-800/60 transition-all duration-200 group">
                    <div className="md:col-span-3">
                      <div className="md:hidden text-xs text-white/60 mb-1">Número de Serie</div>
                      <div className="flex items-center gap-3">
                      <div className="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                        <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={getEquipmentIcon(equipment.equipment_type?.name)} />
                        </svg>
                      </div>
                      <div>
                        <Text className="font-medium text-white">{equipment.serial_number}</Text>
                      </div>
                      </div>
                    </div>
                    <div className="md:col-span-2">
                      <div className="md:hidden text-xs text-white/60 mb-1">Marca</div>
                      <div className="flex items-center">
                        <Text className="text-white/90">{equipment.brand}</Text>
                      </div>
                    </div>
                    <div className="md:col-span-2">
                      <div className="md:hidden text-xs text-white/60 mb-1">Modelo</div>
                      <div className="flex items-center">
                        <Text className="text-white/90">{equipment.model}</Text>
                      </div>
                    </div>
                    <div className="md:col-span-2">
                      <div className="md:hidden text-xs text-white/60 mb-1">Tipo</div>
                      <div className="flex items-center">
                        <div className="px-2 py-1 rounded-md bg-purple-500/25 text-purple-200 text-xs border border-purple-400/20">
                          {equipment.equipment_type?.name || 'No definido'}
                        </div>
                      </div>
                    </div>
                    <div className="md:col-span-2">
                      <div className="md:hidden text-xs text-white/60 mb-1">Cliente</div>
                      <div className="flex items-center">
                        <Text className="text-white/80">
                          {equipment.client?.name || 'Sin asignar'}
                        </Text>
                      </div>
                    </div>
                    <div className="md:col-span-1 flex items-center gap-2 justify-end md:justify-start">
                      <Button 
                        className="btn-glass p-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity"
                        onClick={() => openEdit(equipment)}
                        title="Editar"
                      >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </Button>
                      {equipment.deletable !== false && (
                        <Button 
                          className="btn-glass p-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity text-red-300 hover:text-red-200"
                          onClick={async () => {
                            const ok = window.confirm('¿Eliminar este equipo? Esta acción no se puede deshacer.')
                            if (!ok) return
                            const r = await fetch(`/api/equipment/${equipment.id}`, { method: 'DELETE' })
                            if (r.ok) {
                              load(q)
                              return
                            }
                            const err = await r.json().catch(()=>({}))
                            const code = err?.error || 'error'
                            if (code === 'has_linked_certificates') {
                              alert('No se puede eliminar: el equipo tiene certificados vinculados.')
                            } else {
                              alert('No se pudo eliminar el equipo.')
                            }
                          }}
                          title="Eliminar"
                        >
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2M4 7h16" />
                          </svg>
                        </Button>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </Card>

        {/* Create/Edit Modal */}
        {showCreate && (
          <Portal>
            <div className="fixed inset-0 bg-black/60 z-[9999] flex items-center justify-center p-4 backdrop-blur-sm">
              <Card className="glass p-0 w-full max-w-md rounded-2xl border border-slate-800/70 ring-1 ring-slate-700/40 bg-slate-900/80 shadow-2xl overflow-hidden">
                <Flex direction="column" gap="0">
                  <div className="flex items-center gap-3 p-6 border-b border-slate-800/60 bg-slate-900/70">
                    <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                      <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <Heading size="5" className="font-heading text-white">{editingId ? 'Editar Equipo' : 'Nuevo Equipo'}</Heading>
                  </div>
                  <div className="p-6 space-y-3">
                    <ModernInput
                      label="Número de serie"
                      value={serial}
                      onChange={e=>setSerial(e.target.value)}
                      placeholder="Número de serie del equipo"
                    />
                    <ModernInput
                      label="Marca"
                      value={brand}
                      onChange={e=>setBrand(e.target.value)}
                      placeholder="Marca del equipo"
                    />
                    <ModernInput
                      label="Modelo"
                      value={model}
                      onChange={e=>setModel(e.target.value)}
                      placeholder="Modelo del equipo"
                    />
                    <div>
                      <Text className="text-sm font-medium text-white/90 mb-2 block">Tipo de equipo</Text>
                      <select 
                        className="w-full input-glass p-2 rounded-lg bg-slate-900/60 border border-slate-800/60 focus:outline-none focus:ring-2 focus:ring-purple-500/40" 
                        value={equipmentTypeId === '' ? '' : String(equipmentTypeId)} 
                        onChange={e=>setEquipmentTypeId(Number(e.target.value))}
                      >
                        {types.map(t => (<option key={t.id} value={t.id}>{t.name}</option>))}
                      </select>
                    </div>
                    <div className="flex items-center justify-between gap-3 p-3 rounded-lg bg-slate-900/50 border border-slate-800/60">
                      <div className="flex items-center gap-2">
                        <svg className="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <Text className="text-sm text-white/80">{editingClient ? `Cliente: ${editingClient.name}` : 'Cliente no vinculado'}</Text>
                      </div>
                      <Button className="btn-glass" onClick={async ()=>{ setClientPage(1); setClientQ(''); await loadClients(1, ''); setClientPickerOpen(true) }}>Seleccionar</Button>
                    </div>
                  </div>
                  <Flex justify="between" gap="3" className="p-6 border-t border-slate-800/60 bg-slate-900/70">
                    <Button 
                      className="btn-glass flex-1" 
                      onClick={()=>{ 
                        setShowCreate(false); 
                        setSerial(''); 
                        setBrand(''); 
                        setModel('');
                        setEditingId(null)
                        setEditingClient(null)
                      }}
                    >
                      Cancelar
                    </Button>
                    <Button 
                      className="btn-primary flex-1 bg-gradient-to-r from-purple-600 to-pink-600" 
                      disabled={!can} 
                      onClick={async ()=>{ 
                        if (editingId) {
                          const r = await fetch(`/api/equipment/${editingId}`, {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                              serial_number: serial,
                              brand,
                              model,
                              equipment_type_id: Number(equipmentTypeId)
                            })
                          })
                          if (r.ok) {
                            setShowCreate(false)
                            setSerial('')
                            setBrand('')
                            setModel('')
                            setEditingId(null)
                            setEditingClient(null)
                            load(q)
                          }
                        } else {
                          const r=await fetch('/api/equipment',{ 
                            method:'POST', 
                            headers:{'Content-Type':'application/json'}, 
                             body: JSON.stringify({ 
                               equipment:{ 
                                 serial_number:serial, 
                                 brand, 
                                 model, 
                                 equipment_type_id:Number(equipmentTypeId),
                                 owner_client_id: editingClient?.id ?? null
                               } 
                             })
                          }); 
                          if(r.ok){ 
                            setShowCreate(false); 
                            setSerial(''); 
                            setBrand(''); 
                            setModel(''); 
                            load(q) 
                          } 
                        }
                      }}
                    >
                      {editingId ? 'Guardar cambios' : 'Guardar'}
                    </Button>
                  </Flex>
                </Flex>
              </Card>
            </div>
          </Portal>
        )}

        {clientPickerOpen && (
          <Portal>
            <div className="fixed inset-0 bg-black/60 z-[10000] flex items-center justify-center p-4 backdrop-blur-sm">
              <Card className="glass p-0 w-full max-w-2xl rounded-2xl border border-slate-800/70 ring-1 ring-slate-700/40 bg-slate-900/80 shadow-2xl overflow-hidden">
                <Flex direction="column" gap="0">
                  <div className="flex items-center justify-between p-6 border-b border-slate-800/60 bg-slate-900/70">
                    <Heading size="5" className="font-heading text-white">Seleccionar cliente</Heading>
                    <Button className="btn-glass" onClick={()=>setClientPickerOpen(false)}>Cerrar</Button>
                  </div>
                  <div className="p-6 border-b border-slate-800/60">
                    <ModernInput
                      value={clientQ}
                      onChange={async e=>{ const v=e.target.value; setClientQ(v); setClientPage(1); await loadClients(1, v) }}
                      placeholder="Buscar clientes por nombre"
                      icon={(
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                      )}
                    />
                  </div>
                  <div className="p-6">
                    <div className="border border-slate-800/60 rounded-xl overflow-hidden">
                      <div className="grid grid-cols-12 gap-4 py-3 px-4 bg-slate-900/70 border-b border-slate-800/60">
                        <div className="col-span-9"><Text className="text-sm text-white/90">Nombre</Text></div>
                        <div className="col-span-3"><Text className="text-sm text-white/90">Acciones</Text></div>
                      </div>
                      <div className="max-h-80 overflow-auto space-y-2 p-4">
                        {clients.map(c => (
                          <div key={c.id} className="grid grid-cols-12 gap-4 p-3 rounded-xl bg-slate-900/50 hover:bg-slate-900/70 border border-slate-800/60">
                            <div className="col-span-9"><Text className="text-white/90">{c.name}</Text></div>
                            <div className="col-span-3 flex justify-end">
                              <Button className="btn-primary bg-gradient-to-r from-purple-600 to-pink-600" onClick={async ()=>{
                                if (!editingId) {
                                  setEditingClient({ id: c.id, name: c.name })
                                  setClientPickerOpen(false)
                                  return
                                }
                                const confirmMsg = `¿Vincular el equipo al cliente "${c.name}"?`
                                const ok = window.confirm(confirmMsg)
                                if (!ok) return
                                const r = await fetch(`/api/equipment/${editingId}`, { method:'PATCH', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ owner_client_id: c.id }) })
                                if (r.ok) {
                                  setEditingClient({ id: c.id, name: c.name })
                                  setClientPickerOpen(false)
                                  load(q)
                                }
                              }}>Seleccionar</Button>
                            </div>
                          </div>
                        ))}
                        {clients.length===0 && (
                          <div className="p-6 text-center text-white/60">Sin resultados</div>
                        )}
                      </div>
                    </div>
                    <div className="flex items-center justify-between pt-4">
                      <Button className="btn-glass" disabled={clientPage<=1} onClick={async ()=>{ const p=clientPage-1; setClientPage(p); await loadClients(p, clientQ) }}>Anterior</Button>
                      <Text className="text-white/70">Página {clientPage} de {clientTotalPages}</Text>
                      <Button className="btn-glass" disabled={clientPage>=clientTotalPages} onClick={async ()=>{ const p=clientPage+1; setClientPage(p); await loadClients(p, clientQ) }}>Siguiente</Button>
                    </div>
                  </div>
                </Flex>
              </Card>
            </div>
          </Portal>
        )}
      </div>
    </div>
  )
}
