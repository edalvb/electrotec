'use client'
import { CertificatePublicStore } from './Certificate_public_store'
import { useCertificatePublicState } from './certificate_public_states'

export class CertificatePublicController {
  private static _instance: CertificatePublicController
  private store: CertificatePublicStore | null = null
  static instance() { if (!this._instance) this._instance = new CertificatePublicController(); return this._instance }
  async initialize(store: CertificatePublicStore, id: string) {
    this.store = store
    const data = await store.getById(id)
    useCertificatePublicState.getState().setData(data)
  }
}
