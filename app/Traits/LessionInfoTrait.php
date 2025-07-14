<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use App\Models\Audience;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

trait LessionInfoTrait{
    
    public function lesson_icons($type = null){
        $array = ['video' => 'sio media-icon-065-video-camera', 'image' => 'sio design-and-development-061-picture', 'text' => 'sio project-management-016-to-do-list'];

        return $array;
    }

    public function lesson_skeleton(){
        return [
            'video' => [
                'name' => 'Video Lesson',
                'icon' => 'sio media-icon-065-video-camera sligh-thick',
                'svg_folder' => 'Photo Edit',
                'svg_file' => 'Record, Videos, Camera',
            ],
            'text' => [
                'name' => 'Text Lesson',
                'icon' => 'sio project-management-016-to-do-list sligh-thick',
                'svg_folder' => 'Payments Finance',
                'svg_file' => 'document-protection-hand',
            ],
            'audio' => [
                'name' => 'Audio Lesson',
                'icon' => 'sio music-icon-013-music-note sligh-thick',
                'svg_folder' => 'Music, Audio',
                'svg_file' => 'Note',
            ],
            'iframe' => [
                'name' => 'Iframe / Embed Lesson',
                'icon' => 'sio office-036-page-layout sligh-thick',
                'svg_folder' => 'Internet, Network',
                'svg_file' => 'Browser, Internet, Web, Network, Window, App, Icon',
            ]
        ];
    }


    public function isIframe($stream){
        $skeleton = $this->video_skel();

        return ao($skeleton, "$stream.isIframe");
    }

    public function video_skel(){
        return [
            'youtube' => [
                'icon' => 'ph ph-youtube-logo',
                'color' => '#ff0000',
                'name' => 'Youtube',
                'placeholder' => 'https://youtu.be/wVrH_IUx74s',
                'isIframe' => false
            ],
            'vimeo' => [
                'icon' => 'sni sni-vimeo',
                'color' => '#1ab7ea',
                'name' => 'Vimeo',
                'placeholder' => 'https://vimeo.com/23423rwefse',
                'isIframe' => false
            ],
            'dailymotion' => [
                'svg' => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="416 207.4 400 400"
             style="enable-background:new 416 207.4 400 400;" xml:space="preserve"> <path style="fill: #000" class="st1" d="M653.5,308.2v68.1c-10.5-13.4-25-19.8-43.9-19.8c-19.8,0-37.5,7.3-52.1,21.5c-16.3,15.7-25,35.8-25,58.2 c0,24.4,9.3,45.4,27.3,61.4c13.7,12.2,30,18.3,48.9,18.3c18.6,0,32.6-4.9,46.3-18.3v17.5h44.5v-8.5h0V389v-10.6v-79.7L653.5,308.2z
             M618.9,474.4c-23.6,0-40.4-16.6-40.4-38.4c0-20.9,16.9-38.7,38.7-38.7c21.5,0,38.1,16.9,38.1,39.3
            C655.3,458.1,638.7,474.4,618.9,474.4z"/></svg>',
                'color' => '#000',
                'name' => 'Daily Motion',
                'placeholder' => 'https://www.dailymotion.com/video/x82jkc5',
                'isIframe' => false
            ],
            'others' => [
                'icon' => 'sni sni-dashboard',
                'color' => '#bbb',
                'name' => 'Others',
                'placeholder' => 'https://www.example.com',
                'isIframe' => true
            ],
        ];
    }
}
