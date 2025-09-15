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
  setResults(partial: any): void { const { results } = useCertificatesModalState.getState(); useCertificatesModalState.getState().set({ results: { ...results, ...partial } }) }
  validate(){
    const s = useCertificatesModalState.getState();
    const errs: Record<string,string> = {};
    if (!s.client) errs.client = 'Selecciona un cliente';
    if (!s.equipmentId) errs.equipment = 'Selecciona un equipo';
    const dz = datesSchema.safeParse({ calibrationDate: s.calibrationDate, nextCalibrationDate: s.nextCalibrationDate });
    if (!dz.success) errs.dates = 'Fechas requeridas';
    const today = new Date().toISOString().slice(0,10);
    if (s.calibrationDate && s.calibrationDate > today) errs.calibrationDate = 'No puede ser futura';
    if (s.calibrationDate && s.nextCalibrationDate && s.nextCalibrationDate <= s.calibrationDate) errs.nextCalibrationDate = 'Debe ser posterior';
    if (s.lab.humidity != null && (s.lab.humidity < 0 || s.lab.humidity > 100)) errs.humidity = '0-100';

    // Validación de resultados según tipo de equipo para evitar 'results_validation'
    const selEq = s.equipmentList.find(e => e.id === s.equipmentId) || null
    const norm = (t: string) => t.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase()
    const tname = norm(selEq?.equipment_type?.name || '')
    const r = s.results as any
    if (tname.includes('estacion')) {
      const ok = !!r?.angular_precision && Array.isArray(r?.angular_measurements) && r.angular_measurements.length > 0 && !!r?.distance_precision && Array.isArray(r?.prism_measurements) && r.prism_measurements.length > 0 && Array.isArray(r?.no_prism_measurements) && r.no_prism_measurements.length > 0
      if (!ok) errs.results = 'Completa: Precisión angular, mediciones angulares, precisión de distancia y mediciones con/sin prisma.'
    } else if (tname.includes('teodolito')) {
      const ok = !!r?.angular_precision && Array.isArray(r?.angular_measurements) && r.angular_measurements.length > 0
      if (!ok) errs.results = 'Completa: Precisión angular y al menos una medición angular.'
    } else if (tname.includes('nivel')) {
      const rows = (r?.level_rows as any[]) || []
      const ok = rows.length > 0 && typeof rows[0]?.precision === 'number'
      if (!ok) errs.results = 'Agrega al menos una fila de nivel con precisión.'
    }

    useCertificatesModalState.getState().set({ errors: errs });
    return Object.keys(errs).length === 0
  }
  async create(){
    const s = useCertificatesModalState.getState()
    if (!this.validate()) return null
    const tech = s.technicianId
    if (!tech) { useCertificatesModalState.getState().set({ errors: { auth: 'Debes iniciar sesión' } }); return null }
    useCertificatesModalState.getState().set({ isLoading: true, errors: {} })
    try {
      // Determinar tipo de equipo para adaptar el payload de resultados al esquema del backend
      const selEq = s.equipmentList.find(e => e.id === s.equipmentId) || null
      const norm = (t: string) => t.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase()
      const tname = norm(selEq?.equipment_type?.name || '')

      // Sanitizar resultados por defecto (solo claves conocidas)
      const pickKnown = (r: Record<string, unknown>) => {
        const {
          angular_precision,
          angular_measurements,
          prism_measurements,
          no_prism_measurements,
          distance_precision,
          level_precision_mm,
          level_error,
          meta
        } = (r || {}) as any
        return {
          ...(angular_precision != null ? { angular_precision } : {}),
          ...(angular_measurements != null ? { angular_measurements } : {}),
          ...(prism_measurements != null ? { prism_measurements } : {}),
          ...(no_prism_measurements != null ? { no_prism_measurements } : {}),
          ...(distance_precision != null ? { distance_precision } : {}),
          ...(level_precision_mm != null ? { level_precision_mm } : {}),
          ...(level_error != null ? { level_error } : {}),
          ...(meta != null ? { meta } : {})
        }
      }

      let resultsPayload: Record<string, unknown> = {}
      if (tname.includes('nivel')) {
        // Mapear level_rows a la estructura requerida por el backend para NIVEL
        const rows = (s.results as any).level_rows as Array<{
          pattern: { d: number; m: number; s: number }
          obtained: { d: number; m: number; s: number }
          precision: number
          precision_unit: 'mm' | '"'
          error: string
        }> || []
        const fmtDms = (x: { d: number; m: number; s: number }) => `${Number(x?.d ?? 0)}° ${Number(x?.m ?? 0)}' ${Number(x?.s ?? 0)}"`
        const angular_measurements = rows.map(r => ({ pattern: fmtDms(r.pattern), obtained: fmtDms(r.obtained), error: r.error || '' }))
        // El backend espera level_precision_mm (número). Si el usuario eligió 'mm', usamos el valor tal cual.
        // Si eligió '"', no contamos con datos suficientes para convertir de segundos a mm; usamos el valor como está para no bloquear la creación.
        const first = rows[0]
        const level_precision_mm = typeof first?.precision === 'number' ? first.precision : undefined
        const level_error = (first?.error || rows.map(r => r.error).filter(Boolean).join(' / ') || '')
        resultsPayload = pickKnown({ angular_measurements, level_precision_mm, level_error, meta: (s.results as any).meta })
      } else {
        // Para estación total y teodolito, enviar solo claves conocidas y omitir level_rows
        resultsPayload = pickKnown(s.results as any)
      }

      const r = await this.store!.createCertificate({
        equipment_id: s.equipmentId,
        calibration_date: s.calibrationDate,
        next_calibration_date: s.nextCalibrationDate,
        lab_conditions: s.lab,
        results: resultsPayload as any,
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
