
<?php

   use App\Yena\Page\Generate;
   use App\Models\BioPage;
   use Illuminate\Support\Facades\Validator;
   use function Livewire\Volt\{state, mount, placeholder};

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   state([
      'address' => '',
   ]);

   state(['site']);

   // Methods

   $createPage = function($data){
      $this->skipRender();
      // Create Page

      $slug = slugify(ao($data, 'name') . str()->random(3), '-');

      $page = new BioPage;
      $page->fill($data);
      $page->site_id = $this->site->id;
      $page->slug = $slug;
      $page->default = 0;
      $page->published = 1;
      $page->save();

      $this->js('createPage=false');

      //$this->js('pages.push('. $page .')');
      $this->dispatch('builder::pageCreated', $page);
   };
?>

<div>
  <div x-data="builder__create_page">
   
      <div>
         <div class="design-navbar">
            <ul >
                <li class="close-header !flex">
                  <a @click="createPage=false">
                    <span>
                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                    </span>
                  </a>
               </li>
               <li class="!pl-0">{{ __('Add Page') }}</li>
               <li></li>
            </ul>
         </div>
         <div class="container-small">
          <div class="p-5">
            <form @submit.prevent="_createPage" class="">
               <div class="flex flex-col gap-3">
                  {{-- <div class="text-xl font-extrabold tracking-[-1px]">{{ __('Domain') }}</div> --}}
                  <div class="flex flex-col justify-center items-center px-[20px] pt-[60px]">
                     {!! __i('interface-essential', 'item-pen-text-square', 'w-14 h-14') !!}
                     <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                        {!! __t('Create new bio page for your visitors to navigate to.') !!}
                     </p>
                  </div>
               </div>

               <div class="custom-content-input border-2 border-dashed mb-1">
                  <input type="text" x-model="page_name" placeholder="{{ __('Address') }}" class="w-[100%] !bg-gray-100">
               </div>
                                    

               <button class="yena-button-stack w-[100%]">
                  <span>{{ __('Create') }}</span>
               </button>
            </form>
          </div>
         </div>
      </div>
  </div>
  @script
      <script>
          Alpine.data('builder__create_page', () => {
             return {
               page_name: '',

               _createPage(){
                  var newPage = {
                     uuid: this.$store.builder.generateUUID(),
                     name: this.page_name,
                     published: 1,
                     settings: {
                        enableHeader: true,
                     }
                  }

                  this.pages.push(newPage);

                  this.$wire.createPage(newPage);
               },

               generateName(){
                  this.page_name = 'Page ' + (this.pages.length + 1);
               },
               init(){
                  let $this = this;

                  $this.generateName();
               }
             }
          });
      </script>
  @endscript
</div>