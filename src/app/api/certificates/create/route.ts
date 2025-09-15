import { NextResponse } from 'next/server'
import { supabaseServer } from '@/lib/supabase/server'
import PDFDocument from 'pdfkit'
import fs from 'node:fs/promises'
import { join } from 'node:path'
import { z } from 'zod'

const labSchema = z
  .object({
    temperature: z.number().optional(),
    humidity: z.number().min(0).max(100).optional(),
    pressure: z.number().optional(),
    calibration: z.boolean().optional(),
    maintenance: z.boolean().optional()
  })
  .optional()
const resultsSchema = z.record(z.unknown()).or(z.array(z.unknown()))

const schema = z.object({
  equipment_id: z.string().uuid(),
  calibration_date: z.string(),
  next_calibration_date: z.string(),
  results: resultsSchema,
  lab_conditions: labSchema,
  technician_id: z.string().uuid()
})

export async function POST(req: Request) {
  try {
    const body = await req.json()
    const parsed = schema.safeParse(body)
    if (!parsed.success) return NextResponse.json({ error: 'validation', details: parsed.error.flatten() }, { status: 400 })
    const db = supabaseServer()
    const number = `ET-${new Date().getFullYear()}-${Math.floor(Math.random() * 900000 + 100000)}`
    const { data: cert, error } = await db
      .from('certificates')
      .insert({
        certificate_number: number,
        equipment_id: parsed.data.equipment_id,
        technician_id: parsed.data.technician_id,
        calibration_date: parsed.data.calibration_date,
        next_calibration_date: parsed.data.next_calibration_date,
        results: parsed.data.results,
        lab_conditions: parsed.data.lab_conditions || null
      })
      .select('*')
      .single()
    if (error || !cert) {
      const msg = (error && (error.message || (error as { details?: string }).details || (error as { hint?: string }).hint)) || 'unknown'
      const rls = typeof msg === 'string' && msg.toLowerCase().includes('row-level security')
      return NextResponse.json({ error: 'create_failed', details: msg }, { status: rls ? 403 : 500 })
    }

    // Cargar datos relacionados: equipo, tipo, cliente y técnico
    const { data: equipment } = await db
      .from('equipment')
      .select('id, serial_number, brand, model, owner_client_id, equipment_type_id')
      .eq('id', cert.equipment_id)
      .single()

    const { data: type } = equipment
      ? await db.from('equipment_types').select('id, name').eq('id', equipment.equipment_type_id).single()
      : { data: null as { id: number; name: string } | null }

    const { data: client } = equipment?.owner_client_id
      ? await db.from('clients').select('id, name').eq('id', equipment.owner_client_id).single()
      : { data: null as { id: string; name: string } | null }

    const { data: technician } = await db
      .from('user_profiles')
      .select('id, full_name, signature_image_url, role')
      .eq('id', cert.technician_id)
      .single()

    // Helpers de formateo y dibujo
    const W = 595.28 // width A4 pts
    const H = 841.89 // height A4 pts
    const M = 48

    const formatDateEs = (iso: string) => {
      const d = new Date(iso)
      const dd = d.getUTCDate().toString().padStart(2, '0')
      const meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC']
      const mm = meses[d.getUTCMonth()]
      const yyyy = d.getUTCFullYear()
      return `${dd} – ${mm} – ${yyyy}`
    }

  const getPublicPath = (rel: string) => join(process.cwd(), 'public', rel)

    const loadLocalImage = async (rel: string) => {
      const p = getPublicPath(rel)
      return fs.readFile(p)
    }

    const loadRemoteImage = async (url?: string | null) => {
      if (!url) return null
      try {
        const r = await fetch(url)
        if (!r.ok) return null
        const ab = await r.arrayBuffer()
        return Buffer.from(ab)
      } catch {
        return null
      }
    }

    const drawWatermarkFull = async (doc: PDFKit.PDFDocument) => {
      const img = await loadLocalImage('marca_agua.png')
      doc.save()
      doc.opacity(0.06) // sutil
      // Imagen a 100% de página, sin respetar márgenes
      doc.image(img, 0, 0, { width: W, height: H })
      doc.restore()
    }

    const drawHeader = (doc: PDFKit.PDFDocument) => {
      doc.fontSize(22).fillColor('#2f2c82').text('ELECTROTEC', M, M)
      doc.fontSize(22).fillColor('#2f2c82').text('CONSULTING S.A.C.', M, M + 26)
      doc.fontSize(12).fillColor('#2f2c82').text('CALIBRACIÓN - MANTENIMIENTO - REPARACIÓN', M, M + 58)
      doc.fillColor('#000')
    }

    const pill = (doc: PDFKit.PDFDocument, text: string, x: number, y: number) => {
      const padX = 8
      const padY = 4
      const w = doc.widthOfString(text) + padX * 2
      const h = doc.currentLineHeight() + padY * 2
      doc.save()
      doc.roundedRect(x, y, w, h, 6).fill('#ffe97f')
      doc.fillColor('#333').fontSize(10).text(text, x + padX, y + padY)
      doc.restore()
      doc.fillColor('#000')
    }

    const labelBox = (doc: PDFKit.PDFDocument, label: string, y: number) => {
      const lw = doc.widthOfString(label) + 12
      doc.save()
      doc.rect(M, y, lw, 18).fill('#000')
      doc.fillColor('#fff').fontSize(10).text(label, M + 6, y + 3)
      doc.restore()
      doc.fillColor('#000')
    }

    type Col = { header: string; width: number; align?: 'left'|'center'|'right' }
    const drawTable = (
      doc: PDFKit.PDFDocument,
      x: number,
      y: number,
      cols: Col[],
      rows: (Array<string | number | null | undefined>)[],
      opts: { headerFill?: string; cellFill?: (r: number, c: number) => string | undefined; cellFormatter?: (v: any, r: number, c: number) => string }
    ) => {
      const rowH = 20
      const tableW = cols.reduce((a, c) => a + c.width, 0)
      // Header
      doc.save()
      doc.rect(x, y, tableW, rowH).fill(opts.headerFill || '#e9edf3')
      doc.fillColor('#000').fontSize(10)
      let cx = x
      cols.forEach(col => {
        doc.text(col.header, cx + 6, y + 5, { width: col.width - 12, align: col.align || 'center' })
        cx += col.width
      })
      doc.restore()
      // Rows
      let ry = y + rowH
      rows.forEach((row, ri) => {
        let cx2 = x
        row.forEach((val, ci) => {
          const col = cols[ci]
          const f = opts.cellFormatter || ((v: any) => (v ?? '').toString())
          const text = f(val, ri, ci)
          const fill = opts.cellFill?.(ri, ci)
          if (fill) {
            doc.save(); doc.rect(cx2, ry, col.width, rowH).fill(fill); doc.restore()
          }
          doc.rect(cx2, ry, col.width, rowH).stroke('#999')
          doc.fillColor('#000').text(text, cx2 + 6, ry + 5, { width: col.width - 12, align: col.align || 'center' })
          cx2 += col.width
        })
        ry += rowH
      })
      return ry
    }

    const numberFormat = (n: number, decimals = 3) => new Intl.NumberFormat('es-PE', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(n)

    const doc = new PDFDocument({ size: 'A4', margin: M })
    const chunks: Uint8Array[] = []

    // ------ Página 1 ------
    await drawWatermarkFull(doc)
    drawHeader(doc)
    // Título y número
    doc.fontSize(14).text('CERTIFICADO DE CALIBRACION', M, M + 90, { align: 'center' })
    pill(doc, `N° ${cert.certificate_number}`, W - M - 140, M + 80)

    // Otorgado a
    labelBox(doc, 'OTORGADO A:', M + 120)
    const otorgadoY = M + 120
    const name = client?.name || '—'
    doc.save()
    doc.roundedRect(M + 110, otorgadoY - 2, doc.widthOfString(name) + 24, 22, 4).fill('#fff099')
    doc.fillColor('#000').fontSize(12).text(name, M + 122, otorgadoY + 2)
    doc.restore()

    // Datos del equipo (tabla)
    labelBox(doc, 'DATOS DEL EQUIPO:', M + 160)
    const tableY = M + 185
    const cols1: Col[] = [
      { header: 'EQUIPO', width: (W - 2*M) * 0.25 },
      { header: 'MARCA', width: (W - 2*M) * 0.25 },
      { header: 'MODELO', width: (W - 2*M) * 0.25 },
      { header: 'SERIE', width: (W - 2*M) * 0.25 }
    ]
    drawTable(
      doc,
      M,
      tableY,
      cols1,
      [[ type?.name || '—', equipment?.brand || '—', equipment?.model || '—', equipment?.serial_number || '—' ]],
      {
        cellFill: (r,c) => '#fff099',
        cellFormatter: (v) => (v ?? '').toString()
      }
    )

    // Texto normativo
    doc.moveTo(M, tableY + 40)
    doc.moveDown()
    doc.fontSize(10).text(
      'ELECTROTEC CONSULTING S.A.C. certifica que el equipo de topografía descrito ha sido revisado y calibrado en todos los puntos en nuestro laboratorio y se encuentra en perfecto estado de funcionamiento de acuerdo con los estándares internacionales establecidos (DIN18723).',
      { align: 'justify' }
    )

    // Equipo patrón utilizado (2 columnas)
    const meta: any = (parsed.data.results as any)?.meta || {}
    const patronName = meta.pattern_equipment_name || 'COLIMADOR GF550'
    const patronSerial = meta.pattern_equipment_serial || '130644'
    doc.moveDown(0.5)
    doc.fontSize(10).text('EQUIPO PATRON UTILIZADO:')
    const cols2: Col[] = [
      { header: '', width: (W - 2*M) * 0.6 },
      { header: '', width: (W - 2*M) * 0.4 }
    ]
    drawTable(
      doc,
      M,
      doc.y + 4,
      cols2,
      [[patronName, patronSerial]],
      { headerFill: '#ffffff', cellFill: () => '#fff', cellFormatter: (v) => (v ?? '').toString() }
    )

    // Metodología aplicada
    doc.moveDown(1.2)
    doc.fontSize(11).text('METODOLOGÍA APLICADA Y TRAZABILIDAD DE LOS PATRONES.', { underline: false })
    const tapeBrand = meta.tape_brand || 'HULTAFORS'
    const tapeModel = meta.tape_model || 'BT8M'
    const tapeSerial = meta.tape_serial || 'BT80977242'
    const tapeCert = meta.tape_calibration_certificate || 'LLA - 066 - 2024'
    const issuedBy = meta.issued_by || 'INACAL Instituto Nacional de calidad.'
    const metodTxt = `Cinta métrica, marca: ${tapeBrand}, modelo: ${tapeModel}, número de serie: ${tapeSerial}, Certificado de calibración ${tapeCert}. Emitido por Laboratorio de Longitud y Angulo - Dirección de metrología - ${issuedBy}
Para Controlar y calibrar este instrumento se contrasta con un colimador marca KOLIDA modelo GF550 Patronado mensualmente con estación total marca LEICA modelo TS06 PLUS PRECISION 1” y nivel automático marca TOPCON modelo AT-B2 PRECISION 0.7mm. El control se ejecuta en la base metálica fijada en la pared y piso, ajena a influencias del clima y enfocada el retículo al infinito.`
    doc.fontSize(10).text(metodTxt, { align: 'justify' })

    // ------ Página 2 ------
    doc.addPage()
    await drawWatermarkFull(doc)
    drawHeader(doc)

    // Procedimiento (varía según tipo)
    doc.moveDown(4)
    const tname = (type?.name || '').toLowerCase()
    if (tname.includes('estación')) {
      doc.fontSize(10).text('Procedimiento de Calibración Angular del Equipo: por medición del cierre angular en directa y tránsito visando hacia un colimador con el enfoque al infinito. Los valores consignados corresponden al promedio de 3 mediciones.')
    } else if (tname.includes('teodolito')) {
      doc.fontSize(10).text('Procedimiento de Calibración Angular del Equipo: por medición del cierre angular en directa y tránsito visando hacia un colimador con el enfoque al infinito.')
    } else if (tname.includes('nivel')) {
      doc.fontSize(10).text('Procedimiento de Calibración del Equipo: por nivelación directa e inversa visando hacia un colimador con el enfoque al infinito. Los valores consignados corresponden al visado de colimador.')
    }

    doc.moveDown(1)
    doc.fontSize(10).text('RESULTADOS:', { continued: false })

    // Resultados/tablas según equipo
    const results = (parsed.data.results || {}) as any
    const angularPrecision = results.angular_precision as string | undefined
    const angularRows = (results.angular_measurements as any[]) || []
    const prismRows = (results.prism_measurements as any[]) || []
    const noPrismRows = (results.no_prism_measurements as any[]) || []
    const distancePrecision = results.distance_precision as string | undefined
    const levelPrecision = results.level_precision_mm as number | undefined
    const levelError = results.level_error as string | undefined

    // Tabla angular común (cuando haya datos)
    const colsAng: Col[] = [
      { header: 'Valor de Patrón', width: (W - 2*M) * 0.25 },
      { header: 'Valor Obtenido', width: (W - 2*M) * 0.25 },
      { header: 'Precisión', width: (W - 2*M) * 0.25 },
      { header: 'Error', width: (W - 2*M) * 0.25 }
    ]
    let yCursor = doc.y + 6
    const rowsAng = angularRows.map((r: any) => [r.pattern, r.obtained, angularPrecision || '', r.error])
    if (rowsAng.length) {
      yCursor = drawTable(
        doc,
        M,
        yCursor,
        colsAng,
        rowsAng,
        {
          cellFill: (r,c) => (c <= 3 ? '#fff099' : undefined),
          cellFormatter: (v, _r, c) => {
            if (c === 2 && levelPrecision != null && tname.includes('nivel')) return `± ${levelPrecision.toFixed(1)} mm`
            return (v ?? '').toString()
          }
        }
      ) + 12
    }

    if (tname.includes('estación')) {
      // Medición con prisma
      doc.fontSize(10).text('MEDICION CON PRISMA:', M, yCursor)
      yCursor += 18
      const colsD: Col[] = [
        { header: 'Puntos de Control', width: (W - 2*M) * 0.25 },
        { header: 'Distancia Obtenida', width: (W - 2*M) * 0.25 },
        { header: 'Precisión', width: (W - 2*M) * 0.25 },
        { header: 'Variación', width: (W - 2*M) * 0.25 }
      ]
      const rowsD = (prismRows || []).map((r: any) => [
        `${numberFormat(r.control)} m`,
        `${numberFormat(r.obtained)} m`,
        distancePrecision || '',
        `${numberFormat(r.delta ?? Math.abs((r.obtained ?? 0) - (r.control ?? 0)))} m`
      ])
      if (rowsD.length) yCursor = drawTable(doc, M, yCursor, colsD, rowsD, { cellFill: () => '#fff099', cellFormatter: (v) => (v ?? '').toString() }) + 12

      // Medición sin prisma
      doc.fontSize(10).text('MEDICION SIN PRISMA:', M, yCursor)
      yCursor += 18
      const rowsND = (noPrismRows || []).map((r: any) => [
        `${numberFormat(r.control)} m`,
        `${numberFormat(r.obtained)} m`,
        distancePrecision || '',
        `${numberFormat(r.delta ?? Math.abs((r.obtained ?? 0) - (r.control ?? 0)))} m`
      ])
      if (rowsND.length) yCursor = drawTable(doc, M, yCursor, colsD, rowsND, { cellFill: () => '#fff099', cellFormatter: (v) => (v ?? '').toString() }) + 12
    }

    // Bloque laboratorio
    const lab = (parsed.data.lab_conditions || {}) as any
    const blockY = Math.max(yCursor + 10, H - 260)
    doc.fontSize(10).text('LABORATORIO.', M, blockY)
    const row = (label: string, value: string, y: number) => {
      doc.fontSize(9).text(`${label} :`, M, y)
      const w = doc.widthOfString(value) + 16
      doc.save(); doc.roundedRect(M + 90, y - 3, Math.max(w, 80), 18, 4).fill('#fff099'); doc.restore()
      doc.fillColor('#000').fontSize(10).text(value, M + 96, y + 1)
    }
    if (typeof lab.temperature === 'number') row('TEMPERATURA', `${lab.temperature}°`, blockY + 18)
    if (typeof lab.humidity === 'number') row('HUMEDAD', `${lab.humidity}%`, blockY + 38)
    if (typeof lab.pressure === 'number') row('PRESION ATM.', `${lab.pressure}mmHg`, blockY + 58)

    // Checkboxes Calibración / Mantenimiento
    const drawCheck = (label: string, checked: boolean, x: number, y: number) => {
      doc.fontSize(10).text(label, x, y)
      doc.rect(x + 85, y - 3, 16, 16).stroke()
      if (checked) {
        doc.fontSize(12).text('X', x + 88.5, y - 2)
      }
    }
    drawCheck('CALIBRACION', !!lab.calibration, M + 70, blockY + 95)
    drawCheck('MANTENIMIENTO', !!lab.maintenance, M + 220, blockY + 95)

    // Bloque final: técnico, firma, fechas
    const col1X = M
    const col2X = M + (W - 2*M) * 0.4 + 10
    const col3X = M + (W - 2*M) * 0.7 + 10
    const baseY = H - 140
    // Columna 1
    doc.fontSize(10).text('Certificado por:', col1X, baseY)
    doc.fontSize(11).text(`Tec. ${technician?.full_name || '—'}`, col1X, baseY + 18)
    doc.fontSize(10).text('Jefe de Laboratorio.', col1X, baseY + 36)
    // Columna 2: firma
    const sigBuf = await loadRemoteImage(technician?.signature_image_url || undefined)
    if (sigBuf) {
      doc.image(sigBuf, col2X, baseY - 10, { width: (W - 2*M) * 0.25, height: 60, align: 'center' })
    } else {
      doc.fontSize(9).text('FIRMA NO REGISTRADA', col2X, baseY + 10, { width: (W - 2*M) * 0.25, align: 'center' })
    }
    // Columna 3: fechas
    const box = (title: string, value: string, x: number, y: number) => {
      const ww = (W - 2*M) * 0.25
      const hh = 36
      doc.rect(x, y, ww, hh).stroke('#999')
      doc.fontSize(10).text(title, x + 8, y + 6)
      const wv = doc.widthOfString(value) + 16
      doc.save(); doc.roundedRect(x + ww - (wv + 8) - 8, y + hh - 22, wv + 8, 18, 4).fill('#fff099'); doc.restore()
      doc.fontSize(10).text(value, x + ww - (wv + 8), y + hh - 18)
    }
    box('Calibrado:', formatDateEs(cert.calibration_date), col3X, baseY)
    box('Próxima calibración:', formatDateEs(cert.next_calibration_date), col3X, baseY + 46)

    const buffer = await new Promise<Buffer>((resolve) => {
      doc.on('data', (c: Uint8Array) => chunks.push(c))
      doc.on('end', () => resolve(Buffer.concat(chunks)))
      doc.end()
    })
  const storagePath = `certificates/${cert.id}.pdf`
  const { error: upErr } = await db.storage.from('public').upload(storagePath, buffer, { upsert: true, contentType: 'application/pdf' })
    if (upErr) {
      const msg = upErr.message || (upErr as { details?: string }).details || 'unknown'
      const rls = typeof msg === 'string' && msg.toLowerCase().includes('row-level security')
      return NextResponse.json({ error: 'upload_failed', details: msg }, { status: rls ? 403 : 500 })
    }
  const { data: pub } = db.storage.from('public').getPublicUrl(storagePath)
    await db.from('certificates').update({ pdf_url: pub.publicUrl }).eq('id', cert.id)
    return NextResponse.json({ id: cert.id, certificate_number: cert.certificate_number, pdf_url: pub.publicUrl })
  } catch (e: unknown) {
    const message = e instanceof Error ? e.message : 'unknown'
    return NextResponse.json({ error: 'unexpected', details: message }, { status: 500 })
  }
}
