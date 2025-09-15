'use client'
import { useEffect, useMemo, useState } from 'react'
import { Card, Heading, Text, Button, Badge, Dialog, Flex } from '@radix-ui/themes'
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
  equipment: { id: string; serial_number: string; brand: string; model: string; equipment_type?: { id: number; name: string } | null } | null
}

export default function CertificadosIndexPage() {
  const [items, setItems] = useState<CertItem[]>([])
  const [loading, setLoading] = useState(true)
  const [open, setOpen] = useState(false)
  const [editingId, setEditingId] = useState<string | null>(null)
  const editing = useMemo(() => items.find(i => i.id === editingId) || null, [editingId, items])
  const [editCal, setEditCal] = useState<string>('')
  const [editNext, setEditNext] = useState<string>('')
  const [saving, setSaving] = useState(false)
  const [deleteId, setDeleteId] = useState<string | null>(null)
  const [lab, setLab] = useState<{ temperature?: string; humidity?: string; pressure?: string; calibration?: boolean; maintenance?: boolean }>({})
  const [results, setResults] = useState<any>({})

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
                          className="text-xs px-3 py-1.5 bg-slate-700/30 text-slate-200 border border-slate-600/30 rounded-lg hover:bg-slate-600/40 transition-colors"
                          onClick={async () => {
                            setEditingId(it.id)
                            setEditCal(it.calibration_date.slice(0,10))
                            setEditNext(it.next_calibration_date.slice(0,10))
                            const r = await http.get(`/api/certificates/${it.id}`)
                            const c = r.data
                            const lc = c?.lab_conditions || {}
                            setLab({
                              temperature: lc.temperature != null ? String(lc.temperature) : '',
                              humidity: lc.humidity != null ? String(lc.humidity) : '',
                              pressure: lc.pressure != null ? String(lc.pressure) : '',
                              calibration: !!lc.calibration,
                              maintenance: !!lc.maintenance,
                            })
                            setResults(c?.results || {})
                          }}
                        >
                          ✏️ Editar
                        </Button>
                        <Button 
                          size="1" 
                          className="text-xs px-3 py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 text-white border-0 rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105"
                          onClick={async () => {
                            if (!it.pdf_url) {
                              const r = await http.post('/api/certificates', { id: it.id })
                              if (r.data?.pdf_url) {
                                setItems(prev => prev.map(p => p.id === it.id ? { ...p, pdf_url: r.data.pdf_url } : p))
                                window.open(r.data.pdf_url, '_blank')
                              }
                            } else {
                              const r = await http.patch(`/api/certificates/${it.id}`, {})
                              if (r.data?.pdf_url) {
                                setItems(prev => prev.map(p => p.id === it.id ? { ...p, pdf_url: r.data.pdf_url } : p))
                                window.open(r.data.pdf_url, '_blank')
                              }
                            }
                          }}
                        >
                          📄 Generar PDF
                        </Button>
                        <Button 
                          size="1"
                          variant="soft"
                          color="red"
                          className="text-xs px-3 py-1.5 bg-red-900/30 text-red-300 border border-red-600/30 rounded-lg hover:bg-red-800/40 transition-colors"
                          onClick={() => setDeleteId(it.id)}
                        >
                          🗑️ Eliminar
                        </Button>
                      </div>
                    </ModernTableCell>
                  </ModernTableRow>
                ))
              )}
            </ModernTableBody>
          </ModernTable>
        </Card>

        {/* Edit dialog */}
        <Dialog.Root open={!!editing} onOpenChange={(o) => { if (!o) setEditingId(null) }}>
          <Dialog.Content className="glass max-w-md">
            <Dialog.Title>Editar certificado</Dialog.Title>
            <div className="space-y-3 mt-3">
              <label className="block text-sm text-white/80">Fecha de Calibración
                <input type="date" className="input-glass w-full" value={editCal} onChange={e => setEditCal(e.target.value)} />
              </label>
              <label className="block text-sm text-white/80">Próxima Calibración
                <input type="date" className="input-glass w-full" value={editNext} onChange={e => setEditNext(e.target.value)} />
              </label>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                <label className="block text-sm text-white/80">Temp (°C)
                  <input type="number" className="input-glass w-full" value={lab.temperature ?? ''} onChange={e => setLab(v => ({ ...v, temperature: e.target.value }))} />
                </label>
                <label className="block text-sm text-white/80">Humedad (%)
                  <input type="number" className="input-glass w-full" value={lab.humidity ?? ''} onChange={e => setLab(v => ({ ...v, humidity: e.target.value }))} />
                </label>
                <label className="block text-sm text-white/80">Presión
                  <input type="number" className="input-glass w-full" value={lab.pressure ?? ''} onChange={e => setLab(v => ({ ...v, pressure: e.target.value }))} />
                </label>
                <label className="inline-flex items-center gap-2 text-white/80">
                  <input type="checkbox" className="accent-blue-500" checked={!!lab.calibration} onChange={e => setLab(v => ({ ...v, calibration: e.target.checked }))} /> Calibración
                </label>
                <label className="inline-flex items-center gap-2 text-white/80">
                  <input type="checkbox" className="accent-blue-500" checked={!!lab.maintenance} onChange={e => setLab(v => ({ ...v, maintenance: e.target.checked }))} /> Mantenimiento
                </label>
              </div>

              {editing?.equipment?.equipment_type?.name === 'Nivel' && (
                <div className="space-y-2">
                  <label className="block text-sm text-white/80">Precisión de nivel (mm)
                    <input type="number" className="input-glass w-full" value={results.level_precision_mm ?? ''} onChange={e => setResults((r: any) => ({ ...r, level_precision_mm: e.target.value ? Number(e.target.value) : undefined }))} />
                  </label>
                  <label className="block text-sm text-white/80">Error de nivel
                    <input type="text" className="input-glass w-full" value={results.level_error ?? ''} onChange={e => setResults((r: any) => ({ ...r, level_error: e.target.value }))} />
                  </label>
                </div>
              )}

              {(editing?.equipment?.equipment_type?.name === 'Teodolito' || editing?.equipment?.equipment_type?.name === 'Estación Total') && (
                <div className="space-y-2">
                  <label className="block text-sm text-white/80">Precisión angular
                    <input type="text" className="input-glass w-full" value={results.angular_precision ?? ''} onChange={e => setResults((r: any) => ({ ...r, angular_precision: e.target.value }))} />
                  </label>
                </div>
              )}

              {editing?.equipment?.equipment_type?.name === 'Estación Total' && (
                <div className="space-y-2">
                  <label className="block text-sm text-white/80">Precisión de distancia
                    <input type="text" className="input-glass w-full" value={results.distance_precision ?? ''} onChange={e => setResults((r: any) => ({ ...r, distance_precision: e.target.value }))} />
                  </label>
                </div>
              )}
            </div>
            <Flex justify="end" gap="3" mt="4">
              <Button variant="soft" onClick={() => setEditingId(null)}>Cancelar</Button>
              <Button disabled={!editing} loading={saving as unknown as boolean} onClick={async () => {
                if (!editing) return
                try {
                  setSaving(true)
                  const payload: any = { calibration_date: editCal, next_calibration_date: editNext }
                  const lc: any = {}
                  if (lab.temperature !== '') lc.temperature = lab.temperature ? Number(lab.temperature) : undefined
                  if (lab.humidity !== '') lc.humidity = lab.humidity ? Number(lab.humidity) : undefined
                  if (lab.pressure !== '') lc.pressure = lab.pressure ? Number(lab.pressure) : undefined
                  lc.calibration = !!lab.calibration
                  lc.maintenance = !!lab.maintenance
                  payload.lab_conditions = lc
                  payload.results = results

                  const r = await http.patch(`/api/certificates/${editing.id}`, payload)
                  const { calibration_date, next_calibration_date, pdf_url } = r.data || {}
                  setItems(prev => prev.map(p => p.id === editing.id ? { ...p, calibration_date, next_calibration_date, pdf_url: pdf_url || p.pdf_url } : p))
                  setEditingId(null)
                } finally { setSaving(false) }
              }}>Guardar</Button>
            </Flex>
          </Dialog.Content>
        </Dialog.Root>

        {/* Delete confirm */}
        <Dialog.Root open={!!deleteId} onOpenChange={(o) => { if (!o) setDeleteId(null) }}>
          <Dialog.Content className="glass max-w-sm">
            <Dialog.Title>Eliminar certificado</Dialog.Title>
            <Dialog.Description>Esta acción eliminará el certificado y su PDF. ¿Continuar?</Dialog.Description>
            <Flex justify="end" gap="3" mt="4">
              <Button variant="soft" onClick={() => setDeleteId(null)}>Cancelar</Button>
              <Button color="red" onClick={async () => {
                if (!deleteId) return
                await http.delete(`/api/certificates/${deleteId}`)
                setItems(prev => prev.filter(p => p.id !== deleteId))
                setDeleteId(null)
              }}>Eliminar</Button>
            </Flex>
          </Dialog.Content>
        </Dialog.Root>

        <CertificatesModalProvider>
          {open && (
            <CertificatesModalLayout onCreated={() => { loadCertificates(); setOpen(false) }} onClose={() => setOpen(false)} />
          )}
        </CertificatesModalProvider>
      </div>
    </div>
  )
}

 
