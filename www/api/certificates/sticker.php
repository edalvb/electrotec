<?php
require_once __DIR__ . '/../../bootstrap.php';

use App\Shared\Pdf\StickerGenerator;
use App\Infrastructure\Database\PdoFactory;
use App\Shared\Config\Config;
use App\Shared\Http\JsonResponse;

// Público: no requiere autenticación
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    JsonResponse::error('Método no permitido', 405); exit;
}

$id = (string)($_GET['id'] ?? '');
if ($id === '') { JsonResponse::error('ID requerido', 422); exit; }

try {
    $pdo = (new PdoFactory(new Config()))->create();
    $stmt = $pdo->prepare('SELECT c.*, cl.nombre AS client_name FROM certificates c LEFT JOIN clients cl ON cl.id = c.client_id WHERE c.id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $cert = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cert) { JsonResponse::error('Certificado no encontrado', 404); exit; }

    $outDir = __DIR__ . '/stickers';
    if (!is_dir($outDir)) { @mkdir($outDir, 0775, true); }
    $filename = 'sticker_' . ($cert['certificate_number'] ?? 'cert') . '.png';
    $path = $outDir . '/' . $filename;
    if (!is_file($path)) {
        $gen = new StickerGenerator();
        $gen->generate([
            'certificate_number' => (string)($cert['certificate_number'] ?? ''),
            'client_name' => (string)($cert['client_name'] ?? ''),
            'calibration_date' => (string)($cert['calibration_date'] ?? ''),
            'next_calibration_date' => (string)($cert['next_calibration_date'] ?? ''),
            'qr_url' => sprintf('http://%s:%s/api/certificates/pdf_fpdf.php?id=%s', $_ENV['APP_HOST'] ?? 'localhost', $_ENV['APP_PORT'] ?? '8080', $id),
        ], $path);
    }

    header('Content-Type: image/png');
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Cache-Control: private, max-age=60');
    readfile($path);
} catch (Throwable $e) {
    JsonResponse::error('Error generando sticker: '.$e->getMessage(), 500);
}
