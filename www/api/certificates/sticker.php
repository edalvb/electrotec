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
$forceFormat = isset($_GET['format']) ? strtolower((string)$_GET['format']) : '';

try {
    $pdo = (new PdoFactory(new Config()))->create();
    $stmt = $pdo->prepare('SELECT c.*, cl.nombre AS client_name FROM certificates c LEFT JOIN clients cl ON cl.id = c.client_id WHERE c.id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $cert = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cert) { JsonResponse::error('Certificado no encontrado', 404); exit; }

    // Obtener técnico (si existe calibrator_id)
    $technician = null;
    if (!empty($cert['calibrator_id'])) {
        $stmtT = $pdo->prepare('SELECT id, nombre_completo, path_firma, firma_base64 FROM tecnico WHERE id = :id LIMIT 1');
        $stmtT->execute([':id' => $cert['calibrator_id']]);
        $technician = $stmtT->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Construir URL para QR (detectar https y puertos por defecto)
    $envHost = $_ENV['APP_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $envPort = (string)($_ENV['APP_PORT'] ?? '');
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) ||
        (isset($_ENV['APP_SCHEME']) && strtolower((string)$_ENV['APP_SCHEME']) === 'https')
    );
    $scheme = $isHttps ? 'https' : 'http';
    // Si HTTP y puerto 80, u HTTPS y puerto 443, omitir puerto
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
    // Si HTTP_HOST ya incluye puerto, no dupliques
    if (strpos($host, ':') !== false) { $port = ''; }
    $qrUrl = sprintf('%s://%s%s/api/certificates/pdf_fpdf.php?id=%s', $scheme, $host, $port, $id);

    // Por defecto servir SVG; solo generar PNG si format=png y GD está disponible
    if ($forceFormat !== 'png' || !function_exists('imagecreatetruecolor')) {
        // Dimensiones del sticker: 48mm x 18.5mm a 300 DPI (reducido para optimizar impresión)
        // 48mm = 567px, 18.5mm = 218px (a 300 DPI: 1mm = 11.811px)
        $w = 567; $h = 218;
        
        // QR ajustado proporcionalmente
        $qrSize = 165;
        $qrPadLeft = 20;
        $qrY = ($h - $qrSize) / 2; // Centrado vertical
        
        // Área de texto a la derecha del QR
        $textAreaX = $qrPadLeft + $qrSize + 20; // 20px de margen
        $textAreaWidth = $w - $textAreaX - 20; // 20px margen derecho
        
        // Calcular altura total del contenido de texto para centrarlo verticalmente
        $lineHeight = 23;
        $totalTextHeight = $lineHeight * 6; // 6 líneas de texto (ELECTROTEC en línea separada)
        $textStartY = ($h - $totalTextHeight) / 2 + 14; // +14 para baseline del texto
        
        $qrRemote = 'https://api.qrserver.com/v1/create-qr-code/?size='.$qrSize.'x'.$qrSize.'&data='.rawurlencode($qrUrl);
        $qrRemoteEsc = htmlspecialchars($qrRemote, ENT_QUOTES, 'UTF-8');
        $certNum = htmlspecialchars((string)($cert['certificate_number'] ?? ''), ENT_QUOTES, 'UTF-8');
        $clientName = htmlspecialchars((string)($cert['client_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $cal = htmlspecialchars((string)($cert['calibration_date'] ?? ''), ENT_QUOTES, 'UTF-8');
        $ncal = htmlspecialchars((string)($cert['next_calibration_date'] ?? ''), ENT_QUOTES, 'UTF-8');
        
        // Calcular posiciones Y para cada línea de texto
        $y1 = $textStartY;
        $y2 = $y1 + $lineHeight;
        $y3 = $y2 + $lineHeight;
        $y4 = $y3 + $lineHeight;
        $y5 = $y4 + $lineHeight;
        $y6 = $y5 + $lineHeight;
        
        // Construir SVG rediseñado sin footer
        $svg = <<<SVG
    <?xml version="1.0" encoding="UTF-8"?>
    <svg xmlns="http://www.w3.org/2000/svg" width="{$w}" height="{$h}" viewBox="0 0 {$w} {$h}">
      <!-- Fondo blanco con borde reducido -->
      <rect x="0" y="0" width="{$w}" height="{$h}" fill="#fff" stroke="#000" stroke-width="1"/>
      
      <!-- QR Code centrado verticalmente a la izquierda -->
      <image href="{$qrRemoteEsc}" x="{$qrPadLeft}" y="{$qrY}" width="{$qrSize}" height="{$qrSize}" />
      
      <!-- Textos centrados verticalmente a la derecha del QR -->
      <text x="{$textAreaX}" y="{$y1}" font-size="22" font-weight="bold" font-family="Arial" fill="#1c3773">ELECTROTEC</text>
      <text x="{$textAreaX}" y="{$y2}" font-size="22" font-weight="bold" font-family="Arial" fill="#1c3773">CONSULTING S.A.C.</text>
      <text x="{$textAreaX}" y="{$y3}" font-size="18" font-weight="bold" font-family="Arial" fill="#1c3773">RUC: 20602124305</text>
      <text x="{$textAreaX}" y="{$y4}" font-size="18" font-weight="bold" font-family="Arial" fill="#000">CERTIFICADO N° {$certNum}</text>
      <text x="{$textAreaX}" y="{$y5}" font-size="18" font-weight="bold" font-family="Arial" fill="#000">CALIBRACIÓN: {$cal}</text>
      <text x="{$textAreaX}" y="{$y6}" font-size="18" font-weight="bold" font-family="Arial" fill="#000">PRÓXIMA: {$ncal}</text>
    </svg>
    SVG;
        header('Content-Type: image/svg+xml');
        header('Content-Disposition: inline; filename="sticker_'.$certNum.'.svg"');
        header('Cache-Control: private, max-age=60');
        echo $svg; exit;
    }

    // Si se fuerza PNG y GD está disponible, generar PNG (caché en disco)
    $outDir = __DIR__ . '/stickers';
    if (!is_dir($outDir)) { @mkdir($outDir, 0775, true); }
    $filename = 'sticker_' . ($cert['certificate_number'] ?? 'cert') . '.png';
    $path = $outDir . '/' . $filename;
    $force = isset($_GET['rebuild']) && $_GET['rebuild'] === '1';
    if ($force || !is_file($path)) {
        $gen = new StickerGenerator();
        $gen->generate([
            'certificate_number' => (string)($cert['certificate_number'] ?? ''),
            'client_name' => (string)($cert['client_name'] ?? ''),
            'calibration_date' => (string)($cert['calibration_date'] ?? ''),
            'next_calibration_date' => (string)($cert['next_calibration_date'] ?? ''),
            'qr_url' => $qrUrl,
            // Datos de firma/nombre del técnico para PNG
            'technician_name' => (string)($technician['nombre_completo'] ?? ''),
            'technician_firma_base64' => (string)($technician['firma_base64'] ?? ''),
            'technician_path_firma' => (string)($technician['path_firma'] ?? ''),
        ], $path);
    }

    header('Content-Type: image/png');
    header('Content-Disposition: inline; filename="'.$filename.'"');
    header('Cache-Control: private, max-age=60');
    readfile($path);
} catch (Throwable $e) {
    // Último recurso: SVG fallback simple (sin QR)
    $w = 567; $h = 218; $pad = 12; $certNum = htmlspecialchars((string)($cert['certificate_number'] ?? ''), ENT_QUOTES, 'UTF-8');
    header('Content-Type: image/svg+xml');
    header('Content-Disposition: inline; filename="sticker_'.$certNum.'_fallback.svg"');
    echo '<svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h.'" viewBox="0 0 '.$w.' '.$h.'">'
        .'<rect x="0" y="0" width="'.$w.'" height="'.$h.'" fill="#fff" stroke="#000"/>'
        .'<text x="'.($pad).'" y="'.($pad+24).'" font-size="16" font-family="Arial" fill="#c00">Sticker no disponible</text>'
        .'<text x="'.($pad).'" y="'.($pad+48).'" font-size="12" font-family="Arial" fill="#000">Instale/active PHP-GD para habilitar PNG</text>'
        .'</svg>';
}
