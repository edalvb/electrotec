'use client'
import { DashboardStore } from './Dashboard_store'
import { useDashboardState } from './dashboard_states'

export class DashboardController {
  private static _instance: DashboardController
  private store: DashboardStore | null = null
  static instance() { if (!this._instance) this._instance = new DashboardController(); return this._instance }
  async initialize(store: DashboardStore) {
    this.store = store
    const profile = await store.getProfile()
    useDashboardState.getState().setProfileName(profile?.full_name || '')
    const s = await store.getSummary()
    useDashboardState.getState().setSummary(s)
  }
}
