'use client'
import { Card, Text } from '@radix-ui/themes'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function ConfirmCard() {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  return (
    <Card className="glass p-4">
      <Text>Confirmo que los datos ingresados son correctos y válidos.</Text>
      <div className="mt-2">
        <label className="inline-flex items-center gap-2">
          <input className="h-4 w-4 rounded border border-white/40 bg-white/20" type="checkbox" checked={s.confirmed} onChange={e => controller.setConfirmed(e.target.checked)} />
          <span className="text-muted">Acepto</span>
        </label>
      </div>
    </Card>
  )
}
