<?php

require_once __DIR__ . '/../../bootstrap.php';

use App\Shared\Pdf\PdfGenerator;
use App\Shared\Auth\AuthMiddleware;
use App\Shared\Auth\JwtService;
use App\Shared\Http\JsonResponse;

// Proteger ruta con autenticación
$jwtService = new JwtService();
$authMiddleware = new AuthMiddleware($jwtService);
$user = $authMiddleware->requireAuth();

// Solo permitir GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo JsonResponse::error('Método no permitido', 405);
    exit;
}

// Obtener ID del certificado
$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo JsonResponse::error('ID de certificado requerido', 400);
    exit;
}

try {
    global $pdo;
    
    // Obtener certificado con datos relacionados
    $stmt = $pdo->prepare('
        SELECT 
            c.*,
            cl.name AS client_name,
            e.name AS equipment_name,
            e.brand AS equipment_brand,
            e.model AS equipment_model,
            e.serial_number AS equipment_serial_number
        FROM certificates c
        LEFT JOIN clients cl ON cl.id = c.client_id
        LEFT JOIN equipment e ON e.id = c.equipment_id
        WHERE c.id = ?
    ');
    $stmt->execute([$id]);
    $certificate = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$certificate) {
        http_response_code(404);
        echo JsonResponse::error('Certificado no encontrado', 404);
        exit;
    }
    
    // Obtener técnico (de users)
    $technician = null;
    if (!empty($certificate['technician_id'])) {
        $techStmt = $pdo->prepare('SELECT id, nombre as full_name FROM users WHERE id = ?');
        $techStmt->execute([$certificate['technician_id']]);
        $technician = $techStmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Preparar datos para el PDF
    $pdfData = [
        'certificate_number' => $certificate['certificate_number'],
        'calibration_date' => $certificate['calibration_date'],
        'next_calibration_date' => $certificate['next_calibration_date'],
        'results' => json_decode($certificate['results'] ?? '{}', true),
        'lab_conditions' => json_decode($certificate['lab_conditions'] ?? '{}', true),
        'equipment' => [
            'name' => $certificate['equipment_name'] ?? 'N/A',
            'brand' => $certificate['equipment_brand'] ?? 'N/A',
            'model' => $certificate['equipment_model'] ?? 'N/A',
            'serial_number' => $certificate['equipment_serial_number'] ?? 'N/A',
        ],
        'client' => [
            'name' => $certificate['client_name'] ?? 'N/A'
        ],
        'technician' => [
            'full_name' => $technician['full_name'] ?? 'N/A'
        ]
    ];
    
    // Generar PDF
    $pdfGenerator = new PdfGenerator();
    $filename = 'certificado_' . $certificate['certificate_number'] . '.pdf';
    
    // Determinar si es descarga o visualización
    $action = $_GET['action'] ?? 'download';
    
    if ($action === 'view') {
        $pdfGenerator->streamCertificatePdf($pdfData, $filename);
    } else {
        $pdfGenerator->downloadCertificatePdf($pdfData, $filename);
    }
    
} catch (Exception $e) {
    error_log('Error generando PDF: ' . $e->getMessage());
    http_response_code(500);
    echo JsonResponse::error('Error al generar el PDF: ' . $e->getMessage(), 500);
}
