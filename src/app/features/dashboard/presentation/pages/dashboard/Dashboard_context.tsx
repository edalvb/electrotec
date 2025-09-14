'use client'
import { createContext, useContext } from 'react'
import { DashboardController } from './Dashboard_controller'
import { DashboardStore } from './Dashboard_store'

type Ctx = { controller: DashboardController; store: DashboardStore }
export const DashboardContext = createContext<Ctx | null>(null)
export function useDashboardContext(){ const ctx = useContext(DashboardContext); if(!ctx) throw new Error('DashboardContext'); return ctx }
