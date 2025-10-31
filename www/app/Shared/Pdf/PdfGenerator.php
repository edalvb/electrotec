<?php

namespace App\Shared\Pdf;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfGenerator
{
    private Dompdf $dompdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $this->dompdf = new Dompdf($options);
        $this->dompdf->setPaper('A4', 'portrait');
    }

    /**
     * Genera el HTML del certificado
     */
    private function generateCertificateHtml(array $data): string
    {
        $certificateNumber = htmlspecialchars($data['certificate_number'] ?? '');
        $calibrationDate = htmlspecialchars($data['calibration_date'] ?? '');
        $nextCalibrationDate = htmlspecialchars($data['next_calibration_date'] ?? '');
        $equipmentName = htmlspecialchars($data['equipment']['name'] ?? '');
        $equipmentBrand = htmlspecialchars($data['equipment']['brand'] ?? '');
        $equipmentModel = htmlspecialchars($data['equipment']['model'] ?? '');
        $equipmentSerial = htmlspecialchars($data['equipment']['serial_number'] ?? '');
        $clientName = htmlspecialchars($data['client']['name'] ?? '');
    $technicianName = htmlspecialchars($data['technician']['full_name'] ?? $data['technician']['nombre_completo'] ?? '');
        
        // Resultados
        $results = $data['results'] ?? null;
        $resultsHtml = '';
        if ($results && is_array($results)) {
            $resultsHtml = '<div style="margin-top: 20px;">';
            $resultsHtml .= '<h3 style="font-size: 14px; color: #1a237e; margin-bottom: 10px;">Resultados de Calibración</h3>';
            $resultsHtml .= '<table style="width: 100%; border-collapse: collapse;">';
            foreach ($results as $key => $value) {
                $keyFormatted = ucfirst(str_replace('_', ' ', $key));
                $resultsHtml .= '<tr>';
                $resultsHtml .= '<td style="padding: 8px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold; width: 40%;">' . htmlspecialchars($keyFormatted) . '</td>';
                $resultsHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars(is_array($value) ? json_encode($value) : $value) . '</td>';
                $resultsHtml .= '</tr>';
            }
            $resultsHtml .= '</table>';
            $resultsHtml .= '</div>';
        }

        // Condiciones de laboratorio
        $labConditions = $data['lab_conditions'] ?? null;
        $labConditionsHtml = '';
        if ($labConditions && is_array($labConditions)) {
            $labConditionsHtml = '<div style="margin-top: 20px;">';
            $labConditionsHtml .= '<h3 style="font-size: 14px; color: #1a237e; margin-bottom: 10px;">Condiciones de Laboratorio</h3>';
            $labConditionsHtml .= '<table style="width: 100%; border-collapse: collapse;">';
            
            if (isset($labConditions['temperature'])) {
                $labConditionsHtml .= '<tr>';
                $labConditionsHtml .= '<td style="padding: 8px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold; width: 40%;">Temperatura</td>';
                $labConditionsHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($labConditions['temperature']) . ' °C</td>';
                $labConditionsHtml .= '</tr>';
            }
            
            if (isset($labConditions['humidity'])) {
                $labConditionsHtml .= '<tr>';
                $labConditionsHtml .= '<td style="padding: 8px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold;">Humedad</td>';
                $labConditionsHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($labConditions['humidity']) . ' %</td>';
                $labConditionsHtml .= '</tr>';
            }
            
            if (isset($labConditions['pressure'])) {
                $labConditionsHtml .= '<tr>';
                $labConditionsHtml .= '<td style="padding: 8px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold;">Presión</td>';
                $labConditionsHtml .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($labConditions['pressure']) . ' hPa</td>';
                $labConditionsHtml .= '</tr>';
            }
            
            $labConditionsHtml .= '</table>';
            $labConditionsHtml .= '</div>';
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Calibración</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header h2 {
            margin: 10px 0 0 0;
            font-size: 18px;
            font-weight: normal;
        }
        .content {
            padding: 0 40px;
        }
        .certificate-number {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 30px;
            padding: 15px;
            border: 2px solid #1a237e;
            background-color: #f5f5f5;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1a237e;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1a237e;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .info-table td:first-child {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 35%;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .signature {
            text-align: center;
            margin-top: 60px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 250px;
            margin: 0 auto 10px auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ELECTROTEC CONSULTING S.A.C.</h1>
        <p style="margin: 5px 0 0 0; font-size: 14px;">RUC: 20602124305</p>
        <h2>Certificado de Calibración</h2>
    </div>
    
    <div class="content">
        <div class="certificate-number">
            Certificado N° {$certificateNumber}
        </div>
        
        <div class="section">
            <div class="section-title">Información del Cliente</div>
            <table class="info-table">
                <tr>
                    <td>Cliente</td>
                    <td>{$clientName}</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">Información del Equipo</div>
            <table class="info-table">
                <tr>
                    <td>Nombre del Equipo</td>
                    <td>{$equipmentName}</td>
                </tr>
                <tr>
                    <td>Marca</td>
                    <td>{$equipmentBrand}</td>
                </tr>
                <tr>
                    <td>Modelo</td>
                    <td>{$equipmentModel}</td>
                </tr>
                <tr>
                    <td>Número de Serie</td>
                    <td>{$equipmentSerial}</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">Fechas de Calibración</div>
            <table class="info-table">
                <tr>
                    <td>Fecha de Calibración</td>
                    <td>{$calibrationDate}</td>
                </tr>
                <tr>
                    <td>Próxima Calibración</td>
                    <td>{$nextCalibrationDate}</td>
                </tr>
            </table>
        </div>
        
        {$resultsHtml}
        
        {$labConditionsHtml}
        
        <div class="footer">
            <div class="signature">
                <div class="signature-line"></div>
                <strong>{$technicianName}</strong><br>
                Técnico Responsable
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Genera un PDF del certificado y devuelve el contenido
     */
    public function generateCertificatePdf(array $data): string
    {
        $html = $this->generateCertificateHtml($data);
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }

    /**
     * Genera un PDF y lo envía al navegador para descarga
     */
    public function downloadCertificatePdf(array $data, string $filename = 'certificado.pdf'): void
    {
        $html = $this->generateCertificateHtml($data);
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();
        $this->dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * Genera un PDF y lo muestra en el navegador
     */
    public function streamCertificatePdf(array $data, string $filename = 'certificado.pdf'): void
    {
        $html = $this->generateCertificateHtml($data);
        $this->dompdf->loadHtml($html);
        $this->dompdf->render();
        $this->dompdf->stream($filename, ['Attachment' => false]);
    }

    /**
     * Genera el HTML del ticket (versión compacta para impresión)
     */
    private function generateTicketHtml(array $data): string
    {
        $certificateNumber = htmlspecialchars($data['certificate_number'] ?? '');
        $calibrationDate = htmlspecialchars($data['calibration_date'] ?? '');
        $nextCalibrationDate = htmlspecialchars($data['next_calibration_date'] ?? '');
        $equipmentName = htmlspecialchars($data['equipment']['name'] ?? '');
        $equipmentSerial = htmlspecialchars($data['equipment']['serial_number'] ?? '');
        $clientName = htmlspecialchars($data['client']['name'] ?? '');

        return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Calibración</title>
    <style>
        @page {
            size: 80mm 120mm;
            margin: 5mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1a237e;
        }
        .header h1 {
            margin: 0;
            font-size: 14px;
            color: #1a237e;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 11px;
            font-weight: normal;
        }
        .cert-number {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin: 10px 0;
            padding: 5px;
            background-color: #f5f5f5;
            border: 1px solid #1a237e;
        }
        .info-row {
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #ddd;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            margin-top: 2px;
        }
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 8px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ELECTROTEC</h1>
        <p style="margin: 3px 0 0 0; font-size: 9px; color: #555;">RUC: 20602124305</p>
        <h2>Ticket de Calibración</h2>
    </div>
    
    <div class="cert-number">
        {$certificateNumber}
    </div>
    
    <div class="info-row">
        <div class="label">Cliente:</div>
        <div class="value">{$clientName}</div>
    </div>
    
    <div class="info-row">
        <div class="label">Equipo:</div>
        <div class="value">{$equipmentName}</div>
    </div>
    
    <div class="info-row">
        <div class="label">Serie:</div>
        <div class="value">{$equipmentSerial}</div>
    </div>
    
    <div class="info-row">
        <div class="label">Calibración:</div>
        <div class="value">{$calibrationDate}</div>
    </div>
    
    <div class="info-row">
        <div class="label">Próxima:</div>
        <div class="value">{$nextCalibrationDate}</div>
    </div>
    
    <div class="footer">
        www.electrotec.com.pe
    </div>
</body>
</html>
HTML;
    }

    /**
     * Genera un ticket (PDF compacto) para impresión
     */
    public function generateTicketPdf(array $data): string
    {
        $html = $this->generateTicketHtml($data);
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper([0, 0, 226.77, 340.16], 'portrait'); // 80mm x 120mm
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }

    /**
     * Genera un ticket y lo envía al navegador para descarga
     */
    public function downloadTicketPdf(array $data, string $filename = 'ticket.pdf'): void
    {
        $html = $this->generateTicketHtml($data);
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper([0, 0, 226.77, 340.16], 'portrait'); // 80mm x 120mm
        $this->dompdf->render();
        $this->dompdf->stream($filename, ['Attachment' => true]);
    }

    /**
     * Genera un ticket y lo muestra en el navegador
     */
    public function streamTicketPdf(array $data, string $filename = 'ticket.pdf'): void
    {
        $html = $this->generateTicketHtml($data);
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper([0, 0, 226.77, 340.16], 'portrait'); // 80mm x 120mm
        $this->dompdf->render();
        $this->dompdf->stream($filename, ['Attachment' => false]);
    }
}
