<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Shared\Http\JsonResponse;
use App\Shared\Pdf\FpdfCertificateRenderer;
use App\Shared\Pdf\StickerGenerator;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;

// Endpoint público: no requiere autenticación

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    JsonResponse::error('Método no permitido', 405);
    exit;
}

$id = (string)($_GET['id'] ?? '');
if ($id === '') { JsonResponse::error('ID de certificado requerido', 422); exit; }

try {
    $pdo = (new PdoFactory(new Config()))->create();
    // Traer datos con detalles (incluye tipo de equipo)
    $stmt = $pdo->prepare('SELECT c.*, cl.nombre AS client_name, e.brand AS equipment_brand, e.model AS equipment_model, e.serial_number AS equipment_serial_number, et.name AS equipment_type_name FROM certificates c LEFT JOIN clients cl ON cl.id = c.client_id LEFT JOIN equipment e ON e.id = c.equipment_id LEFT JOIN equipment_types et ON et.id = e.equipment_type_id WHERE c.id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $cert = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cert) { JsonResponse::error('Certificado no encontrado', 404); exit; }

    // Condiciones ambiente
    $stmtC = $pdo->prepare('SELECT * FROM condiciones_ambientales WHERE id_certificado = :id LIMIT 1');
    $stmtC->execute([':id' => $id]);
    $cond = $stmtC->fetch(PDO::FETCH_ASSOC) ?: null;

    // Resultados
    $stmtR = $pdo->prepare('SELECT * FROM resultados WHERE id_certificado = :id ORDER BY id ASC');
    $stmtR->execute([':id' => $id]);
    $resultados = $stmtR->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $stmtD = $pdo->prepare('SELECT * FROM resultados_distancia WHERE id_certificado = :id ORDER BY id_resultado ASC');
    $stmtD->execute([':id' => $id]);
    $dist = $stmtD->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Traer técnico (calibrador)
    $technician = null;
    if (!empty($cert['calibrator_id'])) {
        $stmtT = $pdo->prepare('SELECT id, nombre_completo, cargo, path_firma, firma_base64 FROM tecnico WHERE id = :id LIMIT 1');
        $stmtT->execute([':id' => (int)$cert['calibrator_id']]);
        $technician = $stmtT->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    $resultsJson = [];
    if (isset($cert['results']) && is_string($cert['results']) && $cert['results'] !== '') {
        $decoded = json_decode($cert['results'], true);
        if (is_array($decoded)) { $resultsJson = $decoded; }
    }

    // Construir URL pública del PDF (para QR): detectar https y puertos por defecto
    $envHost = $_ENV['APP_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $envPort = (string)($_ENV['APP_PORT'] ?? '');
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ||
        (isset($_ENV['APP_SCHEME']) && strtolower((string)$_ENV['APP_SCHEME']) === 'https')
    );
    $scheme = $isHttps ? 'https' : 'http';
    $port = '';
    if ($envPort !== '') {
        $p = (int)$envPort;
        if (!($scheme === 'http' && $p === 80) && !($scheme === 'https' && $p === 443)) {
            $port = ':' . $p;
        }
    } else if (isset($_SERVER['SERVER_PORT'])) {
        $p = (int)$_SERVER['SERVER_PORT'];
        if (!($scheme === 'http' && $p === 80) && !($scheme === 'https' && $p === 443)) {
            $port = ':' . $p;
        }
    }
    $host = $envHost;
    if (strpos($host, ':') !== false) { $port = ''; }
    $publicPdfUrl = sprintf('%s://%s%s/api/certificates/pdf_fpdf.php?id=%s', $scheme, $host, $port, $id);

    $payload = [
        'certificate_number' => $cert['certificate_number'] ?? '',
        'calibration_date' => $cert['calibration_date'] ?? '',
        'next_calibration_date' => $cert['next_calibration_date'] ?? '',
        'client' => ['name' => $cert['client_name'] ?? ''],
        'equipment' => [
            // Mostrar en EQUIPO únicamente el tipo
            'type' => $cert['equipment_type_name'] ?? '',
            'brand' => $cert['equipment_brand'] ?? '',
            'model' => $cert['equipment_model'] ?? '',
            'serial_number' => $cert['equipment_serial_number'] ?? '',
        ],
        'resultados' => $resultados,
        'resultados_distancia' => $dist,
        'results_json' => $resultsJson,
        'lab_conditions' => $cond ? [
            'temperature' => $cond['temperatura_celsius'] ?? null,
            'humidity' => $cond['humedad_relativa_porc'] ?? null,
            'pressure' => $cond['presion_atm_mmhg'] ?? null,
        ] : null,
        'technician' => $technician,
        // Datos para sticker
        'sticker' => [
            'public_pdf_url' => $publicPdfUrl,
        ],
    ];

    $renderer = new FpdfCertificateRenderer();
    $disposition = ($_GET['action'] ?? 'download') === 'view' ? 'inline' : 'attachment';
    // Generar sticker automáticamente antes de emitir PDF
    try {
        $stickerGen = new StickerGenerator();
        $outDir = __DIR__ . '/stickers';
        if (!is_dir($outDir)) { @mkdir($outDir, 0775, true); }
        $stickerPath = $outDir . '/sticker_' . ($cert['certificate_number'] ?? 'cert') . '.png';
        $stickerData = [
            'certificate_number' => (string)($cert['certificate_number'] ?? ''),
            'client_name' => (string)($cert['client_name'] ?? ''),
            'calibration_date' => (string)($cert['calibration_date'] ?? ''),
            'next_calibration_date' => (string)($cert['next_calibration_date'] ?? ''),
            'qr_url' => $publicPdfUrl,
        ];
        $stickerGen->generate($stickerData, $stickerPath);
    } catch (\Throwable $e2) {
        // No bloquear la descarga del PDF si falla el sticker
        error_log('Sticker generation failed: ' . $e2->getMessage());
    }

    $renderer->output($payload, $disposition);
} catch (Throwable $e) {
    JsonResponse::error('Error generando PDF: '.$e->getMessage(), 500);
}
