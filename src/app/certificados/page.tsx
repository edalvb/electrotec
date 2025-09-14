'use client'
import { useEffect, useState } from 'react'
import { Card, Heading, Text, Button, Table, Badge } from '@radix-ui/themes'
import { http } from '@/lib/http/axios'
import { ModernButton, ModernModal } from '@/app/shared/ui'
import { supabaseBrowser } from '@/lib/supabase/client'
import { isAxiosError } from 'axios'

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
        {/* Header con botón añadir */}
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

        {/* Listado */}
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

        {/* Modal para añadir */}
        <AddCertificateModal open={open} onClose={() => setOpen(false)} onCreated={(created) => {
          setItems(prev => [created, ...prev])
          setOpen(false)
        }}/>
      </div>
    </div>
  )
}

type SearchEquipmentItem = { id: string; serial_number: string; brand: string; model: string }

function AddCertificateModal({ open, onClose, onCreated }: { open: boolean; onClose: () => void; onCreated: (c: CertItem) => void }){
  const [equipmentQuery, setEquipmentQuery] = useState('')
  const [suggestions, setSuggestions] = useState<SearchEquipmentItem[]>([])
  const [selectedEquipment, setSelectedEquipment] = useState<SearchEquipmentItem | null>(null)
  const [calibrationDate, setCalibrationDate] = useState('')
  const [nextCalibrationDate, setNextCalibrationDate] = useState('')
  const [submitting, setSubmitting] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [technicianId, setTechnicianId] = useState<string | null>(null)

  useEffect(() => {
    if (!open) return
    // obtener usuario actual
    (async () => {
      const sb = supabaseBrowser()
      const { data } = await sb.auth.getUser()
      setTechnicianId(data.user?.id || null)
    })()
  }, [open])

  // Búsqueda simple de equipos
  useEffect(() => {
    const q = equipmentQuery.trim()
    if (q.length < 2) { setSuggestions([]); return }
    const ctrl = new AbortController()
    ;(async () => {
      try {
        const r = await http.get<{ items: SearchEquipmentItem[] }>(
          '/api/equipment/search',
          { params: { q }, signal: ctrl.signal }
        )
        const items = (r.data.items || []).map((x) => ({ id: x.id, serial_number: x.serial_number, brand: x.brand, model: x.model }))
        setSuggestions(items)
      } catch { /* ignore */ }
    })()
    return () => ctrl.abort()
  }, [equipmentQuery])

  const canSubmit = !!selectedEquipment && !!calibrationDate && !!nextCalibrationDate && !!technicianId && !submitting

  const submit = async () => {
    if (!canSubmit || !selectedEquipment || !technicianId) return
    setSubmitting(true); setError(null)
    try {
      const r = await http.post('/api/certificates/create', {
        equipment_id: selectedEquipment.id,
        calibration_date: calibrationDate,
        next_calibration_date: nextCalibrationDate,
        results: {},
        technician_id: technicianId
      })
      const id = r.data.id as string
      onCreated({
        id,
        certificate_number: r.data.certificate_number,
        calibration_date: calibrationDate,
        next_calibration_date: nextCalibrationDate,
        pdf_url: r.data.pdf_url,
        equipment: selectedEquipment
      })
    } catch (err) {
      const message = isAxiosError(err) ? (err.response?.data as { error?: string } | undefined)?.error : undefined
      setError(message || 'Error al crear')
    } finally { setSubmitting(false) }
  }

  return (
    <ModernModal open={open} onOpenChange={(o) => { if (!o) onClose() }} title="Nuevo certificado" description="Selecciona un equipo y fechas para crear el certificado" size="lg">
      <div className="space-y-4">
        <div>
          <label className="block text-sm text-white/80 mb-1">Equipo</label>
          <input
            value={equipmentQuery}
            onChange={e => { setEquipmentQuery(e.target.value); setSelectedEquipment(null) }}
            placeholder="Buscar por número de serie"
            className="w-full px-3 py-2 rounded bg-white/10 border border-white/20 text-white"
          />
          {suggestions.length > 0 && !selectedEquipment && (
            <div className="mt-2 max-h-48 overflow-auto rounded border border-white/20 bg-slate-900/90">
              {suggestions.map(s => (
                <button key={s.id} className="w-full text-left px-3 py-2 hover:bg-white/10 text-white/90" onClick={() => { setSelectedEquipment(s); setEquipmentQuery(`${s.serial_number} · ${s.brand} ${s.model}`); setSuggestions([]) }}>
                  <div className="font-medium">{s.serial_number}</div>
                  <div className="text-xs text-white/60">{s.brand} {s.model}</div>
                </button>
              ))}
            </div>
          )}
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm text-white/80 mb-1">Fecha de calibración</label>
            <input type="date" value={calibrationDate} onChange={e => setCalibrationDate(e.target.value)} className="w-full px-3 py-2 rounded bg-white/10 border border-white/20 text-white" />
          </div>
          <div>
            <label className="block text-sm text-white/80 mb-1">Próxima calibración</label>
            <input type="date" value={nextCalibrationDate} onChange={e => setNextCalibrationDate(e.target.value)} className="w-full px-3 py-2 rounded bg-white/10 border border-white/20 text-white" />
          </div>
        </div>

        {!technicianId && <div className="text-amber-300 text-sm">Debes iniciar sesión para crear certificados.</div>}
        {error && <div className="text-red-300 text-sm">{error}</div>}

        <div className="flex justify-end gap-2 pt-2">
          <ModernButton variant="glass" onClick={onClose}>Cancelar</ModernButton>
          <ModernButton variant="primary" disabled={!canSubmit} loading={submitting} onClick={submit}>Crear</ModernButton>
        </div>
      </div>
    </ModernModal>
  )
}
