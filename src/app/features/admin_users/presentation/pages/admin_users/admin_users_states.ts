import { create } from 'zustand'

export type UserProfile = { id: string; full_name: string; signature_image_url: string | null; role: 'ADMIN' | 'TECHNICIAN'; is_active: boolean; created_at?: string }

type State = {
  items: UserProfile[]
  isLoading: boolean
  error: string | null
  inviteOpen: boolean
  editOpen: boolean
  editing: UserProfile | null
  setItems: (v: UserProfile[]) => void
  setIsLoading: (v: boolean) => void
  setError: (v: string | null) => void
  openInvite: () => void
  closeInvite: () => void
  openEdit: (u: UserProfile) => void
  closeEdit: () => void
}

export const useAdminUsersState = create<State>((set) => ({
  items: [],
  isLoading: false,
  error: null,
  inviteOpen: false,
  editOpen: false,
  editing: null,
  setItems: v => set({ items: v }),
  setIsLoading: v => set({ isLoading: v }),
  setError: v => set({ error: v }),
  openInvite: () => set({ inviteOpen: true }),
  closeInvite: () => set({ inviteOpen: false }),
  openEdit: (u) => set({ editOpen: true, editing: u }),
  closeEdit: () => set({ editOpen: false, editing: null })
}))
