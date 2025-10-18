<?php

namespace App\Shared\Pdf;

// FPDF simple vendor inclusion: prefer composer package setasign/fpdf si disponible.
// Como fallback, incluimos FPDF si está en vendor/fpdf/fpdf.php; si no, error controlado.

class FpdfCertificateRenderer
{
    /**
     * Genera y envía el PDF al navegador usando FPDF con el formato solicitado.
     * @param array<string,mixed> $data
     * @param string $disposition inline|attachment
     */
    public function output(array $data, string $disposition = 'attachment'): void
    {
        // Intentar cargar FPDF
        $fpdfPathCandidates = [
            __DIR__ . '/../../../vendor/setasign/fpdf/fpdf.php',
            __DIR__ . '/../../../vendor/fpdf/fpdf.php',
            __DIR__ . '/../../../../vendor/setasign/fpdf/fpdf.php',
            __DIR__ . '/../../../../vendor/fpdf/fpdf.php',
        ];
        $loaded = false;
        foreach ($fpdfPathCandidates as $path) {
            if (is_file($path)) { require_once $path; $loaded = true; break; }
        }
        if (!$loaded && class_exists('FPDF') === false) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['ok'=>false,'error'=>'FPDF no está instalado. Agregue la dependencia setasign/fpdf.']);
            return;
        }

        // Definir clase extendiendo FPDF vía eval() para evitar errores de análisis cuando FPDF no está instalado
        // Crear una instancia de FPDF extendida como clase anónima (vía eval) para evitar referencias directas
        $code = <<<'PHP'
return new class extends FPDF {
    function Header() {
        // Marca de agua a página completa en todas las páginas
        $bgCandidates = [
            __DIR__.'/../../../assets/images/marca_agua.png',
            __DIR__.'/../../../../assets/images/marca_agua.png',
        ];
        $bgPath = null;
        foreach ($bgCandidates as $p) { if (is_file($p)) { $bgPath = $p; break; } }
        if ($bgPath) {
            $bakAuto = $this->AutoPageBreak; $bakBtm = $this->bMargin;
            $this->SetAutoPageBreak(false);
            // Cubrir toda la página
            $this->Image($bgPath, 0, 0, $this->w, $this->h, 'PNG');
            $this->SetAutoPageBreak($bakAuto, $bakBtm);
            // Restaurar cursor
            $this->SetXY($this->lMargin, $this->tMargin);
        }
    }
    function Footer() {
        // Sin footer textual; viene en la marca de agua
    }
    function BasicTable($header, $data) {
        $this->SetFillColor(230,230,230);
        $this->SetFont('Arial','B',9);
        // Calcular anchos proporcionalmente al ancho disponible (contenido)
        $available = $this->w - $this->lMargin - $this->rMargin;
        $w = [0.28*$available, 0.22*$available, 0.22*$available, 0.28*$available];
        foreach ($header as $i => $h) { $this->Cell($w[$i],7,utf8_decode($h),1,0,'C',true); }
        $this->Ln();
        $this->SetFont('Arial','',9);
        foreach ($data as $row) {
            $this->Cell($w[0],6,utf8_decode($row[0]??''),'LR',0,'C');
            $this->Cell($w[1],6,utf8_decode($row[1]??''),'LR',0,'C');
            $this->Cell($w[2],6,utf8_decode($row[2]??''),'LR',0,'C');
            $this->Cell($w[3],6,utf8_decode($row[3]??''),'LR',0,'C');
            $this->Ln();
        }
        $this->Cell(array_sum($w),0,'','T');
    }
    // Exponer dimensiones y márgenes de forma segura para uso externo
    public function pageWidth() { return $this->w; }
    public function pageHeight() { return $this->h; }
    public function leftMargin() { return $this->lMargin; }
    public function rightMargin() { return $this->rMargin; }
    public function topMargin() { return $this->tMargin; }
    public function bottomMargin() { return $this->bMargin; }
    public function contentWidth() { return $this->w - $this->lMargin - $this->rMargin; }
};
PHP;
        $pdf = eval($code);
    // Márgenes y pie para respetar los arcos de la marca de agua
    // Top mayor para el arco superior, bottom mayor para el arco inferior
    $pdf->SetMargins(20, 55, 20); // left, top, right (mm)
    $pdf->SetAutoPageBreak(true, 40); // bottom margin (mm)
    $pdf->AddPage();
    // Separación inicial para caer en la zona blanca bajo el arco superior
    $pdf->Ln(10);

    // Título principal
    $pdf->SetFont('Arial','B',18);
    $pdf->Cell(0,10,utf8_decode('CERTIFICADO DE CALIBRACIÓN'),0,1,'C');
        $pdf->Ln(2);

    // Cabecera: OTORGADO A y número de certificado a la derecha en la misma línea
        $clientName = (string)($data['client']['name'] ?? $data['client_name'] ?? '');
        $certNum = (string)($data['certificate_number'] ?? '');
        $pdf->SetFont('Arial','',11);
        $pdf->Cell(0,8,utf8_decode('OTORGADO A:'),0,0,'L');
        // número alineado a la derecha
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(0,8,utf8_decode('N° '. $certNum),0,1,'R');
        // Nombre del cliente centrado y en negrita
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(0,8,utf8_decode($clientName),0,1,'C');
        $pdf->Ln(5);

        // Datos del Equipo
        $pdf->SetFont('Arial','B',10);
        $pdf->SetFillColor(13, 42, 79);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0,8,utf8_decode('DATOS DEL EQUIPO:'),0,1,'C',true);

    $header = ['EQUIPO','MARCA','MODELO','SERIE'];
    $equipmentType = (string)($data['equipment']['type'] ?? $data['equipment_type'] ?? '');
        $equipmentBrand = (string)($data['equipment']['brand'] ?? $data['equipment_brand'] ?? '');
        $equipmentModel = (string)($data['equipment']['model'] ?? $data['equipment_model'] ?? '');
        $equipmentSerial = (string)($data['equipment']['serial_number'] ?? $data['equipment_serial_number'] ?? '');
        $pdf->SetTextColor(0,0,0);
    // Pasar strings en UTF-8 y dejar que BasicTable aplique utf8_decode una sola vez
    $pdf->BasicTable($header, [[
        $equipmentType,
        $equipmentBrand,
        $equipmentModel,
        $equipmentSerial
    ]]);
        $pdf->Ln(5);

        // Declaración
        $pdf->SetFont('Arial','',9);
        $txt = "ELECTROTEC CONSULTING S.A.C. certifica que el equipo de topografía descrito ha sido revisado y calibrado en todos los puntos en nuestro laboratorio y se encuentra en perfecto estado de funcionamiento de acuerdo con los estándares internacionales establecidos (DIN18723).";
        $pdf->MultiCell(0,5,utf8_decode($txt),0,'J');
        $pdf->Ln(5);

    // Patrón (placeholder)
    // En la misma línea: izquierda etiqueta y a la derecha el número de certificado en negrita
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,utf8_decode('EQUIPO PATRON UTILIZADO:'),0,0,'L');
    $pdf->Cell(0,8,utf8_decode('N° '. $certNum),0,1,'R');
    // pequeño espacio y luego el patrón
    $pdf->Ln(2);
    $pdf->SetFont('Arial','',10);
    $pdf->SetFillColor(230,230,230);
    // Mostrar patrón resumido (si existiera en data['results_json']['patron'])
    $patronText = 'COLIMADOR GF550 - N° 130644';
    if (!empty($data['results_json']['patron'])) { $patronText = (string)$data['results_json']['patron']; }
    $pdf->Cell(0,8,utf8_decode($patronText),0,1,'L');
    $pdf->Ln(3);

    // Metodología aplicada y trazabilidad
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,8,utf8_decode('METODOLOGÍA APLICADA Y TRAZABILIDAD DE LOS PATRONES.'),0,1,'L');
    $pdf->SetFont('Arial','',9);
    // Para resaltar marcas en negrita, imprimimos en segmentos
    $line1a = 'Cinta métrica, marca: ';
    $line1b = 'HULTAFORS';
    $line1c = ', modelo: ';
    $line1d = 'BT8M';
    $line1e = ', número de serie: ';
    $line1f = 'BT80977242';
    $line1g = ', Certificado de calibración ';
    $line1h = 'LLA - 066 - 2024';
    $pdf->Write(5, utf8_decode($line1a));
    $pdf->SetFont('Arial','B',9); $pdf->Write(5, utf8_decode($line1b));
    $pdf->SetFont('Arial','',9); $pdf->Write(5, utf8_decode($line1c));
    $pdf->SetFont('Arial','B',9); $pdf->Write(5, utf8_decode($line1d));
    $pdf->SetFont('Arial','',9); $pdf->Write(5, utf8_decode($line1e));
    $pdf->SetFont('Arial','B',9); $pdf->Write(5, utf8_decode($line1f));
    $pdf->SetFont('Arial','',9); $pdf->Write(5, utf8_decode($line1g));
    $pdf->SetFont('Arial','B',9); $pdf->Write(5, utf8_decode($line1h));
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',9);
    $pdf->MultiCell(0,5,utf8_decode('Emitido por Laboratorio de Longitud y Angulo - Dirección de metrología - INACAL Instituto Nacional de calidad.'),0,'L');
    // Segunda línea con marcas
    $pdf->SetFont('Arial','',9);
    $part2a = 'Para Controlar y calibrar este instrumento se contrasta con un colimador marca ';
    $part2b = 'KOLIDA';
    $part2c = ' modelo ';
    $part2d = 'GF550';
    $part2e = ' Patronado mensualmente con estación total marca ';
    $part2f = 'LEICA';
    $part2g = ' modelo ';
    $part2h = 'TS06 PLUS PRECISION 1"';
    $part2i = ' y ivel automático marca ';
    $part2j = 'TOPCON';
    $part2k = ' modelo ';
    $part2l = 'AT-B2 PRECISION 0.7mm.';
    $pdf->Write(5, utf8_decode($part2a));
    $pdf->SetFont('Arial','B',9); $pdf->Write(5, utf8_decode($part2b));
    $pdf->SetFont('Arial','',9); $pdf->Write(5, utf8_decode($part2c));
    $pdf->SetFont('Arial','B',9); $pdf->Write(5, utf8_decode($part2d));
    $pdf->SetFont('Arial','',9); $pdf->Write(5, utf8_decode($part2e));
    $pdf->SetFont('Arial','B',9); $pdf->Write(5, utf8_decode($part2f));
    $pdf->SetFont('Arial','',9); $pdf->Write(5, utf8_decode($part2g));
    $pdf->SetFont('Arial','B',9); $pdf->Write(5, utf8_decode($part2h));
    $pdf->SetFont('Arial','',9); $pdf->Write(5, utf8_decode($part2i));
    $pdf->SetFont('Arial','B',9); $pdf->Write(5, utf8_decode($part2j));
    $pdf->SetFont('Arial','',9); $pdf->Write(5, utf8_decode($part2k));
    $pdf->SetFont('Arial','B',9); $pdf->Write(5, utf8_decode($part2l));
    $pdf->Ln(6);
    $pdf->SetFont('Arial','',9);
    $pdf->MultiCell(0,5,utf8_decode('El control se ejecuta en la base metálica fijada en la pared y piso, ajena a influencias del clima y enfocado el retículo al infinito.'),0,'L');
    $pdf->Ln(4);

        // Guardaremos condiciones de laboratorio y datos de servicio para el final
        $lab = $data['lab_conditions'] ?? null;
        $resultsJson = $data['results_json'] ?? [];

    // Segunda página con resultados si existen
    $pdf->AddPage();
    $pdf->Ln(6);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(0,8,utf8_decode('RESULTADOS:'),1,1,'C');

        $headerR = ['Valor de Patrón','Valor Obtenido','Precisión','Error'];
        $rowsR = [];
        foreach (($data['resultados'] ?? []) as $r) {
            $fmt = function($g,$m,$s){ return sprintf('%d° %02d\' %02d"',(int)$g,(int)$m,(int)$s); };
            $prec = ($r['tipo_resultado'] ?? 'segundos') === 'lineal' ? sprintf('± %02d mm',(int)($r['precision'] ?? $r['precision_val'] ?? 0)) : sprintf('± %02d"',(int)($r['precision'] ?? $r['precision_val'] ?? 0));
            $rowsR[] = [
                $fmt($r['valor_patron_grados']??0,$r['valor_patron_minutos']??0,$r['valor_patron_segundos']??0),
                $fmt($r['valor_obtenido_grados']??0,$r['valor_obtenido_minutos']??0,$r['valor_obtenido_segundos']??0),
                $prec,
                sprintf('%02d"',(int)($r['error_segundos'] ?? 0)),
            ];
        }
        if (!$rowsR) { $rowsR[] = ['-','-','-','-']; }
        $pdf->BasicTable($headerR, $rowsR);
        $pdf->Ln(5);

        // Distancias: separar en Con Prisma y Sin Prisma y mostrarlas a ancho completo
        $dist = $data['resultados_distancia'] ?? [];
        if ($dist) {
            $headerD = ['Puntos de Control','Distancia Obtenida','Precisión','Variación'];
            $rowsCon = [];
            $rowsSin = [];
            foreach ($dist as $d) {
                $row = [
                    number_format((float)($d['punto_control_metros'] ?? 0),3,'.',' ').' m.',
                    number_format((float)($d['distancia_obtenida_metros'] ?? 0),3,'.',' ').' m.',
                    ((int)($d['precision_base_mm'] ?? 0)).' mm + '.((int)($d['precision_ppm'] ?? 0)).' ppm',
                    number_format((float)($d['variacion_metros'] ?? 0),3,'.',' ').' m.',
                ];
                if (!empty($d['con_prisma'])) { $rowsCon[] = $row; } else { $rowsSin[] = $row; }
            }

            if ($rowsCon) {
                $pdf->Ln(2);
                $pdf->SetFont('Arial','B',10);
                $pdf->SetFillColor(230,230,230);
                $pdf->Cell(0,8,utf8_decode('MEDICION CON PRISMA'),0,1,'L',true);
                $pdf->BasicTable($headerD, $rowsCon);
            }
            if ($rowsSin) {
                $pdf->Ln(4);
                $pdf->SetFont('Arial','B',10);
                $pdf->SetFillColor(230,230,230);
                $pdf->Cell(0,8,utf8_decode('MEDICION SIN PRISMA'),0,1,'L',true);
                $pdf->BasicTable($headerD, $rowsSin);
            }
        }

        // BLOQUES FINALES SEGÚN REQUERIMIENTO: Laboratorio, opciones de servicio y recuadro con firma y fechas
        // 1) LABORATORIO.
        if ($lab) {
            $pdf->Ln(6);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,6,utf8_decode('LABORATORIO.'),0,1,'L');
            $pdf->SetFont('Arial','',10);
            $tmp = trim((string)($lab['temperature'] ?? ''));
            $hum = trim((string)($lab['humidity'] ?? ''));
            $prs = trim((string)($lab['pressure'] ?? ''));
            $pdf->Cell(0,6,utf8_decode('TEMPERATURA : '.($tmp !== '' ? $tmp.'°' : '-')),0,1,'L');
            $pdf->Cell(0,6,utf8_decode('HUMEDAD : '.($hum !== '' ? $hum.'%' : '-')),0,1,'L');
            $pdf->Cell(0,6,utf8_decode('PRESION ATM. : '.($prs !== '' ? $prs.'mmHg' : '-')),0,1,'L');
        }

        // 2) Opciones de servicio como checkboxes
        if ($resultsJson) {
            $pdf->Ln(4);
            $pdf->SetFont('Arial','',10);
            $service = $resultsJson['service_type'] ?? [];
            $calX = (!empty($service['calibration'])) ? 'x' : ' ';
            $manX = (!empty($service['maintenance'])) ? 'x' : ' ';
            $pdf->Cell(0,6,utf8_decode('CALIBRACION ['.$calX.']    MANTENIMIENTO ['.$manX.']'),0,1,'L');
        }

        // 3) Recuadro final con firma y fechas
        // Utilidades de fecha en español
        $formatEsp = function(?string $iso): string {
            if (!$iso) return '';
            $ts = @strtotime($iso);
            if (!$ts) return (string)$iso;
            $meses = [1=>'ENE','FEB','MAR','ABR','MAY','JUN','JUL','AGO','SEP','OCT','NOV','DIC'];
            $d = (int)date('d', $ts);
            $m = (int)date('n', $ts);
            $y = (int)date('Y', $ts);
            $mes = $meses[$m] ?? strtoupper(date('M',$ts));
            // Usar guiones ASCII para evitar caracteres no soportados en FPDF/Windows-1252
            return sprintf('%02d - %s - %04d', $d, $mes, $y);
        };

        $fechaCal = $formatEsp((string)($data['calibration_date'] ?? ''));
        $fechaProx = $formatEsp((string)($data['next_calibration_date'] ?? ''));

        $tech = $data['technician'] ?? null;
        $nombreTec = is_array($tech) ? (string)($tech['nombre_completo'] ?? '') : '';
        $cargoTec = is_array($tech) ? (string)($tech['cargo'] ?? '') : '';
        if ($cargoTec === '') { $cargoTec = 'Jefe de Laboratorio.'; }

        $pdf->Ln(6);
    $x0 = $pdf->GetX();
        $y0 = $pdf->GetY();
    $contentW = $pdf->contentWidth();
        $col1 = $contentW * 0.42; // izquierda
        $col2 = $contentW * 0.33; // centro (firma)
        $col3 = $contentW - $col1 - $col2; // derecha

        // Columna izquierda: texto de responsable
    $pdf->SetXY($pdf->leftMargin(), $y0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell($col1,6,utf8_decode('Certificado por:'),0,1,'L');
    $pdf->SetX($pdf->leftMargin());
        $pdf->SetFont('Arial','B',10);
        $pdf->MultiCell($col1,6,utf8_decode($nombreTec),0,'L');
    $pdf->SetX($pdf->leftMargin());
        $pdf->SetFont('Arial','',9);
        $pdf->MultiCell($col1,6,utf8_decode($cargoTec),0,'L');
        $yEndLeft = $pdf->GetY();

        // Columna central: imagen de la firma centrada
        $yStartMid = $y0; $yEndMid = $y0 + 20; // altura base
        if ($tech) {
            $addedImage = false;
            $sigX = $pdf->leftMargin() + $col1;
            $sigW = min(50.0, $col2 - 10.0); // margen interno
            $sigY = $yStartMid + 6;
            if (!empty($tech['path_firma'])) {
                $sigPath = (string)$tech['path_firma'];
                $sigFs = $sigPath;
                if (!is_file($sigFs)) {
                    $alt = __DIR__ . '/../../../../' . ltrim($sigPath, '/\\');
                    if (is_file($alt)) { $sigFs = $alt; }
                }
                if (is_file($sigFs)) {
                    $ext = strtolower(pathinfo($sigFs, PATHINFO_EXTENSION));
                    $type = '';
                    if ($ext === 'png') { $type = 'PNG'; }
                    elseif (in_array($ext, ['jpg','jpeg'], true)) { $type = 'JPG'; }
                    if (!$type) {
                        $blob = @file_get_contents($sigFs);
                        $imgInfo = $blob ? @getimagesizefromstring($blob) : false;
                        $mime = is_array($imgInfo) && isset($imgInfo['mime']) ? $imgInfo['mime'] : '';
                        $type = (str_contains($mime,'png') ? 'PNG' : (str_contains($mime,'jpeg')||str_contains($mime,'jpg') ? 'JPG' : ''));
                    }
                    // Centrar imagen en la columna
                    $imgX = $sigX + (($col2 - $sigW) / 2);
                    $pdf->Image($sigFs, $imgX, $sigY, $sigW, 0, $type ?: '');
                    $addedImage = true;
                }
            } elseif (!empty($tech['firma_base64'])) {
                $b64Raw = (string)$tech['firma_base64'];
                $mime = '';
                $b64 = $b64Raw;
                if (str_starts_with($b64Raw, 'data:image/')) {
                    $metaEnd = strpos($b64Raw, ',');
                    $meta = substr($b64Raw, 0, $metaEnd !== false ? $metaEnd : 0);
                    if (preg_match('#data:(image/[^;]+)#', $meta ?? '', $m)) { $mime = $m[1]; }
                    $parts = explode(',', $b64Raw, 2);
                    $b64 = $parts[1] ?? '';
                }
                $type = '';
                if (str_contains($mime,'png')) { $type = 'PNG'; }
                elseif (str_contains($mime,'jpeg') || str_contains($mime,'jpg')) { $type = 'JPG'; }
                if (!$type) {
                    $probe = base64_decode($b64, true) ?: '';
                    $info = $probe ? @getimagesizefromstring($probe) : false;
                    $m = is_array($info) && isset($info['mime']) ? $info['mime'] : '';
                    if (str_contains($m,'png')) { $type = 'PNG'; }
                    elseif (str_contains($m,'jpeg')||str_contains($m,'jpg')) { $type = 'JPG'; }
                }
                if ($b64 !== '') {
                    $tmpBase = tempnam(sys_get_temp_dir(), 'sig_');
                    if ($tmpBase) {
                        $ext = $type === 'PNG' ? '.png' : ($type === 'JPG' ? '.jpg' : '');
                        $tmpFile = $tmpBase . $ext;
                        @file_put_contents($tmpFile, base64_decode($b64));
                        $imgX = $sigX + (($col2 - $sigW) / 2);
                        $pdf->Image($tmpFile, $imgX, $sigY, $sigW, 0, $type ?: '');
                        @unlink($tmpFile);
                        if ($tmpBase && is_file($tmpBase)) { @unlink($tmpBase); }
                        $addedImage = true;
                    }
                }
            }
            if ($addedImage) { $yEndMid = max($yEndMid, $sigY + 22); }
        }

        // Columna derecha: fechas
    $pdf->SetXY($pdf->leftMargin() + $col1 + $col2, $y0);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell($col3,6,utf8_decode('Calibrado:'),0,1,'L');
    $pdf->SetX($pdf->leftMargin() + $col1 + $col2);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell($col3,6,utf8_decode($fechaCal),0,1,'L');
        // línea separadora
    $xSep = $pdf->leftMargin() + $col1 + $col2;
        $ySep = $pdf->GetY() + 1;
        $pdf->Line($xSep, $ySep, $xSep + $col3, $ySep);
        $pdf->Ln(3);
    $pdf->SetX($pdf->leftMargin() + $col1 + $col2);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell($col3,6,utf8_decode('Próxima calibración:'),0,1,'L');
    $pdf->SetX($pdf->leftMargin() + $col1 + $col2);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell($col3,6,utf8_decode($fechaProx),0,1,'L');
        $yEndRight = $pdf->GetY();

        $yBottom = max($yEndLeft, $yEndMid, $yEndRight) + 4;
        $blockH = max(30, $yBottom - $y0);

        // Dibujar bordes del recuadro y divisiones de columnas
    $pdf->Rect($pdf->leftMargin(), $y0, $contentW, $blockH);
    $pdf->Line($pdf->leftMargin() + $col1, $y0, $pdf->leftMargin() + $col1, $y0 + $blockH);
    $pdf->Line($pdf->leftMargin() + $col1 + $col2, $y0, $pdf->leftMargin() + $col1 + $col2, $y0 + $blockH);
        $pdf->SetY($y0 + $blockH + 2);

        $filename = 'certificado_'.($data['certificate_number'] ?? 'cert').'.pdf';
        header('Content-Type: application/pdf');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        header('Content-Disposition: '.($disposition==='inline'?'inline':'attachment').'; filename="'.$filename.'"');
        $pdf->Output($disposition==='inline' ? 'I' : 'D', $filename);
    }
}
