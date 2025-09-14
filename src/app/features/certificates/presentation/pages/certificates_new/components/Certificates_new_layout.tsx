"use client"
import { useMemo, useState } from 'react'
import { Button, Card, Heading } from '@radix-ui/themes'
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
    <div className="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-indigo-950 p-6 flex items-center justify-center">
      <div className="w-full max-w-4xl space-y-8">
        <div className="flex justify-between items-center">
          <div className="space-y-2">
            <Heading size="8" className="font-heading bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">Generar Nuevo Certificado</Heading>
            <p className="text-white/60 text-lg">Sigue los pasos para crear un certificado de calibración</p>
          </div>
          <Button className="btn-glass transition-all hover:scale-105" onClick={() => { window.location.href = '/'; }}>
            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
            Cancelar
          </Button>
        </div>
        
        <Stepper step={s.step}/>
        
        <Card className="glass p-8 border-2 border-white/20 backdrop-blur-xl bg-white/10 shadow-2xl shadow-black/25">
          <div className="space-y-6">
            {s.step === 1 && <EquipmentStep/>}
            {s.step === 2 && <CalibrationStep/>}
            {s.step === 3 && <ReviewStep/>}
          </div>
        </Card>
        
        <div className="flex justify-between items-center pt-4">
          <Button 
            className="btn-glass px-6 py-3 transition-all hover:scale-105" 
            disabled={s.step === 1} 
            onClick={() => controller.back()}
          >
            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
            </svg>
            Atrás
          </Button>
          
          <div className="flex items-center gap-4">
            <div className="text-sm text-white/60">
              Paso {s.step} de 3
            </div>
            <Button 
              className="btn-primary px-8 py-3 text-base font-medium bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 active:scale-95" 
              disabled={(s.step === 1 && !canNext1) || (s.step === 2 && !canNext2) || (s.step === 3 && (!s.confirmed || submitting))} 
              onClick={handlePrimary}
            >
              {s.step < 3 ? (
                <div className="flex items-center gap-2">
                  <span>Siguiente</span>
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </div>
              ) : submitting ? (
                <div className="flex items-center gap-2">
                  <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  Generando...
                </div>
              ) : (
                <div className="flex items-center gap-2">
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Generar Certificado
                </div>
              )}
            </Button>
          </div>
        </div>
      </div>
    </div>
  )
}
