<?php
namespace App\QrCode;

use App\QrCode\PhpQr\QRencode;

class QRencdr extends QRencode
{
    public $size;
    public $margin;
    public $fore_color;
    public $back_color;
    public $cmyk;
    /**
     * Factory
     *
     * @param num    $level      level
     * @param num    $size       size
     * @param num    $margin     margin
     * @param string $back_color back_color
     * @param string $fore_color fore_color
     * @param bool   $cmyk       style
     *
     * @return Encoded
     */
    public static function factory($level = QR_ECLEVEL_L, $size = 3, $margin = 4, $back_color = 0xFFFFFF, $fore_color = 0x000000, $cmyk = false)
    {
        $enc = new QRencdr();
        $enc->size = $size;
        $enc->margin = $margin;
        $enc->fore_color = $fore_color;
        $enc->back_color = $back_color;
        $enc->cmyk = $cmyk;

        switch ($level.'') {
        case '0':
        case '1':
        case '2':
        case '3':
            $enc->level = $level;
            break;
        case 'l':
        case 'L':
            $enc->level = QR_ECLEVEL_L;
            break;
        case 'm':
        case 'M':
            $enc->level = QR_ECLEVEL_M;
            break;
        case 'q':
        case 'Q':
            $enc->level = QR_ECLEVEL_Q;
            break;
        case 'h':
        case 'H':
            $enc->level = QR_ECLEVEL_H;
            break;
        }
        return $enc;
    }

    /**
     * Encode SVG
     *
     * @param string $intext       text
     * @param bool   $outfile      outfile
     * @param bool   $saveandprint save and print
     * @param bool   $style        style
     *
     * @return QRvtc
     */
    public function encodeSVG($intext, $outfile = false, $saveandprint = false, $style = false)
    {
        try {
            ob_start();
            $tab = $this->encode($intext);
            $err = ob_get_contents();
            ob_end_clean();
            
            if ($err != '') {
                QRtools::log($outfile, $err);
            }
            
            $maxSize = (int)(QR_PNG_MAXIMUM_SIZE / (count($tab)+2*$this->margin));

            return QRvct::svg($tab, $outfile, min(max(1, $this->size), $maxSize), $this->margin, $saveandprint, $this->back_color, $this->fore_color, $style);

        } catch (Exception $e) {
        
            QRtools::log($outfile, $e->getMessage());
        }
    }
}
