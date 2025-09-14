'use client'
import { Card, Flex, Text } from '@radix-ui/themes'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function SelectedEquipmentCard() {
  const s = useCertificatesNewState(st => st)
  if (!s.selectedEquipment) return null
  return (
    <Card className="glass p-4">
      <Flex direction="column" gap="2">
        <Text size="3" className="text-muted">Equipo Seleccionado</Text>
        <Text>Tipo: <span className="text-primary">{s.selectedEquipment.equipment_type?.name || ''}</span></Text>
        <Text>Marca: <span className="text-primary">{s.selectedEquipment.brand}</span></Text>
        <Text>Modelo: <span className="text-primary">{s.selectedEquipment.model}</span></Text>
        <Text>Propietario: <span className="text-primary">{s.selectedEquipment.client?.name || ''}</span></Text>
      </Flex>
    </Card>
  )
}
