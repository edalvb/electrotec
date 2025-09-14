'use client'
import { useEffect, useState } from 'react'
import { DashboardContext } from './Dashboard_context'
import { DashboardController } from './Dashboard_controller'
import { DashboardStore } from './Dashboard_store'
import DashboardLayout from './components/Dashboard_layout'

export default function DashboardView() {
  const [controller] = useState(() => DashboardController.instance())
  const [store] = useState(() => new DashboardStore())
  const [ready, setReady] = useState(false)
  useEffect(() => { controller.initialize(store).finally(() => setReady(true)) }, [controller, store])
  if (!ready) return null
  return (
    <DashboardContext.Provider value={{ controller, store }}>
      <DashboardLayout/>
    </DashboardContext.Provider>
  )
}
