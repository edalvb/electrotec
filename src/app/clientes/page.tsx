'use client'
import { useEffect, useState, ChangeEvent } from 'react'
import { Flex, Heading, Text } from '@radix-ui/themes'
import { ModernButton, ModernCard, ModernInput, ModernModal } from '@/app/shared/ui'
import Link from 'next/link'

// Podemos tipar contact_details de forma flexible
type ContactDetails = Record<string, unknown> | null | undefined

type Client = { id: string; name: string; contact_details?: ContactDetails }

export default function ClientesPage() {
  const [items, setItems] = useState<Client[]>([])
  const [q, setQ] = useState('')
  const [loading, setLoading] = useState(true)

  // Crear
  const [showCreate, setShowCreate] = useState(false)
  const [name, setName] = useState('')
  const [ruc, setRuc] = useState('')
  const [dni, setDni] = useState('')
  const [phone, setPhone] = useState('')
  const [email, setEmail] = useState('')

  // Editar
  const [showEdit, setShowEdit] = useState(false)
  const [editing, setEditing] = useState<Client | null>(null)
  const [editName, setEditName] = useState('')
  const [editRuc, setEditRuc] = useState('')
  const [editDni, setEditDni] = useState('')
  const [editPhone, setEditPhone] = useState('')
  const [editEmail, setEditEmail] = useState('')

  // Eliminar
  const [showDelete, setShowDelete] = useState(false)
  const [deleting, setDeleting] = useState<Client | null>(null)

  const load = async (query = '') => {
    setLoading(true)
    const r = await fetch(`/api/clients${query ? `?q=${encodeURIComponent(query)}` : ''}`)
    const j = await r.json()
    setItems(j.items || [])
    setLoading(false)
  }

  useEffect(() => {
    load()
  }, [])

  useEffect(() => {
    const h = setTimeout(() => load(q), 300)
    return () => clearTimeout(h)
  }, [q])

  // Construye contact_details sin campos vacíos (todos opcionales)
  const buildContactDetails = (vals: { ruc?: string; dni?: string; phone?: string; email?: string }) => {
    const out: Record<string, string> = {}
    if (vals.ruc && vals.ruc.trim()) out.ruc = vals.ruc.trim()
    if (vals.dni && vals.dni.trim()) out.dni = vals.dni.trim()
    if (vals.phone && vals.phone.trim()) out.phone = vals.phone.trim()
    if (vals.email && vals.email.trim()) out.email = vals.email.trim()
    return Object.keys(out).length ? out : null
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
              <Heading size="7" className="font-heading bg-gradient-to-r from-green-400 via-teal-400 to-cyan-400 bg-clip-text text-transparent">
                Gestión de Clientes
              </Heading>
            </div>
            <Text className="text-white/60 text-lg">Administra la información de tus clientes</Text>
          </div>
          <ModernButton variant="primary" onClick={() => setShowCreate(true)}>
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
              onChange={(e: ChangeEvent<HTMLInputElement>) => setQ(e.target.value)}
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
              <Heading size="4" className="text-white/90 mb-2">
                No hay clientes registrados
              </Heading>
              <Text className="text-white/60 mb-4">Comienza creando tu primer cliente</Text>
              <ModernButton variant="primary" onClick={() => setShowCreate(true)}>
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
                {items.map((client) => (
                  <div
                    key={client.id}
                    className="grid grid-cols-12 gap-4 p-4 rounded-lg bg-white/5 hover:bg-white/10 transition-all duration-200 group"
                  >
                    <div className="col-span-6 flex items-center gap-3">
                      <div className="w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                        <span className="text-white font-medium text-sm">
                          {client.name.charAt(0).toUpperCase()}
                        </span>
                      </div>
                      <div>
                        <Text className="font-medium text-white">{client.name}</Text>
                      </div>
                    </div>
                    <div className="col-span-4 flex items-center">
                      {(() => {
                        const cd = (client.contact_details || {}) as Record<string, unknown>
                        const parts: string[] = []
                        if (typeof cd.ruc === 'string' && cd.ruc.trim()) parts.push(`RUC: ${cd.ruc}`)
                        if (typeof cd.dni === 'string' && cd.dni.trim()) parts.push(`DNI: ${cd.dni}`)
                        if (typeof cd.email === 'string' && cd.email.trim()) parts.push(cd.email)
                        if (typeof cd.phone === 'string' && cd.phone.trim()) parts.push(cd.phone)
                        const visible = parts.slice(0, 2).join(' · ')
                        return (
                          <Text className="text-white/70">
                            {visible || 'Sin información de contacto'}
                          </Text>
                        )
                      })()}
                    </div>
                    <div className="col-span-2 flex items-center gap-2">
                      {/* Edit */}
                      <ModernButton
                        variant="glass"
                        size="sm"
                        className="p-2 opacity-0 group-hover:opacity-100 transition-opacity"
                        onClick={() => {
                          setEditing(client)
                          setEditName(client.name)
                          const cd = (client.contact_details || {}) as Record<string, unknown>
                          setEditRuc(typeof cd.ruc === 'string' ? cd.ruc : '')
                          setEditDni(typeof cd.dni === 'string' ? cd.dni : '')
                          setEditPhone(typeof cd.phone === 'string' ? cd.phone : '')
                          setEditEmail(typeof cd.email === 'string' ? cd.email : '')
                          setShowEdit(true)
                        }}
                      >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                          />
                        </svg>
                      </ModernButton>
                      {/* Delete */}
                      <ModernButton
                        variant="glass"
                        size="sm"
                        className="p-2 opacity-0 group-hover:opacity-100 transition-opacity"
                        onClick={() => {
                          setDeleting(client)
                          setShowDelete(true)
                        }}
                      >
                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-1-3H10a1 1 0 00-1 1v1h6V5a1 1 0 00-1-1z"
                          />
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
          onOpenChange={(v) => {
            setShowCreate(v)
            if (!v) {
              setName('')
              setRuc('')
              setDni('')
              setPhone('')
              setEmail('')
            }
          }}
          title="Nuevo Cliente"
          description="Completa la información para crear un nuevo cliente"
        >
          <div className="space-y-4">
            <ModernInput
              value={name}
              onChange={(e: ChangeEvent<HTMLInputElement>) => setName(e.target.value)}
              label="Nombre del cliente"
              placeholder="Ingrese el nombre del cliente"
              icon={
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              }
            />
            {/* Campos de contacto opcionales */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <ModernInput
                value={ruc}
                onChange={(e: ChangeEvent<HTMLInputElement>) => setRuc(e.target.value)}
                label="RUC"
                placeholder="RUC del cliente"
              />
              <ModernInput
                value={dni}
                onChange={(e: ChangeEvent<HTMLInputElement>) => setDni(e.target.value)}
                label="DNI"
                placeholder="DNI del cliente"
              />
              <ModernInput
                value={phone}
                onChange={(e: ChangeEvent<HTMLInputElement>) => setPhone(e.target.value)}
                label="Celular"
                placeholder="Número de celular"
              />
              <ModernInput
                value={email}
                onChange={(e: ChangeEvent<HTMLInputElement>) => setEmail(e.target.value)}
                label="Email"
                placeholder="correo@dominio.com"
                type="email"
              />
            </div>

            <Flex justify="between" gap="3" className="mt-6">
              <ModernButton
                variant="glass"
                className="flex-1"
                onClick={() => {
                  setShowCreate(false)
                  setName('')
                  setRuc('')
                  setDni('')
                  setPhone('')
                  setEmail('')
                }}
              >
                Cancelar
              </ModernButton>
              <ModernButton
                variant="primary"
                className="flex-1"
                disabled={!name}
                onClick={async () => {
                  const contact = buildContactDetails({ ruc, dni, phone, email })
                  const body: { name: string; contact_details?: Record<string, string> } = { name }
                  if (contact) body.contact_details = contact
                  const r = await fetch('/api/clients', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                  })
                  if (r.ok) {
                    setShowCreate(false)
                    setName('')
                    setRuc('')
                    setDni('')
                    setPhone('')
                    setEmail('')
                    load(q)
                  }
                }}
              >
                Guardar
              </ModernButton>
            </Flex>
          </div>
        </ModernModal>

        {/* Edit Modal */}
        <ModernModal
          open={showEdit}
          onOpenChange={(v) => {
            setShowEdit(v)
            if (!v) setEditing(null)
          }}
          title="Editar Cliente"
          description={editing ? `Actualiza la información de ${editing.name}` : ''}
        >
          <div className="space-y-4">
            <ModernInput
              value={editName}
              onChange={(e: ChangeEvent<HTMLInputElement>) => setEditName(e.target.value)}
              label="Nombre del cliente"
              placeholder="Ingrese el nombre del cliente"
            />
            {/* Campos de contacto opcionales */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <ModernInput
                value={editRuc}
                onChange={(e: ChangeEvent<HTMLInputElement>) => setEditRuc(e.target.value)}
                label="RUC"
                placeholder="RUC del cliente"
              />
              <ModernInput
                value={editDni}
                onChange={(e: ChangeEvent<HTMLInputElement>) => setEditDni(e.target.value)}
                label="DNI"
                placeholder="DNI del cliente"
              />
              <ModernInput
                value={editPhone}
                onChange={(e: ChangeEvent<HTMLInputElement>) => setEditPhone(e.target.value)}
                label="Celular"
                placeholder="Número de celular"
              />
              <ModernInput
                value={editEmail}
                onChange={(e: ChangeEvent<HTMLInputElement>) => setEditEmail(e.target.value)}
                label="Email"
                placeholder="correo@dominio.com"
                type="email"
              />
            </div>

            <Flex justify="between" gap="3" className="mt-6">
              <ModernButton
                variant="glass"
                className="flex-1"
                onClick={() => {
                  setShowEdit(false)
                  setEditing(null)
                }}
              >
                Cancelar
              </ModernButton>
              <ModernButton
                variant="primary"
                className="flex-1"
                disabled={!editName || !editing}
                onClick={async () => {
                  if (!editing) return
                  const contact = buildContactDetails({ ruc: editRuc, dni: editDni, phone: editPhone, email: editEmail })
                  const body: { name: string; contact_details: Record<string, string> | null } = { name: editName, contact_details: contact ?? null }
                  const r = await fetch(`/api/clients/${editing.id}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                  })
                  if (r.ok) {
                    setShowEdit(false)
                    setEditing(null)
                    load(q)
                  }
                }}
              >
                Guardar cambios
              </ModernButton>
            </Flex>
          </div>
        </ModernModal>

        {/* Delete confirm */}
        <ModernModal
          open={showDelete}
          onOpenChange={setShowDelete}
          title="Eliminar Cliente"
          description={deleting ? `Esta acción no se puede deshacer. Se eliminará el cliente "${deleting.name}".` : ''}
        >
          <div className="space-y-4">
            <Text className="text-white/70">¿Seguro que quieres eliminar este cliente?</Text>
            <Flex justify="between" gap="3" className="mt-6">
              <ModernButton
                variant="glass"
                className="flex-1"
                onClick={() => {
                  setShowDelete(false)
                  setDeleting(null)
                }}
              >
                Cancelar
              </ModernButton>
              <ModernButton
                variant="primary"
                className="flex-1 bg-red-600 hover:bg-red-500"
                disabled={!deleting}
                onClick={async () => {
                  if (!deleting) return
                  const r = await fetch(`/api/clients/${deleting.id}`, { method: 'DELETE' })
                  if (r.status === 204) {
                    setShowDelete(false)
                    setDeleting(null)
                    load(q)
                  }
                }}
              >
                Eliminar
              </ModernButton>
            </Flex>
          </div>
        </ModernModal>
      </div>
    </div>
  )
}
