
<?php
   use App\Yena\SandyAudience;
   use App\Livewire\Actions\ToastUp;
   use App\Models\Audience;
   use App\Models\AudienceBroadcastUser;
   use App\Models\AudienceBroadcast;
   use App\Models\AudienceFolder;
   use App\Models\AudienceFoldersUser;
   use App\Traits\AudienceTraits;
   use App\Yena\VCard\VCard;
   use Illuminate\Support\Facades\Storage;
   use function Livewire\Volt\{state, mount, placeholder, uses, on, updated};
   uses([ToastUp::class]);
   updated([
    'search' => fn() => $this->loadData(),
   ]);

   state([
      'audiences' => [],
      'folders' => [],
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
       'selected' => [],
       'folderSelected' => [],
   ]);

   state([
       'subject' => '',
       'content' => [],
       'email' => '',
       'name' => '',
       'schedule' => false,
       'schedule_on' => '',
   ]);

   mount(function(){
    $this->loadData();
    $this->_get();
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

   $_get = function(){
     $this->folders = AudienceFolder::where('user_id', iam()->id)->orderBy('id', 'desc')->get();
   };

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

   $toggleFolderSelection = function($id){
        if (in_array($id, $this->folderSelected)) {
            $this->folderSelected = array_diff($this->folderSelected, [$id]);
        } else {
            $this->folderSelected[] = $id;
        }
   };

   $saveImage = function($data){
        $filesystem = sandy_filesystem('media/broadcast');

        // Extract the file extension from the base64 data
        if (preg_match('/data:image\/(?<ext>[^;]+);base64,/', $data, $matches)) {
            $extension = $matches['ext'];
        } else {
            return [
                'data' => [
                    'success' => 0,
                    'message' => 'Invalid image data.'
                ]
            ];
        }

        // Generate a random file name with the correct extension
        $imageName = str()->random(10) . '.' . $extension;

        // Clean the base64 string
        $image = str_replace('data:image/' . $extension . ';base64,', '', $data);
        $image = str_replace(' ', '+', $image);

        // Upload the image
        $upload = putStorage("media/broadcast/$imageName", base64_decode($image));

        return [
            'data' => [
                'success' => 1,
                'file' => [
                    'url' => gs('media/broadcast', $imageName),
                ]
            ]
        ];
   };
   

   $save = function(){

        dd($this->content);
        $broadcast = new AudienceBroadcast;
        $broadcast->user_id = $this->user->id;
        $broadcast->name = $this->name;
        $broadcast->subject = $this->subject;
        $broadcast->email = $this->email;
        $broadcast->content = $this->content;
        $broadcast->schedule = $this->schedule;
        $broadcast->schedule_on = $this->schedule_on;
        $broadcast->save();

        $selected = [];

        // Collect audience_ids from selected folders
        foreach ($this->folderSelected as $key => $value) {
            if(!$folder = AudienceFolder::find($value)) continue;

            $users = $folder->users()->get();

            foreach($users as $user){
                $selected[] = $user->audience_id;
            }
        }

        // Add selected values to the array
        foreach ($this->selected as $key => $value) {
            $selected[] = $value;
        }

        // Remove duplicates
        $selected = array_unique($selected);

        foreach ($selected as $key => $value) {
            $create = new AudienceBroadcastUser;
            $create->audience_id = $value;
            $create->broadcast_id = $broadcast->id;
            $create->save();
        }

        // // Decode base64 string
        // $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->base64Image));

        // // Create a unique name for the image
        // $imageName = uniqid() . '.jpg';

        // // Save the image to the storage (e.g., public directory)
        // Storage::disk('public')->put($imageName, $image);

        // // Optionally, you can return the path of the saved image
        // $imagePath = Storage::url($imageName);


        $this->flashToast('success', __('Broadcast created successfully'));

        $this->dispatch('close');
        $this->dispatch('broadcastRefresh');
        // $this->dispatch('folderCreated');
   };
?>
<div class="w-full">
   <div x-data="audience_create_broadcast">
      <div class="flex flex-col">
         <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross text-sm"></i>
         </a>
   
         <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Broadcast') }}</header>
   
         <hr class="yena-divider">
   
         <form @submit.prevent="saveForm" class="">
            <div class=" px-8 pt-5 h-[calc(100vh_-_150px)] overflow-y-auto">
                <div class="flex flex-col gap-4">
                    <div class="form-input">
                        <label>{{ __('Subject') }}</label>
                        <input type="text" name="subject" placeholder="{{ __('My Newsletter') }}" wire:model="subject">
                    </div>


                    <div class="flex flex-col gap-4">
                        <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                           <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Sender') }}</span>
                           <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                        </div>
    
                        <div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="form-input">
                                    <input type="text" name="name" wire:model="name" placeholder="{{ __('Micheal John') }}">
                                </div>
                                <div class="form-input">
                                    <input type="text" name="name" wire:model="email" placeholder="{{ __('micheal@gmail.com') }}">
                                </div>
                            </div>
                        </div>

                        <div class="bg-[#f1f1f1] items-center rounded-[12px] text-[#5b5b5b] w-1/2 px-[5px] py-[5px] grid grid-cols-2">
                            <div class="rounded-[10px] text-[#333] text-sm px-[2px] py-[5px] text-center cursor-pointer" :class="{
                                'bg-white !cursor-default': !schedule,
                            }" @click="schedule=false">{{ __('Send Now') }}</div>
                            <div class="rounded-[10px] text-[#333] text-sm px-[2px] py-[5px] text-center cursor-pointer" :class="{
                                'bg-white !cursor-default': schedule,
                            }" @click="schedule=true">{{ __('Schedule') }}</div>
                        </div>

                        <div x-cloak x-show="schedule">
                            <div>
                                <div class="form-input">
                                    <input type="date" name="schedule_on" wire:model="schedule_on" placeholder="{{ __('Schedule Date') }}">
                                </div>
                            </div>
                        </div>

                        <div wire:ignore>
                            <div x-ref="editor" class="border-[1px] border-solid border-[#c7c7c7] p-[12px] rounded-[20px]"></div>
                        </div>

                        <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                           <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Receiver') }}</span>
                           <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                        </div>

                        <div class="bg-[#f1f1f1] items-center rounded-[12px] text-[#5b5b5b] w-1/2 px-[5px] py-[5px] grid grid-cols-2">
                            <div class="rounded-[10px] text-[#333] text-sm px-[2px] py-[5px] text-center cursor-pointer" :class="{
                                'bg-white !cursor-default': sendTo == 'audience',
                            }" @click="sendTo='audience'">{{ __('Audience') }}</div>
                            <div class="rounded-[10px] text-[#333] text-sm px-[2px] py-[5px] text-center cursor-pointer" :class="{
                                'bg-white !cursor-default': sendTo == 'folder',
                            }" @click="sendTo='folder'">{{ __('Folder') }}</div>
                        </div>
                    </div>
                </div>



                <div>
                    <div x-cloak x-show="sendTo == 'audience'">

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
           
                            <div class="overflow-y-auto h-full max-h-[600px] pt-2" x-ref="windowContactWrapper" wire:target="loadData">
                                
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
                    <div x-cloak x-show="sendTo == 'folder'">

                        <div class="grid grid-cols-1 gap-4 mt-4">
                            @foreach ($folders as $item)
                            @php
                                $sel = in_array($item->id, $folderSelected);
                            @endphp
                            <div class="flex items-center pt-[27px] pb-[27px] [border-bottom:1px_solid_#F9F7F7] bg-[#f7f3f2] rounded-2xl px-10  !border-2 border-solid border-transparent {{ $sel ? '!border-black' : '' }}" wire:click="toggleFolderSelection('{{ $item->id }}')">
                               <div class="w-14 social bg-white h-10 rounded-xl flex items-center justify-center">
                                  {!! __i('shopping-ecommerce', 'email-shopping-bag', 'text-black w-8 h-8') !!}
                               </div>
                               
                               <div class="w-6/12 ml-2">
                                 <h6 class="font-medium text-lg leading-[24px] tracking-[-.014em] text-[#000] cursor-pointer block align-middle">{{ $item->name }}</h6>
             
                                 <span class="block text-[#000] text-center font-medium text-[11px] leading-[16px] tracking-[.01em] no-underline px-[10px] py-[4px] rounded-[350px] capitalize bg-[#E7E9EB] align-middle">{{ __(':users Users', ['users' => $item->users()->count()]) }}</span>
                               </div>
                               
                               <div class="ml-auto">
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
       Alpine.data('audience_create_broadcast', () => {
          return {
            editor: null,
            sendTo: 'audience',
            schedule: false,
            has_more_pages: @entangle('has_more_pages'),

            content: @entangle('content'),
            schedule: @entangle('schedule'),
            _getBase64(file, onLoadCallback) {
                return new Promise(function(resolve, reject) {
                    var reader = new FileReader();
                    reader.onload = function() { return resolve(reader.result ); };
                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });
            },
            ediorJs(){
                let $this = this;

                let $el = $this.$root.querySelector('[x-ref="editor"]');

                let editor = new EditorJS({ 
                /** 
                * Id of Element that should contain the Editor 
                */ 
                holder: $el, 

                /** 
                * Available Tools list. 
                * Pass Tool's class or Settings object for each Tool you want to use 
                */ 
                placeholder: "{{ __('Type text or paste a link') }}",
                tools: { 
                    header: {
                        class: EditorTools.Header,
                        inlineToolbar: ['link'],
                        shortcut: 'CMD+SHIFT+H',
                    },
                    paragraph: {
                        class: EditorTools.Paragraph,
                        inlineToolbar: 1,
                        shortcut: "CMD+SHIFT+E"
                    },
                    delimiter: {
                        class: EditorTools.Delimiter,
                        shortcut: "CMD+SHIFT+D"
                    },
                    image: {
                        class: EditorTools.ImageTool,
                        config: {
                            uploader: {
                                uploadByFile: function(e) {
                                    return $this._getBase64(e).then((data) => {
                                        return $this.$wire.saveImage(data).then(r => {
                                            console.log(r)
                                            return r.data;
                                        });
                                        console.log(data);
                                    });
                                    // console.log(e)
                                }
                            },
                            field: "image",
                            types: "image/*",
                        }
                    },
                    list: { 
                        class: EditorTools.List,
                        inlineToolbar: !0,
                        config: {
                            defaultStyle: "unordered"
                        },
                        shortcut: "CMD+SHIFT+L"
                    },
                    raw: EditorTools.RawTool,
                    checklist: {
                        class: EditorTools.Checklist,
                    },
                    quote: {
                        class: EditorTools.Quote,
                        inlineToolbar: 1,
                        shortcut: "CMD+SHIFT+O",
                        config: {
                            quotePlaceholder: "Enter a quote",
                            captionPlaceholder: "Quote's author"
                        }
                    },
                    code: EditorTools.CodeTool,
                    warning: {
                        class: EditorTools.Warning,
                        inlineToolbar: 1,
                        shortcut: "CMD+SHIFT+W",
                        config: {
                            titlePlaceholder: "Title",
                            messagePlaceholder: "Message"
                        }
                    },
                    }, 
                });

                this.editor = editor;
            },

            saveForm(){

                let $this = this;

                this.editor.save().then((savedData) =>{
                    $this.content = savedData;

                    $this.$wire.save();
                }).catch((error) =>{
                    window.runToast('error', '{{ __("Error saving editor data. Please try again.") }}')
                })
            },
            init(){
                let $this = this;

                $this.ediorJs();


                let _throttleTimer = null;
                let _throttleDelay = 100;
                let $windowContactWrapper = $this.$root.querySelector('[x-ref="windowContactWrapper"]');

                let handler = function(e) {
                    clearTimeout(_throttleTimer);
                    _throttleTimer = setTimeout(function() {
                        if ($windowContactWrapper.scrollTop + $windowContactWrapper.clientHeight > $windowContactWrapper.scrollHeight - 100) {
                            if($this.has_more_pages){
                                $this.$wire.loadData();
                            }
                        }
                    }, _throttleDelay);
                };

                $windowContactWrapper.removeEventListener('scroll', handler);
                $windowContactWrapper.addEventListener('scroll', handler);
            }
          }
       });
   </script>
   @endscript
</div>