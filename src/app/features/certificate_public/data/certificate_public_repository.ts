import { http } from '@/lib/http/axios'

export class CertificatePublicRepository {
  async getById(id: string){ const r = await http.get(`/api/public/certificates/${id}`); return r.data }
}
