import { DashboardRepository } from '../../../data/dashboard_repository'

export class DashboardStore {
  private repo: DashboardRepository
  constructor(){ this.repo = new DashboardRepository() }
  async getSummary(){ return this.repo.getSummary() }
  async getProfile(){ return this.repo.getProfile() }
}
