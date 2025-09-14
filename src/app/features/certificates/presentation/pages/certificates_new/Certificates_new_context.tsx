'use client'
import { createContext, useContext } from 'react'
import { CertificatesNewController } from './Certificates_new_controller'
import { CertificatesNewStore } from './Certificates_new_store'

type Ctx = { controller: CertificatesNewController; store: CertificatesNewStore }
export const CertificatesNewContext = createContext<Ctx | null>(null)
export function useCertificatesNew(){ const ctx = useContext(CertificatesNewContext); if (!ctx) throw new Error('no ctx'); return ctx }
