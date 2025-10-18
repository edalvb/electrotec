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
};
PHP;
        $pdf = eval($code);
    // Márgenes y pie para respetar los arcos de la marca de agua
    // Top mayor para el arco superior, bottom mayor para el arco inferior
    $pdf->SetMargins(20, 55, 20); // left, top, right (mm)
    $pdf->SetAutoPageBreak(true, 40); // bottom margin (mm)
    $pdf->AddPage();

    // Fechas de calibración
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(13, 42, 79);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(0,8,utf8_decode('FECHAS DE CALIBRACION:'),0,1,'C',true);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(60,8,utf8_decode('Fecha de Calibración:'),0,0,'L');
    $pdf->Cell(60,8,utf8_decode((string)($data['calibration_date'] ?? '')),0,1,'L');
    $pdf->Cell(60,8,utf8_decode('Próxima Calibración:'),0,0,'L');
    $pdf->Cell(60,8,utf8_decode((string)($data['next_calibration_date'] ?? '')),0,1,'L');
    $pdf->Ln(5);

    // Separación inicial para caer en la zona blanca bajo el arco superior
    $pdf->Ln(10);

    // Cabecera: cliente y número
        $clientName = (string)($data['client']['name'] ?? $data['client_name'] ?? '');
        $certNum = (string)($data['certificate_number'] ?? '');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(30,8,utf8_decode('OTORGADO A:'),0,0,'L');
        $pdf->SetFont('Arial','B',10);
        $pdf->SetFillColor(255,223,100);
        $pdf->Cell(100,8,utf8_decode($clientName),0,0,'C',true);
        $pdf->Cell(10);
        $pdf->Cell(40,8,utf8_decode($certNum),0,1,'C',true);
        $pdf->Ln(5);

        // Datos del Equipo
        $pdf->SetFont('Arial','B',10);
        $pdf->SetFillColor(13, 42, 79);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0,8,utf8_decode('DATOS DEL EQUIPO:'),0,1,'C',true);

        $header = ['EQUIPO','MARCA','MODELO','SERIE'];
        $equipmentName = (string)($data['equipment']['name'] ?? $data['equipment_name'] ?? '');
        $equipmentBrand = (string)($data['equipment']['brand'] ?? $data['equipment_brand'] ?? '');
        $equipmentModel = (string)($data['equipment']['model'] ?? $data['equipment_model'] ?? '');
        $equipmentSerial = (string)($data['equipment']['serial_number'] ?? $data['equipment_serial_number'] ?? '');
        $pdf->SetTextColor(0,0,0);
        $pdf->BasicTable($header, [[utf8_decode($equipmentName), utf8_decode($equipmentBrand), utf8_decode($equipmentModel), utf8_decode($equipmentSerial)]]);
        $pdf->Ln(5);

        // Declaración
        $pdf->SetFont('Arial','',9);
        $txt = "ELECTROTEC CONSULTING S.A.C. certifica que el equipo de topografía descrito ha sido revisado y calibrado en todos los puntos en nuestro laboratorio y se encuentra en perfecto estado de funcionamiento de acuerdo con los estándares internacionales establecidos (DIN18723).";
        $pdf->MultiCell(0,5,utf8_decode($txt),0,'J');
        $pdf->Ln(5);

        // Patrón (placeholder) - podría venir desde BD en el futuro
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(50,8,utf8_decode('EQUIPO PATRON UTILIZADO:'),0,0,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->SetFillColor(230,230,230);
        $pdf->Cell(40,8,utf8_decode('COLIMADOR GF550'),1,0,'C',true);
        $pdf->Cell(40,8,utf8_decode('130644'),1,1,'C',true);
        $pdf->Ln(5);

        // Bloque de condiciones del laboratorio si existen
        $lab = $data['lab_conditions'] ?? null;
        if ($lab) {
            $pdf->SetFont('Arial','B',10);
            $pdf->SetFillColor(13,42,79);
            $pdf->SetTextColor(255,255,255);
            $pdf->Cell(0,8,utf8_decode('CONDICIONES DEL LABORATORIO'),0,1,'C',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(60,8,utf8_decode('Temperatura:'),0,0,'L');
            $pdf->Cell(40,8,utf8_decode(($lab['temperature'] ?? '').' °C'),0,1,'L');
            $pdf->Cell(60,8,utf8_decode('Humedad:'),0,0,'L');
            $pdf->Cell(40,8,utf8_decode(($lab['humidity'] ?? '').' %'),0,1,'L');
            $pdf->Cell(60,8,utf8_decode('Presión Atmosférica:'),0,0,'L');
            $pdf->Cell(40,8,utf8_decode(($lab['pressure'] ?? '').' mmHg'),0,1,'L');
            $pdf->Ln(3);
        }

        // Bloque de servicio/observaciones/estado del JSON results
        $resultsJson = $data['results_json'] ?? [];
        if ($resultsJson) {
            $pdf->SetFont('Arial','B',10);
            $pdf->SetFillColor(13,42,79);
            $pdf->SetTextColor(255,255,255);
            $pdf->Cell(0,8,utf8_decode('DATOS DEL SERVICIO'),0,1,'C',true);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('Arial','',10);
            // Servicio
            $service = $resultsJson['service_type'] ?? null;
            if (is_array($service)) {
                $svc = [];
                if (!empty($service['calibration'])) { $svc[] = 'Calibración'; }
                if (!empty($service['maintenance'])) { $svc[] = 'Mantenimiento'; }
                $pdf->Cell(60,8,utf8_decode('Servicio Realizado:'),0,0,'L');
                $pdf->Cell(120,8,utf8_decode(implode(' y ', $svc) ?: '-'),0,1,'L');
            }
            // Estado
            if (isset($resultsJson['status']) && $resultsJson['status']!=='') {
                $map = ['approved'=>'Aprobado','conditional'=>'Aprobado con observaciones','rejected'=>'Rechazado'];
                $st = $resultsJson['status'];
                $pdf->Cell(60,8,utf8_decode('Estado del Equipo:'),0,0,'L');
                $pdf->Cell(120,8,utf8_decode($map[$st] ?? (string)$st),0,1,'L');
            }
            // Observaciones
            if (!empty($resultsJson['observations'])) {
                $pdf->Cell(60,8,utf8_decode('Observaciones:'),0,1,'L');
                $pdf->SetFont('Arial','',9);
                $pdf->MultiCell(0,6,utf8_decode((string)$resultsJson['observations']),0,'L');
                $pdf->SetFont('Arial','',10);
            }
            $pdf->Ln(2);
        }

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

        // Firma y técnico responsable
        $tech = $data['technician'] ?? null;
        if ($tech) {
            $pdf->Ln(8);
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(0,6,utf8_decode('Certificado por:'),0,1,'L');
            // Imagen de firma si tenemos path o base64
            $yStart = $pdf->GetY();
            $addedImage = false;
            if (!empty($tech['path_firma'])) {
                $sigPath = (string)$tech['path_firma'];
                $sigFs = $sigPath;
                if (!is_file($sigFs)) {
                    // intentar relativo a www
                    $alt = __DIR__ . '/../../../../' . ltrim($sigPath, '/\\');
                    if (is_file($alt)) { $sigFs = $alt; }
                }
                if (is_file($sigFs)) {
                    // Determinar tipo por extensión; si no hay, intentar detectar y pasarlo explícitamente
                    $ext = strtolower(pathinfo($sigFs, PATHINFO_EXTENSION));
                    $type = '';
                    if ($ext === 'png') { $type = 'PNG'; }
                    elseif (in_array($ext, ['jpg','jpeg'], true)) { $type = 'JPG'; }
                    if ($type) { $pdf->Image($sigFs, 15, $yStart, 50, 0, $type); }
                    else {
                        // Intentar detectar por contenido
                        $blob = @file_get_contents($sigFs);
                        $imgInfo = $blob ? @getimagesizefromstring($blob) : false;
                        $mime = is_array($imgInfo) && isset($imgInfo['mime']) ? $imgInfo['mime'] : '';
                        $type = (str_contains($mime,'png') ? 'PNG' : (str_contains($mime,'jpeg')||str_contains($mime,'jpg') ? 'JPG' : ''));
                        $pdf->Image($sigFs, 15, $yStart, 50, 0, $type);
                    }
                    $addedImage = true;
                }
            } elseif (!empty($tech['firma_base64'])) {
                $b64Raw = (string)$tech['firma_base64'];
                $mime = '';
                $b64 = $b64Raw;
                // Soportar data URL o base64 puro
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
                    // Intentar detectar desde los bytes
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
                        $pdf->Image($tmpFile, 15, $yStart, 50, 0, $type);
                        @unlink($tmpFile);
                        if ($tmpBase && is_file($tmpBase)) { @unlink($tmpBase); }
                        $addedImage = true;
                    }
                }
            }
            $pdf->Ln($addedImage ? 28 : 12);
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(0,6,utf8_decode($tech['nombre_completo'] ?? ''),0,1,'L');
            $pdf->SetFont('Arial','',9);
            $pdf->Cell(0,6,utf8_decode(($tech['cargo'] ?? 'Servicio Técnico')),0,1,'L');
        }

        $filename = 'certificado_'.($data['certificate_number'] ?? 'cert').'.pdf';
        header('Content-Type: application/pdf');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        header('Content-Disposition: '.($disposition==='inline'?'inline':'attachment').'; filename="'.$filename.'"');
        $pdf->Output($disposition==='inline' ? 'I' : 'D', $filename);
    }
}
