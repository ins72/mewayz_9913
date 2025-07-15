
<?php
   use App\Models\Folder;
   use function Livewire\Volt\{state, mount, placeholder, updated, on};

   mount(fn() => $this->getFolders());

   state([
      'folders' => []   
   ]);

   on([
      'refreshFolders' => fn() => $this->getFolders(),
      'refreshFolderComponent' => function(){
         $this->dispatch('$refresh');
      }
   ]);

   $getFolders = function(){
      // Get them folders
      $foldersModel = Folder::where('owner_id', iam()->id)->get();


      $folders = [];

      foreach ($foldersModel as $item) {
          if(!$item->isMember(iam()->get_original_user()->id)) continue;
          $folders[] = $item;
      }
      

      $this->folders = $folders;
   };

   placeholder('
   <div class="p-0 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');
?>


<div class=" w-full">
   
   <div class="w-full mt-1">
      <div class="flex items-center justify-between">
         <p class="text-[color:var(--yena-colors-gray-500)] text-sm text-left mb-3 mt-4">{{ __('Folders') }}</p>

         <x-x.href class="dot-button cursor-pointer" @click="$dispatch('open-modal', 'folders-modal');">
            <i class="fi fi-rr-plus text-[10px]"></i>
         </x-x.href>
      </div>

      @if (empty($folders))
          <div class="bg-[var(--yena-colors-gray-50)] text-center p-[var(--yena-space-4)] mb-2">
            <p class="text-sm text-center">{{ __('Organize your sites by topic and share them with your team') }}</p>
            <p class="text-sm mt-2 font-bold text-center">
               <a class="[transition-property:var(--yena-transition-property-common)] cursor-pointer no-underline outline-[transparent_solid_2px] outline-offset-[2px] text-[var(--yena-colors-trueblue-600)] cursor-pointer" @click="$dispatch('open-modal', 'folders-modal');">{{ __('Create or join a folder') }}</a>
            </p>
          </div>
      @endif

      @foreach ($folders as $item)
      <a class="sidebar-item" href="{{ route('dashboard-folders-index', ['slug' => $item->slug]) }}" @navigate {{ _k() }}>
         <div class="--inner">
            {!! __icon('Folders', 'folder-bookmark') !!}
            <p>{{ $item->name }}</p>
         </div>
      </a>
      @endforeach
   </div>

   <template x-teleport="body">
     <x-modal name="folders-modal" :show="false" removeoverflow="true" maxWidth="2xl" focusable>
         <livewire:components.dashboard.sidebar.folders-modal>
         
         {{-- <footer class="flex flex-row gap-2 px-6 py-4 justify-end">
            
            <button class="z-0 group relative inline-flex items-center justify-center box-border appearance-none select-none whitespace-nowrap font-normal subpixel-antialiased overflow-hidden tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 px-unit-4 min-w-unit-20 h-unit-10 text-small gap-unit-2 rounded-medium [&amp;>svg]:max-w-[theme(spacing.unit-8)] data-[pressed=true]:scale-[0.97] transition-transform-colors motion-reduce:transition-none bg-default text-default-foreground" type="button" @click="$dispatch('close')">{{ __('Done') }}</button>
         </footer> --}}
      </x-modal>
   </template>
</div>