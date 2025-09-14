'use client'
import { useCertificatePublicState } from '../certificate_public_states'
import { Button, Card, Flex, Heading, Separator, Text } from '@radix-ui/themes'

function statusColor(next: string | null) {
  if (!next) return 'red'
  const d = new Date(next)
  const now = new Date()
  const diff = Math.ceil((d.getTime() - now.getTime()) / (1000 * 60 * 60 * 24))
  if (diff < 0) return 'red'
  if (diff <= 30) return 'orange'
  return 'green'
}

export default function CertificatePublicLayout() {
  const { data } = useCertificatePublicState(s => s)
  const color = statusColor(data?.next_calibration_date || null)
  return (
    <div className="min-h-screen p-6 flex items-center justify-center">
      <div className="w-full max-w-3xl space-y-6">
        <div className="flex justify-center">
          <Heading size="7">ELECTROTEC CONSULTING S.A.C.</Heading>
        </div>
        <Card className="glass p-6">
          <Flex direction="column" gap="4">
            <Heading size="6">Certificado de Calibración N° {data?.certificate_number || ''}</Heading>
            <div className={`rounded-lg p-4 ${color === 'green' ? 'bg-emerald-600/30' : color === 'orange' ? 'bg-amber-600/30' : 'bg-red-600/30'}`}>
              <Text size="5">{color === 'green' ? 'VIGENTE' : color === 'orange' ? 'PRÓXIMO A VENCER' : 'VENCIDO'}</Text>
            </div>
            <Separator size="4"/>
            <Flex direction="column" gap="2">
              <Text size="3">Equipo</Text>
              <Text>{data?.equipment_type?.name || ''}</Text>
              <Text>{data?.equipment ? `${data.equipment.brand} ${data.equipment.model}` : ''}</Text>
              <Text>N° Serie: {data?.equipment?.serial_number || ''}</Text>
            </Flex>
            <Flex direction="column" gap="2">
              <Text size="3">Validez</Text>
              <Text>Fecha de Calibración: {data?.calibration_date || ''}</Text>
              <Text>Próxima Calibración: {data?.next_calibration_date || ''}</Text>
            </Flex>
            <Flex direction="column" gap="2">
              <Text size="3">Otorgado a</Text>
              <Text>{data?.client?.name || ''}</Text>
            </Flex>
            <Button onClick={() => { if (data?.pdf_url) window.open(data.pdf_url, '_blank') }} className="btn-primary w-full">Ver certificado completo (PDF)</Button>
          </Flex>
        </Card>
        <div className="flex justify-center">
          <Text className="text-muted">Certificado emitido por ELECTROTEC CONSULTING S.A.C.</Text>
        </div>
      </div>
    </div>
  )
}
