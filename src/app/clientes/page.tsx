'use client'
import { useEffect, useState } from 'react'
import { Flex, Heading, Text } from '@radix-ui/themes'
import { ModernButton, ModernCard, ModernInput, ModernModal } from '@/app/shared/ui'
import Link from 'next/link'

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
              <Heading size="7" className="font-heading bg-gradient-to-r from-green-400 via-teal-400 to-cyan-400 bg-clip-text text-transparent">
                Gestión de Clientes
              </Heading>
            </div>
            <Text className="text-white/60 text-lg">Administra la información de tus clientes</Text>
          </div>
          <ModernButton 
            variant="primary"
            onClick={() => setShowCreate(true)}
          >
            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Cliente
          </ModernButton>
        </div>

        {/* Search */}
        <div className="mb-6">
          <div className="relative max-w-md">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg className="w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <ModernInput
              value={q}
              onChange={(e) => setQ(e.target.value)}
              placeholder="Buscar clientes por nombre..."
              icon={
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              }
            />
          </div>
        </div>

        {/* Content */}
        <ModernCard className="p-6">
          {loading ? (
            <div className="flex items-center justify-center py-12">
              <div className="flex items-center gap-3">
                <div className="w-6 h-6 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <Text className="text-white/70">Cargando clientes...</Text>
              </div>
            </div>
          ) : items.length === 0 ? (
            <div className="text-center py-12">
              <div className="w-16 h-16 mx-auto bg-gradient-to-br from-green-500/20 to-teal-500/20 rounded-full flex items-center justify-center mb-4">
                <svg className="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <Heading size="4" className="text-white/90 mb-2">No hay clientes registrados</Heading>
              <Text className="text-white/60 mb-4">Comienza creando tu primer cliente</Text>
              <ModernButton 
                variant="primary"
                onClick={() => setShowCreate(true)}
              >
                Crear primer cliente
              </ModernButton>
            </div>
          ) : (
            <div className="space-y-4">
              {/* Table Header */}
              <div className="grid grid-cols-12 gap-4 pb-3 border-b border-white/20">
                <div className="col-span-6">
                  <Text className="text-sm font-medium text-white/80">Nombre del Cliente</Text>
                </div>
                <div className="col-span-4">
                  <Text className="text-sm font-medium text-white/80">Información de Contacto</Text>
                </div>
                <div className="col-span-2">
                  <Text className="text-sm font-medium text-white/80">Acciones</Text>
                </div>
              </div>

              {/* Table Body */}
              <div className="space-y-2">
                {items.map(client => (
                  <div key={client.id} className="grid grid-cols-12 gap-4 p-4 rounded-lg bg-white/5 hover:bg-white/10 transition-all duration-200 group">
                    <div className="col-span-6 flex items-center gap-3">
                      <div className="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                        <span className="text-white font-medium text-sm">
                          {client.name.charAt(0).toUpperCase()}
                        </span>
                      </div>
                      <div>
                        <Text className="font-medium text-white">{client.name}</Text>
                        <Text className="text-xs text-white/60">ID: {client.id.slice(0, 8)}...</Text>
                      </div>
                    </div>
                    <div className="col-span-4 flex items-center">
                      <Text className="text-white/70">
                        {client.contact_details ? 'Información disponible' : 'Sin información de contacto'}
                      </Text>
                    </div>
                    <div className="col-span-2 flex items-center gap-2">
                      <ModernButton 
                        variant="glass"
                        size="sm"
                        className="p-2 opacity-0 group-hover:opacity-100 transition-opacity"
                        onClick={() => {/* TODO: Edit functionality */}}
                      >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                      </ModernButton>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </ModernCard>

        {/* Create Modal */}
        <ModernModal
          open={showCreate}
          onOpenChange={setShowCreate}
          title="Nuevo Cliente"
          description="Completa la información para crear un nuevo cliente"
        >
          <div className="space-y-4">
            <ModernInput
              value={name}
              onChange={(e) => setName(e.target.value)}
              label="Nombre del cliente"
              placeholder="Ingrese el nombre del cliente"
              icon={
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              }
            />
            
            <Flex justify="between" gap="3" className="mt-6">
              <ModernButton 
                variant="glass"
                className="flex-1"
                onClick={() => { setShowCreate(false); setName('') }}
              >
                Cancelar
              </ModernButton>
              <ModernButton 
                variant="primary"
                className="flex-1"
                disabled={!name}
                onClick={async () => { 
                  const r = await fetch('/api/clients', { 
                    method: 'POST', 
                    headers: { 'Content-Type': 'application/json' }, 
                    body: JSON.stringify({ name })
                  }); 
                  if (r.ok) { 
                    setShowCreate(false); 
                    setName(''); 
                    load(q) 
                  } 
                }}
              >
                Guardar
              </ModernButton>
            </Flex>
          </div>
        </ModernModal>
      </div>
    </div>
  )
}
