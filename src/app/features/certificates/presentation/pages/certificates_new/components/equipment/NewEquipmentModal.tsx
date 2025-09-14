'use client'
import { useState } from 'react'
import { Button, Card, Flex, Heading, Separator, TextField } from '@radix-ui/themes'
import { useCertificatesNew } from '../../Certificates_new_context'
import Portal from '../common/Portal'

export default function NewEquipmentModal({ onClose }: { onClose: () => void }) {
  const { controller } = useCertificatesNew()
  const [serial_number, setSN] = useState('')
  const [brand, setBrand] = useState('')
  const [model, setModel] = useState('')
  const [equipment_type_id, setType] = useState<number>(1)
  const [clientName, setClientName] = useState('')
  const can = serial_number && brand && model
  return (
    <Portal>
      <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-[9999] p-4">
        <Card className="glass p-6 w-full max-w-lg max-h-[90vh] overflow-auto">
        <Flex direction="column" gap="4">
          <Heading size="6" className="font-heading text-primary">Registrar Equipo y Cliente</Heading>
          <TextField.Root className="input-glass" value={serial_number} onChange={e => setSN(e.target.value)} placeholder="Número de Serie"/>
          <TextField.Root className="input-glass" value={brand} onChange={e => setBrand(e.target.value)} placeholder="Marca"/>
          <TextField.Root className="input-glass" value={model} onChange={e => setModel(e.target.value)} placeholder="Modelo"/>
          <TextField.Root className="input-glass" value={String(equipment_type_id)} onChange={e => setType(Number(e.target.value))} placeholder="ID Tipo de Equipo (1=Estación Total,2=Teodolito,3=Nivel)"/>
          <Separator/>
          <TextField.Root className="input-glass" value={clientName} onChange={e => setClientName(e.target.value)} placeholder="Nombre del Cliente (opcional)"/>
          <Flex justify="between" gap="4">
            <Button className="btn-glass" onClick={onClose}>Cancelar</Button>
            <Button className="btn-primary" disabled={!can} onClick={async () => { await controller.createEquipment({ equipment: { serial_number, brand, model, equipment_type_id }, client: clientName ? { name: clientName } : undefined }); onClose() }}>Guardar</Button>
          </Flex>
        </Flex>
        </Card>
      </div>
    </Portal>
  )
}
