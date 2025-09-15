'use client'
import { useEffect, useMemo, useState } from 'react'
import { Badge, Card, Dialog, Flex, Heading, Separator, Table, Text, TextField } from '@radix-ui/themes'
import { ModernButton, ModernModal, ModernSelect } from '@/app/shared/ui'
import { useCertificatesModal } from '../Certificates_modal_context'
import { useCertificatesModalState } from '../Certificates_modal_states'
import AngularResults from './results/AngularResults'
import LevelResults from './results/LevelResults'
import DistanceResults from './results/DistanceResults'

function ClientPicker({ open, onOpenChange }: { open: boolean; onOpenChange: (o: boolean) => void }){
  const { controller } = useCertificatesModal()
  const s = useCertificatesModalState(st => st)
  useEffect(() => { if (!open) return; controller.resetClients(); controller.searchClients(1, 'replace') }, [open])
  useEffect(() => {
    if (!open) return
    const h = setTimeout(() => { controller.resetClients(); controller.searchClients(1, 'replace') }, 250)
    return () => clearTimeout(h)
  }, [open, s.clientQuery])
  const onScroll = (e: React.UIEvent<HTMLDivElement>) => {
    const el = e.currentTarget
    const nearBottom = el.scrollTop + el.clientHeight >= el.scrollHeight - 48
    if (nearBottom && !s.clientsLoading && s.clientsPage < s.clientsTotalPages) {
      controller.searchClients(s.clientsPage + 1, 'append')
    }
  }
  return (
    <ModernModal open={open} onOpenChange={onOpenChange} title="Seleccionar cliente" description="Busca y elige un cliente" size="lg">
      <div className="space-y-4">
        <TextField.Root className="input-glass" value={s.clientQuery} onChange={e => controller.setClientQuery(e.target.value)} placeholder="Buscar por nombre"/>
        <Card className="glass p-0 overflow-hidden border border-white/10">
          <div className="overflow-auto max-h-80" onScroll={onScroll}>
            <Table.Root variant="surface">
              <Table.Header>
                <Table.Row>
                  <Table.ColumnHeaderCell>Nombre</Table.ColumnHeaderCell>
                  <Table.ColumnHeaderCell>Acción</Table.ColumnHeaderCell>
                </Table.Row>
              </Table.Header>
              <Table.Body>
                {(s.clients || []).map(c => (
                  <Table.Row key={c.id}>
                    <Table.Cell className="text-white/90">{c.name}</Table.Cell>
                    <Table.Cell>
                      <ModernButton variant="primary" onClick={() => controller.askConfirmClient(c.id)}>Seleccionar</ModernButton>
                    </Table.Cell>
                  </Table.Row>
                ))}
                {s.clients.length === 0 && !s.clientsLoading && (
                  <Table.Row>
                    <Table.Cell colSpan={2}><Text className="text-white/60">Sin resultados</Text></Table.Cell>
                  </Table.Row>
                )}
                {s.clientsLoading && (
                  Array.from({ length: 6 }).map((_, i) => (
                    <Table.Row key={`sk-${i}`}>
                      <Table.Cell colSpan={2}>
                        <div className="animate-pulse flex items-center gap-3 py-1">
                          <div className="h-4 w-1/3 bg-white/10 rounded" />
                          <div className="h-8 w-24 bg-white/10 rounded" />
                        </div>
                      </Table.Cell>
                    </Table.Row>
                  ))
                )}
              </Table.Body>
            </Table.Root>
          </div>
        </Card>
        {/* Skeleton dentro de la tabla reemplaza el aviso de carga */}
        <Dialog.Root open={!!s.confirmClientId} onOpenChange={(o) => { if (!o) controller.cancelConfirm() }}>
          <Dialog.Content className="glass max-w-md">
            <Dialog.Title>Confirmar selección</Dialog.Title>
            <Dialog.Description>¿Confirmas el cliente seleccionado?</Dialog.Description>
            <Flex justify="end" gap="3" mt="4">
              <ModernButton variant="glass" onClick={() => controller.cancelConfirm()}>Cancelar</ModernButton>
              <ModernButton variant="primary" onClick={() => controller.confirmClient()}>Confirmar</ModernButton>
            </Flex>
          </Dialog.Content>
        </Dialog.Root>
      </div>
    </ModernModal>
  )
}

export default function CertificatesModalLayout({ onCreated, onClose }: { onCreated: (c: any) => void; onClose: () => void }){
  const { controller } = useCertificatesModal()
  const s = useCertificatesModalState(st => st)
  const [openClient, setOpenClient] = useState(false)
  const selectedEquipment = useMemo(() => s.equipmentList.find(e => e.id === s.equipmentId) || null, [s.equipmentList, s.equipmentId])
  const eqType = selectedEquipment?.equipment_type?.name || ''

  const canCreate = s.client && s.equipmentId && s.calibrationDate && s.nextCalibrationDate && s.technicianId && !s.isLoading

  return (
    <>
  <ModernModal open={true} onOpenChange={(o) => { if (!o) onClose() }} title="Nuevo certificado" description="Completa los datos y resultados" size="xl">
        <div className="space-y-6">
          <Card className="glass p-6 border border-white/10">
            <Heading size="5" className="text-white mb-4">Datos generales</Heading>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <Text className="text-sm text-white/80">Cliente</Text>
                <div className="flex items-center gap-3">
                  <Badge color="blue" className="text-white/90">{s.client ? s.client.name : 'Sin seleccionar'}</Badge>
                  <ModernButton variant="glass" onClick={() => setOpenClient(true)}>Seleccionar</ModernButton>
                </div>
                {s.errors.client && <Text className="text-red-400 text-sm">{s.errors.client}</Text>}
              </div>
              <div>
                <ModernSelect value={s.equipmentId} onValueChange={(v) => controller.setEquipment(v)} label="Equipo" placeholder={s.client ? 'Selecciona equipo' : 'Selecciona un cliente'} disabled={!s.client}>
                  {s.equipmentList.map(e => (
                    <ModernSelect.Item key={e.id} value={e.id}>{`${e.serial_number} · ${e.brand} ${e.model}`}</ModernSelect.Item>
                  ))}
                </ModernSelect>
                {s.errors.equipment && <Text className="text-red-400 text-sm">{s.errors.equipment}</Text>}
              </div>
              <div>
                <Text className="block text-sm text-white/80 mb-1">Fecha de Calibración</Text>
                <input type="date" className="input-glass w-full" value={s.calibrationDate} onChange={e => controller.setDate('calibrationDate', e.target.value)} />
                {s.errors.calibrationDate && <Text className="text-red-400 text-sm">{s.errors.calibrationDate}</Text>}
              </div>
              <div>
                <Text className="block text-sm text-white/80 mb-1">Próxima Calibración</Text>
                <input type="date" className="input-glass w-full" value={s.nextCalibrationDate} onChange={e => controller.setDate('nextCalibrationDate', e.target.value)} />
                {s.errors.nextCalibrationDate && <Text className="text-red-400 text-sm">{s.errors.nextCalibrationDate}</Text>}
              </div>
            </div>
            <Separator my="3" size="4"/>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <Text className="block text-sm text-white/80 mb-1">Temperatura (°C)</Text>
                <TextField.Root className="input-glass" value={s.lab.temperature?.toString() || ''} onChange={e => controller.setLab('temperature', e.target.value ? Number(e.target.value) : undefined)} />
              </div>
              <div>
                <Text className="block text-sm text-white/80 mb-1">Humedad (%)</Text>
                <TextField.Root className="input-glass" value={s.lab.humidity?.toString() || ''} onChange={e => controller.setLab('humidity', e.target.value ? Number(e.target.value) : undefined)} />
                {s.errors.humidity && <Text className="text-red-400 text-sm">{s.errors.humidity}</Text>}
              </div>
              <div>
                <Text className="block text-sm text-white/80 mb-1">Presión (mmHg)</Text>
                <TextField.Root className="input-glass" value={s.lab.pressure?.toString() || ''} onChange={e => controller.setLab('pressure', e.target.value ? Number(e.target.value) : undefined)} />
              </div>
              <div className="flex items-center gap-4 md:col-span-3">
                <label className="inline-flex items-center gap-2 text-white/80"><input type="checkbox" className="accent-blue-500" checked={!!s.lab.calibration} onChange={e => controller.setLab('calibration', e.target.checked)} /> Calibración</label>
                <label className="inline-flex items-center gap-2 text-white/80"><input type="checkbox" className="accent-blue-500" checked={!!s.lab.maintenance} onChange={e => controller.setLab('maintenance', e.target.checked)} /> Mantenimiento</label>
              </div>
            </div>
          </Card>

          <Card className="glass p-6 border border-white/10">
            <Heading size="5" className="text-white mb-4">Resultados</Heading>
            {eqType === 'Nivel' && (<LevelResults/>) }
            {(eqType === 'Teodolito' || eqType === 'Estación Total') && (<AngularResults/>) }
            {eqType === 'Estación Total' && (<DistanceResults/>) }
          </Card>

          {s.errors.auth && <Text className="text-red-400 text-sm">{s.errors.auth}</Text>}
          {s.errors.api && <Text className="text-red-400 text-sm">{s.errors.api}</Text>}
          <div className="flex justify-end gap-2">
            <ModernButton variant="glass" onClick={onClose}>Cancelar</ModernButton>
            <ModernButton variant="primary" disabled={!canCreate} loading={s.isLoading} onClick={async () => { const r = await controller.create(); if (r) onCreated({ id: r.id, certificate_number: r.certificate_number, calibration_date: s.calibrationDate, next_calibration_date: s.nextCalibrationDate, pdf_url: r.pdf_url, equipment: selectedEquipment }) }}>
              Crear
            </ModernButton>
          </div>
        </div>
      </ModernModal>
      <ClientPicker open={openClient} onOpenChange={(o) => setOpenClient(o)} />
    </>
  )
}
