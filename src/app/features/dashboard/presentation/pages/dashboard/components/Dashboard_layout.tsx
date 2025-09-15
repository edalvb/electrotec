'use client'
import { useState, useEffect } from 'react'
import { useDashboardState } from '../dashboard_states'
import { Button, Card, Flex, Heading, Separator, Text, TextField } from '@radix-ui/themes'
import Link from 'next/link'
import Portal from '@/app/shared/ui/Portal'

export default function DashboardLayout() {
  const { summary, profileName, isAdmin } = useDashboardState(s => s)
  const [showClient, setShowClient] = useState(false)
  const [showEquipment, setShowEquipment] = useState(false)
  const [mobileNavOpen, setMobileNavOpen] = useState(false)
  const [clientName, setClientName] = useState('')
  const [creatingClient, setCreatingClient] = useState(false)
  const [serial, setSerial] = useState('')
  const [brand, setBrand] = useState('')
  const [model, setModel] = useState('')
  const [types, setTypes] = useState<{ id:number; name:string }[]>([])
  const [equipmentTypeId, setEquipmentTypeId] = useState<number | ''>('')
  const [creatingEq, setCreatingEq] = useState(false)
  
  // Cerrar el menú móvil con ESC
  useEffect(() => {
    if (!mobileNavOpen) return
    const onKey = (e: KeyboardEvent) => { if (e.key === 'Escape') setMobileNavOpen(false) }
    window.addEventListener('keydown', onKey)
    return () => window.removeEventListener('keydown', onKey)
  }, [mobileNavOpen])
  
  useEffect(() => { 
    if (showEquipment) { 
      (async () => { 
        const r = await fetch('/api/equipment/types'); 
        const j = await r.json(); 
        setTypes(j.items||[]); 
        if ((j.items||[]).length) setEquipmentTypeId(j.items[0].id) 
      })() 
    } 
  }, [showEquipment])

  return (
    <>
      <div className="min-h-screen grid md:grid-cols-[280px_1fr] bg-gradient-to-br from-slate-950 via-blue-950 to-indigo-950">
        <aside className="p-6 glass hidden md:block border-r border-white/10 backdrop-blur-xl">
          <Flex direction="column" gap="6">
            <div className="text-center">
              <div className="w-12 h-12 mx-auto bg-gradient-to-br from-blue-400 to-purple-600 rounded-xl flex items-center justify-center mb-3">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <Heading size="5" className="font-heading bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">ELECTROTEC</Heading>
              <Text className="text-xs text-white/60 mt-1">Sistema de certificados</Text>
            </div>
            
            <Separator size="4" className="opacity-30"/>
            
            <nav className="space-y-2">
              <Link href="/" className="group">
                <div className="flex items-center gap-3 p-3 rounded-lg bg-gradient-to-r from-blue-500/20 to-purple-500/20 border border-blue-400/30 text-blue-300 transition-all hover:from-blue-500/30 hover:to-purple-500/30 hover:border-blue-400/50">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z" />
                  </svg>
                  <Text className="font-medium">Dashboard</Text>
                </div>
              </Link>
              
              <Link href="/certificados" className="group">
                <div className="flex items-center gap-3 p-3 rounded-lg text-white/70 transition-all hover:bg-white/10 hover:text-white">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <Text className="font-medium">Certificados</Text>
                </div>
              </Link>
              
              <Link href="/equipos" className="group">
                <div className="flex items-center gap-3 p-3 rounded-lg text-white/70 transition-all hover:bg-white/10 hover:text-white">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <Text className="font-medium">Equipos</Text>
                </div>
              </Link>
              
              <Link href="/clientes" className="group">
                <div className="flex items-center gap-3 p-3 rounded-lg text-white/70 transition-all hover:bg-white/10 hover:text-white">
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                  </svg>
                  <Text className="font-medium">Clientes</Text>
                </div>
              </Link>
            </nav>
            {isAdmin && (
              <nav className="space-y-2 pt-2">
                <Link href="/admin/usuarios" className="group">
                  <div className="flex items-center gap-3 p-3 rounded-lg text-amber-300/80 transition-all hover:bg-white/10 hover:text-amber-200">
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h6M4 20h5m8-8a4 4 0 10-8 0 4 4 0 008 0z" /></svg>
                    <Text className="font-medium">Gestión de Usuarios</Text>
                  </div>
                </Link>
              </nav>
            )}
          </Flex>
        </aside>

        {/* Top bar móvil */}
        <div className="md:hidden sticky top-0 z-20 border-b border-white/10 bg-black/30 backdrop-blur pt-[env(safe-area-inset-top)]">
          <div className="flex items-center justify-between px-4 py-3">
            <div className="flex items-center gap-3">
              <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center">
                <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <Heading size="3" className="font-heading bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">ELECTROTEC</Heading>
                <Text className="text-[11px] text-white/60 -mt-1 block">Sistema de certificados</Text>
              </div>
            </div>
            <button
              aria-label="Abrir menú"
              className="p-2 rounded-md bg-white/10 border border-white/10 text-white hover:bg-white/15 active:scale-95"
              onClick={() => setMobileNavOpen(true)}
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
          {/* Accesos rápidos horizontales */}
          <div className="px-4 pb-3 overflow-x-auto no-scrollbar">
            <div className="flex items-center gap-2 min-w-max">
              <Link href="/certificados" className="px-3 py-1.5 rounded-full bg-white/10 text-white/80 text-sm border border-white/10">Certificados</Link>
              <Link href="/equipos" className="px-3 py-1.5 rounded-full bg-white/10 text-white/80 text-sm border border-white/10">Equipos</Link>
              <Link href="/clientes" className="px-3 py-1.5 rounded-full bg-white/10 text-white/80 text-sm border border-white/10">Clientes</Link>
              <Link href="/certificados/nuevo" className="px-3 py-1.5 rounded-full bg-blue-500/20 text-blue-200 text-sm border border-blue-400/30">Nuevo certificado</Link>
              {isAdmin && (
                <Link href="/admin/usuarios" className="px-3 py-1.5 rounded-full bg-amber-500/20 text-amber-200 text-sm border border-amber-400/30">Gestión usuarios</Link>
              )}
            </div>
          </div>
        </div>

        <main className="p-4 md:p-8">
          <div className="max-w-7xl mx-auto">
            <Flex justify="between" align="center" className="mb-6 md:mb-8 flex-col md:flex-row gap-3 md:gap-0 items-start md:items-center">
              <div className="w-full md:w-auto">
                <Heading size="7" className="font-heading bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent mb-2">Dashboard</Heading>
                <Text className="text-white/60">Panel de control y estadísticas</Text>
              </div>
              <div className="w-full md:w-auto text-left md:text-right">
                <Text className="text-sm text-white/60">Bienvenido</Text>
                <div className="flex md:justify-end items-center gap-2">
                  <Text className="text-lg font-medium bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">{profileName}</Text>
                  {isAdmin && (<span title="Administrador" className="px-2 py-0.5 rounded-md text-[11px] text-amber-100 bg-gradient-to-br from-amber-500 to-orange-600 border border-amber-400/40">ADMIN</span>)}
                </div>
              </div>
            </Flex>
            
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-6 md:mb-8">
              <Card className="glass p-6 border-2 border-white/10 hover:border-blue-400/30 transition-all duration-300 group">
                <div className="flex items-center justify-between mb-4">
                  <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center">
                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div className="text-right">
                    <Text className="text-3xl font-bold bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">{summary.issuedThisMonth}</Text>
                  </div>
                </div>
                <Heading size="4" className="text-white/90 mb-2">Certificados emitidos</Heading>
                <Text className="text-white/60 text-sm">Este mes</Text>
              </Card>
              
              <Card className="glass p-6 border-2 border-white/10 hover:border-orange-400/30 transition-all duration-300 group">
                <div className="flex items-center justify-between mb-4">
                  <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-400 to-red-600 flex items-center justify-center">
                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div className="text-right">
                    <Text className="text-3xl font-bold bg-gradient-to-r from-orange-400 to-red-400 bg-clip-text text-transparent">{summary.next30Days}</Text>
                  </div>
                </div>
                <Heading size="4" className="text-white/90 mb-2">Próximas calibraciones</Heading>
                <Text className="text-white/60 text-sm">Siguientes 30 días</Text>
              </Card>
            </div>
            
            <div className="space-y-6">
              <Heading size="5" className="text-white/90 font-heading">Acciones rápidas</Heading>
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4">
                <Link href="/certificados/nuevo" className="group">
                  <Card className="glass p-6 border-2 border-white/10 hover:border-blue-400/50 transition-all duration-300 cursor-pointer transform hover:scale-[1.02]">
                    <div className="flex items-center gap-4">
                      <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                        <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                        </svg>
                      </div>
                      <div>
                        <Text className="font-medium text-white/90">Nuevo certificado</Text>
                        <Text className="text-sm text-white/60">Generar certificado</Text>
                      </div>
                    </div>
                  </Card>
                </Link>
                
                <Card 
                  className="glass p-6 border-2 border-white/10 hover:border-green-400/50 transition-all duration-300 cursor-pointer transform hover:scale-[1.02]"
                  onClick={() => setShowClient(true)}
                >
                  <div className="flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                      <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </div>
                    <div>
                      <Text className="font-medium text-white/90">Crear cliente</Text>
                      <Text className="text-sm text-white/60">Añadir nuevo cliente</Text>
                    </div>
                  </div>
                </Card>
                
                <Card 
                  className="glass p-6 border-2 border-white/10 hover:border-purple-400/50 transition-all duration-300 cursor-pointer transform hover:scale-[1.02]"
                  onClick={() => setShowEquipment(true)}
                >
                  <div className="flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                      <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <div>
                      <Text className="font-medium text-white/90">Crear equipo</Text>
                      <Text className="text-sm text-white/60">Registrar equipo</Text>
                    </div>
                  </div>
                </Card>
                {isAdmin && (
                  <Link href="/admin/usuarios" className="group">
                    <Card className="glass p-6 border-2 border-white/10 hover:border-amber-400/50 transition-all duration-300 cursor-pointer transform hover:scale-[1.02]">
                      <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                          <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h6M4 20h5m8-8a4 4 0 10-8 0 4 4 0 008 0z" />
                          </svg>
                        </div>
                        <div>
                          <Text className="font-medium text-white/90">Gestión de usuarios</Text>
                          <Text className="text-sm text-white/60">Invitar y administrar</Text>
                        </div>
                      </div>
                    </Card>
                  </Link>
                )}
              </div>
            </div>
          </div>
        </main>
      </div>

      {/* Sidebar móvil como overlay */}
      {mobileNavOpen && (
        <Portal>
          <div className="fixed inset-0 z-[9998]">
            <button
              aria-label="Cerrar menú"
              className="absolute inset-0 bg-black/50 backdrop-blur-sm"
              onClick={() => setMobileNavOpen(false)}
            />
            <aside
              role="dialog"
              aria-modal="true"
              className="absolute left-0 top-0 h-full w-72 max-w-[85%] glass border-r border-white/10 p-6 animate-in slide-in-from-left duration-200"
            >
              <Flex direction="column" gap="6">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-400 to-purple-600 flex items-center justify-center">
                    <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div>
                    <Heading size="4" className="font-heading bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">ELECTROTEC</Heading>
                    <Text className="text-xs text-white/60 -mt-1">Menú</Text>
                  </div>
                </div>
                <Separator size="4" className="opacity-30"/>
                <nav className="space-y-2">
                  <Link href="/" onClick={() => setMobileNavOpen(false)} className="group block">
                    <div className="flex items-center gap-3 p-3 rounded-lg bg-gradient-to-r from-blue-500/20 to-purple-500/20 border border-blue-400/30 text-blue-300 transition-all hover:from-blue-500/30 hover:to-purple-500/30 hover:border-blue-400/50">
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z" />
                      </svg>
                      <Text className="font-medium">Dashboard</Text>
                    </div>
                  </Link>
                  <Link href="/certificados" onClick={() => setMobileNavOpen(false)} className="group block">
                    <div className="flex items-center gap-3 p-3 rounded-lg text-white/70 transition-all hover:bg-white/10 hover:text-white">
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <Text className="font-medium">Certificados</Text>
                    </div>
                  </Link>
                  <Link href="/equipos" onClick={() => setMobileNavOpen(false)} className="group block">
                    <div className="flex items-center gap-3 p-3 rounded-lg text-white/70 transition-all hover:bg-white/10 hover:text-white">
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <Text className="font-medium">Equipos</Text>
                    </div>
                  </Link>
                  <Link href="/clientes" onClick={() => setMobileNavOpen(false)} className="group block">
                    <div className="flex items-center gap-3 p-3 rounded-lg text-white/70 transition-all hover:bg-white/10 hover:text-white">
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                      </svg>
                      <Text className="font-medium">Clientes</Text>
                    </div>
                  </Link>
                </nav>
                {isAdmin && (
                  <Link href="/admin/usuarios" onClick={() => setMobileNavOpen(false)} className="group block">
                    <div className="flex items-center gap-3 p-3 rounded-lg text-amber-300/80 transition-all hover:bg-white/10 hover:text-amber-200">
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a4 4 0 00-3-3.87M9 20h6M4 20h5m8-8a4 4 0 10-8 0 4 4 0 008 0z" /></svg>
                      <Text className="font-medium">Gestión de Usuarios</Text>
                    </div>
                  </Link>
                )}
              </Flex>
            </aside>
          </div>
        </Portal>
      )}

      {showClient && (
        <Portal>
          <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center p-4 backdrop-blur-sm">
            <Card className="glass p-6 w-full max-w-md border-2 border-white/20">
              <Flex direction="column" gap="4">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
                    <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  </div>
                  <Heading size="5" className="font-heading text-white">Nuevo Cliente</Heading>
                </div>
                <div className="space-y-3">
                  <div>
                    <Text className="text-sm font-medium text-white/90 mb-2">Nombre del cliente</Text>
                    <TextField.Root 
                      className="input-glass" 
                      value={clientName} 
                      onChange={e => setClientName(e.target.value)} 
                      placeholder="Ingrese el nombre del cliente"
                    />
                  </div>
                </div>
                <Flex justify="between" gap="3" className="mt-6">
                  <Button 
                    className="btn-glass flex-1" 
                    onClick={() => { setClientName(''); setShowClient(false) }}
                  >
                    Cancelar
                  </Button>
                  <Button 
                    className="btn-primary flex-1" 
                    disabled={!clientName || creatingClient} 
                    onClick={async () => { 
                      try { 
                        setCreatingClient(true); 
                        const r = await fetch('/api/clients', { 
                          method: 'POST', 
                          headers: { 'Content-Type':'application/json' }, 
                          body: JSON.stringify({ name: clientName }) 
                        }); 
                        if (r.ok) { 
                          setClientName(''); 
                          setShowClient(false) 
                        } 
                      } finally { 
                        setCreatingClient(false) 
                      } 
                    }}
                  >
                    {creatingClient ? 'Guardando...' : 'Guardar'}
                  </Button>
                </Flex>
              </Flex>
            </Card>
          </div>
        </Portal>
      )}

      {showEquipment && (
        <Portal>
          <div className="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center p-4 backdrop-blur-sm">
            <Card className="glass p-6 w-full max-w-md border-2 border-white/20">
              <Flex direction="column" gap="4">
                <div className="flex items-center gap-3">
                  <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center">
                    <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
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
                      onChange={e => setSerial(e.target.value)} 
                      placeholder="Número de serie"
                    />
                  </div>
                  <div>
                    <Text className="text-sm font-medium text-white/90 mb-2">Marca</Text>
                    <TextField.Root 
                      className="input-glass" 
                      value={brand} 
                      onChange={e => setBrand(e.target.value)} 
                      placeholder="Marca del equipo"
                    />
                  </div>
                  <div>
                    <Text className="text-sm font-medium text-white/90 mb-2">Modelo</Text>
                    <TextField.Root 
                      className="input-glass" 
                      value={model} 
                      onChange={e => setModel(e.target.value)} 
                      placeholder="Modelo del equipo"
                    />
                  </div>
                  <div>
                    <Text className="text-sm font-medium text-white/90 mb-2">Tipo de equipo</Text>
                    <select 
                      className="w-full input-glass p-2 rounded-lg" 
                      value={equipmentTypeId === '' ? '' : String(equipmentTypeId)} 
                      onChange={e => setEquipmentTypeId(Number(e.target.value))}
                    >
                      {types.map(t => (<option key={t.id} value={t.id}>{t.name}</option>))}
                    </select>
                  </div>
                </div>
                <Flex justify="between" gap="3" className="mt-6">
                  <Button 
                    className="btn-glass flex-1" 
                    onClick={() => { 
                      setSerial(''); 
                      setBrand(''); 
                      setModel(''); 
                      setShowEquipment(false) 
                    }}
                  >
                    Cancelar
                  </Button>
                  <Button 
                    className="btn-primary flex-1" 
                    disabled={!serial || !brand || !model || !equipmentTypeId || creatingEq} 
                    onClick={async () => { 
                      try { 
                        setCreatingEq(true); 
                        const r = await fetch('/api/equipment', { 
                          method: 'POST', 
                          headers: { 'Content-Type':'application/json' }, 
                          body: JSON.stringify({ 
                            equipment: { 
                              serial_number: serial, 
                              brand, 
                              model, 
                              equipment_type_id: Number(equipmentTypeId) 
                            } 
                          }) 
                        }); 
                        if (r.ok) { 
                          setSerial(''); 
                          setBrand(''); 
                          setModel(''); 
                          setShowEquipment(false) 
                        } 
                      } finally { 
                        setCreatingEq(false) 
                      } 
                    }}
                  >
                    {creatingEq ? 'Guardando...' : 'Guardar'}
                  </Button>
                </Flex>
              </Flex>
            </Card>
          </div>
        </Portal>
      )}
    </>
  )
}