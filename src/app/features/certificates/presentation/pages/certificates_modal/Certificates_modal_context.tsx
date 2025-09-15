'use client'
import { createContext, useContext, useEffect, useMemo, useState } from 'react'
import { CertificatesModalController } from './Certificates_modal_controller'
import { CertificatesModalStore } from './Certificates_modal_store'

type Ctx = { controller: CertificatesModalController; store: CertificatesModalStore }
const CertificatesModalCtx = createContext<Ctx | null>(null)

export function CertificatesModalProvider({ children }: { children: React.ReactNode }){
  const [controller] = useState(() => CertificatesModalController.instance())
  const [store] = useState(() => new CertificatesModalStore())
  useEffect(() => { controller.initialize(store) }, [controller, store])
  const value = useMemo(() => ({ controller, store }), [controller, store])
  return <CertificatesModalCtx.Provider value={value}>{children}</CertificatesModalCtx.Provider>
}

export function useCertificatesModal(){ const ctx = useContext(CertificatesModalCtx); if (!ctx) throw new Error('CertificatesModalProvider'); return ctx }
