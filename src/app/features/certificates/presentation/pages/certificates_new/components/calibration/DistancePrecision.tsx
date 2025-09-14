'use client'
import { useMemo, useState } from 'react'
import { Button, IconButton, Text, TextField } from '@radix-ui/themes'
import { PlusIcon, TrashIcon } from '@heroicons/react/24/outline'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'

function PrismTable({ kind }: { kind: 'prism' | 'no_prism' }) {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  const rows = useMemo(() => (kind === 'prism' ? (s.results.prism_measurements || []) : (s.results.no_prism_measurements || [])), [kind, s.results])
  const setRows = (n: typeof rows) => kind === 'prism' ? controller.setResults({ prism_measurements: n }) : controller.setResults({ no_prism_measurements: n })
  
  return (
    <div className="space-y-3">
      <div className="overflow-auto">
        <div className="min-w-[540px] space-y-3">
          <div className="grid grid-cols-12 gap-3 text-sm font-medium text-white/70 p-3 bg-white/5 rounded-lg border border-white/10">
            <div className="col-span-4">Puntos de Control (m)</div>
            <div className="col-span-4">Distancia Obtenida (m)</div>
            <div className="col-span-3">Variación (m)</div>
            <div className="col-span-1">Acción</div>
          </div>
          {rows.map((row, idx) => (
            <div key={idx} className="grid grid-cols-12 gap-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors">
              <div className="col-span-4">
                <TextField.Root 
                  className="input-glass" 
                  value={row.control.toString()} 
                  onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], control: Number(e.target.value || 0) }; setRows(n) }}
                  placeholder="Control"
                />
              </div>
              <div className="col-span-4">
                <TextField.Root 
                  className="input-glass" 
                  value={row.obtained.toString()} 
                  onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], obtained: Number(e.target.value || 0) }; setRows(n) }}
                  placeholder="Obtenida"
                />
              </div>
              <div className="col-span-3">
                <TextField.Root 
                  className="input-glass" 
                  value={row.delta.toString()} 
                  onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], delta: Number(e.target.value || 0) }; setRows(n) }}
                  placeholder="Variación"
                />
              </div>
              <div className="col-span-1 flex items-center">
                <IconButton 
                  className="btn-glass w-8 h-8 hover:bg-red-500/20 hover:border-red-400/30 transition-colors" 
                  onClick={() => { const n = rows.filter((_, i) => i !== idx); setRows(n) }}
                >
                  <TrashIcon className="w-4 h-4"/>
                </IconButton>
              </div>
            </div>
          ))}
          <div className="pt-2">
            <Button 
              className="btn-glass flex items-center gap-2 hover:bg-blue-500/20 hover:border-blue-400/30 transition-colors" 
              onClick={() => setRows([...(rows || []), { control: 0, obtained: 0, delta: 0 }])}
            >
              <PlusIcon className="w-4 h-4"/> 
              Añadir Medición
            </Button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default function DistancePrecision() {
  const { controller } = useCertificatesNew()
  const [dp, setDp] = useState('')
  return (
    <div className="space-y-4 p-4 rounded-lg bg-white/5 border border-white/20">
      <div className="flex items-center gap-3">
        <div className="w-6 h-6 rounded bg-gradient-to-br from-cyan-500/20 to-blue-500/20 flex items-center justify-center">
          <svg className="w-3 h-3 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
          </svg>
        </div>
        <Text className="font-semibold text-white">Precisión de Distancia</Text>
      </div>
      
      <div className="space-y-2">
        <Text className="text-sm font-medium text-white/80">Precisión de Distancia</Text>
        <TextField.Root 
          className="input-glass" 
          value={dp} 
          onChange={e => { setDp(e.target.value); controller.setResults({ distance_precision: e.target.value }) }} 
          placeholder="Ej: 2 mm + 2 ppm"
        />
      </div>

      <div className="space-y-6">
        <div className="space-y-3">
          <div className="flex items-center gap-2">
            <div className="w-4 h-4 rounded bg-green-500/20 flex items-center justify-center">
              <div className="w-2 h-2 bg-green-400 rounded"></div>
            </div>
            <Text className="font-medium text-white/90">Medición con Prisma</Text>
          </div>
          <PrismTable kind="prism"/>
        </div>
        
        <div className="space-y-3">
          <div className="flex items-center gap-2">
            <div className="w-4 h-4 rounded bg-orange-500/20 flex items-center justify-center">
              <div className="w-2 h-2 bg-orange-400 rounded"></div>
            </div>
            <Text className="font-medium text-white/90">Medición sin Prisma</Text>
          </div>
          <PrismTable kind="no_prism"/>
        </div>
      </div>
    </div>
  )
}
