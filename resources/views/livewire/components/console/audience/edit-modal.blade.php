
<?php
   use App\Yena\SandyAudience;
   use App\Livewire\Actions\ToastUp;
   use App\Models\Audience;
   use App\Models\AudienceNote;
   use App\Models\AudienceActivity;
   use App\Traits\AudienceTraits;
   use App\Yena\VCard\VCard;
   use function Livewire\Volt\{state, mount, placeholder, uses, on, rules, updated, usesFileUploads};

   usesFileUploads();
   uses([AudienceTraits::class, ToastUp::class]);
   on([
      'registerAudience' => function($id){
         $this->audience_id = $id;
         $this->get();
      },
   ]);

   state([
      'avatar' => null,
      'countries' => fn() => \App\Yena\Country::list(),
   ]);

   state([
       'audience_id' => null,
   ]);
   state([
      'audience' => function(){
         $audience = new Audience;

         $audience->contact = [
            'silence' => 'golden',
            'settings' => [
               'silence' => 'golden',
            ]
         ];

         return $audience;
      },
      'au' => null,

      'notes' => [],
      'activity' => [],
   ]);

   state([
       'dateNow' => fn() => \Carbon\Carbon::now()->toFormattedDateString()
   ]);

   state([
      'user' => fn () => iam(),
   ]);

   rules(fn () => [
      'audience.contact.name' => 'required',
      'audience.contact.email' => 'required|email',
      'audience.contact.lastCountry' => '',
      'audience.contact.phone_number' => '',
      'audience.contact.settings.location' => '',
      'audience.contact.settings.job_title' => '',
   ]);
   mount(function(){
      // $this->get();
   });

   placeholder('
   <div class="p-5 w-full mt-1">
      <div class="flex mb-2 gap-4">
         <div>
            <div class="--placeholder-skeleton w-[200px] h-[200px] rounded-3xl"></div>
         </div>
         <div class="flex flex-col gap-2 w-full">
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-[150px] h-[40px] rounded-full mt-5"></div>
         </div>
      </div>
      
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');


   $get = function(){
      $this->audience = Audience::where('owner_id', $this->user->id);

      if($this->audience_id){
         $this->audience = $this->audience->where('id', $this->audience_id);
      }

      $this->audience = $this->audience->orderBy('id', 'desc')->first();

      if(!$this->audience->contact || !is_array($this->audience->contact)){
         $this->audience->contact = [
            'silence' => 'golden',
            'settings' => [
               'silence' => 'golden',
            ]
         ];
      }

      $this->notes = AudienceNote::where('user_id', $this->user->id)->where('audience_id', $this->audience->id)->orderBy('id', 'DESC')->get()->map(function($item) {
         $item->date = \Carbon\Carbon::parse($item->created_at)->toFormattedDateString();
         return $item;
      })->toArray();

      $this->activity = AudienceActivity::where('user_id', $this->user->id)->where('audience_id', $this->audience->id)->orderBy('id', 'DESC')->get()->map(function($item) {
         $item->date = \Carbon\Carbon::parse($item->created_at)->toFormattedDateString();
         return $item;
      })->toArray();

      // $this->au = $this->set($this->audience->id);
   };

   $createNote = function($note){
      $extra = [
          'posted_by' => $this->user->id,  
      ];

      $a = new AudienceNote;
      $a->user_id = $this->user->id;
      $a->audience_id = $this->audience->id;
      $a->note = $note;
      $a->extra = $extra;
      $a->save();

      // Add Activity
      $ac = __('Note created by :name on :date_time', ['name' => $this->user->name, 'date_time' =>\Carbon\Carbon::now()->format('F j g:s a')]);
      
      SandyAudience::create_activity($this->user->id, $this->audience->id, __('Notes'), $ac);
      $this->get();
   };

   $save = function($audience){
      
      $contact = ao($audience, 'contact');

      $this->audience->contact = $contact;
      $this->audience->save();
      

      $this->flashToast('success', __('Audience saved'));
      $this->dispatch('audienceRefresh');
      
      return [
         'status' => 1,
         'response' => __('Saved successfully.'),
      ];
   };

   
   $downloadContact = function(){
      $au = $this->set($this->audience->id);
     
      $vcard = new VCard();
      $additional = '';
      $prefix = '';
      $suffix = '';
      $fullName = true;

      $name = $au->info('name');
      $first_name = explode(' ', $name)[0] ?? '';
      $last_name = explode(' ', $name)[1] ?? '';

      $vcard->addnames($last_name, $first_name, $additional, $prefix, $suffix, $fullName);
      $vcard->addPhone($au->info('phone_number'), 'HOME');
      $vcard->addJobtitle($au->info('settings.job_title'));
      $vcard->addEmail($au->info('email'));

      //$vcard->addCompany('XYZ');
      $_date = \Carbon\Carbon::parse($this->audience->created_at)->format('Ymd');
      $_title = __("Date connected via :site", ['site' => config('app.name')]);
      
      $vcard->addCustom("X-ABDATE;TYPE=$_title", $_date);

      $vcard->addCustom('item1.ADR;type=HOME;type=pref', ';;' . $au->info('settings.location'));
      $i=10;

      try {
         //  $vcard->addPhoto($au->avatar(false));
      } catch (\Throwable $th) {
          //throw $th;
      }

      // if($page = $au->has_page()){

      //     $vcard->addNote($page->bio);
      //     foreach (socials() as $key => $items) {
      //         if (!empty(user('social.'.$key, $page->id))):
      //             $vcard->addURL(sprintf(ao($items, 'address'), user("social.$key", $page->id)), ucfirst($key));
      //             $i++;
      //         endif;
      //     }
      // }

      // define output
      $output = $vcard->genVCard();
      return response()
          ->streamDownload(function () use($output, $vcard) {
          echo $output;
      }, 'contact-' .str()->random(3). '.' . $vcard->getFileExtension(), [
          'Content-Type' => 'text/x-vcard'
      ]);
      return $vcard->download();
   };
?>

@php
   $set = $this->set($this->audience);
@endphp

<div class="w-full">
   <div x-data="audience_editing">
      <div class="flex flex-col">
         <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross text-sm"></i>
         </a>
   
         <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Edit Audience') }}</header>
   
         <hr class="yena-divider">
   
         <form @submit.prevent="save" class="">
   
            <div class="overflow-y-auto h-full max-h-[calc(100vh_-_150px)] px-8 pt-2 pb-6">
               <div class="settings__upload" data-generic-preview>
                  <div class="settings__preview !bg-[#f3f3f3] flex items-center justify-center !h-[8rem] !w-[8rem] !rounded-full overflow-hidden">
                     @php
                         $_avatar = false;
                         if($set->info('avatar')){
                           $_avatar = $set->avatar();
                         }
                         
                         if($avatar) $_avatar = $avatar->temporaryUrl();
                     @endphp
      
                     @if (!$_avatar)
                        {!! __i('--ie', 'image-picture', 'text-gray-300 w-8 h-8') !!}
                     @endif
                     @if ($_avatar)
                        <img src="{{ $_avatar }}" alt="">
                     @endif
                  </div>
                  <div class="settings__wrap">
   
   
                    <div class="text-[1.5rem] leading-10 font-bold">{{ $set->info('name') }}</div>
                    <div class="settings__content flex items-center gap-2">
                        @if ($set->info('lastCountry'))
                        <img src="{{ \Country::icon($set->info('lastCountry')) }}" class="w-5 h-5" alt="">
                        {{ __(':country - Joined :date', ['date' => $audience ? \Carbon\Carbon::parse($audience->created_at)->toFormattedDateString() : '', 'country' => \Country::country_code($set->info('lastCountry'))]) }}
                        @endif
                     </div>
                    <div class="flex gap-2">
                        {{-- <a @navigate class="yena-button-stack --primary !text-xs !h-8">
                           <div class="--icon">
                              {!! __icon('interface-essential', 'plus-add.3', 'w-4 h-4') !!}
                           </div>
   
                           {{ __('Manage') }}
                        </a> --}}
                        <a :href="'mailto:' + audience.contact.email" class="yena-button-stack --primary !text-xs !h-8">
                           <div class="--icon">
                              <i class="ph ph-envelope text-sm"></i>
                           </div>
   
                           {{ __('Email') }}
                        </a>
                        <a wire:click="downloadContact" class="yena-button-stack --primary !text-xs !h-8">
                           <div class="--icon">
                              <i class="ph ph-download text-sm"></i>
                           </div>
   
                           {{ __('Download') }}
                        </a>
                    </div>
                  </div>
               </div>
               <div class="flex flex-col gap-3 mt-4">
                    <div x-data="{ open: false }">
                     <div x-show="open" @click.outside="open = false">
                        <div class="form-input">
                            <label>{{ __('Email') }}</label>
                            <input type="email" name="email" x-model="audience.contact.email">
                        </div>
                     </div>
                     <div x-show="!open">
                        <div class="flex items-stretch flex-col gap-1">
                           <div class="flex items-center justify-between flex-row gap-2 w-full pt-2">
                              <div>
                                 <div class="flex items-center">
                                    <div>
                                       <p class="text-[#7C7C7C]">{{ __('Email Address') }}</p>
                                       <p class="text-black" x-text="audience.contact.email"></p>
                                    </div>
                                 </div>
                              </div>
                              <button class="yena-button-o !text-[var(--yena-colors-trueblue-600)]" type="button" @click="open = ! open">
                                 <span class="--icon !mr-0">
                                    <i class="ph ph-pencil text-black text-lg"></i>
                                 </span>
                              </button>
            
                           </div>
                        </div>
                     </div>
                    </div>
                    <div x-data="{ open: false }">
                     <div x-show="open" @click.outside="open = false">
                        <div class="form-input">
                            <label>{{ __('First & Last Name') }}</label>
                            <input type="text" name="name" x-model="audience.contact.name">
                        </div>
                     </div>
                     <div x-show="!open">
                        <div class="flex items-stretch flex-col gap-1">
                           <div class="flex items-center justify-between flex-row gap-2 w-full pt-2">
                              <div>
                                 <div class="flex items-center">
                                    <div>
                                       <p class="text-[#7C7C7C]">{{ __('Name') }}</p>
                                       <p class="text-black" x-text="audience.contact.name"></p>
                                    </div>
                                 </div>
                              </div>
                              <button class="yena-button-o !text-[var(--yena-colors-trueblue-600)]" type="button" @click="open = ! open">
                                 <span class="--icon !mr-0">
                                    <i class="ph ph-pencil text-black text-lg"></i>
                                 </span>
                              </button>
            
                           </div>
                        </div>
                     </div>
                    </div>
                    <div x-data="{ open: false }">
                     <div x-show="open" @click.outside="open = false">
                        <div class="form-input is-link always-active active mt-2">
                            <label>{{ __('Phone Number') }}</label>
                            <div class="is-link-inner">
                                <div class="side-info pl-1 relative">
                                    <img :src="country()" class="w-12 h-10 pl-2 flag-img-tag" alt="">
                                    <select name="lastCountry" wire:model="audience.contact.lastCountry" class="p-0 pl-0 w-14 absolute opacity-0 inset-0 cursor-pointer">
                                       <template x-for="(item, index) in countries" :key="index">
                                          <option :value="index" x-text="item"></option>
                                       </template>
                                    </select>
                                </div>
                                <input type="text" x-model="audience.contact.phone_number" name="phone_number" placeholder="{{ __('Enter a phone number') }}" class="is-alt-input bg-white">
                            </div>
                        </div>
                     </div>
                     <div x-show="!open">
                        <div class="flex items-stretch flex-col gap-1">
                           <div class="flex items-center justify-between flex-row gap-2 w-full pt-2">
                              <div>
                                 <div class="flex items-center">
                                    <div>
                                       <p class="text-[#7C7C7C]">{{ __('Phone number') }}</p>
                                       <p class="text-black" x-text="audience.contact.phone_number"></p>
                                    </div>
                                 </div>
                              </div>
                              <button class="yena-button-o !text-[var(--yena-colors-trueblue-600)]" type="button" @click="open = ! open">
                                 <span class="--icon !mr-0">
                                    <i class="ph ph-pencil text-black text-lg"></i>
                                 </span>
                              </button>
                           </div>
                        </div>
                     </div>
                    </div>
                    <div x-data="{ open: false }">
                     <div x-show="open" @click.outside="open = false">
                        <div class="form-input">
                            <label>{{ __('Address') }}</label>
                            <input type="text" name="location" x-model="audience.contact.settings.location">
                        </div>
                     </div>
                     <div x-show="!open">
                        <div class="flex items-stretch flex-col gap-1">
                           <div class="flex items-center justify-between flex-row gap-2 w-full pt-2">
                              <div>
                                 <div class="flex items-center">
                                    <div>
                                       <p class="text-[#7C7C7C]">{{ __('Address') }}</p>
                                       <p class="text-black" x-text="audience.contact.settings.location"></p>
                                    </div>
                                 </div>
                              </div>
                              <button class="yena-button-o !text-[var(--yena-colors-trueblue-600)]" type="button" @click="open = ! open">
                                 <span class="--icon !mr-0">
                                    <i class="ph ph-pencil text-black text-lg"></i>
                                 </span>
                              </button>
            
                           </div>
                        </div>
                     </div>
                    </div>
                    <div x-data="{ open: false }">
                     <div x-show="open" @click.outside="open = false">
                        <div class="form-input">
                            <label>{{ __('Occupation') }}</label>
                            <input type="text" name="email" x-model="audience.contact.settings.job_title">
                        </div>
                     </div>
                     <div x-show="!open">
                        <div class="flex items-stretch flex-col gap-1">
                           <div class="flex items-center justify-between flex-row gap-2 w-full pt-2">
                              <div>
                                 <div class="flex items-center">
                                    <div>
                                       <p class="text-[#7C7C7C]">{{ __('Occupation') }}</p>
                                       <p class="text-black" x-text="audience.contact.settings.job_title"></p>
                                    </div>
                                 </div>
                              </div>
                              <button class="yena-button-o !text-[var(--yena-colors-trueblue-600)]" type="button" @click="open = ! open">
                                 <span class="--icon !mr-0">
                                    <i class="ph ph-pencil text-black text-lg"></i>
                                 </span>
                              </button>
            
                           </div>
                        </div>
                     </div>
                    </div>
                    <div x-data="{ open: false }">
                     <div x-show="open" @click.outside="open = false">
                        <div class="form-input">
                            <label>{{ __('Address') }}</label>
                            <input type="text" name="location" x-model="audience.contact.settings.location">
                        </div>
                     </div>
                     <div x-show="!open">
                        <div class="flex items-stretch flex-col gap-1">
                           <div class="flex items-center justify-between flex-row gap-2 w-full pt-2">
                              <div>
                                 <div class="flex items-center">
                                    <div>
                                       <p class="text-[#7C7C7C]">{{ __('Notes') }}</p>
                                       <p class="text-black" x-text="audience.contact.settings.location"></p>
                                    </div>
                                 </div>
                              </div>
                              <button class="yena-button-o !text-[var(--yena-colors-trueblue-600)]" type="button" @click="open = ! open">
                                 <span class="--icon !mr-0">
                                    <i class="ph ph-pencil text-black text-lg"></i>
                                 </span>
                              </button>
            
                           </div>
                        </div>
                     </div>
                    </div>
   
                    <hr class="my-4">
                    <div>
                     <div>
                        <div class="flex items-stretch flex-col gap-1 mb-2">
                           <p class="text-[#7C7C7C] text-xl">{{ __('Notes') }}</p>
                        </div>
                     </div>
   
                     <template x-for="(item, index) in notes" :key="index">
                        <div>
                           <div class="flex items-stretch flex-col gap-1">
                              <div class="flex items-center justify-between flex-row gap-2 w-full pt-2">
                                 <div>
                                    <div class="flex items-center tiny-content-init">
                                       <div>
                                          <p class="text-[#7C7C7C] italic" x-text="item.date"></p>
                                          <ul class="!m-0">
                                             <li><p class="text-black" x-text="item.note"></p></li>
                                          </ul>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </template>
                        <div>
                           <div class="flex items-stretch flex-col gap-1">
                              <div class="flex items-center justify-between flex-row gap-2 w-full pt-2">
                                 <div class="flex flex-col w-full">
                                    <div class="flex items-center tiny-content-init">
                                       <div class="w-full">
                                          <p class="text-[#7C7C7C] italic" x-text="dateNow"></p>
                                          <ul class="w-full">
                                             <li class="w-full">
                                                <form class="flex items-center gap-2" @submit.prevent="createNote">
                                                   <div class="form-input !bg-transparent w-full">
                                                      <input type="text" placeholder="{{ __('Type note here...') }}" x-model="newNoteModel">
                                                   </div>
                                                   <button type="submit" class="yena-button-stack !text-black" :class="{
                                                      '!hidden': !newNoteModel
                                                   }">
                                                      {{ __('Save') }}
                                                   </button>
                                                </form>
                                             </li>
                                          </ul>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div>
                     <div>
                        <div class="flex items-stretch flex-col gap-1 mb-2">
                           <p class="text-[#7C7C7C] text-xl">{{ __('Activity') }}</p>
                        </div>
                     </div>
   
                     <template x-for="(item, index) in activity" :key="index">
                        <div>
                           <div class="flex items-stretch flex-col gap-1">
                              <div class="flex items-center justify-between flex-row gap-2 w-full pt-2">
                                 <div>
                                    <div class="flex items-center tiny-content-init">
                                       <div>
                                          <p class="text-[#7C7C7C] italic" x-text="item.date"></p>
                                          <ul class="!m-0">
                                             <li><p class="text-black" x-text="item.message"></p></li>
                                          </ul>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </template>
                    </div>
               </div>
            </div>
   
            <div class="px-8">
               @php
                  $error = false;
      
                  if(!$errors->isEmpty()){
                        $error = $errors->first();
                  }
      
                  if(Session::get('error._error')){
                        $error = Session::get('error._error');
                  }
               @endphp
               @if ($error)
                  <div class="mt-5 bg-red-200 font--11 p-1 px-2 rounded-md">
                        <div class="flex items-center">
                           <div>
                              <i class="fi fi-rr-cross-circle flex text-xs"></i>
                           </div>
                           <div class="flex-grow ml-1 text-xs">{{ str_replace('create.', '', $error) }}</div>
                        </div>
                  </div>
               @endif
               <button class="yena-button-stack mt-5 w-full">{{ __('Save') }}</button>
            </div>
         </form>
      </div>
   </div>

   
   @script
   <script>
       Alpine.data('audience_editing', () => {
          return {
            _page: 'text',
            dateNow: @entangle('dateNow'),
            audience: @entangle('audience').live,
            countries: @entangle('countries'),
            activity: @entangle('activity'),
            notes: @entangle('notes'),
            colors: [],

            newNoteModel: '',

            createNote(){
               let $this = this;
               if(!this.newNoteModel) return;


               $this.$wire.createNote(this.newNoteModel).then(r => {
                  $this.newNoteModel = '';
               });
            },

            country(){
               let country = 'AF';

               if(this.audience.contact && this.audience.contact.lastCountry){
                  country = this.audience.contact.lastCountry;
               }

               let flag = country.toLowerCase();
               var src = "{{ gs('assets/image/countries/') }}/" + flag + '.svg';
               return src;
            },

            save(){
               let $this = this;
               

               $this.$wire.save($this.audience);
            },
            _render(){
               const items = ['eye_ball', 'eye_frame', 'dots', 'frames', 'logos'];

               items.forEach(item => {
                  if (!this.code.extra[item]) {
                     this.code.extra[item] = {
                        silence: 'golden'
                     };
                  }
               });
               for (let i = 1; i <= 8; i++) {
                  this.colors.push(this.$store.builder.getRandomHexColor());
               }
            },
            
            _color(color){
               return color.replace(/#/g, '');
            },
            init(){
               var $this = this;
               // $this._render();

                // document.addEventListener('alpine:navigated', (e) => {
                //    $this.$wire._get();
                // });
            }
          }
       });
   </script>
   @endscript
</div>