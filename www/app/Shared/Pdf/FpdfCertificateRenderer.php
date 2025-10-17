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
        if (is_file(__DIR__.'/../../../../assets/images/logo.png')) {
            $this->Image(__DIR__.'/../../../../assets/images/logo.png', 10, 8, 40);
        }
        $this->SetFont('Arial','B',18);
        $this->SetXY(60,12);
        $this->Cell(0,8,'ELECTROTEC',0,1,'L');
        $this->SetFont('Arial','',12);
        $this->SetXY(60,20);
        $this->Cell(0,8,'CONSULTING S.A.C.',0,1,'L');
        $this->SetFont('Arial','',9);
        $this->SetTextColor(80,80,80);
        $this->SetXY(60,28);
        $this->Cell(0,6,'CALIBRACION - MANTENIMIENTO - REPARACION',0,1,'L');
        $this->Ln(10);
        $this->SetTextColor(0,0,0);
    }
    function Footer() {
        $this->SetY(-25);
        $this->SetFont('Arial','',9);
        $this->SetTextColor(120,120,120);
        $this->Cell(0,6,utf8_decode('Av. Progreso 648 Pj. Santa Rosita - San Juan de Lurigancho - Lima'),0,1,'C');
        $this->Cell(0,6,utf8_decode('Cel.: 930 321 872 / 924 699 206'),0,0,'C');
    }
    function BasicTable($header, $data) {
        $this->SetFillColor(230,230,230);
        $this->SetFont('Arial','B',9);
        $w = [40,40,40,60];
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
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();

        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(0,10,utf8_decode('CERTIFICADO DE CALIBRACION'),1,1,'C');
        $pdf->Ln(5);

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

        // Patrón (placeholder)
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(50,8,utf8_decode('EQUIPO PATRON UTILIZADO:'),0,0,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->SetFillColor(230,230,230);
        $pdf->Cell(40,8,utf8_decode('COLIMADOR GF550'),1,0,'C',true);
        $pdf->Cell(40,8,utf8_decode('130644'),1,1,'C',true);
        $pdf->Ln(5);

        // Segunda página con resultados si existen
        $pdf->AddPage();
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

        // Distancias si existen
        $dist = $data['resultados_distancia'] ?? [];
        if ($dist) {
            $pdf->SetFont('Arial','B',10);
            $pdf->SetFillColor(230,230,230);
            $pdf->Cell(0,8,utf8_decode('MEDICIONES DE DISTANCIA:'),0,1,'L',true);
            $headerD = ['Puntos de Control','Distancia Obtenida','Precisión','Variación'];
            $rowsD = [];
            foreach ($dist as $d) {
                $rowsD[] = [
                    number_format((float)($d['punto_control_metros'] ?? 0),3,'.',' ').' m.',
                    number_format((float)($d['distancia_obtenida_metros'] ?? 0),3,'.',' ').' m.',
                    ((int)($d['precision_base_mm'] ?? 0)).' mm + '.((int)($d['precision_ppm'] ?? 0)).' ppm',
                    number_format((float)($d['variacion_metros'] ?? 0),3,'.',' ').' m.',
                ];
            }
            $pdf->BasicTable($headerD, $rowsD);
        }

        $filename = 'certificado_'.($data['certificate_number'] ?? 'cert').'.pdf';
        header('Content-Type: application/pdf');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        header('Content-Disposition: '.($disposition==='inline'?'inline':'attachment').'; filename="'.$filename.'"');
        $pdf->Output($disposition==='inline' ? 'I' : 'D', $filename);
    }
}
