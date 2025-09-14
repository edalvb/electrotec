'use client'
import { useEffect, useState } from 'react'
import { Button, Card, Flex, Heading, Text, TextField } from '@radix-ui/themes'
import Portal from '@/app/shared/ui/Portal'
import Link from 'next/link'

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
  const [equipmentTypeId, setEquipmentTypeId] = useState<number | ''>('')
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
    setShowCreate(true); 
    const r = await fetch('/api/equipment/types'); 
    const j = await r.json(); 
    setTypes(j.items||[]); 
    if ((j.items||[]).length) setEquipmentTypeId(j.items[0].id) 
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
        <div className="mb-6">
          <div className="relative max-w-md">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg className="w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <TextField.Root 
              className="input-glass pl-10" 
              value={q} 
              onChange={e=>setQ(e.target.value)} 
              placeholder="Buscar por serie, marca o modelo..."
            />
          </div>
        </div>

        {/* Content */}
        <Card className="glass p-6 border-2 border-white/10 backdrop-blur-xl">
          {loading ? (
            <div className="flex items-center justify-center py-12">
              <div className="flex items-center gap-3">
                <div className="w-6 h-6 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <Text className="text-white/70">Cargando equipos...</Text>
              </div>
            </div>
          ) : items.length === 0 ? (
            <div className="text-center py-12">
              <div className="w-16 h-16 mx-auto bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-full flex items-center justify-center mb-4">
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
              <div className="grid grid-cols-12 gap-4 pb-3 border-b border-white/20">
                <div className="col-span-3">
                  <Text className="text-sm font-medium text-white/80">Número de Serie</Text>
                </div>
                <div className="col-span-2">
                  <Text className="text-sm font-medium text-white/80">Marca</Text>
                </div>
                <div className="col-span-2">
                  <Text className="text-sm font-medium text-white/80">Modelo</Text>
                </div>
                <div className="col-span-2">
                  <Text className="text-sm font-medium text-white/80">Tipo</Text>
                </div>
                <div className="col-span-2">
                  <Text className="text-sm font-medium text-white/80">Cliente</Text>
                </div>
                <div className="col-span-1">
                  <Text className="text-sm font-medium text-white/80">Acciones</Text>
                </div>
              </div>

              {/* Table Body */}
              <div className="space-y-2">
                {items.map(equipment => (
                  <div key={equipment.id} className="grid grid-cols-12 gap-4 p-4 rounded-lg bg-white/5 hover:bg-white/10 transition-all duration-200 group">
                    <div className="col-span-3 flex items-center gap-3">
                      <div className="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                        <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={getEquipmentIcon(equipment.equipment_type?.name)} />
                        </svg>
                      </div>
                      <div>
                        <Text className="font-medium text-white">{equipment.serial_number}</Text>
                        <Text className="text-xs text-white/60">ID: {equipment.id.slice(0, 8)}...</Text>
                      </div>
                    </div>
                    <div className="col-span-2 flex items-center">
                      <Text className="text-white/90">{equipment.brand}</Text>
                    </div>
                    <div className="col-span-2 flex items-center">
                      <Text className="text-white/90">{equipment.model}</Text>
                    </div>
                    <div className="col-span-2 flex items-center">
                      <div className="px-2 py-1 rounded-md bg-purple-500/20 text-purple-300 text-xs">
                        {equipment.equipment_type?.name || 'No definido'}
                      </div>
                    </div>
                    <div className="col-span-2 flex items-center">
                      <Text className="text-white/70">
                        {equipment.client?.name || 'Sin asignar'}
                      </Text>
                    </div>
                    <div className="col-span-1 flex items-center">
                      <Button 
                        className="btn-glass p-2 opacity-0 group-hover:opacity-100 transition-opacity"
                        onClick={() => {/* TODO: Edit functionality */}}
                      >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </Button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </Card>

        {/* Create Modal */}
        {showCreate && (
          <Portal>
            <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center p-4 backdrop-blur-sm">
              <Card className="glass p-6 w-full max-w-md border-2 border-white/20">
                <Flex direction="column" gap="4">
                  <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                      <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <Heading size="5" className="font-heading text-white">Nuevo Equipo</Heading>
                  </div>
                  <div className="space-y-3">
                    <div>
                      <Text className="text-sm font-medium text-white/90 mb-2">Número de serie</Text>
                      <TextField.Root 
                        className="input-glass" 
                        value={serial} 
                        onChange={e=>setSerial(e.target.value)} 
                        placeholder="Número de serie del equipo"
                      />
                    </div>
                    <div>
                      <Text className="text-sm font-medium text-white/90 mb-2">Marca</Text>
                      <TextField.Root 
                        className="input-glass" 
                        value={brand} 
                        onChange={e=>setBrand(e.target.value)} 
                        placeholder="Marca del equipo"
                      />
                    </div>
                    <div>
                      <Text className="text-sm font-medium text-white/90 mb-2">Modelo</Text>
                      <TextField.Root 
                        className="input-glass" 
                        value={model} 
                        onChange={e=>setModel(e.target.value)} 
                        placeholder="Modelo del equipo"
                      />
                    </div>
                    <div>
                      <Text className="text-sm font-medium text-white/90 mb-2">Tipo de equipo</Text>
                      <select 
                        className="w-full input-glass p-2 rounded-lg" 
                        value={equipmentTypeId === '' ? '' : String(equipmentTypeId)} 
                        onChange={e=>setEquipmentTypeId(Number(e.target.value))}
                      >
                        {types.map(t => (<option key={t.id} value={t.id}>{t.name}</option>))}
                      </select>
                    </div>
                  </div>
                  <Flex justify="between" gap="3" className="mt-6">
                    <Button 
                      className="btn-glass flex-1" 
                      onClick={()=>{ 
                        setShowCreate(false); 
                        setSerial(''); 
                        setBrand(''); 
                        setModel('') 
                      }}
                    >
                      Cancelar
                    </Button>
                    <Button 
                      className="btn-primary flex-1 bg-gradient-to-r from-purple-600 to-pink-600" 
                      disabled={!can} 
                      onClick={async ()=>{ 
                        const r=await fetch('/api/equipment',{ 
                          method:'POST', 
                          headers:{'Content-Type':'application/json'}, 
                          body: JSON.stringify({ 
                            equipment:{ 
                              serial_number:serial, 
                              brand, 
                              model, 
                              equipment_type_id:Number(equipmentTypeId) 
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
                      }}
                    >
                      Guardar
                    </Button>
                  </Flex>
                </Flex>
              </Card>
            </div>
          </Portal>
        )}
      </div>
    </div>
  )
}
