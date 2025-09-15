'use client'
import { useState } from 'react'
import { Button, IconButton, Text, TextField } from '@radix-ui/themes'
import { PlusIcon, TrashIcon } from '@heroicons/react/24/outline'
import { useCertificatesModal } from '../../Certificates_modal_context'
import { useCertificatesModalState } from '../../Certificates_modal_states'

export default function AngularResults(){
  const { controller } = useCertificatesModal()
  const s = useCertificatesModalState(st => st)
  const rows = s.results.angular_measurements || []
  const [ap, setAp] = useState(s.results.angular_precision || '')
  return (
    <div className="space-y-4 p-4 rounded-lg bg-white/5 border border-white/20">
      <div className="flex items-center gap-3">
        <div className="w-6 h-6 rounded bg-gradient-to-br from-orange-500/20 to-red-500/20 flex items-center justify-center">
          <svg className="w-3 h-3 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
        </div>
        <Text className="font-semibold text-white">Calibración Angular</Text>
      </div>
      <div className="space-y-2">
        <Text className="text-sm font-medium text-white/80">Precisión Angular</Text>
        <TextField.Root className="input-glass" value={ap} onChange={e => { setAp(e.target.value); controller.setResults({ angular_precision: e.target.value }) }} placeholder='Ej: ±02"'/>
      </div>
      <div className="space-y-4">
        <Text className="text-sm font-medium text-white/80">Mediciones</Text>
        <div className="overflow-auto">
          <div className="min-w-[540px] space-y-3">
            <div className="grid grid-cols-12 gap-3 text-sm font-medium text-white/70 p-3 bg-white/5 rounded-lg border border-white/10">
              <div className="col-span-4">Valor de Patrón</div>
              <div className="col-span-4">Valor Obtenido</div>
              <div className="col-span-3">Error</div>
              <div className="col-span-1">Acción</div>
            </div>
            {rows.map((row, idx) => (
              <div key={idx} className="grid grid-cols-12 gap-3 p-3 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors">
                <div className="col-span-4">
                  <TextField.Root className="input-glass" value={row.pattern} onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], pattern: e.target.value }; controller.setResults({ angular_measurements: n }) }} placeholder="Patrón"/>
                </div>
                <div className="col-span-4">
                  <TextField.Root className="input-glass" value={row.obtained} onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], obtained: e.target.value }; controller.setResults({ angular_measurements: n }) }} placeholder="Obtenido"/>
                </div>
                <div className="col-span-3">
                  <TextField.Root className="input-glass" value={row.error} onChange={e => { const n = [...rows]; n[idx] = { ...n[idx], error: e.target.value }; controller.setResults({ angular_measurements: n }) }} placeholder="Error"/>
                </div>
                <div className="col-span-1 flex items-center">
                  <IconButton className="btn-glass w-8 h-8" onClick={() => { const n = rows.filter((_, i) => i !== idx); controller.setResults({ angular_measurements: n }) }}>
                    <TrashIcon className="w-4 h-4"/>
                  </IconButton>
                </div>
              </div>
            ))}
            <div className="pt-2">
              <Button className="btn-glass flex items-center gap-2" onClick={() => controller.setResults({ angular_measurements: [...rows, { pattern: '', obtained: '', error: '' }] })}>
                <PlusIcon className="w-4 h-4"/> Añadir Medición
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}
