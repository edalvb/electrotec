'use client'
import { Button, IconButton, Select, Text, TextField } from '@radix-ui/themes'
import { PlusIcon, TrashIcon } from '@heroicons/react/24/outline'
import { useCertificatesModal } from '../../Certificates_modal_context'
import { useCertificatesModalState } from '../../Certificates_modal_states'

export default function LevelResults(){
  const { controller } = useCertificatesModal()
  const s = useCertificatesModalState(st => st)
  const rows = s.results.level_rows || []
  return (
    <div className="space-y-4 p-4 rounded-lg bg-white/5 border border-white/20">
      <div className="flex items-center gap-3">
        <div className="w-6 h-6 rounded bg-gradient-to-br from-emerald-500/20 to-green-500/20 flex items-center justify-center">
          <svg className="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" /></svg>
        </div>
        <Text className="font-semibold text-white">Resultados de Nivel</Text>
      </div>
      <div className="overflow-auto">
        <div className="min-w-[720px] space-y-3">
          <div className="grid grid-cols-12 gap-3 text-sm font-medium text-white/70 p-3 bg-white/5 rounded-lg border border-white/10">
            <div className="col-span-4">Valor de Patrón (g° m&apos; s&quot;)</div>
            <div className="col-span-4">Valor Obtenido (g° m&apos; s&quot;)</div>
            <div className="col-span-2">Precisión</div>
            <div className="col-span-1">Error</div>
            <div className="col-span-1">Acción</div>
          </div>
          {rows.map((row, i) => (
            <div key={i} className="grid grid-cols-12 gap-3 p-3 bg-white/5 rounded-lg border border-white/10">
              <div className="col-span-4 grid grid-cols-3 gap-2">
                <TextField.Root className="input-glass" value={row.pattern.d.toString()} onChange={e => { const n = [...rows]; n[i] = { ...n[i], pattern: { ...n[i].pattern, d: Number(e.target.value||0) } }; controller.setResults({ level_rows: n }) }} placeholder="g"/>
                <TextField.Root className="input-glass" value={row.pattern.m.toString()} onChange={e => { const n = [...rows]; n[i] = { ...n[i], pattern: { ...n[i].pattern, m: Number(e.target.value||0) } }; controller.setResults({ level_rows: n }) }} placeholder="m"/>
                <TextField.Root className="input-glass" value={row.pattern.s.toString()} onChange={e => { const n = [...rows]; n[i] = { ...n[i], pattern: { ...n[i].pattern, s: Number(e.target.value||0) } }; controller.setResults({ level_rows: n }) }} placeholder="s"/>
              </div>
              <div className="col-span-4 grid grid-cols-3 gap-2">
                <TextField.Root className="input-glass" value={row.obtained.d.toString()} onChange={e => { const n = [...rows]; n[i] = { ...n[i], obtained: { ...n[i].obtained, d: Number(e.target.value||0) } }; controller.setResults({ level_rows: n }) }} placeholder="g"/>
                <TextField.Root className="input-glass" value={row.obtained.m.toString()} onChange={e => { const n = [...rows]; n[i] = { ...n[i], obtained: { ...n[i].obtained, m: Number(e.target.value||0) } }; controller.setResults({ level_rows: n }) }} placeholder="m"/>
                <TextField.Root className="input-glass" value={row.obtained.s.toString()} onChange={e => { const n = [...rows]; n[i] = { ...n[i], obtained: { ...n[i].obtained, s: Number(e.target.value||0) } }; controller.setResults({ level_rows: n }) }} placeholder="s"/>
              </div>
              <div className="col-span-2 flex items-center gap-2">
                <TextField.Root className="input-glass" value={row.precision.toString()} onChange={e => { const n = [...rows]; n[i] = { ...n[i], precision: Number(e.target.value||0) }; controller.setResults({ level_rows: n }) }} placeholder="Valor"/>
                <Select.Root value={row.precision_unit} onValueChange={(v) => { const n = [...rows]; n[i] = { ...n[i], precision_unit: v as 'mm'|'"' }; controller.setResults({ level_rows: n }) }}>
                  <Select.Trigger className="input-glass w-20"/>
                  <Select.Content>
                    <Select.Item value={'mm'}>mm</Select.Item>
                    <Select.Item value={'"'}>&quot;</Select.Item>
                  </Select.Content>
                </Select.Root>
              </div>
              <div className="col-span-1">
                <TextField.Root className="input-glass" value={row.error} onChange={e => { const n = [...rows]; n[i] = { ...n[i], error: e.target.value }; controller.setResults({ level_rows: n }) }} placeholder="Error"/>
              </div>
              <div className="col-span-1 flex items-center">
                <IconButton className="btn-glass w-8 h-8" onClick={() => { const n = rows.filter((_, idx) => idx !== i); controller.setResults({ level_rows: n }) }}>
                  <TrashIcon className="w-4 h-4"/>
                </IconButton>
              </div>
            </div>
          ))}
          <div className="pt-2">
            <Button className="btn-glass" onClick={() => controller.setResults({ level_rows: [...rows, { pattern: { d: 0, m: 0, s: 0 }, obtained: { d: 0, m: 0, s: 0 }, precision: 0, precision_unit: 'mm', error: '' }] })}>
              <PlusIcon className="w-4 h-4"/> Añadir Medición
            </Button>
          </div>
        </div>
      </div>
    </div>
  )
}
