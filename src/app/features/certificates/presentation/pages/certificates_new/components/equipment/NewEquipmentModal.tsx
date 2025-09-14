'use client'
import { useState } from 'react'
import { Button, Card, Flex, Heading, Separator, TextField } from '@radix-ui/themes'
import { useCertificatesNew } from '../../Certificates_new_context'

export default function NewEquipmentModal({ onClose }: { onClose: () => void }) {
  const { controller } = useCertificatesNew()
  const [serial_number, setSN] = useState('')
  const [brand, setBrand] = useState('')
  const [model, setModel] = useState('')
  const [equipment_type_id, setType] = useState<number>(1)
  const [clientName, setClientName] = useState('')
  const can = serial_number && brand && model
  return (
    <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-20">
      <Card className="glass p-6 w-full max-w-lg">
        <Flex direction="column" gap="4">
          <Heading size="6">Registrar Equipo y Cliente</Heading>
          <TextField.Root value={serial_number} onChange={e => setSN(e.target.value)} placeholder="Número de Serie"/>
          <TextField.Root value={brand} onChange={e => setBrand(e.target.value)} placeholder="Marca"/>
          <TextField.Root value={model} onChange={e => setModel(e.target.value)} placeholder="Modelo"/>
          <TextField.Root value={String(equipment_type_id)} onChange={e => setType(Number(e.target.value))} placeholder="ID Tipo de Equipo (1=Estación Total,2=Teodolito,3=Nivel)"/>
          <Separator/>
          <TextField.Root value={clientName} onChange={e => setClientName(e.target.value)} placeholder="Nombre del Cliente (opcional)"/>
          <Flex justify="between" gap="4">
            <Button variant="soft" onClick={onClose}>Cancelar</Button>
            <Button disabled={!can} onClick={async () => { await controller.createEquipment({ equipment: { serial_number, brand, model, equipment_type_id }, client: clientName ? { name: clientName } : undefined }); onClose() }}>Guardar</Button>
          </Flex>
        </Flex>
      </Card>
    </div>
  )
}
