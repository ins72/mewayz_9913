<?php
  use Illuminate\View\View;
  use App\Models\Site;
  use function Laravel\Folio\render;
  use function Laravel\Folio\name;
    
  name('site-index');
  
  render(function (View $view) {
      if(!config('app.INSTALLED')){
        return redirect()->route('console-install');
      }
      
      $site = false;
      if($_site = Site::where('is_admin', 1)->where('is_admin_selected', 1)->first()){
        $site = $_site;
      }
      return $view->with('site', $site);
  });
?>
<x-layouts.site>

<!DOCTYPE html>
<html lang="en"><head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="EjEySsWZDkphwfuffeg1nfAcfYDPFc9yo1xiP4Za">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

        
                <title>Mewayz</title>
                

        

        <!-- Favicon -->
        <link href="https://f005.backblazeb2.com/file/mewayz/mewayz-web/assets/Untitled%20design%20%2868%29.png" rel="shortcut icon" type="image/png">

        <!-- Scripts -->
        <link rel="preload" as="style" href="https://mewayz.com/build/assets/app-fbefa9a2.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/app-1f2e534b.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/console.sidebar-bb389b0b.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/console.placeholder-ccad17d1.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/site-fa793f6e.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/create-0f8797b3.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/app-4f33a8f0.css"><link rel="modulepreload" href="https://mewayz.com/build/assets/moreUtils-eaf77fe0.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/index-7d9a9c74.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/app-f78797f2.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/_commonjs-dynamic-modules-a536689c.js"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-fbefa9a2.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-1f2e534b.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/console.sidebar-bb389b0b.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/console.placeholder-ccad17d1.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/site-fa793f6e.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/create-0f8797b3.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-4f33a8f0.css" data-navigate-track="reload"><script type="module" src="https://mewayz.com/build/assets/moreUtils-eaf77fe0.js" data-navigate-track="reload"></script><script type="module" src="https://mewayz.com/build/assets/app-f78797f2.js" data-navigate-track="reload"></script>

        <!-- Livewire Styles --><style>[wire\:loading][wire\:loading], [wire\:loading\.delay][wire\:loading\.delay], [wire\:loading\.inline-block][wire\:loading\.inline-block], [wire\:loading\.inline][wire\:loading\.inline], [wire\:loading\.block][wire\:loading\.block], [wire\:loading\.flex][wire\:loading\.flex], [wire\:loading\.table][wire\:loading\.table], [wire\:loading\.grid][wire\:loading\.grid], [wire\:loading\.inline-flex][wire\:loading\.inline-flex] {display: none;}[wire\:loading\.delay\.none][wire\:loading\.delay\.none], [wire\:loading\.delay\.shortest][wire\:loading\.delay\.shortest], [wire\:loading\.delay\.shorter][wire\:loading\.delay\.shorter], [wire\:loading\.delay\.short][wire\:loading\.delay\.short], [wire\:loading\.delay\.default][wire\:loading\.delay\.default], [wire\:loading\.delay\.long][wire\:loading\.delay\.long], [wire\:loading\.delay\.longer][wire\:loading\.delay\.longer], [wire\:loading\.delay\.longest][wire\:loading\.delay\.longest] {display: none;}[wire\:offline][wire\:offline] {display: none;}[wire\:dirty]:not(textarea):not(input):not(select) {display: none;}:root {--livewire-progress-bar-color: #2299dd;}[x-cloak] {display: none !important;}</style>

<style>/* Make clicks pass-through */
    .logo {
    font-family: 'Airstrike';
    width: 100%;
           }
    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: var(--livewire-progress-bar-color, #29d);

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    /* Fancy blur effect */
    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px var(--livewire-progress-bar-color, #29d), 0 0 5px var(--livewire-progress-bar-color, #29d);
      opacity: 1.0;

      -webkit-transform: rotate(3deg) translate(0px, -4px);
          -ms-transform: rotate(3deg) translate(0px, -4px);
              transform: rotate(3deg) translate(0px, -4px);
    }

    /* Remove these to get rid of the spinner */
    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: var(--livewire-progress-bar-color, #29d);
      border-left-color: var(--livewire-progress-bar-color, #29d);
      border-radius: 50%;

      -webkit-animation: nprogress-spinner 400ms linear infinite;
              animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
      0%   { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style><style>/* Make clicks pass-through */

    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: var(--livewire-progress-bar-color, #29d);

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    /* Fancy blur effect */
    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px var(--livewire-progress-bar-color, #29d), 0 0 5px var(--livewire-progress-bar-color, #29d);
      opacity: 1.0;

      -webkit-transform: rotate(3deg) translate(0px, -4px);
          -ms-transform: rotate(3deg) translate(0px, -4px);
              transform: rotate(3deg) translate(0px, -4px);
    }

    /* Remove these to get rid of the spinner */
    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: var(--livewire-progress-bar-color, #29d);
      border-left-color: var(--livewire-progress-bar-color, #29d);
      border-radius: 50%;

      -webkit-animation: nprogress-spinner 400ms linear infinite;
              animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
      0%   { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style><style>.ce-hint--align-start{text-align:left}.ce-hint--align-center{text-align:center}.ce-hint__description{opacity:.6;margin-top:3px}</style><style type="text/css">.cdx-notify--error{background:#fffbfb!important}.cdx-notify--error::before{background:#fb5d5d!important}.cdx-notify__input{max-width:130px;padding:5px 10px;background:#f7f7f7;border:0;border-radius:3px;font-size:13px;color:#656b7c;outline:0}.cdx-notify__input:-ms-input-placeholder{color:#656b7c}.cdx-notify__input::placeholder{color:#656b7c}.cdx-notify__input:focus:-ms-input-placeholder{color:rgba(101,107,124,.3)}.cdx-notify__input:focus::placeholder{color:rgba(101,107,124,.3)}.cdx-notify__button{border:none;border-radius:3px;font-size:13px;padding:5px 10px;cursor:pointer}.cdx-notify__button:last-child{margin-left:10px}.cdx-notify__button--cancel{background:#f2f5f7;box-shadow:0 2px 1px 0 rgba(16,19,29,0);color:#656b7c}.cdx-notify__button--cancel:hover{background:#eee}.cdx-notify__button--confirm{background:#34c992;box-shadow:0 1px 1px 0 rgba(18,49,35,.05);color:#fff}.cdx-notify__button--confirm:hover{background:#33b082}.cdx-notify__btns-wrapper{display:-ms-flexbox;display:flex;-ms-flex-flow:row nowrap;flex-flow:row nowrap;margin-top:5px}.cdx-notify__cross{position:absolute;top:5px;right:5px;width:10px;height:10px;padding:5px;opacity:.54;cursor:pointer}.cdx-notify__cross::after,.cdx-notify__cross::before{content:'';position:absolute;left:9px;top:5px;height:12px;width:2px;background:#575d67}.cdx-notify__cross::before{transform:rotate(-45deg)}.cdx-notify__cross::after{transform:rotate(45deg)}.cdx-notify__cross:hover{opacity:1}.cdx-notifies{position:fixed;z-index:2;bottom:20px;left:20px;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Helvetica Neue",sans-serif}.cdx-notify{position:relative;width:220px;margin-top:15px;padding:13px 16px;background:#fff;box-shadow:0 11px 17px 0 rgba(23,32,61,.13);border-radius:5px;font-size:14px;line-height:1.4em;word-wrap:break-word}.cdx-notify::before{content:'';position:absolute;display:block;top:0;left:0;width:3px;height:calc(100% - 6px);margin:3px;border-radius:5px;background:0 0}@keyframes bounceIn{0%{opacity:0;transform:scale(.3)}50%{opacity:1;transform:scale(1.05)}70%{transform:scale(.9)}100%{transform:scale(1)}}.cdx-notify--bounce-in{animation-name:bounceIn;animation-duration:.6s;animation-iteration-count:1}.cdx-notify--success{background:#fafffe!important}.cdx-notify--success::before{background:#41ffb1!important}</style><style>.ce-paragraph{line-height:1.6em;outline:none}.ce-block:only-of-type .ce-paragraph[data-placeholder-active]:empty:before,.ce-block:only-of-type .ce-paragraph[data-placeholder-active][data-empty=true]:before{content:attr(data-placeholder-active)}.ce-paragraph p:first-of-type{margin-top:0}.ce-paragraph p:last-of-type{margin-bottom:0}</style><style>.ce-header{padding:.6em 0 3px;margin:0;line-height:1.25em;outline:none}.ce-header p,.ce-header div{padding:0!important;margin:0!important}</style><style>.cdx-list{margin:0;padding-left:40px;outline:none}.cdx-list__item{padding:5.5px 0 5.5px 3px;line-height:1.6em}.cdx-list--unordered{list-style:disc}.cdx-list--ordered{list-style:decimal}.cdx-list-settings{display:flex}.cdx-list-settings .cdx-settings-button{width:50%}</style><style>.cdx-quote-icon svg{transform:rotate(180deg)}.cdx-quote{margin:0}.cdx-quote__text{min-height:158px;margin-bottom:10px}.cdx-quote [contentEditable=true][data-placeholder]:before{position:absolute;content:attr(data-placeholder);color:#707684;font-weight:400;opacity:0}.cdx-quote [contentEditable=true][data-placeholder]:empty:before{opacity:1}.cdx-quote [contentEditable=true][data-placeholder]:empty:focus:before{opacity:0}.cdx-quote-settings{display:flex}.cdx-quote-settings .cdx-settings-button{width:50%}</style><style>.ce-delimiter{line-height:1.6em;width:100%;text-align:center}.ce-delimiter:before{display:inline-block;content:"***";font-size:30px;line-height:65px;height:30px;letter-spacing:.2em}</style><style>.cdx-warning{position:relative}@media all and (min-width: 736px){.cdx-warning{padding-left:36px}}.cdx-warning [contentEditable=true][data-placeholder]:before{position:absolute;content:attr(data-placeholder);color:#707684;font-weight:400;opacity:0}.cdx-warning [contentEditable=true][data-placeholder]:empty:before{opacity:1}.cdx-warning [contentEditable=true][data-placeholder]:empty:focus:before{opacity:0}.cdx-warning:before{content:"";background-image:url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='5' y='5' width='14' height='14' rx='4' stroke='black' stroke-width='2'/%3E%3Cline x1='12' y1='9' x2='12' y2='12' stroke='black' stroke-width='2' stroke-linecap='round'/%3E%3Cpath d='M12 15.02V15.01' stroke='black' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");width:24px;height:24px;background-size:24px 24px;position:absolute;margin-top:8px;left:0}@media all and (max-width: 735px){.cdx-warning:before{display:none}}.cdx-warning__message{min-height:85px}.cdx-warning__title{margin-bottom:6px}</style><style>.ce-code__textarea{min-height:200px;font-family:Menlo,Monaco,Consolas,Courier New,monospace;color:#41314e;line-height:1.6em;font-size:12px;background:#f8f7fa;border:1px solid #f1f1f4;box-shadow:none;white-space:pre;word-wrap:normal;overflow-x:auto;resize:vertical}</style><style>.ce-rawtool__textarea{min-height:200px;resize:vertical;border-radius:8px;border:0;background-color:#1e2128;font-family:Menlo,Monaco,Consolas,Courier New,monospace;font-size:12px;line-height:1.6;letter-spacing:-.2px;color:#a1a7b6;overscroll-behavior:contain}</style><style>.cdx-checklist{gap:6px;display:flex;flex-direction:column}.cdx-checklist__item{display:flex;box-sizing:content-box;align-items:flex-start}.cdx-checklist__item-text{outline:none;flex-grow:1;line-height:1.57em}.cdx-checklist__item-checkbox{width:22px;height:22px;display:flex;align-items:center;margin-right:8px;margin-top:calc(.785em - 11px);cursor:pointer}.cdx-checklist__item-checkbox svg{opacity:0;height:20px;width:20px;position:absolute;left:-1px;top:-1px;max-height:20px}@media (hover: hover){.cdx-checklist__item-checkbox:not(.cdx-checklist__item-checkbox--no-hover):hover .cdx-checklist__item-checkbox-check svg{opacity:1}}.cdx-checklist__item-checkbox-check{cursor:pointer;display:inline-block;flex-shrink:0;position:relative;width:20px;height:20px;box-sizing:border-box;margin-left:0;border-radius:5px;border:1px solid #C9C9C9;background:#fff}.cdx-checklist__item-checkbox-check:before{content:"";position:absolute;top:0;right:0;bottom:0;left:0;border-radius:100%;background-color:#369fff;visibility:hidden;pointer-events:none;transform:scale(1);transition:transform .4s ease-out,opacity .4s}@media (hover: hover){.cdx-checklist__item--checked .cdx-checklist__item-checkbox:not(.cdx-checklist__item--checked .cdx-checklist__item-checkbox--no-hover):hover .cdx-checklist__item-checkbox-check{background:#0059AB;border-color:#0059ab}}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check{background:#369FFF;border-color:#369fff}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check svg{opacity:1}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check svg path{stroke:#fff}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check:before{opacity:0;visibility:visible;transform:scale(2.5)}</style><style>.ce-paragraph{line-height:1.6em;outline:none}.ce-block:only-of-type .ce-paragraph[data-placeholder-active]:empty:before,.ce-block:only-of-type .ce-paragraph[data-placeholder-active][data-empty=true]:before{content:attr(data-placeholder-active)}.ce-paragraph p:first-of-type{margin-top:0}.ce-paragraph p:last-of-type{margin-bottom:0}</style><style>.image-tool{--bg-color: #cdd1e0;--front-color: #388ae5;--border-color: #e8e8eb}.image-tool__image{border-radius:3px;overflow:hidden;margin-bottom:10px}.image-tool__image-picture{max-width:100%;vertical-align:bottom;display:block}.image-tool__image-preloader{width:50px;height:50px;border-radius:50%;background-size:cover;margin:auto;position:relative;background-color:var(--bg-color);background-position:center center}.image-tool__image-preloader:after{content:"";position:absolute;z-index:3;width:60px;height:60px;border-radius:50%;border:2px solid var(--bg-color);border-top-color:var(--front-color);left:50%;top:50%;margin-top:-30px;margin-left:-30px;animation:image-preloader-spin 2s infinite linear;box-sizing:border-box}.image-tool__caption[contentEditable=true][data-placeholder]:before{position:absolute!important;content:attr(data-placeholder);color:#707684;font-weight:400;display:none}.image-tool__caption[contentEditable=true][data-placeholder]:empty:before{display:block}.image-tool__caption[contentEditable=true][data-placeholder]:empty:focus:before{display:none}.image-tool--empty .image-tool__image,.image-tool--empty .image-tool__caption,.image-tool--loading .image-tool__caption{display:none}.image-tool .cdx-button{display:flex;align-items:center;justify-content:center}.image-tool .cdx-button svg{height:auto;margin:0 6px 0 0}.image-tool--filled .cdx-button,.image-tool--filled .image-tool__image-preloader{display:none}.image-tool--loading .image-tool__image{min-height:200px;display:flex;border:1px solid var(--border-color);background-color:#fff}.image-tool--loading .image-tool__image-picture,.image-tool--loading .cdx-button{display:none}.image-tool--withBorder .image-tool__image{border:1px solid var(--border-color)}.image-tool--withBackground .image-tool__image{padding:15px;background:var(--bg-color)}.image-tool--withBackground .image-tool__image-picture{max-width:60%;margin:0 auto}.image-tool--stretched .image-tool__image-picture{width:100%}@keyframes image-preloader-spin{0%{transform:rotate(0)}to{transform:rotate(360deg)}}</style></head>
    <body class="font-sans text-gray-900 antialiased loaded" data-theme="light">
        
        <div>
            

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="EjEySsWZDkphwfuffeg1nfAcfYDPFc9yo1xiP4Za">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

        
                <title>Mewayz</title>
                

        

        <!-- Favicon -->
        <link href="https://mewayz.com/media/site/favicon/YxoGJQRzTuwwpmNU3pll11wCrF0ane7BcxMRgKHE.png" rel="shortcut icon" type="image/png">

        <!-- Scripts -->
        <link rel="preload" as="style" href="https://mewayz.com/build/assets/app-fbefa9a2.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/app-1f2e534b.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/console.sidebar-bb389b0b.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/console.placeholder-ccad17d1.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/site-fa793f6e.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/create-0f8797b3.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/app-4f33a8f0.css"><link rel="modulepreload" href="https://mewayz.com/build/assets/moreUtils-eaf77fe0.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/index-7d9a9c74.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/app-f78797f2.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/_commonjs-dynamic-modules-a536689c.js"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-fbefa9a2.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-1f2e534b.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/console.sidebar-bb389b0b.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/console.placeholder-ccad17d1.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/site-fa793f6e.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/create-0f8797b3.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-4f33a8f0.css" data-navigate-track="reload"><script type="module" src="https://mewayz.com/build/assets/moreUtils-eaf77fe0.js" data-navigate-track="reload"></script><script type="module" src="https://mewayz.com/build/assets/app-f78797f2.js" data-navigate-track="reload"></script>

        <!-- Livewire Styles --><style>[wire\:loading][wire\:loading], [wire\:loading\.delay][wire\:loading\.delay], [wire\:loading\.inline-block][wire\:loading\.inline-block], [wire\:loading\.inline][wire\:loading\.inline], [wire\:loading\.block][wire\:loading\.block], [wire\:loading\.flex][wire\:loading\.flex], [wire\:loading\.table][wire\:loading\.table], [wire\:loading\.grid][wire\:loading\.grid], [wire\:loading\.inline-flex][wire\:loading\.inline-flex] {display: none;}[wire\:loading\.delay\.none][wire\:loading\.delay\.none], [wire\:loading\.delay\.shortest][wire\:loading\.delay\.shortest], [wire\:loading\.delay\.shorter][wire\:loading\.delay\.shorter], [wire\:loading\.delay\.short][wire\:loading\.delay\.short], [wire\:loading\.delay\.default][wire\:loading\.delay\.default], [wire\:loading\.delay\.long][wire\:loading\.delay\.long], [wire\:loading\.delay\.longer][wire\:loading\.delay\.longer], [wire\:loading\.delay\.longest][wire\:loading\.delay\.longest] {display: none;}[wire\:offline][wire\:offline] {display: none;}[wire\:dirty]:not(textarea):not(input):not(select) {display: none;}:root {--livewire-progress-bar-color: #2299dd;}[x-cloak] {display: none !important;}</style>
    <style>/* Make clicks pass-through */

    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: var(--livewire-progress-bar-color, #29d);

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    /* Fancy blur effect */
    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px var(--livewire-progress-bar-color, #29d), 0 0 5px var(--livewire-progress-bar-color, #29d);
      opacity: 1.0;

      -webkit-transform: rotate(3deg) translate(0px, -4px);
          -ms-transform: rotate(3deg) translate(0px, -4px);
              transform: rotate(3deg) translate(0px, -4px);
    }

    /* Remove these to get rid of the spinner */
    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: var(--livewire-progress-bar-color, #29d);
      border-left-color: var(--livewire-progress-bar-color, #29d);
      border-radius: 50%;

      -webkit-animation: nprogress-spinner 400ms linear infinite;
              animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
      0%   { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style><style>/* Make clicks pass-through */

    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: var(--livewire-progress-bar-color, #29d);

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    /* Fancy blur effect */
    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px var(--livewire-progress-bar-color, #29d), 0 0 5px var(--livewire-progress-bar-color, #29d);
      opacity: 1.0;

      -webkit-transform: rotate(3deg) translate(0px, -4px);
          -ms-transform: rotate(3deg) translate(0px, -4px);
              transform: rotate(3deg) translate(0px, -4px);
    }

    /* Remove these to get rid of the spinner */
    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: var(--livewire-progress-bar-color, #29d);
      border-left-color: var(--livewire-progress-bar-color, #29d);
      border-radius: 50%;

      -webkit-animation: nprogress-spinner 400ms linear infinite;
              animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
      0%   { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style><style>.ce-hint--align-start{text-align:left}.ce-hint--align-center{text-align:center}.ce-hint__description{opacity:.6;margin-top:3px}</style><style type="text/css">.cdx-notify--error{background:#fffbfb!important}.cdx-notify--error::before{background:#fb5d5d!important}.cdx-notify__input{max-width:130px;padding:5px 10px;background:#f7f7f7;border:0;border-radius:3px;font-size:13px;color:#656b7c;outline:0}.cdx-notify__input:-ms-input-placeholder{color:#656b7c}.cdx-notify__input::placeholder{color:#656b7c}.cdx-notify__input:focus:-ms-input-placeholder{color:rgba(101,107,124,.3)}.cdx-notify__input:focus::placeholder{color:rgba(101,107,124,.3)}.cdx-notify__button{border:none;border-radius:3px;font-size:13px;padding:5px 10px;cursor:pointer}.cdx-notify__button:last-child{margin-left:10px}.cdx-notify__button--cancel{background:#f2f5f7;box-shadow:0 2px 1px 0 rgba(16,19,29,0);color:#656b7c}.cdx-notify__button--cancel:hover{background:#eee}.cdx-notify__button--confirm{background:#34c992;box-shadow:0 1px 1px 0 rgba(18,49,35,.05);color:#fff}.cdx-notify__button--confirm:hover{background:#33b082}.cdx-notify__btns-wrapper{display:-ms-flexbox;display:flex;-ms-flex-flow:row nowrap;flex-flow:row nowrap;margin-top:5px}.cdx-notify__cross{position:absolute;top:5px;right:5px;width:10px;height:10px;padding:5px;opacity:.54;cursor:pointer}.cdx-notify__cross::after,.cdx-notify__cross::before{content:'';position:absolute;left:9px;top:5px;height:12px;width:2px;background:#575d67}.cdx-notify__cross::before{transform:rotate(-45deg)}.cdx-notify__cross::after{transform:rotate(45deg)}.cdx-notify__cross:hover{opacity:1}.cdx-notifies{position:fixed;z-index:2;bottom:20px;left:20px;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Helvetica Neue",sans-serif}.cdx-notify{position:relative;width:220px;margin-top:15px;padding:13px 16px;background:#fff;box-shadow:0 11px 17px 0 rgba(23,32,61,.13);border-radius:5px;font-size:14px;line-height:1.4em;word-wrap:break-word}.cdx-notify::before{content:'';position:absolute;display:block;top:0;left:0;width:3px;height:calc(100% - 6px);margin:3px;border-radius:5px;background:0 0}@keyframes bounceIn{0%{opacity:0;transform:scale(.3)}50%{opacity:1;transform:scale(1.05)}70%{transform:scale(.9)}100%{transform:scale(1)}}.cdx-notify--bounce-in{animation-name:bounceIn;animation-duration:.6s;animation-iteration-count:1}.cdx-notify--success{background:#fafffe!important}.cdx-notify--success::before{background:#41ffb1!important}</style><style>.ce-paragraph{line-height:1.6em;outline:none}.ce-block:only-of-type .ce-paragraph[data-placeholder-active]:empty:before,.ce-block:only-of-type .ce-paragraph[data-placeholder-active][data-empty=true]:before{content:attr(data-placeholder-active)}.ce-paragraph p:first-of-type{margin-top:0}.ce-paragraph p:last-of-type{margin-bottom:0}</style><style>.ce-header{padding:.6em 0 3px;margin:0;line-height:1.25em;outline:none}.ce-header p,.ce-header div{padding:0!important;margin:0!important}</style><style>.cdx-list{margin:0;padding-left:40px;outline:none}.cdx-list__item{padding:5.5px 0 5.5px 3px;line-height:1.6em}.cdx-list--unordered{list-style:disc}.cdx-list--ordered{list-style:decimal}.cdx-list-settings{display:flex}.cdx-list-settings .cdx-settings-button{width:50%}</style><style>.cdx-quote-icon svg{transform:rotate(180deg)}.cdx-quote{margin:0}.cdx-quote__text{min-height:158px;margin-bottom:10px}.cdx-quote [contentEditable=true][data-placeholder]:before{position:absolute;content:attr(data-placeholder);color:#707684;font-weight:400;opacity:0}.cdx-quote [contentEditable=true][data-placeholder]:empty:before{opacity:1}.cdx-quote [contentEditable=true][data-placeholder]:empty:focus:before{opacity:0}.cdx-quote-settings{display:flex}.cdx-quote-settings .cdx-settings-button{width:50%}</style><style>.ce-delimiter{line-height:1.6em;width:100%;text-align:center}.ce-delimiter:before{display:inline-block;content:"***";font-size:30px;line-height:65px;height:30px;letter-spacing:.2em}</style><style>.cdx-warning{position:relative}@media all and (min-width: 736px){.cdx-warning{padding-left:36px}}.cdx-warning [contentEditable=true][data-placeholder]:before{position:absolute;content:attr(data-placeholder);color:#707684;font-weight:400;opacity:0}.cdx-warning [contentEditable=true][data-placeholder]:empty:before{opacity:1}.cdx-warning [contentEditable=true][data-placeholder]:empty:focus:before{opacity:0}.cdx-warning:before{content:"";background-image:url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='5' y='5' width='14' height='14' rx='4' stroke='black' stroke-width='2'/%3E%3Cline x1='12' y1='9' x2='12' y2='12' stroke='black' stroke-width='2' stroke-linecap='round'/%3E%3Cpath d='M12 15.02V15.01' stroke='black' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");width:24px;height:24px;background-size:24px 24px;position:absolute;margin-top:8px;left:0}@media all and (max-width: 735px){.cdx-warning:before{display:none}}.cdx-warning__message{min-height:85px}.cdx-warning__title{margin-bottom:6px}</style><style>.ce-code__textarea{min-height:200px;font-family:Menlo,Monaco,Consolas,Courier New,monospace;color:#41314e;line-height:1.6em;font-size:12px;background:#f8f7fa;border:1px solid #f1f1f4;box-shadow:none;white-space:pre;word-wrap:normal;overflow-x:auto;resize:vertical}</style><style>.ce-rawtool__textarea{min-height:200px;resize:vertical;border-radius:8px;border:0;background-color:#1e2128;font-family:Menlo,Monaco,Consolas,Courier New,monospace;font-size:12px;line-height:1.6;letter-spacing:-.2px;color:#a1a7b6;overscroll-behavior:contain}</style><style>.cdx-checklist{gap:6px;display:flex;flex-direction:column}.cdx-checklist__item{display:flex;box-sizing:content-box;align-items:flex-start}.cdx-checklist__item-text{outline:none;flex-grow:1;line-height:1.57em}.cdx-checklist__item-checkbox{width:22px;height:22px;display:flex;align-items:center;margin-right:8px;margin-top:calc(.785em - 11px);cursor:pointer}.cdx-checklist__item-checkbox svg{opacity:0;height:20px;width:20px;position:absolute;left:-1px;top:-1px;max-height:20px}@media (hover: hover){.cdx-checklist__item-checkbox:not(.cdx-checklist__item-checkbox--no-hover):hover .cdx-checklist__item-checkbox-check svg{opacity:1}}.cdx-checklist__item-checkbox-check{cursor:pointer;display:inline-block;flex-shrink:0;position:relative;width:20px;height:20px;box-sizing:border-box;margin-left:0;border-radius:5px;border:1px solid #C9C9C9;background:#fff}.cdx-checklist__item-checkbox-check:before{content:"";position:absolute;top:0;right:0;bottom:0;left:0;border-radius:100%;background-color:#369fff;visibility:hidden;pointer-events:none;transform:scale(1);transition:transform .4s ease-out,opacity .4s}@media (hover: hover){.cdx-checklist__item--checked .cdx-checklist__item-checkbox:not(.cdx-checklist__item--checked .cdx-checklist__item-checkbox--no-hover):hover .cdx-checklist__item-checkbox-check{background:#0059AB;border-color:#0059ab}}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check{background:#369FFF;border-color:#369fff}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check svg{opacity:1}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check svg path{stroke:#fff}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check:before{opacity:0;visibility:visible;transform:scale(2.5)}</style><style>.ce-paragraph{line-height:1.6em;outline:none}.ce-block:only-of-type .ce-paragraph[data-placeholder-active]:empty:before,.ce-block:only-of-type .ce-paragraph[data-placeholder-active][data-empty=true]:before{content:attr(data-placeholder-active)}.ce-paragraph p:first-of-type{margin-top:0}.ce-paragraph p:last-of-type{margin-bottom:0}</style><style>.image-tool{--bg-color: #cdd1e0;--front-color: #388ae5;--border-color: #e8e8eb}.image-tool__image{border-radius:3px;overflow:hidden;margin-bottom:10px}.image-tool__image-picture{max-width:100%;vertical-align:bottom;display:block}.image-tool__image-preloader{width:50px;height:50px;border-radius:50%;background-size:cover;margin:auto;position:relative;background-color:var(--bg-color);background-position:center center}.image-tool__image-preloader:after{content:"";position:absolute;z-index:3;width:60px;height:60px;border-radius:50%;border:2px solid var(--bg-color);border-top-color:var(--front-color);left:50%;top:50%;margin-top:-30px;margin-left:-30px;animation:image-preloader-spin 2s infinite linear;box-sizing:border-box}.image-tool__caption[contentEditable=true][data-placeholder]:before{position:absolute!important;content:attr(data-placeholder);color:#707684;font-weight:400;display:none}.image-tool__caption[contentEditable=true][data-placeholder]:empty:before{display:block}.image-tool__caption[contentEditable=true][data-placeholder]:empty:focus:before{display:none}.image-tool--empty .image-tool__image,.image-tool--empty .image-tool__caption,.image-tool--loading .image-tool__caption{display:none}.image-tool .cdx-button{display:flex;align-items:center;justify-content:center}.image-tool .cdx-button svg{height:auto;margin:0 6px 0 0}.image-tool--filled .cdx-button,.image-tool--filled .image-tool__image-preloader{display:none}.image-tool--loading .image-tool__image{min-height:200px;display:flex;border:1px solid var(--border-color);background-color:#fff}.image-tool--loading .image-tool__image-picture,.image-tool--loading .cdx-button{display:none}.image-tool--withBorder .image-tool__image{border:1px solid var(--border-color)}.image-tool--withBackground .image-tool__image{padding:15px;background:var(--bg-color)}.image-tool--withBackground .image-tool__image-picture{max-width:60%;margin:0 auto}.image-tool--stretched .image-tool__image-picture{width:100%}@keyframes image-preloader-spin{0%{transform:rotate(0)}to{transform:rotate(360deg)}}</style>
    
        
        <div>
            

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="UeFLzrjalD0dK2oD6AUxSbVXIwleFJ78WOWw0ZK2">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

        
                <title>Mewayz</title>
                

        

        <!-- Favicon -->
        <link href="https://mewayz.com/media/site/favicon/YxoGJQRzTuwwpmNU3pll11wCrF0ane7BcxMRgKHE.png" rel="shortcut icon" type="image/png">

        <!-- Scripts -->
        <link rel="preload" as="style" href="https://mewayz.com/build/assets/app-fbefa9a2.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/app-1f2e534b.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/console.sidebar-bb389b0b.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/console.placeholder-ccad17d1.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/site-fa793f6e.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/create-0f8797b3.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/app-4f33a8f0.css"><link rel="modulepreload" href="https://mewayz.com/build/assets/moreUtils-eaf77fe0.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/index-7d9a9c74.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/app-f78797f2.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/_commonjs-dynamic-modules-a536689c.js"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-fbefa9a2.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-1f2e534b.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/console.sidebar-bb389b0b.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/console.placeholder-ccad17d1.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/site-fa793f6e.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/create-0f8797b3.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-4f33a8f0.css" data-navigate-track="reload"><script type="module" src="https://mewayz.com/build/assets/moreUtils-eaf77fe0.js" data-navigate-track="reload"></script><script type="module" src="https://mewayz.com/build/assets/app-f78797f2.js" data-navigate-track="reload"></script>

        <!-- Livewire Styles --><style>[wire\:loading][wire\:loading], [wire\:loading\.delay][wire\:loading\.delay], [wire\:loading\.inline-block][wire\:loading\.inline-block], [wire\:loading\.inline][wire\:loading\.inline], [wire\:loading\.block][wire\:loading\.block], [wire\:loading\.flex][wire\:loading\.flex], [wire\:loading\.table][wire\:loading\.table], [wire\:loading\.grid][wire\:loading\.grid], [wire\:loading\.inline-flex][wire\:loading\.inline-flex] {display: none;}[wire\:loading\.delay\.none][wire\:loading\.delay\.none], [wire\:loading\.delay\.shortest][wire\:loading\.delay\.shortest], [wire\:loading\.delay\.shorter][wire\:loading\.delay\.shorter], [wire\:loading\.delay\.short][wire\:loading\.delay\.short], [wire\:loading\.delay\.default][wire\:loading\.delay\.default], [wire\:loading\.delay\.long][wire\:loading\.delay\.long], [wire\:loading\.delay\.longer][wire\:loading\.delay\.longer], [wire\:loading\.delay\.longest][wire\:loading\.delay\.longest] {display: none;}[wire\:offline][wire\:offline] {display: none;}[wire\:dirty]:not(textarea):not(input):not(select) {display: none;}:root {--livewire-progress-bar-color: #2299dd;}[x-cloak] {display: none !important;}</style>
    <style>/* Make clicks pass-through */

    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: var(--livewire-progress-bar-color, #29d);

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    /* Fancy blur effect */
    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px var(--livewire-progress-bar-color, #29d), 0 0 5px var(--livewire-progress-bar-color, #29d);
      opacity: 1.0;

      -webkit-transform: rotate(3deg) translate(0px, -4px);
          -ms-transform: rotate(3deg) translate(0px, -4px);
              transform: rotate(3deg) translate(0px, -4px);
    }

    /* Remove these to get rid of the spinner */
    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: var(--livewire-progress-bar-color, #29d);
      border-left-color: var(--livewire-progress-bar-color, #29d);
      border-radius: 50%;

      -webkit-animation: nprogress-spinner 400ms linear infinite;
              animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
      0%   { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style><style>/* Make clicks pass-through */

    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: var(--livewire-progress-bar-color, #29d);

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    /* Fancy blur effect */
    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px var(--livewire-progress-bar-color, #29d), 0 0 5px var(--livewire-progress-bar-color, #29d);
      opacity: 1.0;

      -webkit-transform: rotate(3deg) translate(0px, -4px);
          -ms-transform: rotate(3deg) translate(0px, -4px);
              transform: rotate(3deg) translate(0px, -4px);
    }

    /* Remove these to get rid of the spinner */
    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: var(--livewire-progress-bar-color, #29d);
      border-left-color: var(--livewire-progress-bar-color, #29d);
      border-radius: 50%;

      -webkit-animation: nprogress-spinner 400ms linear infinite;
              animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
      0%   { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style><style>.ce-hint--align-start{text-align:left}.ce-hint--align-center{text-align:center}.ce-hint__description{opacity:.6;margin-top:3px}</style><style type="text/css">.cdx-notify--error{background:#fffbfb!important}.cdx-notify--error::before{background:#fb5d5d!important}.cdx-notify__input{max-width:130px;padding:5px 10px;background:#f7f7f7;border:0;border-radius:3px;font-size:13px;color:#656b7c;outline:0}.cdx-notify__input:-ms-input-placeholder{color:#656b7c}.cdx-notify__input::placeholder{color:#656b7c}.cdx-notify__input:focus:-ms-input-placeholder{color:rgba(101,107,124,.3)}.cdx-notify__input:focus::placeholder{color:rgba(101,107,124,.3)}.cdx-notify__button{border:none;border-radius:3px;font-size:13px;padding:5px 10px;cursor:pointer}.cdx-notify__button:last-child{margin-left:10px}.cdx-notify__button--cancel{background:#f2f5f7;box-shadow:0 2px 1px 0 rgba(16,19,29,0);color:#656b7c}.cdx-notify__button--cancel:hover{background:#eee}.cdx-notify__button--confirm{background:#34c992;box-shadow:0 1px 1px 0 rgba(18,49,35,.05);color:#fff}.cdx-notify__button--confirm:hover{background:#33b082}.cdx-notify__btns-wrapper{display:-ms-flexbox;display:flex;-ms-flex-flow:row nowrap;flex-flow:row nowrap;margin-top:5px}.cdx-notify__cross{position:absolute;top:5px;right:5px;width:10px;height:10px;padding:5px;opacity:.54;cursor:pointer}.cdx-notify__cross::after,.cdx-notify__cross::before{content:'';position:absolute;left:9px;top:5px;height:12px;width:2px;background:#575d67}.cdx-notify__cross::before{transform:rotate(-45deg)}.cdx-notify__cross::after{transform:rotate(45deg)}.cdx-notify__cross:hover{opacity:1}.cdx-notifies{position:fixed;z-index:2;bottom:20px;left:20px;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Helvetica Neue",sans-serif}.cdx-notify{position:relative;width:220px;margin-top:15px;padding:13px 16px;background:#fff;box-shadow:0 11px 17px 0 rgba(23,32,61,.13);border-radius:5px;font-size:14px;line-height:1.4em;word-wrap:break-word}.cdx-notify::before{content:'';position:absolute;display:block;top:0;left:0;width:3px;height:calc(100% - 6px);margin:3px;border-radius:5px;background:0 0}@keyframes bounceIn{0%{opacity:0;transform:scale(.3)}50%{opacity:1;transform:scale(1.05)}70%{transform:scale(.9)}100%{transform:scale(1)}}.cdx-notify--bounce-in{animation-name:bounceIn;animation-duration:.6s;animation-iteration-count:1}.cdx-notify--success{background:#fafffe!important}.cdx-notify--success::before{background:#41ffb1!important}</style><style>.ce-paragraph{line-height:1.6em;outline:none}.ce-block:only-of-type .ce-paragraph[data-placeholder-active]:empty:before,.ce-block:only-of-type .ce-paragraph[data-placeholder-active][data-empty=true]:before{content:attr(data-placeholder-active)}.ce-paragraph p:first-of-type{margin-top:0}.ce-paragraph p:last-of-type{margin-bottom:0}</style><style>.ce-header{padding:.6em 0 3px;margin:0;line-height:1.25em;outline:none}.ce-header p,.ce-header div{padding:0!important;margin:0!important}</style><style>.cdx-list{margin:0;padding-left:40px;outline:none}.cdx-list__item{padding:5.5px 0 5.5px 3px;line-height:1.6em}.cdx-list--unordered{list-style:disc}.cdx-list--ordered{list-style:decimal}.cdx-list-settings{display:flex}.cdx-list-settings .cdx-settings-button{width:50%}</style><style>.cdx-quote-icon svg{transform:rotate(180deg)}.cdx-quote{margin:0}.cdx-quote__text{min-height:158px;margin-bottom:10px}.cdx-quote [contentEditable=true][data-placeholder]:before{position:absolute;content:attr(data-placeholder);color:#707684;font-weight:400;opacity:0}.cdx-quote [contentEditable=true][data-placeholder]:empty:before{opacity:1}.cdx-quote [contentEditable=true][data-placeholder]:empty:focus:before{opacity:0}.cdx-quote-settings{display:flex}.cdx-quote-settings .cdx-settings-button{width:50%}</style><style>.ce-delimiter{line-height:1.6em;width:100%;text-align:center}.ce-delimiter:before{display:inline-block;content:"***";font-size:30px;line-height:65px;height:30px;letter-spacing:.2em}</style><style>.cdx-warning{position:relative}@media all and (min-width: 736px){.cdx-warning{padding-left:36px}}.cdx-warning [contentEditable=true][data-placeholder]:before{position:absolute;content:attr(data-placeholder);color:#707684;font-weight:400;opacity:0}.cdx-warning [contentEditable=true][data-placeholder]:empty:before{opacity:1}.cdx-warning [contentEditable=true][data-placeholder]:empty:focus:before{opacity:0}.cdx-warning:before{content:"";background-image:url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='5' y='5' width='14' height='14' rx='4' stroke='black' stroke-width='2'/%3E%3Cline x1='12' y1='9' x2='12' y2='12' stroke='black' stroke-width='2' stroke-linecap='round'/%3E%3Cpath d='M12 15.02V15.01' stroke='black' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");width:24px;height:24px;background-size:24px 24px;position:absolute;margin-top:8px;left:0}@media all and (max-width: 735px){.cdx-warning:before{display:none}}.cdx-warning__message{min-height:85px}.cdx-warning__title{margin-bottom:6px}</style><style>.ce-code__textarea{min-height:200px;font-family:Menlo,Monaco,Consolas,Courier New,monospace;color:#41314e;line-height:1.6em;font-size:12px;background:#f8f7fa;border:1px solid #f1f1f4;box-shadow:none;white-space:pre;word-wrap:normal;overflow-x:auto;resize:vertical}</style><style>.ce-rawtool__textarea{min-height:200px;resize:vertical;border-radius:8px;border:0;background-color:#1e2128;font-family:Menlo,Monaco,Consolas,Courier New,monospace;font-size:12px;line-height:1.6;letter-spacing:-.2px;color:#a1a7b6;overscroll-behavior:contain}</style><style>.cdx-checklist{gap:6px;display:flex;flex-direction:column}.cdx-checklist__item{display:flex;box-sizing:content-box;align-items:flex-start}.cdx-checklist__item-text{outline:none;flex-grow:1;line-height:1.57em}.cdx-checklist__item-checkbox{width:22px;height:22px;display:flex;align-items:center;margin-right:8px;margin-top:calc(.785em - 11px);cursor:pointer}.cdx-checklist__item-checkbox svg{opacity:0;height:20px;width:20px;position:absolute;left:-1px;top:-1px;max-height:20px}@media (hover: hover){.cdx-checklist__item-checkbox:not(.cdx-checklist__item-checkbox--no-hover):hover .cdx-checklist__item-checkbox-check svg{opacity:1}}.cdx-checklist__item-checkbox-check{cursor:pointer;display:inline-block;flex-shrink:0;position:relative;width:20px;height:20px;box-sizing:border-box;margin-left:0;border-radius:5px;border:1px solid #C9C9C9;background:#fff}.cdx-checklist__item-checkbox-check:before{content:"";position:absolute;top:0;right:0;bottom:0;left:0;border-radius:100%;background-color:#369fff;visibility:hidden;pointer-events:none;transform:scale(1);transition:transform .4s ease-out,opacity .4s}@media (hover: hover){.cdx-checklist__item--checked .cdx-checklist__item-checkbox:not(.cdx-checklist__item--checked .cdx-checklist__item-checkbox--no-hover):hover .cdx-checklist__item-checkbox-check{background:#0059AB;border-color:#0059ab}}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check{background:#369FFF;border-color:#369fff}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check svg{opacity:1}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check svg path{stroke:#fff}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check:before{opacity:0;visibility:visible;transform:scale(2.5)}</style><style>.ce-paragraph{line-height:1.6em;outline:none}.ce-block:only-of-type .ce-paragraph[data-placeholder-active]:empty:before,.ce-block:only-of-type .ce-paragraph[data-placeholder-active][data-empty=true]:before{content:attr(data-placeholder-active)}.ce-paragraph p:first-of-type{margin-top:0}.ce-paragraph p:last-of-type{margin-bottom:0}</style><style>.image-tool{--bg-color: #cdd1e0;--front-color: #388ae5;--border-color: #e8e8eb}.image-tool__image{border-radius:3px;overflow:hidden;margin-bottom:10px}.image-tool__image-picture{max-width:100%;vertical-align:bottom;display:block}.image-tool__image-preloader{width:50px;height:50px;border-radius:50%;background-size:cover;margin:auto;position:relative;background-color:var(--bg-color);background-position:center center}.image-tool__image-preloader:after{content:"";position:absolute;z-index:3;width:60px;height:60px;border-radius:50%;border:2px solid var(--bg-color);border-top-color:var(--front-color);left:50%;top:50%;margin-top:-30px;margin-left:-30px;animation:image-preloader-spin 2s infinite linear;box-sizing:border-box}.image-tool__caption[contentEditable=true][data-placeholder]:before{position:absolute!important;content:attr(data-placeholder);color:#707684;font-weight:400;display:none}.image-tool__caption[contentEditable=true][data-placeholder]:empty:before{display:block}.image-tool__caption[contentEditable=true][data-placeholder]:empty:focus:before{display:none}.image-tool--empty .image-tool__image,.image-tool--empty .image-tool__caption,.image-tool--loading .image-tool__caption{display:none}.image-tool .cdx-button{display:flex;align-items:center;justify-content:center}.image-tool .cdx-button svg{height:auto;margin:0 6px 0 0}.image-tool--filled .cdx-button,.image-tool--filled .image-tool__image-preloader{display:none}.image-tool--loading .image-tool__image{min-height:200px;display:flex;border:1px solid var(--border-color);background-color:#fff}.image-tool--loading .image-tool__image-picture,.image-tool--loading .cdx-button{display:none}.image-tool--withBorder .image-tool__image{border:1px solid var(--border-color)}.image-tool--withBackground .image-tool__image{padding:15px;background:var(--bg-color)}.image-tool--withBackground .image-tool__image-picture{max-width:60%;margin:0 auto}.image-tool--stretched .image-tool__image-picture{width:100%}@keyframes image-preloader-spin{0%{transform:rotate(0)}to{transform:rotate(360deg)}}</style>
    
        
        <div>
            

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="UeFLzrjalD0dK2oD6AUxSbVXIwleFJ78WOWw0ZK2">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
        <style data-fullcalendar=""></style><link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">

        
                <title>Mewayz</title>
                

        

        <!-- Favicon -->
        <link href="https://mewayz.com/media/site/favicon/YxoGJQRzTuwwpmNU3pll11wCrF0ane7BcxMRgKHE.png" rel="shortcut icon" type="image/png">

        <!-- Scripts -->
        <link rel="preload" as="style" href="https://mewayz.com/build/assets/app-fbefa9a2.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/app-1f2e534b.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/console.sidebar-bb389b0b.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/console.placeholder-ccad17d1.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/site-fa793f6e.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/create-0f8797b3.css"><link rel="preload" as="style" href="https://mewayz.com/build/assets/app-4f33a8f0.css"><link rel="modulepreload" href="https://mewayz.com/build/assets/moreUtils-eaf77fe0.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/index-7d9a9c74.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/app-f78797f2.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/_commonjs-dynamic-modules-a536689c.js"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-fbefa9a2.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-1f2e534b.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/console.sidebar-bb389b0b.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/console.placeholder-ccad17d1.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/site-fa793f6e.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/create-0f8797b3.css" data-navigate-track="reload"><link rel="stylesheet" href="https://mewayz.com/build/assets/app-4f33a8f0.css" data-navigate-track="reload"><script type="module" src="https://mewayz.com/build/assets/moreUtils-eaf77fe0.js" data-navigate-track="reload"></script><script type="module" src="https://mewayz.com/build/assets/app-f78797f2.js" data-navigate-track="reload"></script>

        <!-- Livewire Styles --><style>[wire\:loading][wire\:loading], [wire\:loading\.delay][wire\:loading\.delay], [wire\:loading\.inline-block][wire\:loading\.inline-block], [wire\:loading\.inline][wire\:loading\.inline], [wire\:loading\.block][wire\:loading\.block], [wire\:loading\.flex][wire\:loading\.flex], [wire\:loading\.table][wire\:loading\.table], [wire\:loading\.grid][wire\:loading\.grid], [wire\:loading\.inline-flex][wire\:loading\.inline-flex] {display: none;}[wire\:loading\.delay\.none][wire\:loading\.delay\.none], [wire\:loading\.delay\.shortest][wire\:loading\.delay\.shortest], [wire\:loading\.delay\.shorter][wire\:loading\.delay\.shorter], [wire\:loading\.delay\.short][wire\:loading\.delay\.short], [wire\:loading\.delay\.default][wire\:loading\.delay\.default], [wire\:loading\.delay\.long][wire\:loading\.delay\.long], [wire\:loading\.delay\.longer][wire\:loading\.delay\.longer], [wire\:loading\.delay\.longest][wire\:loading\.delay\.longest] {display: none;}[wire\:offline][wire\:offline] {display: none;}[wire\:dirty]:not(textarea):not(input):not(select) {display: none;}:root {--livewire-progress-bar-color: #2299dd;}[x-cloak] {display: none !important;}</style>
    <style>/* Make clicks pass-through */

    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: var(--livewire-progress-bar-color, #29d);

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    /* Fancy blur effect */
    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px var(--livewire-progress-bar-color, #29d), 0 0 5px var(--livewire-progress-bar-color, #29d);
      opacity: 1.0;

      -webkit-transform: rotate(3deg) translate(0px, -4px);
          -ms-transform: rotate(3deg) translate(0px, -4px);
              transform: rotate(3deg) translate(0px, -4px);
    }

    /* Remove these to get rid of the spinner */
    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: var(--livewire-progress-bar-color, #29d);
      border-left-color: var(--livewire-progress-bar-color, #29d);
      border-radius: 50%;

      -webkit-animation: nprogress-spinner 400ms linear infinite;
              animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
      0%   { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style><style>/* Make clicks pass-through */

    #nprogress {
      pointer-events: none;
    }

    #nprogress .bar {
      background: var(--livewire-progress-bar-color, #29d);

      position: fixed;
      z-index: 1031;
      top: 0;
      left: 0;

      width: 100%;
      height: 2px;
    }

    /* Fancy blur effect */
    #nprogress .peg {
      display: block;
      position: absolute;
      right: 0px;
      width: 100px;
      height: 100%;
      box-shadow: 0 0 10px var(--livewire-progress-bar-color, #29d), 0 0 5px var(--livewire-progress-bar-color, #29d);
      opacity: 1.0;

      -webkit-transform: rotate(3deg) translate(0px, -4px);
          -ms-transform: rotate(3deg) translate(0px, -4px);
              transform: rotate(3deg) translate(0px, -4px);
    }

    /* Remove these to get rid of the spinner */
    #nprogress .spinner {
      display: block;
      position: fixed;
      z-index: 1031;
      top: 15px;
      right: 15px;
    }

    #nprogress .spinner-icon {
      width: 18px;
      height: 18px;
      box-sizing: border-box;

      border: solid 2px transparent;
      border-top-color: var(--livewire-progress-bar-color, #29d);
      border-left-color: var(--livewire-progress-bar-color, #29d);
      border-radius: 50%;

      -webkit-animation: nprogress-spinner 400ms linear infinite;
              animation: nprogress-spinner 400ms linear infinite;
    }

    .nprogress-custom-parent {
      overflow: hidden;
      position: relative;
    }

    .nprogress-custom-parent #nprogress .spinner,
    .nprogress-custom-parent #nprogress .bar {
      position: absolute;
    }

    @-webkit-keyframes nprogress-spinner {
      0%   { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }
    @keyframes nprogress-spinner {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    </style><style>.ce-hint--align-start{text-align:left}.ce-hint--align-center{text-align:center}.ce-hint__description{opacity:.6;margin-top:3px}</style><style type="text/css">.cdx-notify--error{background:#fffbfb!important}.cdx-notify--error::before{background:#fb5d5d!important}.cdx-notify__input{max-width:130px;padding:5px 10px;background:#f7f7f7;border:0;border-radius:3px;font-size:13px;color:#656b7c;outline:0}.cdx-notify__input:-ms-input-placeholder{color:#656b7c}.cdx-notify__input::placeholder{color:#656b7c}.cdx-notify__input:focus:-ms-input-placeholder{color:rgba(101,107,124,.3)}.cdx-notify__input:focus::placeholder{color:rgba(101,107,124,.3)}.cdx-notify__button{border:none;border-radius:3px;font-size:13px;padding:5px 10px;cursor:pointer}.cdx-notify__button:last-child{margin-left:10px}.cdx-notify__button--cancel{background:#f2f5f7;box-shadow:0 2px 1px 0 rgba(16,19,29,0);color:#656b7c}.cdx-notify__button--cancel:hover{background:#eee}.cdx-notify__button--confirm{background:#34c992;box-shadow:0 1px 1px 0 rgba(18,49,35,.05);color:#fff}.cdx-notify__button--confirm:hover{background:#33b082}.cdx-notify__btns-wrapper{display:-ms-flexbox;display:flex;-ms-flex-flow:row nowrap;flex-flow:row nowrap;margin-top:5px}.cdx-notify__cross{position:absolute;top:5px;right:5px;width:10px;height:10px;padding:5px;opacity:.54;cursor:pointer}.cdx-notify__cross::after,.cdx-notify__cross::before{content:'';position:absolute;left:9px;top:5px;height:12px;width:2px;background:#575d67}.cdx-notify__cross::before{transform:rotate(-45deg)}.cdx-notify__cross::after{transform:rotate(45deg)}.cdx-notify__cross:hover{opacity:1}.cdx-notifies{position:fixed;z-index:2;bottom:20px;left:20px;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen,Ubuntu,Cantarell,"Fira Sans","Droid Sans","Helvetica Neue",sans-serif}.cdx-notify{position:relative;width:220px;margin-top:15px;padding:13px 16px;background:#fff;box-shadow:0 11px 17px 0 rgba(23,32,61,.13);border-radius:5px;font-size:14px;line-height:1.4em;word-wrap:break-word}.cdx-notify::before{content:'';position:absolute;display:block;top:0;left:0;width:3px;height:calc(100% - 6px);margin:3px;border-radius:5px;background:0 0}@keyframes bounceIn{0%{opacity:0;transform:scale(.3)}50%{opacity:1;transform:scale(1.05)}70%{transform:scale(.9)}100%{transform:scale(1)}}.cdx-notify--bounce-in{animation-name:bounceIn;animation-duration:.6s;animation-iteration-count:1}.cdx-notify--success{background:#fafffe!important}.cdx-notify--success::before{background:#41ffb1!important}</style><style>.ce-paragraph{line-height:1.6em;outline:none}.ce-block:only-of-type .ce-paragraph[data-placeholder-active]:empty:before,.ce-block:only-of-type .ce-paragraph[data-placeholder-active][data-empty=true]:before{content:attr(data-placeholder-active)}.ce-paragraph p:first-of-type{margin-top:0}.ce-paragraph p:last-of-type{margin-bottom:0}</style><style>.ce-header{padding:.6em 0 3px;margin:0;line-height:1.25em;outline:none}.ce-header p,.ce-header div{padding:0!important;margin:0!important}</style><style>.cdx-list{margin:0;padding-left:40px;outline:none}.cdx-list__item{padding:5.5px 0 5.5px 3px;line-height:1.6em}.cdx-list--unordered{list-style:disc}.cdx-list--ordered{list-style:decimal}.cdx-list-settings{display:flex}.cdx-list-settings .cdx-settings-button{width:50%}</style><style>.cdx-quote-icon svg{transform:rotate(180deg)}.cdx-quote{margin:0}.cdx-quote__text{min-height:158px;margin-bottom:10px}.cdx-quote [contentEditable=true][data-placeholder]:before{position:absolute;content:attr(data-placeholder);color:#707684;font-weight:400;opacity:0}.cdx-quote [contentEditable=true][data-placeholder]:empty:before{opacity:1}.cdx-quote [contentEditable=true][data-placeholder]:empty:focus:before{opacity:0}.cdx-quote-settings{display:flex}.cdx-quote-settings .cdx-settings-button{width:50%}</style><style>.ce-delimiter{line-height:1.6em;width:100%;text-align:center}.ce-delimiter:before{display:inline-block;content:"***";font-size:30px;line-height:65px;height:30px;letter-spacing:.2em}</style><style>.cdx-warning{position:relative}@media all and (min-width: 736px){.cdx-warning{padding-left:36px}}.cdx-warning [contentEditable=true][data-placeholder]:before{position:absolute;content:attr(data-placeholder);color:#707684;font-weight:400;opacity:0}.cdx-warning [contentEditable=true][data-placeholder]:empty:before{opacity:1}.cdx-warning [contentEditable=true][data-placeholder]:empty:focus:before{opacity:0}.cdx-warning:before{content:"";background-image:url("data:image/svg+xml,%3Csvg width='24' height='24' viewBox='0 0 24 24' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect x='5' y='5' width='14' height='14' rx='4' stroke='black' stroke-width='2'/%3E%3Cline x1='12' y1='9' x2='12' y2='12' stroke='black' stroke-width='2' stroke-linecap='round'/%3E%3Cpath d='M12 15.02V15.01' stroke='black' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");width:24px;height:24px;background-size:24px 24px;position:absolute;margin-top:8px;left:0}@media all and (max-width: 735px){.cdx-warning:before{display:none}}.cdx-warning__message{min-height:85px}.cdx-warning__title{margin-bottom:6px}</style><style>.ce-code__textarea{min-height:200px;font-family:Menlo,Monaco,Consolas,Courier New,monospace;color:#41314e;line-height:1.6em;font-size:12px;background:#f8f7fa;border:1px solid #f1f1f4;box-shadow:none;white-space:pre;word-wrap:normal;overflow-x:auto;resize:vertical}</style><style>.ce-rawtool__textarea{min-height:200px;resize:vertical;border-radius:8px;border:0;background-color:#1e2128;font-family:Menlo,Monaco,Consolas,Courier New,monospace;font-size:12px;line-height:1.6;letter-spacing:-.2px;color:#a1a7b6;overscroll-behavior:contain}</style><style>.cdx-checklist{gap:6px;display:flex;flex-direction:column}.cdx-checklist__item{display:flex;box-sizing:content-box;align-items:flex-start}.cdx-checklist__item-text{outline:none;flex-grow:1;line-height:1.57em}.cdx-checklist__item-checkbox{width:22px;height:22px;display:flex;align-items:center;margin-right:8px;margin-top:calc(.785em - 11px);cursor:pointer}.cdx-checklist__item-checkbox svg{opacity:0;height:20px;width:20px;position:absolute;left:-1px;top:-1px;max-height:20px}@media (hover: hover){.cdx-checklist__item-checkbox:not(.cdx-checklist__item-checkbox--no-hover):hover .cdx-checklist__item-checkbox-check svg{opacity:1}}.cdx-checklist__item-checkbox-check{cursor:pointer;display:inline-block;flex-shrink:0;position:relative;width:20px;height:20px;box-sizing:border-box;margin-left:0;border-radius:5px;border:1px solid #C9C9C9;background:#fff}.cdx-checklist__item-checkbox-check:before{content:"";position:absolute;top:0;right:0;bottom:0;left:0;border-radius:100%;background-color:#369fff;visibility:hidden;pointer-events:none;transform:scale(1);transition:transform .4s ease-out,opacity .4s}@media (hover: hover){.cdx-checklist__item--checked .cdx-checklist__item-checkbox:not(.cdx-checklist__item--checked .cdx-checklist__item-checkbox--no-hover):hover .cdx-checklist__item-checkbox-check{background:#0059AB;border-color:#0059ab}}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check{background:#369FFF;border-color:#369fff}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check svg{opacity:1}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check svg path{stroke:#fff}.cdx-checklist__item--checked .cdx-checklist__item-checkbox-check:before{opacity:0;visibility:visible;transform:scale(2.5)}</style><style>.ce-paragraph{line-height:1.6em;outline:none}.ce-block:only-of-type .ce-paragraph[data-placeholder-active]:empty:before,.ce-block:only-of-type .ce-paragraph[data-placeholder-active][data-empty=true]:before{content:attr(data-placeholder-active)}.ce-paragraph p:first-of-type{margin-top:0}.ce-paragraph p:last-of-type{margin-bottom:0}</style><style>.image-tool{--bg-color: #cdd1e0;--front-color: #388ae5;--border-color: #e8e8eb}.image-tool__image{border-radius:3px;overflow:hidden;margin-bottom:10px}.image-tool__image-picture{max-width:100%;vertical-align:bottom;display:block}.image-tool__image-preloader{width:50px;height:50px;border-radius:50%;background-size:cover;margin:auto;position:relative;background-color:var(--bg-color);background-position:center center}.image-tool__image-preloader:after{content:"";position:absolute;z-index:3;width:60px;height:60px;border-radius:50%;border:2px solid var(--bg-color);border-top-color:var(--front-color);left:50%;top:50%;margin-top:-30px;margin-left:-30px;animation:image-preloader-spin 2s infinite linear;box-sizing:border-box}.image-tool__caption[contentEditable=true][data-placeholder]:before{position:absolute!important;content:attr(data-placeholder);color:#707684;font-weight:400;display:none}.image-tool__caption[contentEditable=true][data-placeholder]:empty:before{display:block}.image-tool__caption[contentEditable=true][data-placeholder]:empty:focus:before{display:none}.image-tool--empty .image-tool__image,.image-tool--empty .image-tool__caption,.image-tool--loading .image-tool__caption{display:none}.image-tool .cdx-button{display:flex;align-items:center;justify-content:center}.image-tool .cdx-button svg{height:auto;margin:0 6px 0 0}.image-tool--filled .cdx-button,.image-tool--filled .image-tool__image-preloader{display:none}.image-tool--loading .image-tool__image{min-height:200px;display:flex;border:1px solid var(--border-color);background-color:#fff}.image-tool--loading .image-tool__image-picture,.image-tool--loading .cdx-button{display:none}.image-tool--withBorder .image-tool__image{border:1px solid var(--border-color)}.image-tool--withBackground .image-tool__image{padding:15px;background:var(--bg-color)}.image-tool--withBackground .image-tool__image-picture{max-width:60%;margin:0 auto}.image-tool--stretched .image-tool__image-picture{width:100%}@keyframes image-preloader-spin{0%{transform:rotate(0)}to{transform:rotate(360deg)}}</style>
    
        
        <div>
            

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mewayz - All-in-One Business Platform for Creators &amp; Entrepreneurs</title>
    <meta name="description" content="Complete business platform with social media management, e-commerce, course creation, CRM, and more. Everything you need to grow your business in one place.">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-bg: #0a0a0a;
            --secondary-bg: #1a1a1a;
            --card-bg: #111111;
            --accent-red: #ff3333;
            --accent-red-hover: #ff1a1a;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --text-muted: #808080;
            --border-color: #333333;
            --gradient-primary: linear-gradient(135deg, #ff3333 0%, #cc0000 100%);
            --gradient-dark: linear-gradient(135deg, #1a1a1a 0%, #0a0a0a 100%);
            --shadow-glow: 0 0 30px rgba(255, 51, 51, 0.1);
            --shadow-card: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--primary-bg);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(10, 10, 10, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            z-index: 1000;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: var(--accent-red);
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent-red);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-glow);
        }

        .btn-primary:hover {
            background: transparent;
   	    color: red;
    	    border-color: var(--accent-red);
	    transform: translateY(-2px);
            box-shadow: 0 0 40px rgba(255, 51, 51, 0.2);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            border-color: var(--accent-red);
            color: var(--accent-red);
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-dark);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 50%, rgba(255, 51, 51, 0.05) 0%, transparent 70%);
        }

        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: white;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .hero-text p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.7;
        }

  	.hero-buttons {
    		display: flex;
		gap: 1rem;
            	margin-bottom: 3rem;
        }
  	.hero-buttons2 {
	     display: flex;
	      margin-left: 36%;
		gap: 1rem;
            	margin-bottom: 3rem;
        }

        .btn-hero {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 10px;
        }

        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
	    text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-red);
            display: block;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .hero-image {
            position: relative;
        }

        .hero-image img {
            width: 100%;
            height: auto;
            border-radius: 20px;
            box-shadow: var(--shadow-card);
            transition: transform 0.3s ease;
        }

        .hero-image:hover img {
            transform: scale(1.02);
        }

        /* Features Section */
        .features {
            padding: 8rem 0;
            background: var(--secondary-bg);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-title {
	    text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: white;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
	    text-align: center;
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent-red);
            box-shadow: var(--shadow-card);
        }

        .feature-card:hover::before {
            transform: scaleX(1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .feature-card p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            padding-left: 1.5rem;
            position: relative;
        }

        .feature-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--accent-red);
            font-weight: bold;
        }

        /* Platform Overview */
        .platform-overview {
            padding: 8rem 0;
            background: var(--primary-bg);
        }

        .overview-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            margin-bottom: 4rem;
        }

        .overview-content h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .overview-content p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .overview-image {
            position: relative;
        }

        .overview-image img {
            width: 100%;
            height: auto;
            border-radius: 16px;
            box-shadow: var(--shadow-card);
        }

        /* Pricing Section */
        .pricing {
            padding: 8rem 0;
            background: var(--secondary-bg);
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .pricing-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2.5rem;
            text-align: center;
            position: relative;
            transition: all 0.3s ease;
        }

        .pricing-card.featured {
            border-color: var(--accent-red);
            transform: scale(1.05);
        }

        .pricing-card.featured::before {
            content: 'Most Popular';
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gradient-primary);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-card);
        }

        .pricing-card.featured:hover {
            transform: scale(1.05) translateY(-10px);
        }

        .plan-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .plan-price {
            font-size: 3rem;
            font-weight: 800;
            color: var(--accent-red);
            margin-bottom: 0.5rem;
        }

        .plan-period {
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .plan-features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .plan-features li {
            padding: 0.5rem 0;
            color: var(--text-secondary);
            position: relative;
            padding-left: 1.5rem;
        }

        .plan-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--accent-red);
            font-weight: bold;
        }

        /* CTA Section */
        .cta {
            padding: 6rem 0;
            background: var(--gradient-dark);
            text-align: center;
            position: relative;
        }

        .cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
                background: radial-gradient(circle at 50% 50%, rgba(255, 51, 51, 0.1) 0%, #000000 70%);
        }

        .cta-content {
            position: relative;
            z-index: 1;
        }

        .cta h2 {
	    text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: white;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cta p {
	    text-align: center;
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        .footer {
            background: var(--card-bg);
            border-top: 1px solid var(--border-color);
            padding: 4rem 0 2rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-section h4 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--accent-red);
        }

        .footer-bottom {
            border-top: 1px solid var(--border-color);
            padding-top: 2rem;
            text-align: center;
            color: var(--text-muted);
        }

        /* Mobile Menu */
        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }

            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(10, 10, 10, 0.98);
                backdrop-filter: blur(20px);
                flex-direction: column;
                padding: 2rem;
                border-top: 1px solid var(--border-color);
            }

            .nav-links.active {
                display: flex;
            }

            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
                    margin-top: 10%;
                gap: 3rem;
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            .hero-buttons2 {
                flex-direction: column;
                align-items: center;
            }

            .overview-grid {
                grid-template-columns: 1fr;
                gap: 3rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .pricing-grid {
                grid-template-columns: 1fr;
            }

            .pricing-card.featured {
                transform: none;
            }

            .pricing-card.featured:hover {
                transform: translateY(-10px);
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }

        /* Floating Elements */
        .floating-element {
            position: absolute;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(255, 51, 51, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .floating-element:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }
    </style>


    <!-- Header -->
    @include('pages.syn-new-header.syn-new-header')

    <!-- Hero Section -->
    <section class="hero">
        <div class="floating-element" style="transform: translateY(-300px);"></div>
        <div class="floating-element" style="transform: translateY(-360px);"></div>
        <div class="floating-element" style="transform: translateY(-420px);"></div>
        
        <div class="hero-content">
            <div class="hero-text">
                <h1 style="
    margin-top: 30%;
">All-in-One Business Platform for Modern Creators</h1>
                <p>Transform your business with our comprehensive platform. Social media management, e-commerce, course creation, CRM, and 15+ powerful tools in one unified solution.</p>
                <div class="hero-buttons">
                    <a href="https://mewayz.com/register" class="btn btn-primary btn-hero">Start Free Trial</a>
                    <a href="#features" class="btn btn-secondary btn-hero">Explore Features</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">15</span>
                        <span class="stat-label">Integrated Tools</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">99.9%</span>
                        <span class="stat-label">Uptime</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Support</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://mewayz.com/assets/image/dragdrop/21.png" alt="Mewayz Platform Dashboard" loading="lazy">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header animate-on-scroll animated">
                <h2 class="section-title">Comprehensive Business Tools</h2>
                <p class="section-subtitle">Everything you need to build, manage, and scale your business - all in one powerful platform</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card animate-on-scroll animated">
                    <div class="feature-icon">📱</div>
                    <h3>Social Media Management</h3>
                    <p>Complete Instagram database with advanced filtering, automated posting, and engagement analytics across all major platforms.</p>
                    <ul class="feature-list">
                        <li>Instagram lead generation database</li>
                        <li>Multi-platform scheduling</li>
                        <li>Automated bio link creation</li>
                        <li>Hashtag research &amp; analytics</li>
                        <li>Content templates</li>
                    </ul>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">🔗</div>
                    <h3>Link in Bio Builder</h3>
                    <p>Drag-and-drop builder with responsive templates, custom domains, and comprehensive analytics for maximum conversions.</p>
                    <ul class="feature-list">
                        <li>Visual drag-and-drop builder</li>
                        <li>Industry-specific templates</li>
                        <li>Custom domain support</li>
                        <li>Real-time analytics</li>
                        <li>QR code generation</li>
                    </ul>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">🎓</div>
                    <h3>Course &amp; Community Platform</h3>
                    <p>Skool-like course creation with video hosting, community features, live streaming, and gamification elements.</p>
                    <ul class="feature-list">
                        <li>Video hosting &amp; player</li>
                        <li>Interactive quizzes &amp; assignments</li>
                        <li>Community discussions</li>
                        <li>Live streaming integration</li>
                        <li>Progress tracking &amp; certificates</li>
                    </ul>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">🛒</div>
                    <h3>E-commerce &amp; Marketplace</h3>
                    <p>Amazon-style marketplace with individual storefronts, inventory management, and integrated payment processing.</p>
                    <ul class="feature-list">
                        <li>Multi-vendor marketplace</li>
                        <li>Custom storefronts</li>
                        <li>Inventory management</li>
                        <li>Payment processing</li>
                        <li>Review &amp; rating system</li>
                    </ul>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">📊</div>
                    <h3>CRM &amp; Email Marketing</h3>
                    <p>Advanced lead management with automated workflows, email campaigns, and comprehensive analytics.</p>
                    <ul class="feature-list">
                        <li>Contact management &amp; scoring</li>
                        <li>Automated email sequences</li>
                        <li>Visual sales pipeline</li>
                        <li>A/B testing capabilities</li>
                        <li>Deliverability optimization</li>
                    </ul>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">🌐</div>
                    <h3>Website Builder</h3>
                    <p>No-code website builder with responsive templates, SEO optimization, and e-commerce integration.</p>
                    <ul class="feature-list">
                        <li>Drag-and-drop interface</li>
                        <li>SEO optimization tools</li>
                        <li>Mobile-responsive design</li>
                        <li>Third-party integrations</li>
                        <li>Custom code injection</li>
                    </ul>
                </div>

                <div class="feature-card animate-on-scroll animated">
                    <div class="feature-icon">📅</div>
                    <h3>Booking System</h3>
                    <p>Comprehensive appointment scheduling with calendar integration, automated reminders, and payment processing.</p>
                    <ul class="feature-list">
                        <li>Calendar integration</li>
                        <li>Automated reminders</li>
                        <li>Payment collection</li>
                        <li>Staff management</li>
                        <li>Waitlist functionality</li>
                    </ul>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">🛡️</div>
                    <h3>Escrow System</h3>
                    <p>Secure transaction platform for digital products and services with dispute resolution and milestone payments.</p>
                    <ul class="feature-list">
                        <li>Multi-purpose escrow</li>
                        <li>Dispute resolution</li>
                        <li>Milestone payments</li>
                        <li>Identity verification</li>
                        <li>Transaction history</li>
                    </ul>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">💰</div>
                    <h3>Financial Management</h3>
                    <p>Complete invoicing system with multi-currency support, automated billing, and comprehensive reporting.</p>
                    <ul class="feature-list">
                        <li>Professional invoicing</li>
                        <li>Multi-currency support</li>
                        <li>Automated billing</li>
                        <li>Financial reporting</li>
                        <li>Accounting integrations</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Platform Overview -->
    <section class="platform-overview" id="platform">
        <div class="container">
            <div class="overview-grid">
                <div class="overview-content animate-on-scroll animated">
                    <h3>Multi-Workspace Management</h3>
                    <p>Create unlimited workspaces for different projects and businesses. Invite team members with role-based permissions and manage everything from a single dashboard.</p>
                    <a href="https://mewayz.com/register" class="btn btn-primary">Start Building</a>
                </div>
                <div class="overview-image animate-on-scroll animated">
                    <img src="https://mewayz.com/assets/image/dragdrop/22.png" alt="Workspace Management" loading="lazy">
                </div>
            </div>

            <div class="overview-grid">
                <div class="overview-image animate-on-scroll animated">
                    <img src="https://mewayz.com/assets/image/dragdrop/23.png" alt="Analytics Dashboard" loading="lazy">
                </div>
                <div class="overview-content animate-on-scroll animated">
                    <h3>Advanced Analytics &amp; Reporting</h3>
                    <p>Comprehensive analytics dashboard with custom reporting, real-time insights, and automated report delivery. Track performance across all your business tools.</p>
                    <a href="https://mewayz.com/register" class="btn btn-primary">View Demo</a>
                </div>
            </div>

            <div class="overview-grid">
                <div class="overview-content animate-on-scroll animated">
                    <h3>AI-Powered Automation</h3>
                    <p>Leverage artificial intelligence for content generation, SEO optimization, predictive analytics, and automated workflows to streamline your business operations.</p>
                    <a href="https://mewayz.com/register" class="btn btn-primary">Explore AI Features</a>
                </div>
                <div class="overview-image animate-on-scroll animated">
                    <img src="https://mewayz.com/assets/image/dragdrop/24.png" alt="AI Automation" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing" id="pricing">
        <div class="container">
            <div class="section-header animate-on-scroll animated">
                <h2 class="section-title">Choose Your Plan</h2>
                <p class="section-subtitle">Scale your business with our flexible pricing plans designed for creators and entrepreneurs</p>
            </div>
            
            <div class="pricing-grid">
                <div class="pricing-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="plan-name">Starter</div>
                    <div class="plan-price">$0</div>
                    <div class="plan-period">per month</div>
                    <ul class="plan-features">
                        <li>5 Workspaces</li>
                        <li>Basic Social Media Tools</li>
                        <li>Link in Bio Builder</li>
                        <li>Basic Website Builder</li>
                        <li>Email Support</li>
                        <li>5GB Storage</li>
                        <li>Basic Analytics</li>
                    </ul>
                    <a href="https://mewayz.com/register" class="btn btn-primary">Start now</a>
                </div>

                <div class="pricing-card featured animate-on-scroll animated" style="transform: scale(1.05);">
                    <div class="plan-name">Professional</div>
                    <div class="plan-price">$29 (Now $0)</div>
                    <div class="plan-period">per month</div>
                    <ul class="plan-features">
                        <li>Unlimited Workspaces</li>
                        <li>Full Social Media Suite</li>
                        <li>Course &amp; Community Platform</li>
                        <li>E-commerce &amp; Marketplace</li>
                        <li>CRM &amp; Email Marketing</li>
                        <li>Booking System</li>
                        <li>50GB Storage</li>
                        <li>Advanced Analytics</li>
                        <li>Priority Support</li>
                    </ul>
                    <a href="https://mewayz.com/register" class="btn btn-primary">Start now</a>
                </div>

                <div class="pricing-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="plan-name">Enterprise</div>
                    <div class="plan-price">$49 (Now $0)</div>
                    <div class="plan-period">per month</div>
                    <ul class="plan-features">
                        <li>Everything in Professional</li>
                        <li>White-label Solutions</li>
                        <li>AI-Powered Features</li>
                        <li>Escrow System</li>
                        <li>Advanced Automation</li>
                        <li>Custom Integrations</li>
                        <li>Unlimited Storage</li>
                        <li>24/7 Phone Support</li>
                        <li>Dedicated Account Manager</li>
                    </ul>
                    <a href="https://mewayz.com/register" class="btn btn-primary">Start now</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Additional Features Showcase -->
    <section class="platform-overview">
        <div class="container">
            <div class="section-header animate-on-scroll animated">
                <h2 class="section-title">More Powerful Features</h2>
                <p class="section-subtitle">Discover additional tools that make Mewayz the ultimate business platform</p>
            </div>

            <div class="overview-grid">
                <div class="overview-content animate-on-scroll animated">
                    <h3>Template Marketplace</h3>
                    <p>Access thousands of professional templates for websites, emails, social media, and courses. Create and sell your own templates to generate additional revenue streams.</p>
                    <ul class="feature-list">
                        <li>Website &amp; email templates</li>
                        <li>Social media content templates</li>
                        <li>Course templates</li>
                        <li>Monetization opportunities</li>
                    </ul>
                </div>
                <div class="overview-image animate-on-scroll animated">
                    <img src="https://mewayz.com/assets/image/dragdrop/25.png" alt="Template Marketplace" loading="lazy">
                </div>
            </div>

            <div class="overview-grid">
                <div class="overview-image animate-on-scroll animated">
                    <img src="https://mewayz.com/assets/image/dragdrop/26.png" alt="Mobile Apps" loading="lazy">
                </div>
                <div class="overview-content animate-on-scroll animated">
                    <h3>Native Mobile Applications</h3>
                    <p>Manage your business on-the-go with our native iOS and Android apps. Full functionality with offline capabilities and real-time synchronization.</p>
                    <ul class="feature-list">
                        <li>iOS &amp; Android apps</li>
                        <li>Offline functionality</li>
                        <li>Push notifications</li>
                        <li>Real-time sync</li>
                    </ul>
                </div>
            </div>

            <div class="overview-grid">
                <div class="overview-content animate-on-scroll animated">
                    <h3>Advanced Security &amp; Compliance</h3>
                    <p>Enterprise-grade security with end-to-end encryption, GDPR compliance, and regular security audits to protect your business and customer data.</p>
                    <ul class="feature-list">
                        <li>End-to-end encryption</li>
                        <li>Two-factor authentication</li>
                        <li>GDPR &amp; PCI DSS compliance</li>
                        <li>Regular security audits</li>
                    </ul>
                </div>
                <div class="overview-image animate-on-scroll animated">
                    <img src="https://zeph.to/assets/image/dragdrop/7.png" alt="Security Features" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Integration Section -->
    <section class="features">
        <div class="container">
            <div class="section-header animate-on-scroll animated">
                <h2 class="section-title">Seamless Integrations</h2>
                <p class="section-subtitle">Connect with your favorite tools and platforms for a unified workflow</p>
            </div>
            
            <div class="features-grid">
                

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">🔌</div>
                    <h3>Zapier Integration</h3>
                    <p>Connect with 3000+ applications through Zapier for unlimited automation possibilities.</p>
                    <ul class="feature-list">
                        <li>3000+ app connections</li>
                        <li>Automated workflows</li>
                        <li>Trigger-based actions</li>
                        <li>Custom integrations</li>
                    </ul>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">📈</div>
                    <h3>Analytics Integrations</h3>
                    <p>Connect with Google Analytics, Facebook Pixel, and other tracking tools for comprehensive insights.</p>
                    <ul class="feature-list">
                        <li>Google Analytics</li>
                        <li>Facebook Pixel</li>
                        <li>Custom tracking codes</li>
                        <li>Conversion tracking</li>
                    </ul>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">💳</div>
                    <h3>Payment Gateways</h3>
                    <p>Support for multiple payment processors including Stripe, PayPal, and cryptocurrency payments.</p>
                    <ul class="feature-list">
                        <li>Stripe &amp; PayPal</li>
                        <li>Apple Pay &amp; Google Pay</li>
                        <li>Cryptocurrency support</li>
                        <li>Multi-currency processing</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="platform-overview">
        <div class="container">
            <div class="section-header animate-on-scroll animated">
                <h2 class="section-title">Trusted by Thousands</h2>
                <p class="section-subtitle">See what our customers say about transforming their business with Mewayz</p>
            </div>

            <div class="features-grid">
                <div class="feature-card animate-on-scroll animated">
                    <div class="feature-icon">⭐</div>
                    <h3>"Game-changing platform"</h3>
                    <p>"Mewayz transformed how I manage my online business. Having everything in one place has saved me countless hours and significantly increased my revenue."</p>
                    <div style="margin-top: 1rem; color: var(--accent-red); font-weight: 600;">
                        - Sarah Johnson, Digital Marketer
                    </div>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">🚀</div>
                    <h3>"Incredible value"</h3>
                    <p>"The course platform alone is worth the price. Combined with all the other tools, it's an incredible value that has helped me scale my coaching business."</p>
                    <div style="margin-top: 1rem; color: var(--accent-red); font-weight: 600;">
                        - Mike Chen, Business Coach
                    </div>
                </div>

                <div class="feature-card animate-on-scroll animated" style="transform: translateY(0px) scale(1);">
                    <div class="feature-icon">💎</div>
                    <h3>"Professional results"</h3>
                    <p>"The automation features and AI tools have elevated my business to a professional level I never thought possible. Customer support is outstanding too."</p>
                    <div style="margin-top: 1rem; color: var(--accent-red); font-weight: 600;">
                        - Emma Rodriguez, Content Creator
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta" id="contact">
        <div class="container">
            <div class="cta-content animate-on-scroll animated">
                <h2>Ready to Transform Your Business?</h2>
                <p>Join thousands of creators and entrepreneurs who have already revolutionized their business operations with Mewayz. Start your free trial today and experience the power of an all-in-one platform.</p>
                <div class="hero-buttons2">
                    <a href="https://mewayz.com/register" class="btn btn-primary btn-hero">Start Free Trial</a>
                    <a href="https://mewayz.com/login" class="btn btn-secondary btn-hero">Sign In</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Platform</h4>
                    <ul>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#pricing">Pricing</a></li>
                        <li><a href="/api-docs">API Documentation</a></li>
                        <li><a href="/integrations">Integrations</a></li>
                        <li><a href="/templates">Templates</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Tools</h4>
                    <ul>
                        <li><a href="/social-media">Social Media</a></li>
                        <li><a href="/link-in-bio">Link in Bio</a></li>
                        <li><a href="/courses">Courses</a></li>
                        <li><a href="/ecommerce">E-commerce</a></li>
                        <li><a href="/crm">CRM</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="/blog">Blog</a></li>
                        <li><a href="/help">Help Center</a></li>
                        <li><a href="/tutorials">Tutorials</a></li>
                        <li><a href="/webinars">Webinars</a></li>
                        <li><a href="/community">Community</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="/about">About Us</a></li>
                        <li><a href="/careers">Careers</a></li>
                        <li><a href="/press">Press</a></li>
                        <li><a href="/contact">Contact</a></li>
                        <li><a href="/partners">Partner with us</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="/privacy">Privacy Policy</a></li>
                        <li><a href="/terms">Terms of Service</a></li>
                        <li><a href="/security">Security</a></li>
                        <li><a href="/compliance">Compliance</a></li>
                        <li><a href="/gdpr">GDPR</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© 2025 Mewayz. All rights reserved. Built for creators, by creators.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(10, 10, 10, 0.98)';
                header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.3)';
            } else {
                header.style.background = 'rgba(10, 10, 10, 0.95)';
                header.style.boxShadow = 'none';
            }
        });

        // Parallax effect for floating elements
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.floating-element');
            
            parallaxElements.forEach((element, index) => {
                const speed = 0.5 + (index * 0.1);
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        });

        // Counter animation for hero stats
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            
            function updateCounter() {
                start += increment;
                if (start < target) {
                    element.textContent = Math.floor(start);
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target;
                }
            }
            
            updateCounter();
        }

        // Trigger counter animation when hero section is visible
        const heroObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = document.querySelectorAll('.stat-number');
                    counters.forEach(counter => {
                        const text = counter.textContent;
                        if (text.includes('+')) {
                            const num = parseInt(text.replace('+', ''));
                            counter.textContent = '0+';
                            animateCounter(counter, num);
                        } else if (text.includes('%')) {
                            const num = parseFloat(text.replace('%', ''));
                            counter.textContent = '0%';
                            setTimeout(() => {
                                counter.textContent = text;
                            }, 1000);
                        }
                    });
                    heroObserver.unobserve(entry.target);
                }
            });
        });

        heroObserver.observe(document.querySelector('.hero-stats'));

        // Enhanced card hover effects
        document.querySelectorAll('.feature-card, .pricing-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                if (this.classList.contains('featured')) {
                    this.style.transform = 'scale(1.05)';
                } else {
                    this.style.transform = 'translateY(0) scale(1)';
                }
            });
        });

        // Lazy loading for images
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                img.src = img.src;
            });
        } else {
            // Fallback for browsers that don't support lazy loading
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
            document.body.appendChild(script);
        }

        // Page load optimization
        window.addEventListener('load', function() {
            // Remove loading states, add entrance animations
            document.body.classList.add('loaded');
            
            // Initialize any additional features after page load
            console.log('Mewayz platform loaded successfully');
        });

        // Error handling for images
        document.querySelectorAll('img').forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
                console.warn('Image failed to load:', this.src);
            });
        });
    </script>


        </div>

        
        <script>
            var object = {
                mediaUrl: "https://mewayz.com/media/site/images",
                baseUrl: "https://mewayz.com",
                copiedText: "Copied",
            };

            window.builderObject = object;

            window.dark_theme = true;
        </script>
        <script data-navigate-once="true">window.livewireScriptConfig = {"csrf":"UeFLzrjalD0dK2oD6AUxSbVXIwleFJ78WOWw0ZK2","uri":"\/livewire\/update","progressBar":"","nonce":""};</script>

        <link rel="modulepreload" href="https://mewayz.com/build/assets/yenaWire-ee2e1504.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/_commonjs-dynamic-modules-a536689c.js"><script type="module" src="https://mewayz.com/build/assets/yenaWire-ee2e1504.js" data-navigate-track="reload"></script>    

<svg id="SvgjsSvg1001" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;"><defs id="SvgjsDefs1002"></defs><polyline id="SvgjsPolyline1003" points="0,0"></polyline><path id="SvgjsPath1004" d="M0 0 "></path></svg>
        </div>

        
        <script>
            var object = {
                mediaUrl: "https://mewayz.com/media/site/images",
                baseUrl: "https://mewayz.com",
                copiedText: "Copied",
            };

            window.builderObject = object;

            window.dark_theme = true;
        </script>
        <script data-navigate-once="true">window.livewireScriptConfig = {"csrf":"UeFLzrjalD0dK2oD6AUxSbVXIwleFJ78WOWw0ZK2","uri":"\/livewire\/update","progressBar":"","nonce":""};</script>

        <link rel="modulepreload" href="https://mewayz.com/build/assets/yenaWire-ee2e1504.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/_commonjs-dynamic-modules-a536689c.js"><script type="module" src="https://mewayz.com/build/assets/yenaWire-ee2e1504.js" data-navigate-track="reload"></script>    

<svg id="SvgjsSvg1001" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;"><defs id="SvgjsDefs1002"></defs><polyline id="SvgjsPolyline1003" points="0,0"></polyline><path id="SvgjsPath1004" d="M0 0 "></path></svg>
        </div>

        
        <script>
            var object = {
                mediaUrl: "https://mewayz.com/media/site/images",
                baseUrl: "https://mewayz.com",
                copiedText: "Copied",
            };

            window.builderObject = object;

            window.dark_theme = true;
        </script>
        <script data-navigate-once="true">window.livewireScriptConfig = {"csrf":"EjEySsWZDkphwfuffeg1nfAcfYDPFc9yo1xiP4Za","uri":"\/livewire\/update","progressBar":"","nonce":""};</script>

        <link rel="modulepreload" href="https://mewayz.com/build/assets/yenaWire-ee2e1504.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/_commonjs-dynamic-modules-a536689c.js"><script type="module" src="https://mewayz.com/build/assets/yenaWire-ee2e1504.js" data-navigate-track="reload"></script>    

<svg id="SvgjsSvg1001" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;"><defs id="SvgjsDefs1002"></defs><polyline id="SvgjsPolyline1003" points="0,0"></polyline><path id="SvgjsPath1004" d="M0 0 "></path></svg>
        </div>

        
        <script>
            var object = {
                mediaUrl: "https://mewayz.com/media/site/images",
                baseUrl: "https://mewayz.com",
                copiedText: "Copied",
            };

            window.builderObject = object;

            window.dark_theme = true;
        </script>
        <script data-navigate-once="true">window.livewireScriptConfig = {"csrf":"EjEySsWZDkphwfuffeg1nfAcfYDPFc9yo1xiP4Za","uri":"\/livewire\/update","progressBar":"","nonce":""};</script>

        <link rel="modulepreload" href="https://mewayz.com/build/assets/yenaWire-ee2e1504.js"><link rel="modulepreload" href="https://mewayz.com/build/assets/_commonjs-dynamic-modules-a536689c.js"><script type="module" src="https://mewayz.com/build/assets/yenaWire-ee2e1504.js" data-navigate-track="reload"></script>    

<svg id="SvgjsSvg1001" width="2" height="0" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" style="overflow: hidden; top: -100%; left: -100%; position: absolute; opacity: 0;"><defs id="SvgjsDefs1002"></defs><polyline id="SvgjsPolyline1003" points="0,0"></polyline><path id="SvgjsPath1004" d="M0 0 "></path></svg></body></html>
</x-layouts.site>