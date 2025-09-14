'use client'
import { z } from 'zod'
import { CertificatesNewStore } from './Certificates_new_store'
import { ResultsPayload } from '../../../data/certificates_repository'
import { useCertificatesNewState } from './certificates_new_states'

const datesSchema = z.object({ calibrationDate: z.string().min(1), nextCalibrationDate: z.string().min(1) })

export class CertificatesNewController {
  private static _instance: CertificatesNewController
  private store: CertificatesNewStore | null = null
  static instance(){ if (!this._instance) this._instance = new CertificatesNewController(); return this._instance }
  async initialize(store: CertificatesNewStore){
    this.store = store
    const tech = await store.getCurrentTechnicianProfile()
    if (tech) useCertificatesNewState.getState().set({ technician: tech })
  }

  setQuery(v: string){ useCertificatesNewState.getState().set({ equipmentQuery: v }) }

  async searchEquipment(){
    const { equipmentQuery } = useCertificatesNewState.getState()
    if (!equipmentQuery || equipmentQuery.length < 2) { useCertificatesNewState.getState().set({ equipmentSuggestions: [] }); return }
    useCertificatesNewState.getState().set({ isLoading: true })
    try {
      const list = await this.store!.searchEquipment(equipmentQuery)
      useCertificatesNewState.getState().set({ equipmentSuggestions: list })
    } finally { useCertificatesNewState.getState().set({ isLoading: false }) }
  }

  selectEquipment(id: string){
    const { equipmentSuggestions } = useCertificatesNewState.getState()
    const found = (equipmentSuggestions || []).find((i) => i && i.id === id) || null
    useCertificatesNewState.getState().set({ selectedEquipment: found })
  }

  async createEquipment(payload: { client?: { name: string; contact_details?: Record<string, unknown> }, equipment: { serial_number: string; brand: string; model: string; equipment_type_id: number } }){
    useCertificatesNewState.getState().set({ isLoading: true })
    try {
      const eq = await this.store!.createEquipmentAndClient(payload)
      useCertificatesNewState.getState().set({ selectedEquipment: eq, equipmentQuery: eq.serial_number, equipmentSuggestions: [] })
    } finally { useCertificatesNewState.getState().set({ isLoading: false }) }
  }

  setDates(calibrationDate: string, nextCalibrationDate: string){ useCertificatesNewState.getState().set({ calibrationDate, nextCalibrationDate }) }
  setLab(field: 'temperature'|'humidity'|'pressure', v: number | undefined){ const { lab } = useCertificatesNewState.getState(); useCertificatesNewState.getState().set({ lab: { ...lab, [field]: v } }) }
  setResults(partial: Partial<ResultsPayload>){ const { results } = useCertificatesNewState.getState(); useCertificatesNewState.getState().set({ results: { ...results, ...partial } }) }
  setConfirmed(v: boolean){ useCertificatesNewState.getState().set({ confirmed: v }) }

  next(){ const { step } = useCertificatesNewState.getState(); if (step < 3) useCertificatesNewState.getState().set({ step: ((step+1) as 2 | 3) }) }
  back(){ const { step } = useCertificatesNewState.getState(); if (step > 1) useCertificatesNewState.getState().set({ step: ((step-1) as 1 | 2) }) }

  validateStep2(){
    const s = useCertificatesNewState.getState()
    const errs: Record<string,string> = {}
    if (!s.selectedEquipment) errs.selectedEquipment = 'Selecciona un equipo'
    const dz = datesSchema.safeParse({ calibrationDate: s.calibrationDate, nextCalibrationDate: s.nextCalibrationDate })
    if (!dz.success) errs.dates = 'Completa fechas válidas'
    if (s.selectedEquipment?.equipment_type?.name === 'Nivel') {
      if (s.results.level_precision_mm == null) errs.level_precision_mm = 'Requerido'
    }
    useCertificatesNewState.getState().set({ errors: errs })
    return Object.keys(errs).length === 0
  }

  async generate(){
    const s = useCertificatesNewState.getState()
    if (!s.selectedEquipment) return
    const techId = await this.store!.getCurrentTechnicianId()
    if (!techId) { useCertificatesNewState.getState().set({ errors: { auth: 'Debes iniciar sesión' } }); return }
    useCertificatesNewState.getState().set({ isLoading: true })
    try {
      const r = await this.store!.createCertificateAndPdf({
        equipment_id: s.selectedEquipment.id,
        calibration_date: s.calibrationDate,
        next_calibration_date: s.nextCalibrationDate,
        lab_conditions: s.lab,
        results: s.results,
        technician_id: techId
      })
      return r
    } finally { useCertificatesNewState.getState().set({ isLoading: false }) }
  }
}
