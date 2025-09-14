import { AuthRepository } from '../../../data/auth_repository'

export class AuthStore {
  private repo: AuthRepository
  constructor(){ this.repo = new AuthRepository() }
  async signIn(email: string, password: string){ return this.repo.signIn(email, password) }
}
