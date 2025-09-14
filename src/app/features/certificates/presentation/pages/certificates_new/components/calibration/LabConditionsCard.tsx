'use client'
import { Card, Text } from '@radix-ui/themes'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'
import NumberField from '../common/NumberField'

export default function LabConditionsCard() {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  return (
    <Card className="glass p-6 border-2 border-white/20 bg-white/5 backdrop-blur-xl">
      <div className="flex items-center gap-3 mb-4">
        <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center">
          <svg className="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
          </svg>
        </div>
        <Text size="4" className="font-semibold text-white">Condiciones del Laboratorio</Text>
      </div>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="space-y-2">
          <Text className="text-sm font-medium text-white/80">Temperatura (°C)</Text>
          <NumberField value={s.lab.temperature} onChange={v => controller.setLab('temperature', v)} placeholder="Ej: 23.5"/>
        </div>
        <div className="space-y-2">
          <Text className="text-sm font-medium text-white/80">Humedad (%)</Text>
          <NumberField value={s.lab.humidity} onChange={v => controller.setLab('humidity', v)} placeholder="Ej: 65"/>
        </div>
        <div className="space-y-2">
          <Text className="text-sm font-medium text-white/80">Presión (mmHg)</Text>
          <NumberField value={s.lab.pressure} onChange={v => controller.setLab('pressure', v)} placeholder="Ej: 760"/>
        </div>
      </div>
    </Card>
  )
}
