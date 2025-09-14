'use client'
import { Card, Heading, Separator, Text } from '@radix-ui/themes'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function SummaryCard() {
  const s = useCertificatesNewState(st => st)
  return (
    <Card className="glass p-4">
      <Heading size="4">Resumen</Heading>
      <Separator className="my-2"/>
      <Text>Equipo: {s.selectedEquipment?.equipment_type?.name} - {s.selectedEquipment?.brand} {s.selectedEquipment?.model} - {s.selectedEquipment?.serial_number}</Text>
      <Text>Cliente: {s.selectedEquipment?.client?.name || ''}</Text>
      <Text>Calibración: {s.calibrationDate} → {s.nextCalibrationDate}</Text>
      <Text>Condiciones: T {s.lab.temperature ?? '-'}°C, H {s.lab.humidity ?? '-'}%, P {s.lab.pressure ?? '-'} mmHg</Text>
    </Card>
  )
}
