'use client'
import { Card, Text } from '@radix-ui/themes'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'
import NumberField from '../common/NumberField'

export default function LabConditionsCard() {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  return (
    <Card className="glass p-4">
      <Text size="3" className="text-muted">Condiciones del Laboratorio</Text>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">
        <NumberField value={s.lab.temperature} onChange={v => controller.setLab('temperature', v)} placeholder="Temperatura (°C)"/>
        <NumberField value={s.lab.humidity} onChange={v => controller.setLab('humidity', v)} placeholder="Humedad (%)"/>
        <NumberField value={s.lab.pressure} onChange={v => controller.setLab('pressure', v)} placeholder="Presión (mmHg)"/>
      </div>
    </Card>
  )
}
