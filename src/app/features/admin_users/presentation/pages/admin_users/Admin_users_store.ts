import { AdminUsersRepository } from '@/app/features/admin_users/data/admin_users_repository'

export class AdminUsersStore {
  private repo: AdminUsersRepository
  constructor(){ this.repo = new AdminUsersRepository() }
  async list(){ return this.repo.list() }
  async invite(input: { full_name: string; email: string; signature?: File | null }){ return this.repo.invite(input) }
  async update(id: string, input: { role?: 'ADMIN'|'TECHNICIAN'; is_active?: boolean; full_name?: string; signature?: File | null }){ return this.repo.update(id, input) }
}
