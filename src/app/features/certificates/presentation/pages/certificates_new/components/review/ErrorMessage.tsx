'use client'
import { Text } from '@radix-ui/themes'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function ErrorMessage() {
  const s = useCertificatesNewState(st => st)
  if (!s.errors.auth) return null
  return <div className="text-error">{s.errors.auth}</div>
}
