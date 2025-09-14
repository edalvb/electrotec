import { CertificatesRepository, LabConditions, ResultsPayload } from '../../../data/certificates_repository'
import { supabaseBrowser } from '@/lib/supabase/client'

export class CertificatesNewStore {
  private repo: CertificatesRepository
  constructor(){ this.repo = new CertificatesRepository() }
  async searchEquipment(q: string){ return this.repo.searchEquipment(q) }
  async createEquipmentAndClient(payload: { client?: { name: string; contact_details?: Record<string, unknown> }, equipment: { serial_number: string; brand: string; model: string; equipment_type_id: number } }){ return this.repo.createEquipmentAndClient(payload) }
  async createCertificateAndPdf(payload: { equipment_id: string; calibration_date: string; next_calibration_date: string; lab_conditions?: LabConditions; results: ResultsPayload; technician_id: string }){ return this.repo.createCertificateAndPdf(payload) }
  async getCurrentTechnicianId(){
    const sb = supabaseBrowser()
    const { data } = await sb.auth.getUser()
    return data.user?.id || null
  }
  async getCurrentTechnicianProfile(){
    const sb = supabaseBrowser()
    const { data } = await sb.auth.getUser()
    const id = data.user?.id
    if (!id) return null
    const { data: profile } = await sb.from('user_profiles').select('full_name, signature_image_url').eq('id', id).single()
    return profile || null
  }
}
