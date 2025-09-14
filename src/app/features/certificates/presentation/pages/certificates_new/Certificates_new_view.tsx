'use client'
import { useEffect, useState } from 'react'
import { CertificatesNewContext } from './Certificates_new_context'
import { CertificatesNewController } from './Certificates_new_controller'
import { CertificatesNewStore } from './Certificates_new_store'
import CertificatesNewLayout from './components/Certificates_new_layout'

export default function CertificatesNewView(){
  const [controller] = useState(() => CertificatesNewController.instance())
  const [store] = useState(() => new CertificatesNewStore())
  const [ready, setReady] = useState(false)
  useEffect(() => { controller.initialize(store).finally(() => setReady(true)) }, [controller, store])
  if (!ready) return null
  return (
    <CertificatesNewContext.Provider value={{ controller, store }}>
      <CertificatesNewLayout/>
    </CertificatesNewContext.Provider>
  )
}
