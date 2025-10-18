<?php

namespace App\Shared\Pdf;

/**
 * Generador de sticker PNG (5cm x 2.5cm) con QR y datos básicos.
 * Requiere extensión GD habilitada. Genera PNG en la ruta indicada.
 */
class StickerGenerator
{
    /**
     * @param array{certificate_number:string,client_name:string,calibration_date:string,next_calibration_date:string,qr_url:string} $data
     * @param string $outputPath Ruta del archivo PNG a escribir
     */
    public function generate(array $data, string $outputPath): void
    {
        // 300 DPI -> 5 cm (1.9685 in) => ~590 px; 2.5 cm (~295 px)
        $width = 590; $height = 295;
        $im = imagecreatetruecolor($width, $height);
        if (!$im) { throw new \RuntimeException('GD no disponible'); }

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
        imagestring($im, 5, $tx, $ty, 'ELECTROTEC CONSULTING S.A.C.', $blue); $ty += $line;
        imagestring($im, 4, $tx, $ty, 'Certificado N° '. $data['certificate_number'], $black); $ty += $line;
        imagestring($im, 3, $tx, $ty, 'Cliente: '. $this->truncate($data['client_name'], 32), $black); $ty += $line;
        imagestring($im, 3, $tx, $ty, 'Calibracion: '. $this->fmtDate($data['calibration_date']), $black); $ty += $line;
        imagestring($im, 3, $tx, $ty, 'Proxima: '. $this->fmtDate($data['next_calibration_date']), $black);

        // Separador
        imageline($im, $padding, $height - 120, $width - $padding, $height - 120, $black);

        // Pie: número a la izquierda y caja de firma a la derecha
        imagestring($im, 4, $padding, $height - 110, 'N° '. $data['certificate_number'], $black);
        // Recuadro de firma
        $signW = 260; $signH = 70; $signX = $width - $padding - $signW; $signY = $height - 110;
        imagerectangle($im, $signX, $signY, $signX + $signW, $signY + $signH, $black);
        imagestring($im, 3, $signX + 14, $signY + 24, '(Firma del tecnico aqui)', $black);
        imagestring($im, 2, $signX + 14, $signY + 44, 'o bien, el texto: Nombre del Tecnico', $black);
        // Leyenda
        imagestring($im, 3, $width - 210, $height - 24, 'SERVICIO TECNICO', $blue);

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
}
