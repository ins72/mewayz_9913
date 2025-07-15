<div class="banner-layout-6 w-boxed">
    <div class="banner section-component {{ !ao($section->settings, 'enable_image') ? 'full' : '' }}">
       <div class="banner-text content-heading {{ !ao($section->settings, 'enable_image') ? 'full alignment' : '' }}">
            @if (!empty($_label = ao($section->content, 'label')))
                <div class="banner-label section-label t-0">{{ $_label }}</div>
            @endif
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
       </div>
       <div class="banner-description">
            @php
                $_color = 'grey';
                if(ao($section->settings, 'color') == 'transparent') $_color = 'transparent';
                if(ao($section->settings, 'color') == 'default') $_color = 'grey';
                if(ao($section->settings, 'color') == 'accent') $_color = 'accent';
            @endphp

            @if (ao($section->settings, 'enable_image'))
            <div class="banner-image min-shape {{ ao($section->settings, 'image_type') == 'fit' ? 'section-item-image' : '' }}" style="height: {{ao($section->settings, 'height')}}px; --height: {{ao($section->settings, 'height')}}px;">
                 <img src="{{ $media }}" class="{{ ao($section->settings, 'image_type') == 'fit' ? 'Fit' : 'Fill' }} {{ $_color }}">
                 <div class="screen"></div>
              </div>
            @endif
          <div class="banner-text-subtitle {{ !ao($section->settings, 'enable_image') ? 'alignment' : '' }}">
             <p class="t-2 pre-line subtitle-width-size" style="{{ !ao($section->settings, 'enable_image') && ao($section->settings, 'width') ? 
             'width:' . ao($section->settings, 'width') . '%' : '' }}">{{ ao($section->content, 'subtitle') }}</p>

             <div class="button-holder" style="position: relative;">
                <a target="" href="javascript:void(0)" class="btn-1">
                    <button class="t-1 shape">Button 1</button>
                </a>
                <a target="" href="sms:+1undefined" class="btn-2">
                    <button class="t-1 shape">Button 2</button>
                </a>
                <div class="screen"></div>
             </div>
          </div>
       </div>
    </div>
 </div>