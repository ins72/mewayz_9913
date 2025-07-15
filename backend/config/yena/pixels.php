<?php

return [

    'google_analytics' => [
        'name' => 'Google Analytics',
        'color' => '#ffaf0080',
        'class' => 'col-span-2 md:col-span-1',
        "icon" => 'fi fi-rr-signal-alt-2 text-black',
        "template" => "
        <script async src='https://www.googletagmanager.com/gtag/js?id={pixel}'></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{pixel}');
        </script>",
    ],

    'google_tag_manager' => [
        'name' => 'Google Tag Manager',
        'class' => 'col-span-2 md:col-span-1',
        'color' => '#8ab4f8a8',
        "svg" => "<svg class=\"icon icon-google-analytics w-4 h-4\"><symbol id=\"icon-google-tag-manager\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" x=\"0px\" y=\"0px\" viewBox=\"0 0 2469.7 2469.8\" style=\"enable-background:new 0 0 2469.7 2469.8;\" xml:space=\"preserve\"><g><path class=\"st0\" d=\"M1449.8,2376L1021,1946.7l921.1-930.5l436.7,436.6L1449.8,2376z\"/><path class=\"st1\" d=\"M1452.9,527.1L1016.3,90.4L90.5,1016.2c-120.6,120.5-120.7,315.8-0.2,436.4c0.1,0.1,0.2,0.2,0.2,0.2l925.8,925.8l428.3-430.3L745,1235.1L1452.9,527.1z\"/><path class=\"st0\" d=\"M2378.7,1016.2L1452.9,90.4c-120.6-120.6-316.1-120.6-436.7,0c-120.6,120.6-120.6,316.1,0,436.6l926.3,925.8c120.6,120.6,316.1,120.6,436.6,0c120.6-120.6,120.6-316.1,0-436.6L2378.7,1016.2z\"/><circle class=\"st2\" cx=\"1231.2\" cy=\"2163.9\" r=\"306\"/></g></symbol><style type=\"text/css\">.st0{fill:#8AB4F8;}.st1{fill:#4285F4;}.st2{fill:#246FDB;}</style></svg>",

        "template" => "
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{pixel}');
        </script>

        <noscript>
            <iframe src=\"https://www.googletagmanager.com/ns.html?id={pixel}\" height=\"0\" width=\"0\" style=\"display: none; visibility: hidden;\"></iframe>
        </noscript>",
    ],

    'facebook' => [
        'name' => 'Facebook',
        "icon" => "fi fi-brands-facebook",
        "color" => "#4064ac",
        "template" => "
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', {pixel});
            fbq('track', 'PageView');
        </script>

        <noscript><img height=\"1\" width=\"1\" style='display:none' src='https://www.facebook.com/tr?id={pixel}&ev=PageView&noscript=1\"/></noscript>",
    ],

    'twitter' => [
        'name' => 'Twitter',
        'color' => '#08a0e9',
        "icon" => "fi fi-brands-twitter",
        "template" => "
        <script>
            !function(e,t,n,s,u,a){e.twq||(s=e.twq=function(){s.exe?s.exe.apply(s,arguments):s.queue.push(arguments);
            },s.version='1.1',s.queue=[],u=t.createElement(n),u.async=!0,u.src='//static.ads-twitter.com/uwt.js',
                a=t.getElementsByTagName(n)[0],a.parentNode.insertBefore(u,a))}(window,document,'script');

            twq('init', '{pixel}');
            twq('track', 'PageView');
        </script>",
    ],

    'snapchat' => [
        'name' => 'Snapchat',
        'color' => '#bcba14',
        "icon" => "fi fi-brands-snapchat",
        "template" => "
        <script type='text/javascript'>
        (function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function()
        {a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};
        a.queue=[];var s='script';r=t.createElement(s);r.async=!0;
        r.src=n;var u=t.getElementsByTagName(s)[0];
        u.parentNode.insertBefore(r,u);})(window,document,
        'https‍://sc-static.‍net/scevent.min.‍js');
        
        snaptr('init', '{pixel}', {
            'user_email': '{email}'
        });
        
        snaptr('track', 'PAGE_VIEW');
        
        </script>",
    ],

    'tiktok' => [
        'name' => 'TikTok',
        'color' => '#000',
        "icon" => "fi fi-brands-tik-tok",
        "template" => "
        <script>!function (w, d, t) { w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=['page','track','identify','instances','debug','on','off','once','ready','alias','group','enableCookie','disableCookie'],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i='https://analytics.tiktok.com/i18n/pixel/events.js';ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement('script');o.type='text/javascript',o.async=!0,o.src=i+'?sdkid='+e+'&lib='+t;var a=document.getElementsByTagName('script')[0];a.parentNode.insertBefore(o,a)}; ttq.load('{pixel}'); ttq.page();}(window, document, 'ttq');</script>",
    ],

    'linkedin' => [
        'name' => 'Linkedin',
        'color' => '#006097',
        "icon" => "fi fi-brands-linkedin",
        "template" => "
        <script type=\"text/javascript\">
_linkedin_partner_id = \"{pixel}\";
window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
window._linkedin_data_partner_ids.push(_linkedin_partner_id);
</script><script type=\"text/javascript\">
(function(l) {
if (!l){window.lintrk = function(a,b){window.lintrk.q.push([a,b])};
window.lintrk.q=[]}
var s = document.getElementsByTagName(\"script\")[0];
var b = document.createElement(\"script\");
b.type = \"text/javascript\";b.async = true;
b.src = \"https://snap.licdn.com/li.lms-analytics/insight.min.js\";
s.parentNode.insertBefore(b, s);})(window.lintrk);
</script>
<noscript>
<img height=\"1\" width=\"1\" style=\"display:none;\" alt=\"\" src=\"https://px.ads.linkedin.com/collect/?pid={pixel}&fmt=gif\" />
</noscript>
",
    ],

];
