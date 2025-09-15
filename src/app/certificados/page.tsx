'use client'
import { useEffect, useState } from 'react'
import { Card, Heading, Text, Button, Table, Badge } from '@radix-ui/themes'
import { http } from '@/lib/http/axios'
import { ModernButton } from '@/app/shared/ui'
import { CertificatesModalProvider } from '@/app/features/certificates/presentation/pages/certificates_modal/Certificates_modal_context'
import CertificatesModalLayout from '@/app/features/certificates/presentation/pages/certificates_modal/components/Certificates_modal_layout'

type CertItem = {
  id: string
  certificate_number: string
  calibration_date: string
  next_calibration_date: string
  pdf_url?: string | null
  equipment: { id: string; serial_number: string; brand: string; model: string } | null
}

export default function CertificadosIndexPage() {
  const [items, setItems] = useState<CertItem[]>([])
  const [loading, setLoading] = useState(true)
  const [open, setOpen] = useState(false)

  useEffect(() => { (async () => {
    try {
      const r = await http.get('/api/certificates', { params: { pageSize: 50 } })
      setItems(r.data.items || [])
    } finally { setLoading(false) }
  })() }, [])

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-950 via-blue-950 to-indigo-950">
      <div className="container mx-auto p-8 max-w-6xl">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
          <div>
            <Heading size="8" className="font-heading bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">
              Certificados
            </Heading>
            <Text className="text-white/60">Listado de certificados de calibración</Text>
          </div>
          <ModernButton onClick={() => setOpen(true)} variant="primary" className="px-4 py-3">
            <span className="mr-2">+</span> Añadir certificado
          </ModernButton>
        </div>

        <Card className="glass p-0 overflow-hidden border border-white/10">
          <div className="overflow-x-auto">
            <Table.Root variant="surface">
              <Table.Header>
                <Table.Row>
                  <Table.ColumnHeaderCell>Número</Table.ColumnHeaderCell>
                  <Table.ColumnHeaderCell>Equipo</Table.ColumnHeaderCell>
                  <Table.ColumnHeaderCell>Fechas</Table.ColumnHeaderCell>
                  <Table.ColumnHeaderCell>PDF</Table.ColumnHeaderCell>
                  <Table.ColumnHeaderCell>Acciones</Table.ColumnHeaderCell>
                </Table.Row>
              </Table.Header>
              <Table.Body>
                {loading ? (
                  <Table.Row><Table.Cell colSpan={5}><span className="text-white/60">Cargando...</span></Table.Cell></Table.Row>
                ) : items.length === 0 ? (
                  <Table.Row><Table.Cell colSpan={5}><span className="text-white/60">Sin resultados</span></Table.Cell></Table.Row>
                ) : (
                  items.map(it => (
                    <Table.Row key={it.id}>
                      <Table.Cell className="text-white">{it.certificate_number}</Table.Cell>
                      <Table.Cell className="text-white/90">
                        <div className="flex flex-col">
                          <span className="font-medium">{it.equipment?.serial_number || '-'}</span>
                          <span className="text-white/60 text-sm">{it.equipment ? `${it.equipment.brand} ${it.equipment.model}` : ''}</span>
                        </div>
                      </Table.Cell>
                      <Table.Cell className="text-white/80">
                        <div className="flex flex-col">
                          <span>Cal.: {new Date(it.calibration_date).toLocaleDateString()}</span>
                          <span className="text-white/60 text-sm">Próx.: {new Date(it.next_calibration_date).toLocaleDateString()}</span>
                        </div>
                      </Table.Cell>
                      <Table.Cell>
                        {it.pdf_url ? (
                          <Badge color="green"><a href={it.pdf_url} target="_blank" rel="noreferrer">Disponible</a></Badge>
                        ) : (
                          <Badge color="gray">No generado</Badge>
                        )}
                      </Table.Cell>
                      <Table.Cell>
                        <div className="flex gap-2">
                          <Button size="1" variant="soft" disabled>Editar</Button>
                          <Button size="1" onClick={async () => {
                            const r = await http.post('/api/certificates', { id: it.id })
                            if (r.data?.pdf_url) window.open(r.data.pdf_url, '_blank')
                          }}>Generar PDF</Button>
                        </div>
                      </Table.Cell>
                    </Table.Row>
                  ))
                )}
              </Table.Body>
            </Table.Root>
          </div>
        </Card>

        <CertificatesModalProvider>
          {open && (
            <CertificatesModalLayout onCreated={(created) => { setItems(prev => [created, ...prev]); setOpen(false) }} onClose={() => setOpen(false)} />
          )}
        </CertificatesModalProvider>
      </div>
    </div>
  )
}

 
