'use client'
import { useState } from 'react'
import { Button, IconButton, Text, TextField } from '@radix-ui/themes'
import { PlusIcon, TrashIcon } from '@heroicons/react/24/outline'
import { useCertificatesModal } from '../../Certificates_modal_context'
import { useCertificatesModalState } from '../../Certificates_modal_states'

type DistanceRow = { control: number; obtained: number; delta: number }
function TableSection({ kind }: { kind: 'prism' | 'no_prism' }){
  const { controller } = useCertificatesModal()
  const s = useCertificatesModalState(st => st)
  const rows = kind === 'prism' ? (s.results.prism_measurements || []) : (s.results.no_prism_measurements || [])
  const setRows = (n: DistanceRow[]) => kind === 'prism' ? controller.setResults({ prism_measurements: n }) : controller.setResults({ no_prism_measurements: n })
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
          {rows.map((row: DistanceRow, idx: number) => (
            <div key={idx} className="grid grid-cols-12 gap-3 p-3 bg-white/5 rounded-lg border border-white/10">
              <div className="col-span-4">
                <TextField.Root className="input-glass" value={row.control?.toString() || ''} onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], control: Number(e.target.value||0) }; setRows(n) }} placeholder="Control"/>
              </div>
              <div className="col-span-4">
                <TextField.Root className="input-glass" value={row.obtained?.toString() || ''} onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], obtained: Number(e.target.value||0) }; setRows(n) }} placeholder="Obtenida"/>
              </div>
              <div className="col-span-3">
                <TextField.Root className="input-glass" value={row.delta?.toString() || ''} onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], delta: Number(e.target.value||0) }; setRows(n) }} placeholder="Variación"/>
              </div>
              <div className="col-span-1 flex items-center">
                <IconButton className="btn-glass w-8 h-8" onClick={() => { const n = rows.filter((_, i: number) => i !== idx); setRows(n) }}>
                  <TrashIcon className="w-4 h-4"/>
                </IconButton>
              </div>
            </div>
          ))}
          <div className="pt-2">
            <Button className="btn-glass" onClick={() => setRows([...(rows || []), { control: 0, obtained: 0, delta: 0 }])}><PlusIcon className="w-4 h-4"/> Añadir Medición</Button>
          </div>
        </div>
      </div>
    </div>
  )
}

export default function DistanceResults(){
  const { controller } = useCertificatesModal()
  const s = useCertificatesModalState(st => st)
  const [dp, setDp] = useState(s.results.distance_precision || '')
  return (
    <div className="space-y-6 p-4 rounded-lg bg-white/5 border border-white/20">
      <div className="space-y-2">
        <Text className="text-sm font-medium text-white/80">Precisión de Distancia</Text>
        <TextField.Root className="input-glass" value={dp} onChange={e => { setDp(e.target.value); controller.setResults({ distance_precision: e.target.value }) }} placeholder="Ej: 2 mm + 2 ppm"/>
      </div>
      <div className="space-y-3">
        <Text className="font-medium text-white/90">Medición con Prisma</Text>
        <TableSection kind="prism"/>
      </div>
      <div className="space-y-3">
        <Text className="font-medium text-white/90">Medición sin Prisma</Text>
        <TableSection kind="no_prism"/>
      </div>
    </div>
  )
}
