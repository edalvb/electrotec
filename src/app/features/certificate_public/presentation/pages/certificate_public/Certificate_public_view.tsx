'use client'
import { useEffect, useState } from 'react'
import { CertificatePublicContext } from './Certificate_public_context'
import { CertificatePublicController } from './Certificate_public_controller'
import { CertificatePublicStore } from './Certificate_public_store'
import CertificatePublicLayout from './components/Certificate_public_layout'

export default function CertificatePublicView({ id }: { id: string }) {
  const [controller] = useState(() => CertificatePublicController.instance())
  const [store] = useState(() => new CertificatePublicStore())
  const [ready, setReady] = useState(false)
  useEffect(() => { controller.initialize(store, id).finally(() => setReady(true)) }, [controller, store, id])
  if (!ready) return null
  return (
    <CertificatePublicContext.Provider value={{ controller, store }}>
      <CertificatePublicLayout/>
    </CertificatePublicContext.Provider>
  )
}
