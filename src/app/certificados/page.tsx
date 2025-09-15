'use client'
import { useEffect, useMemo, useState } from 'react'
import { Card, Heading, Text, Button, Badge, Dialog, Flex } from '@radix-ui/themes'
import { http } from '@/lib/http/axios'
import { ModernButton, ModernTable, ModernTableHeader, ModernTableBody, ModernTableRow, ModernTableCell } from '@/app/shared/ui'
import { StandardInput } from '@/app/shared/ui'
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

  // Genera y descarga un sticker PNG con QR apuntando al PDF
  const handleDownloadSticker = async (it: CertItem) => {
    try {
      // Obtener datos adicionales del certificado (cliente y técnico)
      const r = await http.get(`/api/certificates/${it.id}`)
      const cert = r.data || {}
      const clientName: string = cert?.client?.name || 'Cliente'
      const technicianName: string = cert?.technician?.full_name || 'Técnico'
      const signatureUrl: string | null = cert?.technician?.signature_image_url || null
      const pdfUrl = it.pdf_url || cert?.pdf_url
      if (!pdfUrl) {
        alert('Este certificado aún no tiene PDF público para enlazar.')
        return
      }

      // Crear canvas
      const width = 800 // px
      const height = 500 // px (formato horizontal)
      const canvas = document.createElement('canvas')
      canvas.width = width
      canvas.height = height
      const ctx = canvas.getContext('2d')!

      // Fondo blanco con borde suave
      ctx.fillStyle = '#ffffff'
      ctx.fillRect(0, 0, width, height)
      ctx.strokeStyle = '#333333'
      ctx.lineWidth = 4
      ctx.strokeRect(4, 4, width - 8, height - 8)

      // Zona QR
      const qrSize = 300
      const qrX = 40
      const qrY = 50

      // Generar QR en un canvas auxiliar usando la API nativa (sin dependencia)
      // Usaremos un método ligero con el servicio chart.googleapis (deprecated para producción) —
      // Mejor opción: instalar paquete qrcode. Aquí implementamos un fallback dinámico.
      const drawQr = async () => {
        // Intentar carga dinámica del paquete qrcode si existe en node_modules
        try {
          // @ts-ignore - carga dinámica en cliente soportando default o modulo entero
          const mod = await import('qrcode')
          // soporta tanto import default como namespace
          const QR = mod?.default ?? mod
          const qrCanvas = document.createElement('canvas')
          await QR.toCanvas(qrCanvas, pdfUrl, { width: qrSize, margin: 1 })
          ctx.drawImage(qrCanvas, qrX, qrY)
          return
        } catch (_) {
          // Fallback simple: dibujar un rectángulo con texto QR no disponible
          ctx.fillStyle = '#f1f5f9'
          ctx.fillRect(qrX, qrY, qrSize, qrSize)
          ctx.strokeStyle = '#94a3b8'
          ctx.strokeRect(qrX, qrY, qrSize, qrSize)
          ctx.fillStyle = '#475569'
          ctx.font = 'bold 20px sans-serif'
          ctx.textAlign = 'center'
          ctx.textBaseline = 'middle'
          ctx.fillText('QR', qrX + qrSize / 2, qrY + qrSize / 2)
        }
      }
      await drawQr()

      // Textos
      const rightX = qrX + qrSize + 40
      let y = qrY
      ctx.fillStyle = '#0f172a'
      ctx.textAlign = 'left'
      ctx.textBaseline = 'top'
      ctx.font = 'bold 34px sans-serif'
      ctx.fillText('ELECTROTEC CONSULTING S.A.C.', rightX, y)
      y += 48

      ctx.font = '600 26px sans-serif'
      ctx.fillText(`Certificado N° ${it.certificate_number}`, rightX, y)
      y += 40

      ctx.font = '16px sans-serif'
      ctx.fillStyle = '#334155'
      ctx.fillText(`Cliente: ${clientName}`, rightX, y)
      y += 28
      ctx.fillText(`Calibración: ${new Date(it.calibration_date).toLocaleDateString()}`, rightX, y)
      y += 24
      ctx.fillText(`Próxima: ${new Date(it.next_calibration_date).toLocaleDateString()}`, rightX, y)

      // Línea inferior de numeración grande
      const baseY = height - 120
      ctx.strokeStyle = '#cbd5e1'
      ctx.lineWidth = 2
      ctx.beginPath()
      ctx.moveTo(40, baseY)
      ctx.lineTo(width - 40, baseY)
      ctx.stroke()

      ctx.fillStyle = '#0f172a'
      ctx.font = 'bold 28px monospace'
      ctx.textAlign = 'left'
      ctx.fillText(`N° ${it.certificate_number}`, 50, baseY + 16)

      // Firma / nombre del técnico en la esquina inferior derecha
      const techBoxW = 320
      const techBoxH = 110
      const techX = width - techBoxW - 40
      const techY = height - techBoxH - 30
      ctx.strokeStyle = '#94a3b8'
      ctx.lineWidth = 1
      ctx.strokeRect(techX, techY, techBoxW, techBoxH)

      if (signatureUrl) {
        try {
          // Evitar canvas tainted: obtener blob vía fetch y usar objectURL
          const res = await fetch(signatureUrl)
          if (!res.ok) throw new Error('No se pudo descargar la firma')
          const blob = await res.blob()
          const objUrl = URL.createObjectURL(blob)
          const img = new Image()
          await new Promise<void>((resolve, reject) => {
            img.onload = () => resolve()
            img.onerror = () => reject(new Error('No se pudo cargar la firma'))
            img.src = objUrl
          })
          // Escalar firma manteniendo proporción
          const maxW = techBoxW - 24
          const maxH = techBoxH - 40
          let w = img.width
          let h = img.height
          const scale = Math.min(maxW / w, maxH / h, 1)
          w *= scale
          h *= scale
          const dx = techX + (techBoxW - w) / 2
          const dy = techY + 8 + (maxH - h) / 2
          ctx.drawImage(img, dx, dy, w, h)
          URL.revokeObjectURL(objUrl)
        } catch {
          // si falla la carga, caemos al nombre del técnico
          ctx.fillStyle = '#0f172a'
          ctx.font = '600 18px sans-serif'
          ctx.textAlign = 'center'
          ctx.fillText(technicianName, techX + techBoxW / 2, techY + techBoxH / 2)
        }
      } else {
        ctx.fillStyle = '#0f172a'
        ctx.font = '600 18px sans-serif'
        ctx.textAlign = 'center'
        ctx.fillText(technicianName, techX + techBoxW / 2, techY + techBoxH / 2)
      }

      // Pie de firma
      ctx.fillStyle = '#64748b'
      ctx.font = '12px sans-serif'
      ctx.textAlign = 'center'
      ctx.fillText('SERVICIO TÉCNICO', techX + techBoxW / 2, techY + techBoxH - 18)

      // Descargar PNG
      try {
        const url = canvas.toDataURL('image/png')
        const a = document.createElement('a')
        a.href = url
        a.download = `sticker_${it.certificate_number}.png`
        document.body.appendChild(a)
        a.click()
        document.body.removeChild(a)
      } catch (e) {
        console.error(e)
        alert('No se pudo exportar la imagen del sticker (CORS de la firma). Intente sin firma o verifique permisos públicos.')
      }
    } catch (err) {
      console.error(err)
      alert('No se pudo generar el sticker.')
    }
  }

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
                          variant="soft"
                          className="text-xs px-3 py-1.5 bg-emerald-700/30 text-emerald-300 border border-emerald-600/30 rounded-lg hover:bg-emerald-700/40 transition-colors"
                          onClick={() => handleDownloadSticker(it)}
                          disabled={!it.pdf_url}
                          title={it.pdf_url ? 'Descargar sticker (QR)' : 'Genere el PDF primero'}
                        >
                          🏷️ Sticker QR
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
              <StandardInput label="Fecha de Calibración" type="date" value={editCal} onChange={e => setEditCal(e.target.value)} />
              <StandardInput label="Próxima Calibración" type="date" value={editNext} onChange={e => setEditNext(e.target.value)} />
              <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                <StandardInput label="Temp (°C)" type="number" value={lab.temperature ?? ''} onChange={e => setLab(v => ({ ...v, temperature: e.target.value }))} />
                <StandardInput label="Humedad (%)" type="number" value={lab.humidity ?? ''} onChange={e => setLab(v => ({ ...v, humidity: e.target.value }))} />
                <StandardInput label="Presión" type="number" value={lab.pressure ?? ''} onChange={e => setLab(v => ({ ...v, pressure: e.target.value }))} />
                <label className="inline-flex items-center gap-2 text-white/80">
                  <input type="checkbox" className="accent-blue-500" checked={!!lab.calibration} onChange={e => setLab(v => ({ ...v, calibration: e.target.checked }))} /> Calibración
                </label>
                <label className="inline-flex items-center gap-2 text-white/80">
                  <input type="checkbox" className="accent-blue-500" checked={!!lab.maintenance} onChange={e => setLab(v => ({ ...v, maintenance: e.target.checked }))} /> Mantenimiento
                </label>
              </div>

              {editing?.equipment?.equipment_type?.name === 'Nivel' && (
                <div className="space-y-2">
                  <StandardInput label="Precisión de nivel (mm)" type="number" value={results.level_precision_mm ?? ''} onChange={e => setResults((r: any) => ({ ...r, level_precision_mm: e.target.value ? Number(e.target.value) : undefined }))} />
                  <StandardInput label="Error de nivel" value={results.level_error ?? ''} onChange={e => setResults((r: any) => ({ ...r, level_error: e.target.value }))} />
                </div>
              )}

              {(editing?.equipment?.equipment_type?.name === 'Teodolito' || editing?.equipment?.equipment_type?.name === 'Estación Total') && (
                <div className="space-y-2">
                  <StandardInput label="Precisión angular" value={results.angular_precision ?? ''} onChange={e => setResults((r: any) => ({ ...r, angular_precision: e.target.value }))} />
                </div>
              )}

              {editing?.equipment?.equipment_type?.name === 'Estación Total' && (
                <div className="space-y-2">
                  <StandardInput label="Precisión de distancia" value={results.distance_precision ?? ''} onChange={e => setResults((r: any) => ({ ...r, distance_precision: e.target.value }))} />
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

 
