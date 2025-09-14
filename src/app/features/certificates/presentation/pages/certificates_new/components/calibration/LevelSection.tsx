'use client'
import { Text, TextField } from '@radix-ui/themes'
import NumberField from '../common/NumberField'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function LevelSection() {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  return (
    <div className="space-y-4 p-4 rounded-lg bg-white/5 border border-white/20">
      <div className="flex items-center gap-3">
        <div className="w-6 h-6 rounded bg-gradient-to-br from-emerald-500/20 to-green-500/20 flex items-center justify-center">
          <svg className="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
          </svg>
        </div>
        <Text className="font-semibold text-white">Calibración de Nivel</Text>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="space-y-2">
          <Text className="text-sm font-medium text-white/80">Precisión (mm)</Text>
          <NumberField 
            value={s.results.level_precision_mm} 
            onChange={v => controller.setResults({ level_precision_mm: v })} 
            placeholder="Ej: 2.5"
          />
        </div>
        <div className="space-y-2">
          <Text className="text-sm font-medium text-white/80">Error</Text>
          <TextField.Root 
            className="input-glass" 
            value={s.results.level_error || ''} 
            onChange={e => controller.setResults({ level_error: e.target.value })} 
            placeholder="Descripción del error"
          />
        </div>
      </div>
    </div>
  )
}
