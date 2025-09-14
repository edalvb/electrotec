'use client'
import { useEffect, useState } from 'react'
import { Button, Card, Flex, Heading, Text, TextField } from '@radix-ui/themes'
import { useCertificatesNew } from '../../Certificates_new_context'
import Portal from '@/app/shared/ui/Portal'

export default function NewEquipmentModal({ onClose }: { onClose: () => void }) {
  const { controller } = useCertificatesNew()
  const [serial_number, setSN] = useState('')
  const [brand, setBrand] = useState('')
  const [model, setModel] = useState('')
  const [equipment_type_id, setType] = useState<number>(1)
  const [types, setTypes] = useState<{ id: number; name: string }[]>([])
  const [loadingTypes, setLoadingTypes] = useState(true)
  useEffect(() => { (async () => { try { const r = await fetch('/api/equipment/types'); const j = await r.json(); setTypes(j.items || []); if ((j.items || []).length) setType(j.items[0].id) } finally { setLoadingTypes(false) } })() }, [])
  const [clientName, setClientName] = useState('')
  const can = serial_number && brand && model
  return (
    <Portal>
      <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[9999] p-4 animate-in fade-in duration-300">
        <div 
          className="absolute inset-0" 
          onClick={onClose}
        />
        <Card className="glass p-8 w-full max-w-2xl max-h-[90vh] overflow-auto border-2 border-white/20 shadow-2xl shadow-black/25 animate-in zoom-in-95 duration-300 relative">
          <Flex direction="column" gap="6">
            {/* Header */}
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                  <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                  </svg>
                </div>
                <div>
                  <Heading size="6" className="font-heading bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                    Registrar Nuevo Equipo
                  </Heading>
                  <Text className="text-white/60 text-sm">Completa la información del equipo y cliente</Text>
                </div>
              </div>
              <button 
                onClick={onClose}
                className="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors"
              >
                <svg className="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            {/* Equipment Section */}
            <div className="space-y-5">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center">
                  <svg className="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <Text className="font-medium text-white/90">Información del Equipo</Text>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Text className="text-sm font-medium text-white/80">Número de Serie</Text>
                  <TextField.Root 
                    className="input-glass" 
                    value={serial_number} 
                    onChange={e => setSN(e.target.value)} 
                    placeholder="Ej: ABC123456"
                  />
                </div>
                <div className="space-y-2">
                  <Text className="text-sm font-medium text-white/80">Marca</Text>
                  <TextField.Root 
                    className="input-glass" 
                    value={brand} 
                    onChange={e => setBrand(e.target.value)} 
                    placeholder="Ej: Fluke, Keysight"
                  />
                </div>
                <div className="space-y-2">
                  <Text className="text-sm font-medium text-white/80">Modelo</Text>
                  <TextField.Root 
                    className="input-glass" 
                    value={model} 
                    onChange={e => setModel(e.target.value)} 
                    placeholder="Ej: 8845A, 34461A"
                  />
                </div>
                <div className="space-y-2">
                  <Text className="text-sm font-medium text-white/80">Tipo de Equipo</Text>
                  <select 
                    className="w-full input-glass p-3 rounded-lg bg-white/5 border border-white/20 text-white focus:bg-white/10 focus:border-blue-400/50 transition-all" 
                    value={equipment_type_id} 
                    onChange={e => setType(Number(e.target.value))} 
                    disabled={loadingTypes}
                  >
                    {loadingTypes ? (
                      <option>Cargando tipos...</option>
                    ) : (
                      types.map(t => (<option key={t.id} value={t.id} className="bg-slate-800 text-white">{t.name}</option>))
                    )}
                  </select>
                </div>
              </div>
            </div>

            {/* Separator */}
            <div className="relative">
              <div className="absolute inset-0 flex items-center">
                <div className="w-full border-t border-white/20"></div>
              </div>
              <div className="relative flex justify-center text-sm">
                <span className="px-4 bg-slate-900/50 text-white/60 rounded-full">Cliente (Opcional)</span>
              </div>
            </div>

            {/* Client Section */}
            <div className="space-y-4">
              <div className="flex items-center gap-3">
                <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center">
                  <svg className="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <Text className="font-medium text-white/90">Información del Cliente</Text>
              </div>

              <div className="space-y-2">
                <Text className="text-sm font-medium text-white/80">Nombre del Cliente</Text>
                <TextField.Root 
                  className="input-glass" 
                  value={clientName} 
                  onChange={e => setClientName(e.target.value)} 
                  placeholder="Ej: Empresa XYZ S.A.C. (opcional)"
                />
                <Text className="text-xs text-white/50">
                  Puedes dejar este campo vacío y asignar el cliente más tarde
                </Text>
              </div>
            </div>

            {/* Actions */}
            <div className="flex justify-between gap-4 pt-4 border-t border-white/20">
              <Button 
                className="btn-glass px-6 py-3 transition-all hover:scale-105" 
                onClick={onClose}
              >
                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
                Cancelar
              </Button>
              <Button 
                className="btn-primary px-8 py-3 text-base font-medium bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" 
                disabled={!can} 
                onClick={async () => { 
                  await controller.createEquipment({ 
                    equipment: { serial_number, brand, model, equipment_type_id }, 
                    client: clientName ? { name: clientName } : undefined 
                  }); 
                  onClose() 
                }}
              >
                <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                </svg>
                Registrar Equipo
              </Button>
            </div>
          </Flex>
        </Card>
      </div>
    </Portal>
  )
}
