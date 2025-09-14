'use client'
import { Text } from '@radix-ui/themes'

export default function Stepper({ step }: { step: 1 | 2 | 3 }) {
  const items = [
    { n: 1, t: 'Equipo' },
    { n: 2, t: 'Calibración' },
    { n: 3, t: 'Revisar y Generar' }
  ]
  return (
    <div className="grid grid-cols-3 gap-3">
      {items.map(i => (
        <div key={i.n} className={`glass rounded-lg px-4 py-2 text-center ${step === i.n ? 'ring-2 ring-primary/80' : ''}`}>
          <Text className="text-muted">{i.n}. {i.t}</Text>
        </div>
      ))}
    </div>
  )
}
