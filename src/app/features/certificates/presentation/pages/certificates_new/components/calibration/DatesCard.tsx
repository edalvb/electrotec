'use client'
import { Card, Text, TextField } from '@radix-ui/themes'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function DatesCard() {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  return (
    <Card className="glass p-6 border-2 border-white/20 bg-white/5 backdrop-blur-xl">
      <div className="flex items-center gap-3 mb-4">
        <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
          <svg className="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
        </div>
        <Text size="4" className="font-semibold text-white">Fechas Importantes</Text>
      </div>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div className="space-y-2">
          <Text className="text-sm font-medium text-white/80">Fecha de Calibración</Text>
          <TextField.Root 
            className="input-glass" 
            type="date" 
            value={s.calibrationDate} 
            onChange={e => controller.setDates(e.target.value, s.nextCalibrationDate)}
          />
        </div>
        <div className="space-y-2">
          <Text className="text-sm font-medium text-white/80">Próxima Calibración</Text>
          <TextField.Root 
            className="input-glass" 
            type="date" 
            value={s.nextCalibrationDate} 
            onChange={e => controller.setDates(s.calibrationDate, e.target.value)}
          />
        </div>
      </div>
    </Card>
  )
}
