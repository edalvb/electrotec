'use client'
import { createContext, useContext } from 'react'
import { CertificatePublicController } from './Certificate_public_controller'
import { CertificatePublicStore } from './Certificate_public_store'

type Ctx = { controller: CertificatePublicController; store: CertificatePublicStore }
export const CertificatePublicContext = createContext<Ctx | null>(null)
export function useCertificatePublicContext(){ const ctx = useContext(CertificatePublicContext); if(!ctx) throw new Error('CertificatePublicContext'); return ctx }
