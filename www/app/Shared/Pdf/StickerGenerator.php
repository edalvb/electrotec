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
        // Dimensiones del sticker: 5 cm x 2 cm a 300 DPI (reducido para optimizar impresión)
        $width = 590; $height = 236;
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
        
        // Fondo blanco con borde
        imagefilledrectangle($im, 0, 0, $width-1, $height-1, $white);
        imagerectangle($im, 0, 0, $width-1, $height-1, $black);
        imagerectangle($im, 1, 1, $width-2, $height-2, $black);

        // QR más grande y centrado verticalmente
        $qrSize = 180;
        $qrPadLeft = 30;
        $qrY = (int)(($height - $qrSize) / 2); // Centrado vertical
        
        // Generar QR (usar librería si está disponible, sino fallback)
        $qrImg = $this->renderQr($data['qr_url'], $qrSize, $black, $white);
        if ($qrImg) { 
            imagecopy($im, $qrImg, $qrPadLeft, $qrY, 0, 0, $qrSize, $qrSize); 
            imagedestroy($qrImg); 
        }

        // Área de texto a la derecha del QR
        $textAreaX = $qrPadLeft + $qrSize + 30; // 30px de margen
        
        // Calcular altura total del contenido de texto para centrarlo verticalmente
        $lineHeight = 24;
        $totalTextHeight = $lineHeight * 5; // 5 líneas de texto
        $textStartY = (int)(($height - $totalTextHeight) / 2);
        
        // Renderizar textos centrados verticalmente
        if ($hasTtf) {
            $ty = $textStartY;
            imagettftext($im, 18, 0, $textAreaX, $ty + 16, $blue, $font, 'ELECTROTEC CONSULTING S.A.C.');
            $ty += $lineHeight;
            imagettftext($im, 16, 0, $textAreaX, $ty + 14, $blue, $font, 'RUC: 20602124305');
            $ty += $lineHeight;
            imagettftext($im, 16, 0, $textAreaX, $ty + 14, $black, $font, 'CERTIFICADO N° '.($data['certificate_number']));
            $ty += $lineHeight;
            imagettftext($im, 16, 0, $textAreaX, $ty + 14, $black, $font, 'CALIBRACIÓN: '.$this->fmtDate($data['calibration_date']));
            $ty += $lineHeight;
            imagettftext($im, 16, 0, $textAreaX, $ty + 14, $black, $font, 'PRÓXIMA: '.$this->fmtDate($data['next_calibration_date']));
        } else {
            $ty = $textStartY;
            imagestring($im, 5, $textAreaX, $ty, $to1252('ELECTROTEC CONSULTING S.A.C.'), $blue);
            $ty += $lineHeight;
            imagestring($im, 4, $textAreaX, $ty, $to1252('RUC: 20602124305'), $blue);
            $ty += $lineHeight;
            imagestring($im, 4, $textAreaX, $ty, $to1252('CERTIFICADO N° '. $data['certificate_number']), $black);
            $ty += $lineHeight;
            imagestring($im, 4, $textAreaX, $ty, $to1252('CALIBRACIÓN: '. $this->fmtDate($data['calibration_date'])), $black);
            $ty += $lineHeight;
            imagestring($im, 4, $textAreaX, $ty, $to1252('PRÓXIMA: '. $this->fmtDate($data['next_calibration_date'])), $black);
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
