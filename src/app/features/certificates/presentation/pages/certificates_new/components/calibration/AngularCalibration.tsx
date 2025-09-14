'use client'
import { useState, useMemo } from 'react'
import { Button, Flex, IconButton, Text, TextField } from '@radix-ui/themes'
import { TrashIcon, PlusIcon } from '@heroicons/react/24/outline'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function AngularCalibration() {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  const [ap, setAp] = useState('')
  const angularRows = useMemo(() => (s.results.angular_measurements || []), [s.results])
  return (
    <div className="space-y-2">
  <Text className="font-medium">Calibración Angular</Text>
  <TextField.Root className="input-glass" value={ap} onChange={e => { setAp(e.target.value); controller.setResults({ angular_precision: e.target.value }) }} placeholder='Precisión Angular (ej: ±02")'/>
      <div className="overflow-auto">
        <div className="min-w-[540px]">
          <div className="grid grid-cols-12 gap-2 text-sm text-muted">
            <div className="col-span-4">Valor de Patrón</div>
            <div className="col-span-4">Valor Obtenido</div>
            <div className="col-span-3">Error</div>
            <div className="col-span-1"></div>
          </div>
          {angularRows.map((row, idx) => (
            <div key={idx} className="grid grid-cols-12 gap-2 mt-2">
              <TextField.Root className="input-glass" value={row.pattern} onChange={e => { const n = [...angularRows]; n[idx] = { ...n[idx], pattern: e.target.value }; controller.setResults({ angular_measurements: n }) }}/>
              <TextField.Root className="input-glass" value={row.obtained} onChange={e => { const n = [...angularRows]; n[idx] = { ...n[idx], obtained: e.target.value }; controller.setResults({ angular_measurements: n }) }}/>
              <TextField.Root className="input-glass" value={row.error} onChange={e => { const n = [...angularRows]; n[idx] = { ...n[idx], error: e.target.value }; controller.setResults({ angular_measurements: n }) }}/>
              <IconButton className="btn-glass" onClick={() => { const n = angularRows.filter((_, i) => i !== idx); controller.setResults({ angular_measurements: n }) }}><TrashIcon/></IconButton>
            </div>
          ))}
          <div className="mt-3">
            <Button className="btn-glass" onClick={() => controller.setResults({ angular_measurements: [...angularRows, { pattern: '', obtained: '', error: '' }] })}><PlusIcon/> Añadir Medición</Button>
          </div>
        </div>
      </div>
    </div>
  )
}
