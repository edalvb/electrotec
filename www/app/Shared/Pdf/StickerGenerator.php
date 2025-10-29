<?php

namespace App\Shared\Pdf;

/**
 * Generador de sticker PNG (5cm x 2.5cm) con QR y datos básicos.
 * Requiere extensión GD habilitada. Genera PNG en la ruta indicada.
 */
class StickerGenerator
{
    /**
    * @param array{
    *   certificate_number:string,
    *   client_name:string,
    *   calibration_date:string,
    *   next_calibration_date:string,
    *   qr_url:string,
    *   technician_name?:string,
    *   technician_firma_base64?:string,
    *   technician_path_firma?:string
    * } $data
     * @param string $outputPath Ruta del archivo PNG a escribir
     */
    public function generate(array $data, string $outputPath): void
    {
        // 300 DPI -> 5 cm (1.9685 in) => ~590 px; 2.5 cm (~295 px)
        $width = 590; $height = 295;
        $im = imagecreatetruecolor($width, $height);
        if (!$im) { throw new \RuntimeException('GD no disponible'); }

        // Conversión segura a monocódigo (para fallback con imagestring)
        $to1252 = static function (string $s): string {
            if ($s === '') return '';
            return mb_convert_encoding($s, 'Windows-1252', 'UTF-8');
        };

        // Intentar localizar una fuente TrueType para soportar UTF-8 en textos del sticker
    $font = $this->resolveFontPath();
    $hasTtf = function_exists('imagettftext') && is_string($font) && $font !== '' && @is_file($font);

        // Colores
        $white = imagecolorallocate($im, 255, 255, 255);
        $black = imagecolorallocate($im, 0, 0, 0);
        $blue = imagecolorallocate($im, 28, 55, 115);
        $gray = imagecolorallocate($im, 230, 230, 230);
        imagefilledrectangle($im, 0, 0, $width-1, $height-1, $white);
        imagerectangle($im, 0, 0, $width-1, $height-1, $black);

        // Zona superior con logo (placeholder) y texto
        $padding = 12; $line = 20;
        // Marco del QR (a la izquierda)
        $qrBoxSize = 120;
        $qrX = $padding; $qrY = $padding + 10;
        imagerectangle($im, $qrX-2, $qrY-2, $qrX + $qrBoxSize + 2, $qrY + $qrBoxSize + 2, $black);

    // Generar QR (usar librería si está disponible, sino fallback)
    $qrImg = $this->renderQr($data['qr_url'], $qrBoxSize, $black, $white);
        if ($qrImg) { imagecopy($im, $qrImg, $qrX, $qrY, 0, 0, $qrBoxSize, $qrBoxSize); imagedestroy($qrImg); }

        // Títulos a la derecha del QR
        $tx = $qrX + $qrBoxSize + 12; $ty = $padding + 6;
        if ($hasTtf) {
            // Cabecera
            imagettftext($im, 16, 0, $tx, $ty + 14, $blue, $font, 'ELECTROTEC CONSULTING S.A.C.');
            $ty += $line;
            imagettftext($im, 13, 0, $tx, $ty + 12, $black, $font, 'Certificado N° '.($data['certificate_number']));
            $ty += $line;
            imagettftext($im, 11, 0, $tx, $ty + 11, $black, $font, 'Cliente: '.$this->truncate($data['client_name'], 32));
            $ty += $line;
            imagettftext($im, 11, 0, $tx, $ty + 11, $black, $font, 'Calibración: '.$this->fmtDate($data['calibration_date']));
            $ty += $line;
            imagettftext($im, 11, 0, $tx, $ty + 11, $black, $font, 'Próxima: '.$this->fmtDate($data['next_calibration_date']));
        } else {
            imagestring($im, 5, $tx, $ty, $to1252('ELECTROTEC CONSULTING S.A.C.'), $blue); $ty += $line;
            imagestring($im, 4, $tx, $ty, $to1252('Certificado N° '. $data['certificate_number']), $black); $ty += $line;
            imagestring($im, 3, $tx, $ty, $to1252('Cliente: '. $this->truncate($data['client_name'], 32)), $black); $ty += $line;
            imagestring($im, 3, $tx, $ty, $to1252('Calibración: '. $this->fmtDate($data['calibration_date'])), $black); $ty += $line;
            imagestring($im, 3, $tx, $ty, $to1252('Próxima: '. $this->fmtDate($data['next_calibration_date'])), $black);
        }

        // Separador
        imageline($im, $padding, $height - 120, $width - $padding, $height - 120, $black);

        // Pie: número a la izquierda y caja de firma a la derecha
        if ($hasTtf) {
            imagettftext($im, 14, 0, $padding, $height - 110 + 13, $black, $font, 'N° '.$data['certificate_number']);
        } else {
            imagestring($im, 4, $padding, $height - 110, $to1252('N° '. $data['certificate_number']), $black);
        }
        // Recuadro de firma (imagen si está disponible, sino nombre del técnico)
        $signW = 260; $signH = 70; $signX = $width - $padding - $signW; $signY = $height - 110;
        imagerectangle($im, $signX, $signY, $signX + $signW, $signY + $signH, $black);
        $sigAreaX = $signX + 8; $sigAreaY = $signY + 6; $sigAreaW = $signW - 16; $sigAreaH = $signH - 12;
        $techName = trim((string)($data['technician_name'] ?? ''));
        $sigImg = $this->loadSignatureImage(
            (string)($data['technician_firma_base64'] ?? ''),
            (string)($data['technician_path_firma'] ?? '')
        );
        if ($sigImg) {
            $srcW = imagesx($sigImg); $srcH = imagesy($sigImg);
            if ($srcW > 0 && $srcH > 0) {
                $scale = min($sigAreaW / $srcW, $sigAreaH / $srcH);
                $dstW = (int)floor($srcW * $scale);
                $dstH = (int)floor($srcH * $scale);
                $dstX = (int)floor($sigAreaX + ($sigAreaW - $dstW) / 2);
                $dstY = (int)floor($sigAreaY + ($sigAreaH - $dstH) / 2);
                $resized = imagescale($sigImg, $dstW, $dstH, IMG_BILINEAR_FIXED);
                if ($resized) {
                    imagecopy($im, $resized, $dstX, $dstY, 0, 0, $dstW, $dstH);
                    imagedestroy($resized);
                } else {
                    imagecopyresampled($im, $sigImg, $dstX, $dstY, 0, 0, $dstW, $dstH, $srcW, $srcH);
                }
            }
            imagedestroy($sigImg);
        } elseif ($techName !== '') {
            // Escribir nombre del técnico dentro del recuadro
            if ($hasTtf) {
                imagettftext($im, 10, 0, $sigAreaX + 6, $sigAreaY + (int)floor($sigAreaH/2) + 4, $black, $font, $this->truncate($techName, 34));
            } else {
                imagestring($im, 3, $sigAreaX + 6, $sigAreaY + (int)floor($sigAreaH/2) - 6, $to1252($this->truncate($techName, 34)), $black);
            }
        } else {
            // Sin firma ni nombre: indicación suave
            if ($hasTtf) {
                imagettftext($im, 9, 0, $sigAreaX + 6, $sigAreaY + (int)floor($sigAreaH/2) + 3, $black, $font, '(sin firma)');
            } else {
                imagestring($im, 2, $sigAreaX + 6, $sigAreaY + (int)floor($sigAreaH/2) - 6, $to1252('(sin firma)'), $black);
            }
        }
        // Leyenda
        if ($hasTtf) {
            imagettftext($im, 12, 0, $width - 210, $height - 24 + 10, $blue, $font, 'SERVICIO TÉCNICO');
        } else {
            imagestring($im, 3, $width - 210, $height - 24, $to1252('SERVICIO TÉCNICO'), $blue);
        }

        imagepng($im, $outputPath);
        imagedestroy($im);
    }

    private function truncate(string $s, int $max): string { return mb_strlen($s) > $max ? mb_substr($s, 0, $max-1).'…' : $s; }

    private function fmtDate(string $iso): string
    {
        $ts = @strtotime($iso); if (!$ts) return $iso;
        return date('d/m/Y', $ts);
    }

    private function renderQr(string $text, int $size, int $fg, int $bg)
    {
        // endroid/qr-code si está instalado
        if (class_exists('Endroid\\QrCode\\QrCode')) {
            try {
                $cls = 'Endroid\\QrCode\\QrCode';
                $qr = new $cls($text);
                $qr->setSize($size);
                $qr->setMargin(0);
                $arr = $qr->writeString();
                $img = @imagecreatefromstring($arr);
                if ($img) { return imagescale($img, $size, $size); }
            } catch (\Throwable $e) {}
        }
        // Intentar QR remoto (PNG) si no está la librería
        $remote = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . rawurlencode($text);
        try {
            $png = @file_get_contents($remote);
            if ($png !== false) {
                $img = @imagecreatefromstring($png);
                if ($img) { return imagescale($img, $size, $size); }
            }
        } catch (\Throwable $e) {}
        // Fallback simple cuadriculado (no estándar)
        $img = imagecreatetruecolor($size, $size);
        if (!$img) return null;
        imagefilledrectangle($img, 0, 0, $size-1, $size-1, $bg);
        $hash = md5($text);
        $cells = 25; // cuadricula 25x25
        $cellSize = (int) floor($size / $cells);
        for ($y=0; $y<$cells; $y++) {
            for ($x=0; $x<$cells; $x++) {
                $i = ($y*$cells + $x) % strlen($hash);
                $hex = hexdec($hash[$i]);
                if ($hex % 2 === 1) {
                    imagefilledrectangle($img, $x*$cellSize, $y*$cellSize, ($x+1)*$cellSize-1, ($y+1)*$cellSize-1, $fg);
                }
            }
        }
        return $img;
    }

    private function loadSignatureImage(string $firmaBase64, string $path)
    {
        // 1) Intentar data URL base64: data:image/png;base64,....
        $firmaBase64 = trim($firmaBase64);
        if ($firmaBase64 !== '') {
            $comma = strpos($firmaBase64, ',');
            if ($comma !== false) {
                $raw = substr($firmaBase64, $comma + 1);
                $bin = base64_decode($raw, true);
                if ($bin !== false) {
                    $img = @imagecreatefromstring($bin);
                    if ($img) { return $img; }
                }
            }
        }
        // 2) Intentar ruta de archivo
        $path = trim($path);
        if ($path !== '') {
            $candidates = [];
            $candidates[] = $path; // absoluta o relativa desde CWD
            $baseWww = dirname(__DIR__, 3); // .../www
            $candidates[] = $baseWww . DIRECTORY_SEPARATOR . ltrim($path, '/\\');
            foreach ($candidates as $p) {
                if (@is_file($p) && @filesize($p) > 0) {
                    $bin = @file_get_contents($p);
                    if ($bin !== false) {
                        $img = @imagecreatefromstring($bin);
                        if ($img) { return $img; }
                    }
                }
            }
        }
        return null;
    }

    private function resolveFontPath(): ?string
    {
        $candidates = [];
        // Dentro del proyecto (si agregas una fuente en assets/fonts)
        $base = dirname(__DIR__, 3); // .../www
        $candidates[] = $base . '/assets/fonts/DejaVuSans.ttf';
        $candidates[] = $base . '/assets/fonts/FreeSans.ttf';
        // Rutas comunes en servidores Linux
        $candidates[] = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
        $candidates[] = '/usr/share/fonts/TTF/DejaVuSans.ttf';
        $candidates[] = '/usr/share/fonts/dejavu/DejaVuSans.ttf';
        $candidates[] = '/usr/share/fonts/truetype/freefont/FreeSans.ttf';
        foreach ($candidates as $p) {
            if (@is_file($p)) { return $p; }
        }
        return null;
    }
}
