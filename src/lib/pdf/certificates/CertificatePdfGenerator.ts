import PDFDocument from 'pdfkit'
import fs from 'node:fs/promises'
import { join } from 'node:path'

// Tipos de dominio mínimos y aislados para no acoplar a capas de UI/repositorios
export type LabConditions = { temperature?: number; humidity?: number; pressure?: number; calibration?: boolean; maintenance?: boolean }
export type AngularRow = { pattern: string; obtained: string; error: string }
export type DistanceRow = { control: number; obtained: number; delta?: number }
export type ResultsPayload = {
  angular_precision?: string
  angular_measurements?: AngularRow[]
  prism_measurements?: DistanceRow[]
  no_prism_measurements?: DistanceRow[]
  distance_precision?: string
  level_precision_mm?: number
  level_error?: string
  meta?: {
    pattern_equipment_name?: string
    pattern_equipment_serial?: string
    tape_brand?: string
    tape_model?: string
    tape_serial?: string
    tape_calibration_certificate?: string
    issued_by?: string
  }
}

export type EquipmentInfo = { typeName?: string | null; brand?: string | null; model?: string | null; serial_number?: string | null }
export type ClientInfo = { name?: string | null }
export type TechnicianInfo = { full_name?: string | null; signature_image_url?: string | null }

export type CertificatePdfInput = {
  certificate_number: string
  equipment: EquipmentInfo
  client: ClientInfo
  technician?: TechnicianInfo | null
  results: ResultsPayload
  lab?: LabConditions | null
  calibration_date: string
  next_calibration_date: string
}

// Principio de Inversión de Dependencias: el generador acepta funciones para cargar recursos.
export type CertificatePdfGeneratorDeps = {
  getPublicAsset?: (relPath: string) => Promise<Buffer>
  fetchRemote?: (url: string) => Promise<Buffer | null>
}

type TypeKey = 'estacion' | 'teodolito' | 'nivel' | 'desconocido'

// Interfaz de estrategia para abrir extensión por tipo de equipo (OCP)
interface ResultsRenderer {
  getProcedureText(): string
  renderResultsSection(doc: PDFKit.PDFDocument, ctx: RenderContext): number
}

type RenderContext = {
  // Entrada
  input: CertificatePdfInput
  typeKey: TypeKey
  // Geometría
  W: number
  H: number
  M: number
  // Utilidades de dibujo
  drawTable: (x: number, y: number, cols: Col[], rows: (Array<string | number | null | undefined>)[], opts: TableOpts) => number
  numberFormat: (n: number, decimals?: number) => string
  startY: number
}

type Col = { header: string; width: number; align?: 'left' | 'center' | 'right' }
type TableOpts = {
  headerFill?: string
  cellFill?: (r: number, c: number) => string | undefined
  cellFormatter?: (v: any, r: number, c: number) => string
}

class EstacionRenderer implements ResultsRenderer {
  getProcedureText() {
    return 'Procedimiento de Calibración Angular del Equipo: por medición del cierre angular en directa y tránsito visando hacia un colimador con el enfoque al infinito. Los valores consignados corresponden al promedio de 3 mediciones.'
  }
  renderResultsSection(doc: PDFKit.PDFDocument, ctx: RenderContext): number {
    const { input, M, W, startY, drawTable, numberFormat } = ctx
    const results = input.results || {}
    let yCursor = startY

    // Tabla Angular
    const angularRows = (results.angular_measurements as AngularRow[]) || []
    const angularPrecision = results.angular_precision || ''
    const colsAng: Col[] = [
      { header: 'Valor de Patrón', width: (W - 2 * M) * 0.25 },
      { header: 'Valor Obtenido', width: (W - 2 * M) * 0.25 },
      { header: 'Precisión', width: (W - 2 * M) * 0.25 },
      { header: 'Error', width: (W - 2 * M) * 0.25 }
    ]
    const rowsAng = angularRows.map(r => [r.pattern, r.obtained, angularPrecision, r.error])
    if (rowsAng.length) {
      yCursor = drawTable(M, yCursor, colsAng, rowsAng, { cellFill: () => '#fff099', cellFormatter: v => (v ?? '').toString() }) + 12
    }

    // Medición con prisma
    doc.fontSize(10).text('MEDICION CON PRISMA:', M, yCursor)
    yCursor += 18
    const colsD: Col[] = [
      { header: 'Puntos de Control', width: (W - 2 * M) * 0.25 },
      { header: 'Distancia Obtenida', width: (W - 2 * M) * 0.25 },
      { header: 'Precisión', width: (W - 2 * M) * 0.25 },
      { header: 'Variación', width: (W - 2 * M) * 0.25 }
    ]
    const prismRows = (results.prism_measurements as DistanceRow[]) || []
    const dPrecision = results.distance_precision || ''
    const rowsD = prismRows.map(r => [
      `${numberFormat(r.control)} m`,
      `${numberFormat(r.obtained)} m`,
      dPrecision,
      `${numberFormat(r.delta ?? Math.abs((r.obtained ?? 0) - (r.control ?? 0)))} m`
    ])
    if (rowsD.length) yCursor = drawTable(M, yCursor, colsD, rowsD, { cellFill: () => '#fff099', cellFormatter: v => (v ?? '').toString() }) + 12

    // Medición sin prisma
    doc.fontSize(10).text('MEDICION SIN PRISMA:', M, yCursor)
    yCursor += 18
    const noPrismRows = (results.no_prism_measurements as DistanceRow[]) || []
    const rowsND = noPrismRows.map(r => [
      `${numberFormat(r.control)} m`,
      `${numberFormat(r.obtained)} m`,
      dPrecision,
      `${numberFormat(r.delta ?? Math.abs((r.obtained ?? 0) - (r.control ?? 0)))} m`
    ])
    if (rowsND.length) yCursor = drawTable(M, yCursor, colsD, rowsND, { cellFill: () => '#fff099', cellFormatter: v => (v ?? '').toString() }) + 12

    return yCursor
  }
}

class TeodolitoRenderer implements ResultsRenderer {
  getProcedureText() {
    return 'Procedimiento de Calibración Angular del Equipo: por medición del cierre angular en directa y tránsito visando hacia un colimador con el enfoque al infinito.'
  }
  renderResultsSection(doc: PDFKit.PDFDocument, ctx: RenderContext): number {
    const { input, M, W, startY, drawTable } = ctx
    const results = input.results || {}
    let yCursor = startY
    const angularRows = (results.angular_measurements as AngularRow[]) || []
    const angularPrecision = results.angular_precision || ''
    const colsAng: Col[] = [
      { header: 'Valor de Patrón', width: (W - 2 * M) * 0.25 },
      { header: 'Valor Obtenido', width: (W - 2 * M) * 0.25 },
      { header: 'Precisión', width: (W - 2 * M) * 0.25 },
      { header: 'Error', width: (W - 2 * M) * 0.25 }
    ]
    const rowsAng = angularRows.map(r => [r.pattern, r.obtained, angularPrecision, r.error])
    if (rowsAng.length) {
      yCursor = drawTable(M, yCursor, colsAng, rowsAng, { cellFill: () => '#fff099', cellFormatter: v => (v ?? '').toString() }) + 12
    }
    return yCursor
  }
}

class NivelRenderer implements ResultsRenderer {
  getProcedureText() {
    return 'Procedimiento de Calibración del Equipo: por nivelación directa e inversa visando hacia un colimador con el enfoque al infinito. Los valores consignados corresponden al visado de colimador.'
  }
  renderResultsSection(doc: PDFKit.PDFDocument, ctx: RenderContext): number {
    const { input, M, W, startY, drawTable } = ctx
    const results = input.results || {}
    let yCursor = startY
    const angularRows = (results.angular_measurements as AngularRow[]) || []
    const levelPrecision = results.level_precision_mm
    const colsAng: Col[] = [
      { header: 'Valor de Patrón', width: (W - 2 * M) * 0.25 },
      { header: 'Valor Obtenido', width: (W - 2 * M) * 0.25 },
      { header: 'Precisión', width: (W - 2 * M) * 0.25 },
      { header: 'Error', width: (W - 2 * M) * 0.25 }
    ]
    const rowsAng = angularRows.map(r => [r.pattern, r.obtained, levelPrecision != null ? `± ${levelPrecision.toFixed(1)} mm` : '', r.error])
    if (rowsAng.length) {
      yCursor = drawTable(M, yCursor, colsAng, rowsAng, { cellFill: () => '#fff099', cellFormatter: v => (v ?? '').toString() }) + 12
    }
    return yCursor
  }
}

class UnknownRenderer implements ResultsRenderer {
  getProcedureText() {
    return 'Procedimiento de Calibración: se aplican métodos de referencia adecuados al tipo de equipo.'
  }
  renderResultsSection(_doc: PDFKit.PDFDocument, ctx: RenderContext): number {
    return ctx.startY
  }
}

export class CertificatePdfGenerator {
  private readonly W = 595.28
  private readonly H = 841.89
  private readonly M = 48
  private readonly deps: Required<CertificatePdfGeneratorDeps>

  private readonly renderers: Record<TypeKey, ResultsRenderer> = {
    estacion: new EstacionRenderer(),
    teodolito: new TeodolitoRenderer(),
    nivel: new NivelRenderer(),
    desconocido: new UnknownRenderer()
  }

  constructor(deps?: CertificatePdfGeneratorDeps) {
    this.deps = {
      getPublicAsset: async (rel: string) => {
        const p = join(process.cwd(), 'public', rel)
        return fs.readFile(p)
      },
      fetchRemote: async (url: string) => {
        try {
          const r = await fetch(url)
          if (!r.ok) return null
          const ab = await r.arrayBuffer()
          return Buffer.from(ab)
        } catch {
          return null
        }
      },
      ...(deps || {})
    }
  }

  static detectType(typeName?: string | null): TypeKey {
    const norm = (s: string) => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase()
    const t = norm(typeName || '')
    if (t.includes('estacion')) return 'estacion'
    if (t.includes('teodolito')) return 'teodolito'
    if (t.includes('nivel')) return 'nivel'
    return 'desconocido'
  }

  async generatePdfBuffer(input: CertificatePdfInput): Promise<Buffer> {
    const doc = new PDFDocument({ size: 'A4', margin: this.M })
    const chunks: Uint8Array[] = []
    const typeKey = CertificatePdfGenerator.detectType(input.equipment.typeName)

    // Marca de agua a página completa
    await this.drawWatermarkFull(doc)

    // Título
    doc.fontSize(14).text('CERTIFICADO DE CALIBRACION', this.M, this.M + 90, { align: 'center' })
    this.pill(doc, `N° ${input.certificate_number}`, this.W - this.M - 140, this.M + 80)

    // Otorgado a
    this.labelBox(doc, 'OTORGADO A:', this.M + 120)
    const otorgadoY = this.M + 120
    const name = input.client.name || '—'
    doc.save()
    doc.roundedRect(this.M + 110, otorgadoY - 2, doc.widthOfString(name) + 24, 22, 4).fill('#fff099')
    doc.fillColor('#000').fontSize(12).text(name, this.M + 122, otorgadoY + 2)
    doc.restore()

    // Datos del equipo
    this.labelBox(doc, 'DATOS DEL EQUIPO:', this.M + 160)
    const tableY = this.M + 185
    const cols1: Col[] = [
      { header: 'EQUIPO', width: (this.W - 2 * this.M) * 0.25 },
      { header: 'MARCA', width: (this.W - 2 * this.M) * 0.25 },
      { header: 'MODELO', width: (this.W - 2 * this.M) * 0.25 },
      { header: 'SERIE', width: (this.W - 2 * this.M) * 0.25 }
    ]
    this.drawTable(doc, this.M, tableY, cols1, [[
      input.equipment.typeName || '—',
      input.equipment.brand || '—',
      input.equipment.model || '—',
      input.equipment.serial_number || '—'
    ]], { cellFill: () => '#fff099', cellFormatter: v => (v ?? '').toString() })

    // Texto normativo
    doc.moveTo(this.M, tableY + 40)
    doc.moveDown()
    doc.fontSize(10).text(
      'ELECTROTEC CONSULTING S.A.C. certifica que el equipo de topografía descrito ha sido revisado y calibrado en todos los puntos en nuestro laboratorio y se encuentra en perfecto estado de funcionamiento de acuerdo con los estándares internacionales establecidos (DIN18723).',
      { align: 'justify' }
    )

    // Equipo patrón utilizado
    const meta = input.results?.meta || {}
    const patronName = meta.pattern_equipment_name || 'COLIMADOR GF550'
    const patronSerial = meta.pattern_equipment_serial || '130644'
    doc.moveDown(0.5)
    doc.fontSize(10).text('EQUIPO PATRON UTILIZADO:')
    const cols2: Col[] = [
      { header: '', width: (this.W - 2 * this.M) * 0.6 },
      { header: '', width: (this.W - 2 * this.M) * 0.4 }
    ]
    this.drawTable(doc, this.M, doc.y + 4, cols2, [[patronName, patronSerial]], { headerFill: '#ffffff', cellFill: () => '#fff', cellFormatter: v => (v ?? '').toString() })

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

    // Página 2
    doc.addPage()
    await this.drawWatermarkFull(doc)

    doc.moveDown(4)
    const renderer = this.renderers[typeKey]
    doc.fontSize(10).text(renderer.getProcedureText())
    doc.moveDown(1)
    doc.fontSize(10).text('RESULTADOS:')

    const yStart = doc.y + 6
    const ctx: RenderContext = {
      input,
      typeKey,
      W: this.W,
      H: this.H,
      M: this.M,
      startY: yStart,
      drawTable: (x, y, cols, rows, opts) => this.drawTable(doc, x, y, cols, rows, opts),
      numberFormat: (n, d) => this.numberFormat(n, d)
    }
    let yCursor = renderer.renderResultsSection(doc, ctx)

    // Bloque laboratorio
    const lab = input.lab || {}
    const blockY = Math.max(yCursor + 10, this.H - 260)
    doc.fontSize(10).text('LABORATORIO.', this.M, blockY)
    const row = (label: string, value: string, y: number) => {
      doc.fontSize(9).text(`${label} :`, this.M, y)
      const w = doc.widthOfString(value) + 16
      doc.save(); doc.roundedRect(this.M + 90, y - 3, Math.max(w, 80), 18, 4).fill('#fff099'); doc.restore()
      doc.fillColor('#000').fontSize(10).text(value, this.M + 96, y + 1)
    }
    if (typeof lab.temperature === 'number') row('TEMPERATURA', `${lab.temperature}°`, blockY + 18)
    if (typeof lab.humidity === 'number') row('HUMEDAD', `${lab.humidity}%`, blockY + 38)
    if (typeof lab.pressure === 'number') row('PRESION ATM.', `${lab.pressure}mmHg`, blockY + 58)

    const drawCheck = (label: string, checked: boolean, x: number, y: number) => {
      doc.fontSize(10).text(label, x, y)
      doc.rect(x + 85, y - 3, 16, 16).stroke()
      if (checked) doc.fontSize(12).text('X', x + 88.5, y - 2)
    }
    drawCheck('CALIBRACION', !!lab.calibration, this.M + 70, blockY + 95)
    drawCheck('MANTENIMIENTO', !!lab.maintenance, this.M + 220, blockY + 95)

    // Bloque final: técnico, firma, fechas
    const col1X = this.M
    const col2X = this.M + (this.W - 2 * this.M) * 0.4 + 10
    const col3X = this.M + (this.W - 2 * this.M) * 0.7 + 10
    const baseY = this.H - 140
    doc.fontSize(10).text('Certificado por:', col1X, baseY)
    doc.fontSize(11).text(`Tec. ${input.technician?.full_name || '—'}`, col1X, baseY + 18)
    doc.fontSize(10).text('Jefe de Laboratorio.', col1X, baseY + 36)

    if (input.technician?.signature_image_url) {
      const sigBuf = await this.deps.fetchRemote(input.technician.signature_image_url)
      if (sigBuf) doc.image(sigBuf, col2X, baseY - 10, { width: (this.W - 2 * this.M) * 0.25, height: 60, align: 'center' })
      else doc.fontSize(9).text('FIRMA NO REGISTRADA', col2X, baseY + 10, { width: (this.W - 2 * this.M) * 0.25, align: 'center' })
    } else {
      doc.fontSize(9).text('FIRMA NO REGISTRADA', col2X, baseY + 10, { width: (this.W - 2 * this.M) * 0.25, align: 'center' })
    }

    const box = (title: string, value: string, x: number, y: number) => {
      const ww = (this.W - 2 * this.M) * 0.25
      const hh = 36
      doc.rect(x, y, ww, hh).stroke('#999')
      doc.fontSize(10).text(title, x + 8, y + 6)
      const wv = doc.widthOfString(value) + 16
      doc.save(); doc.roundedRect(x + ww - (wv + 8) - 8, y + hh - 22, wv + 8, 18, 4).fill('#fff099'); doc.restore()
      doc.fontSize(10).text(value, x + ww - (wv + 8), y + hh - 18)
    }
    box('Calibrado:', this.formatDateEs(input.calibration_date), col3X, baseY)
    box('Próxima calibración:', this.formatDateEs(input.next_calibration_date), col3X, baseY + 46)

    // Finalizar
    const buffer = await new Promise<Buffer>((resolve) => {
      doc.on('data', (c: Uint8Array) => chunks.push(c))
      doc.on('end', () => resolve(Buffer.concat(chunks)))
      doc.end()
    })
    return buffer
  }

  // Helpers de dibujo y formato (SRP dentro de la clase)
  private async drawWatermarkFull(doc: PDFKit.PDFDocument) {
    const img = await this.deps.getPublicAsset('marca_agua.png')
    doc.save()
    doc.image(img, 0, 0, { width: this.W, height: this.H })
    doc.restore()
  }

  private pill(doc: PDFKit.PDFDocument, text: string, x: number, y: number) {
    const padX = 8
    const padY = 4
    const w = (doc as any).widthOfString(text) + padX * 2
    const h = (doc as any).currentLineHeight() + padY * 2
    doc.save()
    doc.roundedRect(x, y, w, h, 6).fill('#ffe97f')
    doc.fillColor('#333').fontSize(10).text(text, x + padX, y + padY)
    doc.restore()
    doc.fillColor('#000')
  }

  private labelBox(doc: PDFKit.PDFDocument, label: string, y: number) {
    const lw = (doc as any).widthOfString(label) + 12
    doc.save()
    doc.rect(this.M, y, lw, 18).fill('#000')
    doc.fillColor('#fff').fontSize(10).text(label, this.M + 6, y + 3)
    doc.restore()
    doc.fillColor('#000')
  }

  private drawTable(
    doc: PDFKit.PDFDocument,
    x: number,
    y: number,
    cols: Col[],
    rows: (Array<string | number | null | undefined>)[],
    opts: TableOpts
  ) {
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
        if (fill) { doc.save(); doc.rect(cx2, ry, col.width, rowH).fill(fill); doc.restore() }
        doc.rect(cx2, ry, col.width, rowH).stroke('#999')
        doc.fillColor('#000').text(text, cx2 + 6, ry + 5, { width: col.width - 12, align: col.align || 'center' })
        cx2 += col.width
      })
      ry += rowH
    })
    return ry
  }

  private numberFormat(n: number, decimals = 3) {
    return new Intl.NumberFormat('es-PE', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }).format(n)
  }

  private formatDateEs(iso: string) {
    const d = new Date(iso)
    const dd = d.getUTCDate().toString().padStart(2, '0')
    const meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC']
    const mm = meses[d.getUTCMonth()]
    const yyyy = d.getUTCFullYear()
    return `${dd} – ${mm} – ${yyyy}`
  }
}
