import { CertificatesRepository, type ResultsPayload, type LabConditions } from '../../../data/certificates_repository'
import { supabaseBrowser } from '@/lib/supabase/client'

export class CertificatesModalStore {
  private repo: CertificatesRepository
  constructor(){ this.repo = new CertificatesRepository() }
  async searchClients(params: { q?: string; page?: number; pageSize?: number }){ return this.repo.listClients(params) }
  async listEquipmentByClient(client_id: string){ return this.repo.listEquipmentByClient(client_id) }
  async createCertificate(payload: { equipment_id: string; calibration_date: string; next_calibration_date: string; lab_conditions?: LabConditions; results: ResultsPayload; technician_id: string }){ return this.repo.createCertificateAndPdf(payload) }
  async getTechnicianId(){ const sb = supabaseBrowser(); const { data } = await sb.auth.getUser(); return data.user?.id || null }
}
