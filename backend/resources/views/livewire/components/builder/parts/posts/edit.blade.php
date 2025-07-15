
<?php

   use App\Models\Section;
   use App\Models\SectionItem;
   use App\Models\Page;
   use function Livewire\Volt\{state, mount, placeholder};

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   state([
      'templates' => function(){
         $sections = [];
            foreach (collect(config('yena.pages')) as $key => $value) {
               $view = "includes.sitePages.$key";

               $section = [
                  ...$value,
                  'icon' => view()->exists($view) ? view($view)->render() : '',
               ];

               $sections[$key] = $section;
            }

         return $sections;
      }
   ]);

   state(['site']);

   // Methods

   $createPage = function($_page, $sections = null){
      $this->skipRender();
      $_c = Page::where('site_id', $this->site->id)->count();
      if(__o_feature('consume.pages', iam()) != -1 && $_c >= __o_feature('consume.pages', iam())){
         $this->js('window.runToast("error", "'. __('You have reached your page creation limit. Please upgrade your plan.') .'");');
         return;
      }
      // Create Page
      // $name = "Untitled-" . ao($template, 'name');
      // $slug = slugify(ao($_page, 'name') . str()->random(3), '-');

      $page = new Page;
      $page->fill($_page);
      $page->site_id = $this->site->id;
      $page->uuid = __a($_page, 'uuid');
      $page->save();

      if($sections){
         foreach ($sections as $section) {
            $_section = new Section;
            $_section->fill($section);
            $_section->published = 1;
            $_section->site_id = $this->site->id;
            $_section->page_id = $page->uuid;
            $_section->uuid = __a($section, 'uuid');
            $_section->save();

            if(is_array($items = __a($section, 'items'))){
               foreach ($items as $key => $value) {
                  $_item = new SectionItem;
                  $_item->fill($value);
                  $_item->section_id = $_section->uuid;
                  $_item->uuid = __a($value, 'uuid');
                  $_item->save();
               }
            }
         }
      }

      // $this->js('createPage=false');
      // $this->js('pages.push('. $page .')');
      // $this->dispatch('builder::pageCreated', $page);
   };
?>

<div>
  <div x-data="builder__posts_edit">
        <div>
            
    
         <div x-show="__page == 'section'">
            <div x-data="{section:post}">
               <x-livewire::components.builder.parts.section />
            </div>
      </div>
     
         <div x-cloak x-show="__page == '-'">
            <div class="banner-section !block">
               <div>
         
                  <div class="banner-navbar">
                     <ul >
                           <li class="close-header">
                           <a @click="_page='-'">
                              <span>
                                 {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                              </span>
                           </a>
                     </li>
                     <li>{{ __('Edit') }}</li>
                     <li></li>
                     </ul>
                  </div>
                  <div class="sticky container-small">
                     <div class="tab-link">
                           <ul class="tabs">
                           <li class="tab !w-full" @click="__tab = 'content'" :class="{'active': __tab == 'content'}">{{ __('Content') }}</li>
                           <li class="tab !w-full" @click="__tab = 'style'" :class="{'active': __tab == 'style'}">{{ __('Style') }}</li>
                           </ul>
                     </div>
                  </div>
                  <div class="container-small tab-content-box">
                     <div class="tab-content">
                           <div x-cloak x-show="__tab == 'content'" data-tab-content>
                              <x-livewire::components.builder.parts.posts.content />
                           </div>
                           <div x-cloak x-show="__tab == 'style'" data-tab-content>
                              <x-livewire::components.builder.parts.posts.style />
                           </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
        </div>
  </div>
  @script
      <script>
          Alpine.data('builder__posts_edit', () => {
             return {
               __tab: 'content',
               __page: '-',

               init(){
                  let $this = this;
               }
             }
          });
      </script>
  @endscript
</div>