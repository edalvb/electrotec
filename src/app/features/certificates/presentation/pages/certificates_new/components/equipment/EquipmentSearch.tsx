'use client'
import { useEffect } from 'react'
import { Button, Flex, Text, TextField } from '@radix-ui/themes'
import { MagnifyingGlassIcon, PlusIcon } from '@heroicons/react/24/outline'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'

export default function EquipmentSearch({ onOpenNew }: { onOpenNew: () => void }) {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  useEffect(() => { const h = setTimeout(() => controller.searchEquipment(), 300); return () => clearTimeout(h) }, [controller, s.equipmentQuery])
  return (
    <Flex direction="column" gap="2">
      <Text size="3" className="text-muted">Número de Serie del Equipo</Text>
      <div className="relative">
        <TextField.Root value={s.equipmentQuery} onChange={(e) => controller.setQuery(e.target.value)} className="input-glass w-full">
          <TextField.Slot>
            <MagnifyingGlassIcon/>
          </TextField.Slot>
        </TextField.Root>
        {s.equipmentSuggestions.length > 0 && (
          <div className="absolute z-10 mt-2 w-full glass rounded-lg p-2 max-h-60 overflow-auto">
            {s.equipmentSuggestions.map(it => (
              <button key={it!.id} className="w-full text-left px-3 py-2 rounded hover:bg-white/10" onClick={() => controller.selectEquipment(it!.id)}>
                <div className="font-medium">{it!.serial_number} - {it!.brand} {it!.model}</div>
                <div className="text-sm text-muted">{it!.client?.name || 'Sin cliente'}</div>
              </button>
            ))}
          </div>
        )}
      </div>
      {s.isLoading && <Text className="text-muted">loading...</Text>}
      <Text className="text-muted">Empieza a escribir el número de serie para buscar un equipo.</Text>
      {!s.selectedEquipment && (
        <div>
          <Button className="btn-glass" onClick={onOpenNew}><PlusIcon className="h-4 w-4"/> Registrar Nuevo Equipo y Cliente</Button>
        </div>
      )}
    </Flex>
  )
}
