import { create } from 'zustand'

type State = { email: string; password: string; isLoading: boolean; error: string | null; setEmail: (v: string) => void; setPassword: (v: string) => void; setIsLoading: (v: boolean) => void; setError: (v: string | null) => void; reset: () => void }

export const useAuthState = create<State>((set) => ({ email: '', password: '', isLoading: false, error: null, setEmail: v => set({ email: v }), setPassword: v => set({ password: v }), setIsLoading: v => set({ isLoading: v }), setError: v => set({ error: v }), reset: () => set({ email: '', password: '', isLoading: false, error: null }) }))
