'use client'
import { useEffect, useState } from 'react'
import { Card, Heading, Text, Button, Badge } from '@radix-ui/themes'
import { http } from '@/lib/http/axios'
import { ModernButton, ModernTable, ModernTableHeader, ModernTableBody, ModernTableRow, ModernTableCell } from '@/app/shared/ui'
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

  const loadCertificates = async () => {
    try {
      const r = await http.get('/api/certificates', { params: { pageSize: 50 } })
      setItems(r.data.items || [])
    } finally { setLoading(false) }
  }

  useEffect(() => { loadCertificates() }, [])

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

        <Card className="glass p-0 overflow-hidden border border-white/10 bg-slate-900/20 backdrop-blur-xl">
          <ModernTable className="w-full">
            <ModernTableHeader>
              <ModernTableRow hover={false}>
                <ModernTableCell header>Número</ModernTableCell>
                <ModernTableCell header>Equipo</ModernTableCell>
                <ModernTableCell header>Fechas</ModernTableCell>
                <ModernTableCell header>PDF</ModernTableCell>
                <ModernTableCell header>Acciones</ModernTableCell>
              </ModernTableRow>
            </ModernTableHeader>
            <ModernTableBody>
              {loading ? (
                <ModernTableRow>
                  <ModernTableCell className="text-center text-slate-300 py-8" colSpan={5}>
                    <div className="flex items-center justify-center gap-2">
                      <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-400"></div>
                      <span>Cargando...</span>
                    </div>
                  </ModernTableCell>
                </ModernTableRow>
              ) : items.length === 0 ? (
                <ModernTableRow>
                  <ModernTableCell className="text-center text-slate-400 py-8" colSpan={5}>
                    <div className="flex flex-col items-center gap-2">
                      <span className="text-lg">📋</span>
                      <span>No hay certificados disponibles</span>
                    </div>
                  </ModernTableCell>
                </ModernTableRow>
              ) : (
                items.map(it => (
                  <ModernTableRow key={it.id}>
                    <ModernTableCell className="font-mono font-medium text-blue-300">
                      {it.certificate_number}
                    </ModernTableCell>
                    <ModernTableCell>
                      <div className="flex flex-col space-y-1">
                        <span className="font-semibold text-slate-100">
                          {it.equipment?.serial_number || '-'}
                        </span>
                        <span className="text-slate-400 text-xs">
                          {it.equipment ? `${it.equipment.brand} ${it.equipment.model}` : 'Sin equipo'}
                        </span>
                      </div>
                    </ModernTableCell>
                    <ModernTableCell>
                      <div className="flex flex-col space-y-1">
                        <div className="flex items-center gap-2">
                          <span className="text-xs px-2 py-1 bg-green-900/30 text-green-300 rounded-full">
                            Cal.
                          </span>
                          <span className="text-sm">
                            {new Date(it.calibration_date).toLocaleDateString()}
                          </span>
                        </div>
                        <div className="flex items-center gap-2">
                          <span className="text-xs px-2 py-1 bg-orange-900/30 text-orange-300 rounded-full">
                            Próx.
                          </span>
                          <span className="text-sm text-slate-300">
                            {new Date(it.next_calibration_date).toLocaleDateString()}
                          </span>
                        </div>
                      </div>
                    </ModernTableCell>
                    <ModernTableCell>
                      {it.pdf_url ? (
                        <Badge 
                          color="green" 
                          className="bg-green-900/30 text-green-300 border border-green-500/30 hover:bg-green-800/40 transition-colors"
                        >
                          <a href={it.pdf_url} target="_blank" rel="noreferrer" className="flex items-center gap-1">
                            <span>📄</span>
                            <span>Disponible</span>
                          </a>
                        </Badge>
                      ) : (
                        <Badge 
                          color="gray" 
                          className="bg-slate-800/40 text-slate-400 border border-slate-600/30"
                        >
                          <span className="flex items-center gap-1">
                            <span>⏳</span>
                            <span>No generado</span>
                          </span>
                        </Badge>
                      )}
                    </ModernTableCell>
                    <ModernTableCell>
                      <div className="flex gap-2">
                        <Button 
                          size="1" 
                          variant="soft" 
                          disabled 
                          className="opacity-50 text-xs px-3 py-1.5 bg-slate-700/30 text-slate-400 border border-slate-600/30 rounded-lg hover:bg-slate-600/40 transition-colors"
                        >
                          ✏️ Editar
                        </Button>
                        <Button 
                          size="1" 
                          className="text-xs px-3 py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white border-0 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105"
                          onClick={async () => {
                            const r = await http.post('/api/certificates', { id: it.id })
                            if (r.data?.pdf_url) window.open(r.data.pdf_url, '_blank')
                          }}
                        >
                          📄 Generar PDF
                        </Button>
                      </div>
                    </ModernTableCell>
                  </ModernTableRow>
                ))
              )}
            </ModernTableBody>
          </ModernTable>
        </Card>

        <CertificatesModalProvider>
          {open && (
            <CertificatesModalLayout onCreated={() => { loadCertificates(); setOpen(false) }} onClose={() => setOpen(false)} />
          )}
        </CertificatesModalProvider>
      </div>
    </div>
  )
}

 
