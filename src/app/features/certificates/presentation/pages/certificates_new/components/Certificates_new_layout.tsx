'use client'
import { useEffect, useMemo, useState } from 'react'
import Image from 'next/image'
import { useCertificatesNew } from '../Certificates_new_context'
import { useCertificatesNewState } from '../certificates_new_states'
import { Button, Card, Flex, Heading, IconButton, Separator, Text, TextField } from '@radix-ui/themes'
import { MagnifyingGlassIcon, PlusIcon, TrashIcon } from '@heroicons/react/24/outline'

function Stepper({ step }: { step: 1 | 2 | 3 }){
  const items = [
    { n: 1, t: 'Equipo' },
    { n: 2, t: 'Calibración' },
    { n: 3, t: 'Revisar y Generar' }
  ]
  return (
    <div className="grid grid-cols-3 gap-3">
      {items.map(i => (
        <div key={i.n} className={`glass rounded-lg px-4 py-2 text-center ${step === i.n ? 'ring-2 ring-primary/80' : ''}`}>
          <Text className="text-muted">{i.n}. {i.t}</Text>
        </div>
      ))}
    </div>
  )
}

function EquipmentStep(){
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  const [showNew, setShowNew] = useState(false)
  useEffect(() => { const h = setTimeout(() => controller.searchEquipment(), 300); return () => clearTimeout(h) }, [controller, s.equipmentQuery])
  return (
    <Flex direction="column" gap="4">
      <Flex direction="column" gap="2">
        <Text size="3" className="text-muted">Número de Serie del Equipo</Text>
        <div className="relative">
          <TextField.Root value={s.equipmentQuery} onChange={(e) => controller.setQuery(e.target.value)} className="input-glass w-full">
            <TextField.Slot>
              <MagnifyingGlassIcon/>
            </TextField.Slot>
          </TextField.Root>
          {s.equipmentSuggestions.length > 0 && (
            <div className="absolute z-10 mt-2 w-full glass rounded-lg p-2 max-h-60 overflow-auto">
              {s.equipmentSuggestions.map(it => (
                <button key={it!.id} className="w-full text-left px-3 py-2 rounded hover:bg-white/10" onClick={() => controller.selectEquipment(it!.id)}>
                  <div className="font-medium">{it!.serial_number} - {it!.brand} {it!.model}</div>
                  <div className="text-sm text-muted">{it!.client?.name || 'Sin cliente'}</div>
                </button>
              ))}
            </div>
          )}
        </div>
        {s.isLoading && <Text className="text-muted">loading...</Text>}
        <Text className="text-muted">Empieza a escribir el número de serie para buscar un equipo.</Text>
      </Flex>

      {s.selectedEquipment ? (
        <Card className="glass p-4">
          <Flex direction="column" gap="2">
            <Text size="3" className="text-muted">Equipo Seleccionado</Text>
            <Text>Tipo: <span className="text-primary">{s.selectedEquipment.equipment_type?.name || ''}</span></Text>
            <Text>Marca: <span className="text-primary">{s.selectedEquipment.brand}</span></Text>
            <Text>Modelo: <span className="text-primary">{s.selectedEquipment.model}</span></Text>
            <Text>Propietario: <span className="text-primary">{s.selectedEquipment.client?.name || ''}</span></Text>
          </Flex>
        </Card>
      ) : (
        <div>
          <Button variant="soft" onClick={() => setShowNew(true)}><PlusIcon/> Registrar Nuevo Equipo y Cliente</Button>
        </div>
      )}

      {showNew && <NewEquipmentModal onClose={() => setShowNew(false)}/>}        
    </Flex>
  )
}

function NewEquipmentModal({ onClose }: { onClose: () => void }){
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

function NumberField({ value, onChange, placeholder }: { value: number | undefined; onChange: (n: number | undefined) => void; placeholder: string }){
  return <TextField.Root value={value == null ? '' : String(value)} onChange={e => { const v = e.target.value; onChange(v === '' ? undefined : Number(v)) }} placeholder={placeholder}/>
}

function CalibrationStep(){
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  const typeName = s.selectedEquipment?.equipment_type?.name || ''
  const [ap, setAp] = useState('')
  const [dp, setDp] = useState('')
  const angularRows = useMemo(() => (s.results.angular_measurements || []), [s.results])
  const prismRows = useMemo(() => (s.results.prism_measurements || []), [s.results])
  const noPrismRows = useMemo(() => (s.results.no_prism_measurements || []), [s.results])
  useEffect(() => { if (typeName === 'Nivel') { controller.setResults({ level_precision_mm: s.results.level_precision_mm || undefined }) } }, [controller, s.results.level_precision_mm, typeName])
  return (
    <Flex direction="column" gap="4">
      <Card className="glass p-4">
        <Text size="3" className="text-muted">Fechas Importantes</Text>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
          <TextField.Root type="date" value={s.calibrationDate} onChange={e => controller.setDates(e.target.value, s.nextCalibrationDate)} placeholder="Fecha de Calibración"/>
          <TextField.Root type="date" value={s.nextCalibrationDate} onChange={e => controller.setDates(s.calibrationDate, e.target.value)} placeholder="Próxima Calibración"/>
        </div>
      </Card>

      <Card className="glass p-4">
        <Text size="3" className="text-muted">Condiciones del Laboratorio</Text>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-3 mt-2">
          <NumberField value={s.lab.temperature} onChange={v => controller.setLab('temperature', v)} placeholder="Temperatura (°C)"/>
          <NumberField value={s.lab.humidity} onChange={v => controller.setLab('humidity', v)} placeholder="Humedad (%)"/>
          <NumberField value={s.lab.pressure} onChange={v => controller.setLab('pressure', v)} placeholder="Presión (mmHg)"/>
        </div>
      </Card>

      <Card className="glass p-4">
        <Flex direction="column" gap="3">
          {['Teodolito', 'Estación Total'].includes(typeName) && (
            <div className="space-y-2">
              <Text className="font-medium">Calibración Angular</Text>
              <TextField.Root value={ap} onChange={e => { setAp(e.target.value); controller.setResults({ angular_precision: e.target.value }) }} placeholder='Precisión Angular (ej: ±02")'/>
              <div className="overflow-auto">
                <div className="min-w-[540px]">
                  <div className="grid grid-cols-12 gap-2 text-sm text-muted">
                    <div className="col-span-4">Valor de Patrón</div>
                    <div className="col-span-4">Valor Obtenido</div>
                    <div className="col-span-3">Error</div>
                    <div className="col-span-1"></div>
                  </div>
                  {angularRows.map((row, idx) => (
                    <div key={idx} className="grid grid-cols-12 gap-2 mt-2">
                      <TextField.Root value={row.pattern} onChange={e => { const n = [...angularRows]; n[idx] = { ...n[idx], pattern: e.target.value }; controller.setResults({ angular_measurements: n }) }}/>
                      <TextField.Root value={row.obtained} onChange={e => { const n = [...angularRows]; n[idx] = { ...n[idx], obtained: e.target.value }; controller.setResults({ angular_measurements: n }) }}/>
                      <TextField.Root value={row.error} onChange={e => { const n = [...angularRows]; n[idx] = { ...n[idx], error: e.target.value }; controller.setResults({ angular_measurements: n }) }}/>
                      <IconButton onClick={() => { const n = angularRows.filter((_, i) => i !== idx); controller.setResults({ angular_measurements: n }) }}><TrashIcon/></IconButton>
                    </div>
                  ))}
                  <div className="mt-3">
                    <Button variant="soft" onClick={() => controller.setResults({ angular_measurements: [...angularRows, { pattern: '', obtained: '', error: '' }] })}><PlusIcon/> Añadir Medición</Button>
                  </div>
                </div>
              </div>
            </div>
          )}
          {typeName === 'Estación Total' && (
            <div className="space-y-2">
              <Text className="font-medium">Precisión de Distancia</Text>
              <TextField.Root value={dp} onChange={e => { setDp(e.target.value); controller.setResults({ distance_precision: e.target.value }) }} placeholder="Precisión de Distancia (ej: 2 mm + 2 ppm)"/>
              <div className="space-y-4">
                <div>
                  <Text className="font-medium">Medición con Prisma</Text>
                  <div className="min-w-[540px]">
                    <div className="grid grid-cols-12 gap-2 text-sm text-muted">
                      <div className="col-span-4">Puntos de Control (m)</div>
                      <div className="col-span-4">Distancia Obtenida (m)</div>
                      <div className="col-span-3">Variación (m)</div>
                      <div className="col-span-1"></div>
                    </div>
                    {prismRows.map((row, idx) => (
                      <div key={idx} className="grid grid-cols-12 gap-2 mt-2">
                        <TextField.Root value={row.control.toString()} onChange={e => { const n = [...prismRows]; n[idx] = { ...n[idx], control: Number(e.target.value || 0) }; controller.setResults({ prism_measurements: n }) }}/>
                        <TextField.Root value={row.obtained.toString()} onChange={e => { const n = [...prismRows]; n[idx] = { ...n[idx], obtained: Number(e.target.value || 0) }; controller.setResults({ prism_measurements: n }) }}/>
                        <TextField.Root value={row.delta.toString()} onChange={e => { const n = [...prismRows]; n[idx] = { ...n[idx], delta: Number(e.target.value || 0) }; controller.setResults({ prism_measurements: n }) }}/>
                        <IconButton onClick={() => { const n = prismRows.filter((_, i) => i !== idx); controller.setResults({ prism_measurements: n }) }}><TrashIcon/></IconButton>
                      </div>
                    ))}
                    <div className="mt-3">
                      <Button variant="soft" onClick={() => controller.setResults({ prism_measurements: [...prismRows, { control: 0, obtained: 0, delta: 0 }] })}><PlusIcon/> Añadir Medición</Button>
                    </div>
                  </div>
                </div>
                <div>
                  <Text className="font-medium">Medición sin Prisma</Text>
                  <div className="min-w-[540px]">
                    <div className="grid grid-cols-12 gap-2 text-sm text-muted">
                      <div className="col-span-4">Puntos de Control (m)</div>
                      <div className="col-span-4">Distancia Obtenida (m)</div>
                      <div className="col-span-3">Variación (m)</div>
                      <div className="col-span-1"></div>
                    </div>
                    {noPrismRows.map((row, idx) => (
                      <div key={idx} className="grid grid-cols-12 gap-2 mt-2">
                        <TextField.Root value={row.control.toString()} onChange={e => { const n = [...noPrismRows]; n[idx] = { ...n[idx], control: Number(e.target.value || 0) }; controller.setResults({ no_prism_measurements: n }) }}/>
                        <TextField.Root value={row.obtained.toString()} onChange={e => { const n = [...noPrismRows]; n[idx] = { ...n[idx], obtained: Number(e.target.value || 0) }; controller.setResults({ no_prism_measurements: n }) }}/>
                        <TextField.Root value={row.delta.toString()} onChange={e => { const n = [...noPrismRows]; n[idx] = { ...n[idx], delta: Number(e.target.value || 0) }; controller.setResults({ no_prism_measurements: n }) }}/>
                        <IconButton onClick={() => { const n = noPrismRows.filter((_, i) => i !== idx); controller.setResults({ no_prism_measurements: n }) }}><TrashIcon/></IconButton>
                      </div>
                    ))}
                    <div className="mt-3">
                      <Button variant="soft" onClick={() => controller.setResults({ no_prism_measurements: [...noPrismRows, { control: 0, obtained: 0, delta: 0 }] })}><PlusIcon/> Añadir Medición</Button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}
          {typeName === 'Nivel' && (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
              <NumberField value={s.results.level_precision_mm} onChange={v => controller.setResults({ level_precision_mm: v })} placeholder="Precisión (mm)"/>
              <TextField.Root value={s.results.level_error || ''} onChange={e => controller.setResults({ level_error: e.target.value })} placeholder="Error"/>
            </div>
          )}
        </Flex>
      </Card>
    </Flex>
  )
}

function ReviewStep(){
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  return (
    <Flex direction="column" gap="4">
      <Card className="glass p-4">
        <Heading size="4">Resumen</Heading>
        <Separator className="my-2"/>
        <Text>Equipo: {s.selectedEquipment?.equipment_type?.name} - {s.selectedEquipment?.brand} {s.selectedEquipment?.model} - {s.selectedEquipment?.serial_number}</Text>
        <Text>Cliente: {s.selectedEquipment?.client?.name || ''}</Text>
        <Text>Calibración: {s.calibrationDate} → {s.nextCalibrationDate}</Text>
        <Text>Condiciones: T {s.lab.temperature ?? '-'}°C, H {s.lab.humidity ?? '-'}%, P {s.lab.pressure ?? '-'} mmHg</Text>
      </Card>
      <Card className="glass p-4">
        <Text>Técnico Certificador: {s.technician?.full_name || ''}</Text>
        {s.technician?.signature_image_url && (
          <div className="mt-2">
            <Image src={s.technician.signature_image_url} alt="Firma" width={240} height={64} className="h-16 w-auto object-contain"/>
          </div>
        )}
      </Card>
      <Card className="glass p-4">
        <Text>Confirmo que los datos ingresados son correctos y válidos.</Text>
        <div className="mt-2">
          <label className="inline-flex items-center gap-2">
            <input type="checkbox" checked={s.confirmed} onChange={e => controller.setConfirmed(e.target.checked)} />
            <span>Acepto</span>
          </label>
        </div>
      </Card>
      <div className="text-error">{s.errors.auth || ''}</div>
    </Flex>
  )
}

export default function CertificatesNewLayout(){
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  const canNext1 = !!s.selectedEquipment
  const canNext2 = useMemo(() => {
    const hasEq = !!s.selectedEquipment
    const datesOk = !!s.calibrationDate && !!s.nextCalibrationDate
    const typeName = s.selectedEquipment?.equipment_type?.name || ''
    if (typeName === 'Nivel') return hasEq && datesOk && s.results.level_precision_mm != null
    return hasEq && datesOk
  }, [s.selectedEquipment, s.calibrationDate, s.nextCalibrationDate, s.results.level_precision_mm])
  const [submitting, setSubmitting] = useState(false)
  const handlePrimary = async () => {
    if (s.step === 1) controller.next()
  else if (s.step === 2) { if (controller.validateStep2()) controller.next() }
    else if (s.step === 3) {
      if (!s.confirmed || submitting) return
      setSubmitting(true)
      const r = await controller.generate()
      setSubmitting(false)
      if (r?.pdf_url) window.open(r.pdf_url, '_blank')
    }
  }
  return (
    <div className="min-h-screen p-6 flex items-center justify-center">
      <div className="w-full max-w-3xl space-y-6">
        <div className="flex justify-between items-center">
          <Heading size="7" className="font-heading text-primary">Generar Nuevo Certificado</Heading>
          <Button variant="ghost" color="red" onClick={() => { window.location.href = '/'; }}>Cancelar</Button>
        </div>
        <Stepper step={s.step}/>
        <Card className="glass p-6">
          {s.step === 1 && <EquipmentStep/>}
          {s.step === 2 && <CalibrationStep/>}
          {s.step === 3 && <ReviewStep/>}
        </Card>
        <Flex justify="between">
          <Button variant="soft" disabled={s.step === 1} onClick={() => controller.back()}>Atrás</Button>
          <Button className="btn-primary" disabled={(s.step === 1 && !canNext1) || (s.step === 2 && !canNext2) || (s.step === 3 && (!s.confirmed || submitting))} onClick={handlePrimary}>
            {s.step < 3 ? 'Siguiente' : (submitting ? 'Generando…' : 'Generar')}
          </Button>
        </Flex>
      </div>
    </div>
  )
}
