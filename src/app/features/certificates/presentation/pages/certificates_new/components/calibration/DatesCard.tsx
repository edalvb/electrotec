'use client'
import { Card, Text, TextField } from '@radix-ui/themes'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function DatesCard() {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  return (
    <Card className="glass p-4">
      <Text size="3" className="text-muted">Fechas Importantes</Text>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
        <TextField.Root type="date" value={s.calibrationDate} onChange={e => controller.setDates(e.target.value, s.nextCalibrationDate)} placeholder="Fecha de Calibración"/>
        <TextField.Root type="date" value={s.nextCalibrationDate} onChange={e => controller.setDates(s.calibrationDate, e.target.value)} placeholder="Próxima Calibración"/>
      </div>
    </Card>
  )
}
