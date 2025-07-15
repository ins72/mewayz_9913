<div class="banner-layout-1 w-boxed">
    <div class="banner section-component">
       <div class="banner-text content-heading left-title">
          <section class="subtitle-width-size" data-size="50" style="width: calc(50% - 50px);">
             <div class="banner-label section-label t-0">{{ ao($section->content, 'label') }}</div>

             @php
                $titleSize = 't-5';
                if(!empty($_title_size = ao($section->settings, 'title'))){
                    switch($_title_size){
                        case 's':
                            $titleSize = 't-5';
                        break;
                        case 'm':
                            $titleSize = 't-6';
                        break;
                        case 'l':
                            $titleSize = 't-7';
                        break;
                        case 'xl':
                            $titleSize = 't-8';
                        break;
                    }
                }
             @endphp
             <h1 class="title pre-line {{$titleSize}}">{{ ao($section->content, 'title') }}</h1>
          </section>

          <section class="subtitle-width-size" data-size="50" style="width: 50%;">
             <p class="t-2 pre-line subtitle-width-size subtitle" data-size="50">{{ ao($section->content, 'subtitle') }}</p>
             
             <p class="t-0 feedback" id="feedbackMessage" style="display: none;"> Thank you for subscribing </p>
             <form class="email subscribe name subtitle-width-size mt-2" data-form="more-input" data-size="50" action="" onsubmit="return false" style="">
                <div class="names-input">
                   <input name="firstname" type="text" class="shape" placeholder="First name"><!---->
                </div>
                <input name="email" type="text" class="shape" placeholder="Email"><!----><!----><!----><!---->
                <p class="t-0 feedback" id="feedbackMessage2" style="display: none;"> Thank you for subscribing </p>
                <button class="site-btn t-1 shape mt-2">Button 1</button>
                <div class="screen"></div>
             </form>
             <p id="error" class="error" style="display: none;">Thank you for subscribing</p>
             <p class="t-1" id="feedback" style="display: none;">Thank you for subscribing</p>
             <!---->
          </section>
       </div>
       

       <div class="banner-image min-shape mt-4" id="banner-image_1" style="height: {{ao($section->settings, 'height')}}px; --height: {{ao($section->settings, 'height')}}px;">
            <img src="{{ $media }}" class="Fill accent">
            <div class="screen"></div>
       </div>
    </div>
 </div>