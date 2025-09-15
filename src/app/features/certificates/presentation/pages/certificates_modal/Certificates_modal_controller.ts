'use client'
import { z } from 'zod'
import { CertificatesModalStore } from './Certificates_modal_store'
import { useCertificatesModalState } from './Certificates_modal_states'

const datesSchema = z.object({ calibrationDate: z.string().min(1), nextCalibrationDate: z.string().min(1) })

export class CertificatesModalController {
  private static _i: CertificatesModalController
  private store: CertificatesModalStore | null = null
  static instance(){ if (!this._i) this._i = new CertificatesModalController(); return this._i }
  async initialize(store: CertificatesModalStore){ this.store = store; const tid = await store.getTechnicianId(); useCertificatesModalState.getState().set({ technicianId: tid }) }
  open(){ useCertificatesModalState.getState().set({ isOpen: true }) }
  close(){ useCertificatesModalState.getState().set({ isOpen: false }); useCertificatesModalState.getState().reset() }
  setClientQuery(q: string){ useCertificatesModalState.getState().set({ clientQuery: q }) }
  async searchClients(page?: number, mode: 'replace'|'append' = 'replace'){
    const s = useCertificatesModalState.getState()
    if (s.clientsLoading) return
    const reqPage = page || s.clientsPage || 1
    useCertificatesModalState.getState().set({ clientsLoading: true })
    try {
      const r = await this.store!.searchClients({ q: s.clientQuery || '', page: reqPage, pageSize: s.clientsPageSize })
      const newItems = mode === 'append' ? [...s.clients, ...r.items] : r.items
      useCertificatesModalState.getState().set({
        clients: newItems,
        clientsPage: r.pagination.page,
        clientsPageSize: r.pagination.pageSize,
        clientsTotal: r.pagination.total,
        clientsTotalPages: r.pagination.totalPages
      })
    } finally {
      useCertificatesModalState.getState().set({ clientsLoading: false })
    }
  }
  resetClients(){ useCertificatesModalState.getState().set({ clients: [], clientsPage: 1, clientsTotal: 0, clientsTotalPages: 1 }) }
  askConfirmClient(id: string){ useCertificatesModalState.getState().set({ confirmClientId: id }) }
  confirmClient(){ const { clients, confirmClientId } = useCertificatesModalState.getState(); const c = clients.find(x => x.id === confirmClientId) || null; useCertificatesModalState.getState().set({ client: c, confirmClientId: null }); if (c) this.loadEquipment(c.id) }
  cancelConfirm(){ useCertificatesModalState.getState().set({ confirmClientId: null }) }
  async loadEquipment(client_id: string){ useCertificatesModalState.getState().set({ isLoading: true }); try { const list = await this.store!.listEquipmentByClient(client_id); const mapped = list.map(i => ({ id: i.id, serial_number: i.serial_number, brand: i.brand, model: i.model, equipment_type: i.equipment_type })); useCertificatesModalState.getState().set({ equipmentList: mapped }) } finally { useCertificatesModalState.getState().set({ isLoading: false }) } }
  setEquipment(id: string){ useCertificatesModalState.getState().set({ equipmentId: id }) }
  setDate(field: 'calibrationDate'|'nextCalibrationDate', v: string){
    if (field === 'calibrationDate') useCertificatesModalState.getState().set({ calibrationDate: v })
    else useCertificatesModalState.getState().set({ nextCalibrationDate: v })
  }
  setLab(field: 'temperature'|'humidity'|'pressure'|'calibration'|'maintenance', v: number | boolean | undefined){
    const { lab } = useCertificatesModalState.getState()
    const next = { ...lab } as Record<string, number | boolean | undefined>
    next[field] = v
  useCertificatesModalState.getState().set({ lab: next as { temperature?: number; humidity?: number; pressure?: number; calibration?: boolean; maintenance?: boolean } })
  }
  setResults(partial: Record<string, unknown>){ const { results } = useCertificatesModalState.getState(); useCertificatesModalState.getState().set({ results: { ...results, ...partial } }) }
  validate(){ const s = useCertificatesModalState.getState(); const errs: Record<string,string> = {}; if (!s.client) errs.client = 'Selecciona un cliente'; if (!s.equipmentId) errs.equipment = 'Selecciona un equipo'; const dz = datesSchema.safeParse({ calibrationDate: s.calibrationDate, nextCalibrationDate: s.nextCalibrationDate }); if (!dz.success) errs.dates = 'Fechas requeridas'; const today = new Date().toISOString().slice(0,10); if (s.calibrationDate && s.calibrationDate > today) errs.calibrationDate = 'No puede ser futura'; if (s.calibrationDate && s.nextCalibrationDate && s.nextCalibrationDate <= s.calibrationDate) errs.nextCalibrationDate = 'Debe ser posterior'; if (s.lab.humidity != null && (s.lab.humidity < 0 || s.lab.humidity > 100)) errs.humidity = '0-100'; useCertificatesModalState.getState().set({ errors: errs }); return Object.keys(errs).length === 0 }
  async create(){
    const s = useCertificatesModalState.getState()
    if (!this.validate()) return null
    const tech = s.technicianId
    if (!tech) { useCertificatesModalState.getState().set({ errors: { auth: 'Debes iniciar sesión' } }); return null }
    useCertificatesModalState.getState().set({ isLoading: true, errors: {} })
    try {
      const r = await this.store!.createCertificate({
        equipment_id: s.equipmentId,
        calibration_date: s.calibrationDate,
        next_calibration_date: s.nextCalibrationDate,
        lab_conditions: s.lab,
        results: s.results,
        technician_id: tech
      })
      return r
    } catch (e: unknown) {
      type ApiError = { response?: { data?: { details?: string; error?: string } } }
      const api = (e as ApiError)?.response?.data
      const msg = api?.details || api?.error || (e instanceof Error ? e.message : undefined) || 'Error inesperado'
      useCertificatesModalState.getState().set({ errors: { api: typeof msg === 'string' ? msg : 'No se pudo crear el certificado' } })
      return null
    } finally {
      useCertificatesModalState.getState().set({ isLoading: false })
    }
  }
}
