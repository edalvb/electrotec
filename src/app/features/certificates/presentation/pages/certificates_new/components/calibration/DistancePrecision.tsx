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
    <div>
      <div className="min-w-[540px]">
        <div className="grid grid-cols-12 gap-2 text-sm text-muted">
          <div className="col-span-4">Puntos de Control (m)</div>
          <div className="col-span-4">Distancia Obtenida (m)</div>
          <div className="col-span-3">Variación (m)</div>
          <div className="col-span-1"></div>
        </div>
        {rows.map((row, idx) => (
          <div key={idx} className="grid grid-cols-12 gap-2 mt-2">
            <TextField.Root value={row.control.toString()} onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], control: Number(e.target.value || 0) }; setRows(n) }}/>
            <TextField.Root value={row.obtained.toString()} onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], obtained: Number(e.target.value || 0) }; setRows(n) }}/>
            <TextField.Root value={row.delta.toString()} onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], delta: Number(e.target.value || 0) }; setRows(n) }}/>
            <IconButton onClick={() => { const n = rows.filter((_, i) => i !== idx); setRows(n) }}><TrashIcon/></IconButton>
          </div>
        ))}
        <div className="mt-3">
          <Button variant="soft" onClick={() => setRows([...(rows || []), { control: 0, obtained: 0, delta: 0 }])}><PlusIcon/> Añadir Medición</Button>
        </div>
      </div>
    </div>
  )
}

export default function DistancePrecision() {
  const { controller } = useCertificatesNew()
  const [dp, setDp] = useState('')
  return (
    <div className="space-y-2">
      <Text className="font-medium">Precisión de Distancia</Text>
      <TextField.Root value={dp} onChange={e => { setDp(e.target.value); controller.setResults({ distance_precision: e.target.value }) }} placeholder="Precisión de Distancia (ej: 2 mm + 2 ppm)"/>
      <div className="space-y-4">
        <div>
          <Text className="font-medium">Medición con Prisma</Text>
          <PrismTable kind="prism"/>
        </div>
        <div>
          <Text className="font-medium">Medición sin Prisma</Text>
          <PrismTable kind="no_prism"/>
        </div>
      </div>
    </div>
  )
}
