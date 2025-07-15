
<?php
   use App\Livewire\Actions\ToastUp;
   use App\Models\Folder;
   use App\Models\FolderMember;
   use function Livewire\Volt\{state, mount, placeholder, updated, uses, on};

   uses([ToastUp::class]);
   mount(fn() => $this->getFolders());

   state([
      'query' => null,
      'folders' => []
   ]);

   on([
      'refreshFoldersModal' => fn() => $this->getFolders(),
   ]);

   updated([
      'query' => function(){
         $this->getFolders();
      },
   ]);

   $getFolders = function(){
      $this->folders = Folder::where('owner_id', iam()->id)->where('name', 'like', '%'.$this->query.'%')->get();
   };

   $createFolder = function(){
      $this->validate([
         'query' => 'required',
      ]);

      $slug = slugify($this->query, '-') . '-' . \Str::random(8);

      $create = new Folder;
      $create->owner_id = iam()->id;
      $create->name = $this->query;
      $create->slug = $slug;
      $create->save();

      $member = new FolderMember;
      $member->user_id = iam()->get_original_user()->id;
      $member->folder_id = $create->id;
      $member->save();


      $this->query = '';
      $this->getFolders();
      $this->dispatch('refreshFolders');
   };

   $leaveMember = function($id){
      FolderMember::where('user_id', iam()->get_original_user()->id)->where('folder_id', $id)->delete();
      
      $this->getFolders();
      $this->dispatch('refreshFolders');
   };

   $joinMember = function ($id){
      // Check if i have team.

      $member = new FolderMember;
      $member->user_id = iam()->get_original_user()->id;
      $member->folder_id = $id;
      $member->save();

      $folder = $member->folder;

      $this->getFolders();
      $this->dispatch('refreshFolders');

      //$this->_f('success', __('You are no longer a member of ":folder"', ['folder' => $folder->name]));
   };

   placeholder('
   <div class="p-0 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');
?>


<div class="w-full">
   <div>
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross-small"></i>
      </a>

      <header class="flex pt-4 px-6 flex-initial text-3xl font-black">{{ __('Create or join a folder') }}</header>

      <div class="flex flex-1 flex-col gap-3 px-6 py-2 mb-2 text-[color:var(--yena-colors-gray-500)]">
         {{ __('You can join a folder to keep track of what folks are working on.') }}
      </div>


      <div class="px-6 pb-5">
         <div class="flex flex-col">
            <form wire:submit="createFolder">
               <div class="flex items-start gap-4">
                  <div class="w-full">
                     <div class="yena-form-group">
                        <div class="--left-element">
                           {!! __i('interface-essential', 'search.1', 'w-5 h-5') !!}
                        </div>
      
                        <input type="text" wire:model.live="query" placeholder="{{ __('Find or create a new folder') }}">
                        <div class="--right-element !hidden" wire:loading.class.remove="!hidden" wire:target="query">
                           <div class="yena-spinner !w-4 !h-4 !border-2"></div>
                        </div>
                     </div>
                  </div>
      
                  <button class="yena-button-stack w-[300px] {{ empty($query) ? 'opacity-40 cursor-not-allowed [box-shadow:var(--yena-shadows-none)] pointer-events-none' : '' }}">{{ __('Create folder') }}</button>
               </div>
            </form>
         </div>

         <div class="mt-5">
            <p class="text-[color:var(--yena-colors-gray-500)] text-sm font-semibold">
               {{ __('All folders') }}
            </p>

            <div class="flex flex-col gap-4 mt-5">
               @foreach ($folders as $item)
               <div class="flex items-center flex-row pt-[var(--yena-space-2)] pb-[var(--yena-space-2)] rounded-[var(--yena-radii-md)] [transition-property:var(--yena-transition-property-common)] hover:bg-[var(--yena-colors-gray-100)] px-4">
                  <div class="p-2">
                     @if ($item->isMember(iam()->id))
                     {!! __i('interface-essential', 'checkmark-circle-1', 'w-5 h-5') !!}
                     @else
                     {!! __i('Folders', 'folder-open', 'w-5 h-5') !!}
                     @endif
                  </div>

                  @php

                     $membersText = __(":members members:include", [
                        'members' => $item->members()->count(),
                        'include' => $item->isMember(iam()->id) ? __(', including you') : '',
                     ]);
                  @endphp

                  <div class="flex items-center flex-row flex-1 ml-4">
                     <div class="flex flex-col">
                        <p>{{ $item->name }}</p>
                        <p class="text-sm text-[color:var(--yena-colors-gray-400)]">{{ $membersText }}</p>
                     </div>

                     <div class="flex-[1] justify-self-stretch self-stretch ml-2"></div>

                     <div class="flex items-center justify-end flex-row-reverse">
                        <div class="relative">
                           @foreach ($item->members()->orderBy('id', 'DESC')->get() as $member)
                           <div class="yena-avatar !w-6 !h-6 [box-shadow:var(--yena-shadows-md)] border-2 border-white !-mr-3">
                              <img src="{{ $member->user->getAvatar() }}" alt="{{ $member->user->name }}" class="w-full h-full object-cover">
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </div>

                  @if ($item->isMember(iam()->get_original_user()->id))

                     <a wire:click="leaveMember({{ $item->id }})" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-[var(--yena-radii-md)] font-semibold [transition-property:var(--yena-transition-property-common)] h-[var(--yena-sizes-8)] [var(--yena-sizes-8)] text-sm bg-[var(--yena-colors-transparent)] w-[var(--yena-sizes-20)] text-[color:var(--yena-colors-gray-600)] cursor-pointer ml-4 hover:bg-[var(--yena-colors-trueblue-50)]">
                        {{ __('Leave') }}
                     </a>

                     @else
                     <a wire:click="joinMember({{ $item->id }})" class="yena-button-stack ml-4 !h-8 cursor-pointer">{{ __('Join') }}</a>
                  @endif
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>
</div>