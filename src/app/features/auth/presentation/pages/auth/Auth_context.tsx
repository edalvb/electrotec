'use client'
import { createContext, useContext } from 'react'
import { AuthController } from './Auth_controller'
import { AuthStore } from './Auth_store'

type Ctx = { controller: AuthController; store: AuthStore }
export const AuthContext = createContext<Ctx | null>(null)
export function useAuthContext(){ const ctx = useContext(AuthContext); if(!ctx) throw new Error('AuthContext'); return ctx }
