'use client'
import { Text, TextField } from '@radix-ui/themes'
import NumberField from '../common/NumberField'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function LevelSection() {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
      <NumberField value={s.results.level_precision_mm} onChange={v => controller.setResults({ level_precision_mm: v })} placeholder="Precisión (mm)"/>
      <TextField.Root className="input-glass" value={s.results.level_error || ''} onChange={e => controller.setResults({ level_error: e.target.value })} placeholder="Error"/>
    </div>
  )
}
