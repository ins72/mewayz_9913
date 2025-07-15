
<?php
   use App\Models\UserConversation;
   use App\Models\UserMessage;
   use App\Livewire\Actions\ToastUp;
   use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated, usesFileUploads};

   usesFileUploads();

   state([
      'image' => null,

      'create' => [
         'name' => '',
      ]
   ]);

   rules(fn () => [
      'create.name' => 'required',
   ]);
   uses([ToastUp::class]);
   mount(fn() => '');

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


   $getUsers = function($data){
      
      $search = ao($data, 'search');
      $filter_type = filter_var($search, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

      if($search != '' && strlen($search) >= 2){
         $users = \App\Models\User::where(function ($query) use ($search) {
            foreach (['name', 'email'] as $field)
               $query->orWhere($field, 'like', '%' . $search . '%');
         })->limit(5)->get()->map(function($item){
            $item->avatar_link = $item->getAvatar();

            return $item;
         });
    
         return $users;
      }


      return false;
   };

   $_create = function($id){

      if(!$user = \App\Models\User::where('id', $id)->first()) return;

      $create = true;

      $conversation_id = null;
      $first = UserConversation::where('user_1', $user->id)->where('user_2', auth()->user()->id)->first();
      $second = UserConversation::where('user_1', auth()->user()->id)->where('user_2', $user->id)->first();

      if($first){
         $create = false;
         $conversation_id = $first->id;
      }

      if($second){
         $create = false;
         $conversation_id = $second->id;
      }


      if($create){
         $n = new UserConversation;
         $n->user_1 = $user->id;
         $n->user_2 = auth()->user()->id;
         $n->save();
         
         $conversation_id = $n->id;
         $this->dispatch('conversationRefresh');
      }
      

      if(!$conversation_id){
         $this->dispatch('openConversation', ['id' => $conversation_id]);
      }
   };
?>


<div class="w-full">
  <div x-data="create_message">
   <div class="flex flex-col">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Start a Conversation') }}</header>

      <hr class="yena-divider">

      <form wire:submit="createProduct" class="px-8 pt-2 pb-6">

         <div class="form-input mb-4 mt-4">
            <label>{{ __('Search') }}</label>
            <input type="text" name="name" x-model="filter_user" x-on:keyup="get_user()">
        </div>

        <div class="flex flex-col">
            
            <template x-for="(user, index) in _users" :key="index">
                <a class="contact-list block remove-before" @click="createConversation(user.id)">
                    <div class="flex items-center justify-center">
                        <div>
                           <div class="rounded-md w-10 h-10 [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] p-0.5 flex items-center">
                              <img :src="user.avatar_link" class="w-full h-full object-cover rounded-md p-0 m-0" alt="">
                          </div>
                        </div>

                        <div class="mr-auto ml-4 w-full flex items-center">
                            <div>
                                <h2 class="flex items-center text-sm font-semibold">
                                    <div class="truncate" x-text="user.name"></div>
                                </h2>
                                <div class="text-sm text-gray-400">
                                    <span x-text="'@' + user.email"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </template>
        </div>

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
      </form>
   </div>
  </div>

  
  @script
  <script>
      Alpine.data('create_message', () => {
         return {
            filter_user: '',
            field_timer: '',
            _users: {},
            createConversation(user_id){
               let $this = this;
               this.$wire._create(user_id).then(r => {
                  $this.$dispatch('close');
               });
            },

            get_user(){
                _this = this;
  
                _this._users = {};
                var data = {
                    search: _this.filter_user
                };

                if (this.field_timer) {
                    clearTimeout(this.field_timer);
                    this.field_timer = null;
                }

                this.field_timer = setTimeout(() => {
                  _this.$wire.getUsers(data).then(r => {
                     if(r){
                        _this._users = r;
                     }
                  });
                }, 800);
            }
         }
      });
  </script>
  @endscript
</div>