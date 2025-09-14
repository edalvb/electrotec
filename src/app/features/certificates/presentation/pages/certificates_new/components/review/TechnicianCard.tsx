'use client'
import Image from 'next/image'
import { Card, Text } from '@radix-ui/themes'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function TechnicianCard() {
  const s = useCertificatesNewState(st => st)
  return (
    <Card className="glass p-4">
      <Text className="font-heading text-primary">Técnico Certificador: {s.technician?.full_name || ''}</Text>
      {s.technician?.signature_image_url && (
        <div className="mt-2">
          <Image src={s.technician.signature_image_url} alt="Firma" width={240} height={64} className="h-16 w-auto object-contain"/>
        </div>
      )}
    </Card>
  )
}
