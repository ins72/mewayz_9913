<?php

namespace App\Models;

use App\Models\Base\Qrcode as BaseQrcode;

class Qrcode extends BaseQrcode
{
	protected $fillable = [
		'user_id',
		'text',
		'logo',
		'background',
		'extra'
	];

	protected $casts = [
		'extra' => 'array'
	];

    public function generate(){
        
        $margin = 4;
        $radial = ao($this->extra, 'dots.radial') ? true : false;
        $gradient = ao($this->extra, 'dots.type') ? true : false;
        $gradient_color = ao($this->extra, 'dots.color_2');

        $stringbackcolor = '#FFFFFF';
        $stringfrontcolor = ao($this->extra, 'dots.color');

        // Mask frame
        $negative_qr = ao($this->extra, 'frames.mask');
        // Check gradient

        if($gradient){
            $stringfrontcolor = ao($this->extra, 'dots.color_1');
        }


        $backcolor = qrcdr()->hexdecColor($stringbackcolor, '#FFFFFF');
        $frontcolor = qrcdr()->hexdecColor($stringfrontcolor, '#000000');

        // BG
        $bg_image = 'none';

        if (ao($this->extra, 'frames.enable')) {
            if(!empty($this->background) && mediaExists('media/qrcode/background', $this->background)){
                $_get_bg = file_get_contents(gs('media/qrcode/background', $this->background));
                if($_get_bg){
                    $bg_image = 'data:image/jpg;base64,'.base64_encode($_get_bg);

                    $gradient = false;
                }
            }

            if(!empty($preset = ao($this->extra, 'frames.preset'))){
                $_get_bg = file_get_contents(gs('assets/image/others/qrframes', $preset));
                if($_get_bg){
                    $bg_image = 'data:image/jpg;base64,'.base64_encode($_get_bg);
                    $margin = 12;
                    $negative_qr = false;
                }
            }
        }

        // Logo
        $base64Logo = 'none';
        if (ao($this->extra, 'logos.enable')) {
            $preset = ao($this->extra, 'logos.preset');
            if(!empty($this->logo) && mediaExists('media/qrcode/logo', $this->logo)){
                $_get_logo = file_get_contents(gs('media/qrcode/logo', $this->logo));

                if($_get_logo){
                    $base64Logo = 'data:image/jpg;base64,'.base64_encode($_get_logo);
                }
            }

            // Check if other isset

            // if($preset == '_mypagelogo'){
            //     $_get_logo = file_get_contents($this->getLogo());
            //     if($_get_logo){
            //         $base64Logo = 'data:image/jpg;base64,'.base64_encode($_get_logo);
            //     }
            // }
            
            if(!empty($preset) && $preset !== '_mypagelogo'){
                $_get_logo = file_get_contents(gs('assets/image/logos', $preset));
                if($_get_logo){
                    $base64Logo = 'data:image/jpg;base64,'.base64_encode($_get_logo);
                }
            }
        }
        
        $options = [
            'optionlogo' => $base64Logo,
            'pattern' => ao($this->extra, 'pattern'),
            'marker_in' => ao($this->extra, 'eye_ball.shape'),
            'marker_in_color' => ao($this->extra, 'eye_ball.color'),

            'marker_out' => ao($this->extra, 'eye_frame.shape'),
            'marker_out_color' => ao($this->extra, 'eye_frame.color'),
            'markers_color' => '#6d0202',

            'gradient' => $gradient,
            'gradient_color' => $gradient_color,
            //'markers_color' => $markers_color,
            'radial' => $radial,

            //
            'bg_image' => $bg_image,
            'logo_size' => 80,
            'no_logo_bg' => true,
            'negative' => $negative_qr
        ];

        // print_r($gradient_color);
        // if (!__o_feature('feature.qr_code', $this->user->id)){
        //     $options = [];
        //     $back_color = qrcdr()->hexdecColor('#ffffff', '#000000');
        //     $frontcolor = qrcdr()->hexdecColor('#000000', '#000000');
        // }

        // print_r($this->text);
        return \App\QrCode\SandyQrCode::svg($this->text, $outfile = false, $level = QR_ECLEVEL_Q, $size = 32, $margin, $saveandprint = false, $back_color = 0xFFFFFF, $frontcolor, $options);
    }
    
    public function convertQrcode(){
        $_qr = $this->generate();
    
        $svg = $_qr;
        $dom = new \DomDocument();
        $dom->loadXML($svg);
        foreach($dom->getElementsByTagName('image') as $image) {
            $encoded = $image->attributes->getNamedItem('href')->value;
            if(!empty($encoded)) {
                $binary = base64_decode(substr($encoded,strpos($encoded,'base64,') + 7));
                $info = getimagesizefromstring ($binary);

                $image->setAttributeNS('http://www.w3.org/1999/xlink','xlink:href','data:'.$info['mime'].';base64,' . base64_encode($binary));
            }
        }
        $svg = $dom->saveXML();
        $im = new \Imagick();
        $im->readImageBlob($svg);
        $im->setResolution(1000,1000);
        $im->setImageFormat("png");
        $base64=base64_encode($im);
        $im->clear();
        $im->destroy();

        return $base64;

        return $svg;
    }
    public function processQrDownload($qr = false){
        $_qr = $qr;
        if(!$qr){
            $_qr = $this->generate();
        }
        
        $svg = $_qr;
        $dom = new \DomDocument();
        $dom->loadXML($svg);
        foreach($dom->getElementsByTagName('image') as $image) {
            $encoded = $image->attributes->getNamedItem('href')->value;
            if(!empty($encoded)) {
                $binary = base64_decode(substr($encoded,strpos($encoded,'base64,') + 7));
                $info = getimagesizefromstring ($binary);

                $image->setAttributeNS('http://www.w3.org/1999/xlink','xlink:href','data:'.$info['mime'].';base64,' . base64_encode($binary));
            }
        }

        $svg = $dom->saveXML();
        $im = new \Imagick();
        $im->readImageBlob($svg);
        $im->setResolution(1000,1000);
        $im->setImageFormat("png");
        $base64=base64_encode($im);
        $im->clear();
        $im->destroy();

        return $base64;
    }
}
