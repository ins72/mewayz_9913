
<?php

   use function Livewire\Volt\{state, mount, on};
   state([
      'site',
   ]);

   state([
      'sections' => [],
      'components' => fn() => \Storage::disk('components')->files('mediakit/pages'),

      'sectionConfig' => fn () => config("yena.sections"),
   ]);

   mount(function(){
      $this->getSections();
   });

   on([
      'builder::createdSection' => function($section){
         $this->getSections();
      },
      'builder::setPage' => function(){
         $this->getSections();
      },
   ]);

   // Methods
   $getSections = function(){
      //$this->sections = $this->site->getEditSections();
   };

?>
<div class="edit-panel-wrapper" :class="{'show': page!=='-'}">

   <div class="editor-controls-section">
      <div class="top-controls">
         <ul>
            <li :class="{'active': page=='section'}">
               <a name="page-settings" @click="navigatePage('section')">
                  {!! __i('interface-essential', 'menu-block-checkmark') !!}
               </a>
               <span> {{ __('Section') }}</span>
            </li>
            <li :class="{'active': page=='my'}">
               <a name="page-settings" @click="navigatePage('my')">
                  {!! __i('interface-essential', 'browser-internet-web-network-window-app-icon') !!}
               </a>
               <span> {{ __('linkinBio') }}</span>
            </li>
            <li :class="{'active': page=='story'}">
               <a name="page-settings" @click="navigatePage('story')">
                  {!! __i('interface-essential', 'heart-favorite-like-story') !!}
               </a>
               <span> {{ __('Story') }}</span>
            </li>
            {{-- <li :class="{'active': page=='design'}">
               <a @click="navigatePage('design')">
                  {!! __icon('Design Tools', 'Bucket, Paint') !!}
               </a>
               <span>{{ __('Design') }}</span>
            </li> --}}
            {{-- <li :class="{'active': page=='contact'}">
               <a @click="navigatePage('contact')">
                  {!! __i('Support, Help, Question', 'checklist-user') !!}
               </a>
               <span> {{ __('Contacts') }}</span>
            </li> --}}
            <li :class="{'active': page=='analytics'}">
               <a @click="navigatePage('analytics')">
                  {!! __icon('Business, Products', 'blackboard-business-chart') !!}
               </a>
               <span> {{ __('Analytics') }}</span>
            </li>
            <li :class="{'active': page=='media'}">
               <a @click="navigatePage('media')">
                  {!! __i('Music, Audio', 'media-library-playlist-play') !!}
               </a>
               <span> {{ __('Media') }}</span>
            </li>
            <li :class="{'active': page=='settings'}">
               <a name="page-settings" @click="navigatePage('settings')">
                  {!! __i('interface-essential', 'setting4') !!}
               </a>
               <span> {{ __('Settings') }}</span>
            </li>
            {{-- <li>
               <a name="support">
                  {!! __i('interface-essential', 'question') !!}
               </a>
               <span>{{ __('Support') }}</span>
            </li> --}}
         </ul>
      </div>
   </div>
   <div class="edit-panel">
    <!--[if BLOCK]><![endif]-->
    <div></div>
    <!--[if ENDBLOCK]><![endif]-->
      <div>
         {{-- <x-builder.textarea placeholder="Add text here" class="input-small resizable-textarea blur-body overflow-y-hidden !h-[110px] !min-h-[100px]"/> --}}

         @foreach($components as $item)
            @php
               $file = Str::before($item, '.blade.php');
               $name = basename($file);

               $tag = 'template';
               $cond = 'if';
               $tag = 'div';
               $cond = 'show';
               $lazyType = 'false';
               if(str()->startsWith($name, '-')) {
                  $tag = 'div';
                  $cond = 'show';
                  $name = str_replace('-', '', $name);
                  $lazyType = 'on-load';
               }
               
               $component = "components/$file";
            @endphp

            <{{ $tag }} x-{{ $cond }}="page == '{{$name}}'">
               <div>
                  <livewire:is :component="$component" :$site lazy="{{ $lazyType }}" :key="uukey('builder::component', 'component' . $component)">
               </div>
            </{{ $tag }}>
         @endforeach
      </div>
      @foreach(__collect_sectons() as $key => $item)
         @php
            if(!$_name = __a($item, 'components.editComponent')) continue;

            $_name = str_replace('/', '.', $_name);
            $component = "livewire::components.mediakit.$_name";


            $baseName = basename(__a($item, 'components.editComponent'));
            // print_r($baseName);

            $tag = 'template';
            $cond = 'if';
            
            if(str()->startsWith($baseName, '-')) {
               $tag = 'div';
               $cond = 'show';
               $name = str_replace(['-', '+'], '', $baseName);
               $component = "components/mediakit/sections/$key/$name";
            }

         @endphp
         <{{ $tag }} x-{{ $cond }}="page=='section::{{ $key }}'">
            <div wire:ignore>
               @if(str()->startsWith($baseName, '-'))
                  <livewire:is :component="$component" lazy :key="uukey('builder::component', 'component:Section' . $component)">
                  @else
                  <x-dynamic-component :component="$component"/>
               @endif
            </div>
         </{{$tag}}>
      @endforeach
      
      {{-- <div x-show="page=='section::header'">
         <div wire:ignore>
            <livewire:sections.header.editComponent :$site lazy="on-load" :key="uukey('builder::component', 'header')"/>
         </div>
      </div> --}}

      {{-- <div x-show="page=='section::footer'">
         <div wire:ignore>
            <livewire:sections.footer.editComponent :$site lazy="on-load" :key="uukey('builder::component', 'footer')"/>
         </div>
      </div> --}}

      @foreach($sectionConfig as $key => $item)
         @php

            $_name = str_replace('/', '.', __a($item, 'components.editComponent'));
            $component = "livewire::$_name";
         @endphp
         <template x-if="page=='section::{{ $key }}'">
            <div wire:ignore>
               <x-dynamic-component :component="$component"/>
            </div>
         </template>
      @endforeach
   </div>

</div>
