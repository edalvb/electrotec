import { CertificatePublicRepository } from '../../../data/certificate_public_repository'

export class CertificatePublicStore {
  private repo: CertificatePublicRepository
  constructor(){ this.repo = new CertificatePublicRepository() }
  async getById(id: string){ return this.repo.getById(id) }
}
