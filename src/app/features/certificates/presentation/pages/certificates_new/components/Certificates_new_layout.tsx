"use client"
import { useMemo, useState } from 'react'
import { Button, Card, Flex, Heading } from '@radix-ui/themes'
import { useCertificatesNew } from '../Certificates_new_context'
import { useCertificatesNewState } from '../certificates_new_states'
import Stepper from './common/Stepper'
import EquipmentStep from './equipment/EquipmentStep'
import CalibrationStep from './calibration/CalibrationStep'
import ReviewStep from './review/ReviewStep'

export default function CertificatesNewLayout() {
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
