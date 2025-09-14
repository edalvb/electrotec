import { create } from 'zustand'

type Summary = { issuedThisMonth: number; next30Days: number }

type State = { summary: Summary; profileName: string; setSummary: (s: Summary) => void; setProfileName: (v: string) => void }

export const useDashboardState = create<State>((set) => ({ summary: { issuedThisMonth: 0, next30Days: 0 }, profileName: '', setSummary: s => set({ summary: s }), setProfileName: v => set({ profileName: v }) }))
