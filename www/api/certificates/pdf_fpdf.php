<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Shared\Auth\AuthMiddleware;
use App\Shared\Auth\JwtService;
use App\Shared\Http\JsonResponse;
use App\Shared\Pdf\FpdfCertificateRenderer;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;

$jwtService = new JwtService();
$auth = new AuthMiddleware($jwtService);
$user = $auth->requireAuth();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    JsonResponse::error('Método no permitido', 405);
    exit;
}

$id = (string)($_GET['id'] ?? '');
if ($id === '') { JsonResponse::error('ID de certificado requerido', 422); exit; }

try {
    $pdo = (new PdoFactory(new Config()))->create();
    // Traer datos con detalles
    $stmt = $pdo->prepare('SELECT c.*, cl.nombre AS client_name, e.brand AS equipment_brand, e.model AS equipment_model, e.serial_number AS equipment_serial_number FROM certificates c LEFT JOIN clients cl ON cl.id = c.client_id LEFT JOIN equipment e ON e.id = c.equipment_id WHERE c.id = :id LIMIT 1');
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

    $payload = [
        'certificate_number' => $cert['certificate_number'] ?? '',
        'calibration_date' => $cert['calibration_date'] ?? '',
        'next_calibration_date' => $cert['next_calibration_date'] ?? '',
        'client' => ['name' => $cert['client_name'] ?? ''],
        'equipment' => [
            'name' => trim(($cert['equipment_brand'] ?? '').' '.($cert['equipment_model'] ?? '')),
            'brand' => $cert['equipment_brand'] ?? '',
            'model' => $cert['equipment_model'] ?? '',
            'serial_number' => $cert['equipment_serial_number'] ?? '',
        ],
        'resultados' => $resultados,
        'resultados_distancia' => $dist,
        'lab_conditions' => $cond ? [
            'temperature' => $cond['temperatura_celsius'] ?? null,
            'humidity' => $cond['humedad_relativa_porc'] ?? null,
            'pressure' => $cond['presion_atm_mmhg'] ?? null,
        ] : null,
    ];

    $renderer = new FpdfCertificateRenderer();
    $disposition = ($_GET['action'] ?? 'download') === 'view' ? 'inline' : 'attachment';
    $renderer->output($payload, $disposition);
} catch (Throwable $e) {
    JsonResponse::error('Error generando PDF: '.$e->getMessage(), 500);
}
