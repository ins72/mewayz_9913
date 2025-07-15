<?php
namespace App\QrCode;

use App\QrCode\PhpQr\QRcode as MyQrCode;

class SandyQrCode extends MyQrCode
{
    /**
     * Create SVG
     *
     * @param string $text         text
     * @param bool   $outfile      outfile
     * @param num    $level        level
     * @param num    $size         size
     * @param num    $margin       margin
     * @param bool   $saveandprint save and print
     * @param string $back_color   back_color
     * @param string $fore_color   fore_color
     * @param bool   $style        style
     *
     * @return SVG
     */
    public static function svg($text, $outfile = false, $level = QR_ECLEVEL_Q, $size = 3, $margin = 4, $saveandprint = false, $back_color = 0xFFFFFF, $fore_color = 0x000000, $style = false)
    {
        $enc = QRencdr::factory($level, $size, $margin, $back_color, $fore_color);
        return $enc->encodeSVG($text, $outfile, $saveandprint, $style);
    }
}