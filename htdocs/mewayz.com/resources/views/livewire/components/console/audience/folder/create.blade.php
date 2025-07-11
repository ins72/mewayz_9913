
<?php
   use App\Yena\SandyAudience;
   use App\Livewire\Actions\ToastUp;
   use App\Models\Audience;
   use App\Models\AudienceFolder;
   use App\Models\AudienceFoldersUser;
   use App\Traits\AudienceTraits;
   use App\Yena\VCard\VCard;
   use function Livewire\Volt\{state, mount, placeholder, uses, on, updated};
   uses([ToastUp::class]);
   updated([
    'search' => fn() => $this->loadData(),
   ]);

   state([
      'audiences' => [],
   ]);

   state([
      'user' => fn () => iam(),
   ]);

   state([
        'has_more_pages' => false,
        'per_page' => 10,
   ]);

   state([
       'search' => '',
       'name' => '',
       'selected' => [],
   ]);

   mount(function(){
      $this->loadData();
   });

   placeholder('
   <div class="p-5 w-full mt-1">
        <div class="--placeholder-skeleton w-full h-[40px] rounded-md"></div>
      
        <div class="zp-5 rounded-xl zbg-[#f7f3f2] mt-4">
            <div class="--placeholder-skeleton w-full h-[40px] rounded-md"></div>
            <div class="--placeholder-skeleton w-full h-[400px] rounded-sm mt-2"></div>
        </div>
      <div class="--placeholder-skeleton w-full h-[40px] rounded-md mt-3"></div>
   </div>');


   $loadData = function(){
        if(request()->session()->has('session_rand')){
            if((time() - request()->session()->get('session_rand')) > 3600){
                request()->session()->put('session_rand', time());
            }
        }else{
            request()->session()->put('session_rand', time());
        }
        
        $audiences = Audience::where('owner_id', $this->user->id);

        if (!empty($query = $this->search)) {
            $searchBy = filter_var($query, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
            $audiences = $audiences->where("contact->$searchBy", 'LIKE', '%' . $query . '%');
        }

        $audiences = $audiences->orderBy(\DB::raw('RAND('. request()->session()->get('session_rand') .')'));

        $audiences = $audiences->paginate($this->per_page, ['*'], 'page');
        $this->per_page = $this->per_page + 5;
        $this->has_more_pages = $audiences->hasMorePages();

        $this->audiences = $audiences->items();
   };

   $toggleSelection = function($id){
        if (in_array($id, $this->selected)) {
            $this->selected = array_diff($this->selected, [$id]);
        } else {
            $this->selected[] = $id;
        }
   };

   $save = function(){
        $folder = new AudienceFolder;
        $folder->user_id = $this->user->id;
        $folder->name = $this->name;
        $folder->save();


        foreach ($this->selected as $key => $value) {
            $create = new AudienceFoldersUser;
            $create->audience_id = $value;
            $create->folder_id = $folder->id;
            $create->save();
        }

        $this->flashToast('success', __('Folder created successfully'));

        $this->dispatch('close');
        $this->dispatch('audienceRefresh');
        $this->dispatch('folderCreated');
   };
?>
<div class="w-full">
   <div x-data="audience_create_folder">
      <div class="flex flex-col">
         <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross text-sm"></i>
         </a>
   
         <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Folder') }}</header>
   
         <hr class="yena-divider">
   
         <form @submit.prevent="$wire.save()" class="">
            <div class=" px-8 pt-5">
                <div class="form-input">
                    <label>{{ __('Folder Name') }}</label>
                    <input type="text" name="name" wire:model="name">
                </div>

                <div class="mt-4 p-5 rounded-xl bg-[#f7f3f2]">
                    <div class="form-input !bg-transparent">
                        <label>{{ __('Search') }}</label>
                        <input type="text" name="name" wire:model.live.debounce.850ms="search">
                    </div>
                    @if ($has_more_pages)
                        <div wire:loading wire:target="loadData" class="w-full">
                            <div class="flex justify-center w-full">
                                <div class="lds-ring !flex justify-center items-center"><div></div><div></div><div></div><div></div></div>
                            </div>
                        </div>
                    @endif
   
                    <div class="overflow-y-auto h-full max-h-[calc(100vh_-_360px)] pt-2" x-ref="windowContactWrapper" wire:target="loadData" wire:loading.class="!max-h-[calc(100vh_-_450px)]">
                        
                        <div class="flex flex-col gap-2 a-card-grid">
                            @foreach ($audiences as $item)
                            @php
                                $set = $item->_set();
                                $sel = in_array($item->id, $selected);
                            @endphp
                            <div class="-card rounded-xl -shadow-lg !bg-white cursor-pointer !border-2 border-solid border-transparent {{ $sel ? '!border-black' : '' }}" wire:click="toggleSelection('{{ $item->id }}')">
                                <div class="flex items-center justify-center">
                                    <div>
                                    <div class="rounded-full w-10 h-10 !bg-[#f3f3f3] flex items-center justify-center">
                                            @if (!$set->info('avatar'))
                                            {!! __i('--ie', 'image-picture', 'text-gray-300 w-5 h-5') !!}
                                            @else
                                            <img src="{{ $set->avatar() }}" class="rounded-full w-full h-full object-cover" alt="">
                                            @endif
                                    </div>
                                    </div>
                    
                                    <div class="mr-auto ml-4 truncate">
                                        <h2 class="flex items-center text-sm font-semibold">
                                            <div class="truncate">{{ $set->info('name') }}</div>
                                        </h2>
                                        <div class="text-xs text-gray-500">
                                            <div class="truncate">
                                                <span class="flex gap-3">
                                                {{ $set->info('email') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <div class="flex justify-end gap-2">
                                        <div>
                                            @if ($sel)
                                                <div class="menu--icon !bg-green-400 !text-[10px] !text-black !w-auto !px-2 !h-5 cursor-pointer">
                                                    {{ __('Selected') }}
                                                </div>
                                                @else
                                                <div class="menu--icon !bg-black !text-[10px] !text-white !w-auto !px-2 !h-5 cursor-pointer">
                                                    {{ __('Select') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
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
       Alpine.data('audience_create_folder', () => {
          return {
            has_more_pages: @entangle('has_more_pages'),
            init(){
                let $this = this;
                let _throttleTimer = null;
                let _throttleDelay = 100;

                let handler = function(e) {
                    console.log($this.$refs.windowContactWrapper.scrollTop + $this.$refs.windowContactWrapper.clientHeight, $this.$refs.windowContactWrapper.scrollHeight - 100);

                    clearTimeout(_throttleTimer);
                    _throttleTimer = setTimeout(function() {
                        if ($this.$refs.windowContactWrapper.scrollTop + $this.$refs.windowContactWrapper.clientHeight > $this.$refs.windowContactWrapper.scrollHeight - 100) {
                            if($this.has_more_pages){
                                $this.$wire.loadData();
                            }
                        }
                    }, _throttleDelay);
                };

                $this.$refs.windowContactWrapper.removeEventListener('scroll', handler);
                $this.$refs.windowContactWrapper.addEventListener('scroll', handler);
            }
          }
       });
   </script>
   @endscript
</div>