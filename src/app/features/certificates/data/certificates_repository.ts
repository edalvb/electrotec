import { http } from '@/lib/http/axios'

export type SearchEquipmentItem = { id: string; serial_number: string; brand: string; model: string; client: { id: string; name: string } | null; equipment_type: { id: number; name: string } | null }

export type LabConditions = { temperature?: number; humidity?: number; pressure?: number }
export type AngularRow = { pattern: string; obtained: string; error: string }
export type DistanceRow = { control: number; obtained: number; delta: number }
export type ResultsPayload = {
  angular_precision?: string
  angular_measurements?: AngularRow[]
  prism_measurements?: DistanceRow[]
  no_prism_measurements?: DistanceRow[]
  distance_precision?: string
  level_precision_mm?: number
  level_error?: string
}

export class CertificatesRepository {
  async searchEquipment(q: string){ const r = await http.get('/api/equipment/search', { params: { q } }); return r.data.items as SearchEquipmentItem[] }
  async createEquipmentAndClient(payload: { client?: { name: string; contact_details?: Record<string, unknown> }, equipment: { serial_number: string; brand: string; model: string; equipment_type_id: number } }){ const r = await http.post('/api/equipment', payload); return r.data as SearchEquipmentItem }
  async createCertificateAndPdf(payload: { equipment_id: string; calibration_date: string; next_calibration_date: string; lab_conditions?: LabConditions; results: ResultsPayload; technician_id: string }){ const r = await http.post('/api/certificates/create', payload); return r.data as { id: string; certificate_number: string; pdf_url: string } }
  async listClients(params: { q?: string; page?: number; pageSize?: number } = {}){
    const { q = '', page = 1, pageSize = 50 } = params
    const r = await http.get('/api/clients', { params: { q, page, pageSize } })
    return r.data as { items: { id: string; name: string }[]; pagination: { page: number; pageSize: number; total: number; totalPages: number } }
  }
  async listEquipmentByClient(client_id: string){ const r = await http.get('/api/equipment', { params: { client_id } }); return r.data.items as SearchEquipmentItem[] }
  async listEquipmentTypes(){ const r = await http.get('/api/equipment/types'); return r.data.items as { id: number; name: string }[] }
}
