'use client'
import { useEffect, useMemo, useState } from 'react'
import { useAdminUsers } from '../Admin_users_context'
import { useAdminUsersState } from '../admin_users_states'
import Link from 'next/link'
import { ModernButton, ModernCard, ModernInput, ModernModal, ModernSelect } from '@/app/shared/ui'

function RoleBadge({ role }: { role: 'ADMIN'|'TECHNICIAN' }){
  const color = role === 'ADMIN' ? 'from-amber-500 to-orange-600' : 'from-blue-500 to-indigo-600'
  const label = role === 'ADMIN' ? 'ADMIN' : 'TÉCNICO'
  return <span className={`px-2 py-1 rounded-md text-xs text-white bg-gradient-to-br ${color}`}>{label}</span>
}

export default function AdminUsersLayout(){
  const { controller } = useAdminUsers()
  const { items, isLoading, inviteOpen, editOpen, editing, deleteOpen, deleting } = useAdminUsersState(s => s)
  const [q, setQ] = useState('')
  const filtered = useMemo(() => items.filter(u => u.full_name.toLowerCase().includes(q.toLowerCase()) || u.id.includes(q)), [items, q])

  const [fullName, setFullName] = useState('')
  const [email, setEmail] = useState('')
  const [file, setFile] = useState<File | null>(null)
  const [modeCreate, setModeCreate] = useState(false)
  const [password, setPassword] = useState('')
  const [role, setRole] = useState<'ADMIN'|'TECHNICIAN'>('TECHNICIAN')

  const [editName, setEditName] = useState('')
  const [editRole, setEditRole] = useState<'ADMIN'|'TECHNICIAN'>('TECHNICIAN')
  const [editActive, setEditActive] = useState(true)
  const [editFile, setEditFile] = useState<File | null>(null)

  const [hardDelete, setHardDelete] = useState(false)

  useEffect(() => { if (editing){ setEditName(editing.full_name); setEditRole(editing.role); setEditActive(!!editing.is_active); setEditFile(null) } }, [editing])

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-indigo-950">
      <div className="container mx-auto p-8 max-w-7xl">
        <div className="flex justify-between items-center mb-8">
          <div className="space-y-2">
            <div className="flex items-center gap-3">
              <Link href="/" className="text-white/60 hover:text-white transition-colors">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" /></svg>
              </Link>
              <h1 className="text-3xl font-semibold bg-gradient-to-r from-amber-300 via-orange-300 to-yellow-300 bg-clip-text text-transparent">Gestión de Usuarios</h1>
            </div>
            <p className="text-white/60 text-lg">Administración de roles y accesos</p>
          </div>
          <ModernButton variant="primary" onClick={() => useAdminUsersState.getState().openInvite()}>Invitar técnico</ModernButton>
        </div>

        <div className="mb-6">
          <div className="max-w-md">
            <ModernInput value={q} onChange={(e) => setQ(e.target.value)} placeholder="Buscar por nombre o ID" />
          </div>
        </div>

        <ModernCard className="p-6">
          {isLoading ? (
            <div className="flex items-center justify-center py-12"><div className="w-6 h-6 border-2 border-white/30 border-t-white rounded-full animate-spin"/></div>
          ) : (
            <div className="space-y-2">
              <div className="grid grid-cols-12 gap-4 pb-3 border-b border-white/20">
                <div className="col-span-5 text-white/80 text-sm font-medium">Usuario</div>
                <div className="col-span-3 text-white/80 text-sm font-medium">Rol</div>
                <div className="col-span-2 text-white/80 text-sm font-medium">Estado</div>
                <div className="col-span-2 text-white/80 text-sm font-medium">Acciones</div>
              </div>
              {filtered.map(u => (
                <div key={u.id} className="grid grid-cols-12 gap-4 p-4 rounded-lg bg-white/5 hover:bg-white/10 transition-all duration-200">
                  <div className="col-span-5 flex items-center gap-3">
                    <div className="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white/80">{u.full_name.charAt(0).toUpperCase()}</div>
                    <div>
                      <div className="text-white font-medium">{u.full_name}</div>
                      <div className="text-white/60 text-xs">{u.id}</div>
                    </div>
                  </div>
                  <div className="col-span-3 flex items-center"><RoleBadge role={u.role} /></div>
                  <div className="col-span-2 flex items-center">
                    <span className={`px-2 py-1 rounded-md text-xs ${u.is_active ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300'}`}>{u.is_active ? 'Activo' : 'Inactivo'}</span>
                  </div>
                  <div className="col-span-2 flex items-center gap-2">
                    <ModernButton variant="glass" size="sm" onClick={() => useAdminUsersState.getState().openEdit(u)}>Editar</ModernButton>
                    <ModernButton variant="danger" size="sm" onClick={() => { setHardDelete(false); useAdminUsersState.getState().openDelete(u) }}>Eliminar</ModernButton>
                  </div>
                </div>
              ))}
              {filtered.length === 0 && (
                <div className="text-center py-12 text-white/60">Sin resultados</div>
              )}
            </div>
          )}
        </ModernCard>

        <ModernModal open={inviteOpen} onOpenChange={(o) => o ? useAdminUsersState.getState().openInvite() : useAdminUsersState.getState().closeInvite()} title={modeCreate ? 'Crear usuario' : 'Invitar técnico'}>
          <div className="space-y-3">
            <label className="flex items-center gap-2 text-white/80 select-none">
              <input type="checkbox" checked={modeCreate} onChange={e => setModeCreate(e.target.checked)} /> Crear con contraseña
            </label>
            <ModernInput value={fullName} onChange={(e) => setFullName(e.target.value)} placeholder="Nombre completo" />
            <ModernInput type="email" value={email} onChange={(e) => setEmail(e.target.value)} placeholder="Correo" />
            {modeCreate && (
              <ModernInput type="password" value={password} onChange={(e) => setPassword(e.target.value)} placeholder="Contraseña (min 8)" />
            )}
            <ModernSelect value={role} onValueChange={(v: string) => setRole(v as 'ADMIN'|'TECHNICIAN')}>
              <ModernSelect.Item value="TECHNICIAN">Rol: Técnico</ModernSelect.Item>
              <ModernSelect.Item value="ADMIN">Rol: Administrador</ModernSelect.Item>
            </ModernSelect>
            <input type="file" accept="image/*" onChange={e => setFile(e.target.files?.[0] || null)} className="block w-full text-sm text-white/80 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/20" />
            <div className="flex gap-2 justify-end">
              <ModernButton variant="glass" onClick={() => { setFullName(''); setEmail(''); setFile(null); useAdminUsersState.getState().closeInvite() }}>Cancelar</ModernButton>
              {!modeCreate ? (
                <ModernButton variant="primary" onClick={() => controller.invite({ full_name: fullName, email, signature: file, role })}>Enviar invitación</ModernButton>
              ) : (
                <ModernButton variant="primary" onClick={() => controller.create({ full_name: fullName, email, password, signature: file, role })}>Crear usuario</ModernButton>
              )}
            </div>
          </div>
        </ModernModal>

        <ModernModal open={editOpen} onOpenChange={(o) => o ? useAdminUsersState.getState().openEdit(editing!) : useAdminUsersState.getState().closeEdit()} title="Editar usuario">
          <div className="space-y-3">
            <ModernInput value={editName} onChange={(e) => setEditName(e.target.value)} placeholder="Nombre completo" />
            <ModernSelect value={editRole} onValueChange={(v: string) => setEditRole((v as 'ADMIN'|'TECHNICIAN'))}>
              <ModernSelect.Item value="ADMIN">Administrador</ModernSelect.Item>
              <ModernSelect.Item value="TECHNICIAN">Técnico</ModernSelect.Item>
            </ModernSelect>
            <label className="flex items-center gap-2 text-white/80 select-none"><input type="checkbox" checked={editActive} onChange={e => setEditActive(e.target.checked)} /> Activo</label>
            <input type="file" accept="image/*" onChange={e => setEditFile(e.target.files?.[0] || null)} className="block w-full text-sm text-white/80 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-white/10 file:text-white hover:file:bg-white/20" />
            <div className="flex gap-2 justify-end">
              <ModernButton variant="glass" onClick={() => { useAdminUsersState.getState().closeEdit() }}>Cancelar</ModernButton>
              <ModernButton variant="primary" onClick={() => controller.update(editing!.id, { full_name: editName, role: editRole, is_active: editActive, signature: editFile })}>Guardar</ModernButton>
            </div>
          </div>
        </ModernModal>

        <ModernModal open={deleteOpen} onOpenChange={(o) => o ? useAdminUsersState.getState().openDelete(deleting!) : useAdminUsersState.getState().closeDelete()} title="Eliminar usuario">
          <div className="space-y-4">
            <p className="text-white/80">Vas a eliminar al usuario <span className="font-semibold text-white">{deleting?.full_name}</span>.</p>
            <div className="p-3 rounded-md bg-yellow-500/10 text-yellow-200 text-sm">
              Si el usuario tiene certificados asociados, sólo se podrá realizar un borrado suave. En ese caso se marcará como inactivo y eliminado, conservando trazabilidad.
            </div>
            <label className="flex items-center gap-2 text-white/80 select-none">
              <input type="checkbox" checked={hardDelete} onChange={e => setHardDelete(e.target.checked)} /> Forzar borrado definitivo (si no tiene certificados)
            </label>
            <div className="flex gap-2 justify-end">
              <ModernButton variant="glass" onClick={() => useAdminUsersState.getState().closeDelete()}>Cancelar</ModernButton>
              <ModernButton variant="danger" onClick={() => controller.delete(deleting!.id, { hard: hardDelete })}>Eliminar</ModernButton>
            </div>
          </div>
        </ModernModal>
      </div>
    </div>
  )
}

// Modal de eliminación
// Lo colocamos fuera del return principal por claridad en el diff, pero debe estar dentro para renderizar
