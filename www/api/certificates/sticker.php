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

    // Construir URL para QR
    $qrUrl = sprintf('http://%s:%s/api/certificates/pdf_fpdf.php?id=%s', $_ENV['APP_HOST'] ?? 'localhost', $_ENV['APP_PORT'] ?? '8080', $id);

    // Fallback si GD no está disponible: servir SVG con QR remoto
    if (!function_exists('imagecreatetruecolor')) {
        $w = 590; $h = 295; $qrSize = 120; $pad = 12; $tx = $pad + $qrSize + 12; $line = 20; $ty = $pad + 6;
        $y1 = $ty; $y2 = $ty + $line; $y3 = $ty + ($line*2); $y4 = $ty + ($line*3);
        $sepY = $h - 120; $sepX2 = $w - $pad;
        $signW = 260; $signH = 70; $signX = $w - $pad - $signW; $signY = $h - 110;
        $signTextX = $signX + 14; $signTextY1 = $signY + 24; $signTextY2 = $signY + 44;
    $xServicio = $w - 210; $yServicio = $h - 24; $yNum = $h - 90;
    $qrRemote = 'https://api.qrserver.com/v1/create-qr-code/?size='.$qrSize.'x'.$qrSize.'&data='.rawurlencode($qrUrl);
    $qrRemoteEsc = htmlspecialchars($qrRemote, ENT_QUOTES, 'UTF-8');
        $certNum = htmlspecialchars((string)($cert['certificate_number'] ?? ''), ENT_QUOTES, 'UTF-8');
        $clientName = htmlspecialchars((string)($cert['client_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $cal = htmlspecialchars((string)($cert['calibration_date'] ?? ''), ENT_QUOTES, 'UTF-8');
        $ncal = htmlspecialchars((string)($cert['next_calibration_date'] ?? ''), ENT_QUOTES, 'UTF-8');
        $svg = <<<SVG
    <?xml version="1.0" encoding="UTF-8"?>
    <svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}">
      <rect x="0" y="0" width="{$w}" height="{$h}" fill="#fff" stroke="#000"/>
    <image href="{$qrRemoteEsc}" x="{$pad}" y="{$pad}" width="{$qrSize}" height="{$qrSize}" />
        <text x="{$tx}" y="{$y1}" font-size="18" font-family="Arial" fill="#1c3773">ELECTROTEC CONSULTING S.A.C.</text>
        <text x="{$tx}" y="{$y2}" font-size="16" font-family="Arial" fill="#000">Certificado N° {$certNum}</text>
        <text x="{$tx}" y="{$y3}" font-size="14" font-family="Arial" fill="#000">Cliente: {$clientName}</text>
        <text x="{$tx}" y="{$y4}" font-size="14" font-family="Arial" fill="#000">Calibración: {$cal}</text>
        <text x="{$tx}" y="{$y4}" dy="{$line}" font-size="14" font-family="Arial" fill="#000">Próxima: {$ncal}</text>
        <line x1="{$pad}" y1="{$sepY}" x2="{$sepX2}" y2="{$sepY}" stroke="#000" />
    <text x="{$pad}" y="{$yNum}" font-size="16" font-family="Arial" fill="#000">N° {$certNum}</text>
        <rect x="{$signX}" y="{$signY}" width="{$signW}" height="{$signH}" fill="none" stroke="#000" />
        <text x="{$signTextX}" y="{$signTextY1}" font-size="12" font-family="Arial" fill="#000">(Firma del técnico aquí)</text>
        <text x="{$signTextX}" y="{$signTextY2}" font-size="10" font-family="Arial" fill="#000">o bien, el texto: Nombre del Técnico</text>
        <text x="{$xServicio}" y="{$yServicio}" font-size="14" font-family="Arial" fill="#1c3773">SERVICIO TÉCNICO</text>
    </svg>
    SVG;
        header('Content-Type: image/svg+xml');
        header('Content-Disposition: inline; filename="sticker_'.$certNum.'.svg"');
        header('Cache-Control: private, max-age=60');
        echo $svg; exit;
    }

    // Si GD está disponible, generar PNG (caché en disco)
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
            'qr_url' => $qrUrl,
        ], $path);
    }

    header('Content-Type: image/png');
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Cache-Control: private, max-age=60');
    readfile($path);
} catch (Throwable $e) {
    // Último recurso: SVG fallback simple (sin QR)
    $w = 590; $h = 295; $pad = 12; $certNum = htmlspecialchars((string)($cert['certificate_number'] ?? ''), ENT_QUOTES, 'UTF-8');
    header('Content-Type: image/svg+xml');
    header('Content-Disposition: inline; filename="sticker_'.$certNum.'_fallback.svg"');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h.'" viewBox="0 0 '.$w.' '.$h.'">'
        .'<rect x="0" y="0" width="'.$w.'" height="'.$h.'" fill="#fff" stroke="#000"/>'
        .'<text x="'.($pad).'" y="'.($pad+24).'" font-size="16" font-family="Arial" fill="#c00">Sticker no disponible</text>'
        .'<text x="'.($pad).'" y="'.($pad+48).'" font-size="12" font-family="Arial" fill="#000">Instale/active PHP-GD para habilitar PNG</text>'
        .'</svg>';
}
