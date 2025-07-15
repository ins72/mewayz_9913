
<?php
   use App\Yena\Teams;
   use function Livewire\Volt\{state, mount, placeholder, on};


   state([
      'teams' => function(){
         $team = Teams::get_other_teams()->map(function($item){
            $item->team = $item->team ? $item->team->toArray() : [];
            return $item;
         })->toArray();

         return $team;
      },
      'avatar' => fn() => iam()->get_original_user()->getAvatar(),
      'userTeam' => fn() => iam()->get_original_user()->team()->toArray(),

      'team' => [],
      'is_set_team' => false,
   ]);

   $_is_set_team = function(){
      $manager = app('impersonate');

      $this->is_set_team = false;

      if(session()->has('set_team') && $manager->isImpersonating()){
         $this->is_set_team = true;
      }
   };

   $_get_team = function(){
      $this->team = Teams::get_team()->toArray();
   };

   $initGet = function(){
      $this->_is_set_team();
      $this->_get_team();
   };

   on([
      'updatedAvatar' => function($avatar = null) {
         $this->avatar = $avatar;
      },

      'refreshSidebarComponent' => function(){
         $this->dispatch('$refresh');
      }
   ]);


   $setTeam = function($team){
      $auth = Teams::get_original_user();

      // dd($auth, $team, $this->teams, Teams::has_team($auth->id, ao($team, 'id')));


      if(!$t = Teams::has_team($auth->id, ao($team, 'team.id'))) abort(404);
      if(!$t->team) abort(404);

      Teams::set_team($t->team->id);

      $this->initGet();
      $this->dispatch('refreshFoldersModal');
      $this->dispatch('refreshWorkspace');
      $this->dispatch('set-team-name', $t->team->name);
      $this->dispatch('updated-workspace-logo', $t->team->getLogo());
      $this->dispatch('refreshFolders');
      $this->redirect(route('dashboard-index'), true);
   };

   $removeTeam = function(){
      if(Teams::is_set_team()){
         \Auth::user()->leaveImpersonation();
         session()->forget('set_team');
      }
      
      $this->initGet();
      $this->dispatch('refreshWorkspace');
      $this->dispatch('refreshFoldersModal');
      $this->dispatch('set-team-name', ao($this->team, 'name'));
      $this->dispatch('updated-workspace-logo', Teams::get_team()->getLogo());
      $this->dispatch('refreshFolders');
      $this->redirect(route('dashboard-index'), true);
   };

   mount(function(){
      $this->initGet();
   });

   placeholder('placeholders.console.sidebar-placeholder');
?>

<div class="app-sidebar">


   <div x-data="sidebar__menu">
      <div class="yena-sidebar-inner bg-white dark:bg-gray-800 sidebar">
         <div class="-header-sidebar" x-data="{ sidebarTippy: {
            content: () => $refs.template.innerHTML,
            allowHTML: true,
            appendTo: $root,
            maxWidth: 415,
            interactive: true,
            trigger: 'click',
            animation: 'scale',
         } }">

            <div class="md:!hidden flex items-center justify-end mb-2">
               <div class="block">
                  <button type="button" class="yena-btn-clean !shadow-none !pr-0" @click="$store.app.toggleSidebar()">
                     <svg viewBox="0 0 24 24" focusable="false" class="h-[1em] w-[1em] inline-block leading-[1em] flex-shrink-0 text-current align-middle w-[var(--yena-sizes-3)]" aria-hidden="true"><path fill="currentColor" d="M.439,21.44a1.5,1.5,0,0,0,2.122,2.121L11.823,14.3a.25.25,0,0,1,.354,0l9.262,9.263a1.5,1.5,0,1,0,2.122-2.121L14.3,12.177a.25.25,0,0,1,0-.354l9.263-9.262A1.5,1.5,0,0,0,21.439.44L12.177,9.7a.25.25,0,0,1-.354,0L2.561.44A1.5,1.5,0,0,0,.439,2.561L9.7,11.823a.25.25,0,0,1,0,.354Z"></path></svg>
                  </button>
               </div>
            </div>

            <template x-ref="template" class="hidden">
               <div class="yena-menu-list !w-[415px]">
            
                  <a class="yena-menu-list-item border border-transparent hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer" @click="$dispatch('open-modal', 'edit-profile-modal');">
                     <span class="yena-avatar !h-8 !w-8">
                        <img src="{{ $avatar }}" class="yena-update-avatar w-full h-full object-cover" alt=" " class="">
                     </span>
                     <div class="flex flex-col ml-2">
                        <p class=" text-sm text-gray-900 dark:text-white">{{ iam()->get_original_user()->name }}</p>
                        <p class="text-gray-500 dark:text-gray-400 text-xs">{{ iam()->email }}</p>
                     </div>
                     <div class="--icon ml-auto !mr-0">
                        {!! __icon('interface-essential', 'setting4', 'w-5 h-5') !!}
                     </div>
                  </a>
                  <hr class="--divider border-gray-200 dark:border-gray-700">

                  <div class="-menu-group">
                     <div class="mx-4 my-2 font-semibold text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Workspaces') }}
                     </div>
                  </div>

                  
                  <div class="yena-menu-list-item border border-transparent hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer" @click="is_set_team ? $wire.removeTeam() : Livewire.navigate('{{ route('dashboard-settings-index') }}');">
                     <span class="yena-avatar !h-[32px] !w-[32px] !bg-purple-500 !text-white !flex items-center justify-center">
                        <img src="{{ iam()->get_original_user()->team()->getLogo() }}" class="w-full h-full rounded-full !object-cover" alt="">
                     </span>
                     <div class="flex flex-col ml-2">
                        <p class="text-sm text-gray-900 dark:text-white">{{ iam()->get_original_user()->team()->name }}</p>
                     </div>
                     <template x-if="team.id == userTeam.id">
                        <div class="--icon ml-auto !mr-0">
                           <a class="yena-btn-clean !text-[color:var(--yena-colors-trueblue-600)] border-2 cursor-pointer">{{ __('Settings & members') }}</a>
                        </div>
                     </template>
                  </div>

                  <template x-for="(item, index) in teams" :key="item.uuid">
                     <div class="yena-menu-list-item border border-transparent hover:border-[color:var(--yena-colors-gray-200)!important] hover:bg-[var(--yena-colors-gray-100)!important] cursor-pointer" @click="is_set_team && team.id == item.team.id ? Livewire.navigate('{{ route('dashboard-settings-index') }}') : $wire.setTeam(item)">
                        <span class="yena-avatar !h-[32px] !w-[32px] !bg-[var(--yena-colors-orchid-300)] !text-white !flex items-center justify-center">
                           <img :src="item.team.logo_json" class="w-full h-full rounded-full !object-cover" alt="">
                        </span>
                        <div class="flex flex-col ml-2">
                           <p class="text-sm" x-text="item.team.name"></p>
                        </div>
                        <template x-if="team.id == item.team.id">
                           <div class="--icon ml-auto !mr-0">
                              <a class="yena-btn-clean !text-[color:var(--yena-colors-trueblue-600)] border-2 cursor-pointer">{{ __('Settings & members') }}</a>
                           </div>
                        </template>
                     </div>
                  </template>


                  <hr class="--divider">
                  <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="$dispatch('logout')">
                     <div class="--icon">
                        {!! __icon('interface-essential', 'login-ogout', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Sign out') }}</span>
                  </a>
               </div>
            </template>

            {{-- <template x-teleport="body">
               <x-modal name="workspace-modal" :show="false" removeoverflow="true" maxWidth="max-w-[var(--yena-sizes-3xl)]" >
                  <livewire:components.console.sidebar.workspace-modal lazy>
               </x-modal>
            </template> --}}


            <template x-teleport="body">
               <x-modal name="edit-profile-modal" :show="false" removeoverflow="true" maxWidth="xl" >
                  <livewire:components.dashboard.sidebar.profile-modal lazy>
               </x-modal>
            </template>


            <div class="logo">
               <a href="/" name="">
                  <img src="{{ logo() }}" class="h-16 w-16 object-contain" alt=" " width="36" class="block">
               </a>
            </div>
            

            <a class="sidebar-workspace" x-tooltip="{...sidebarTippy}">
               <div class="--padded" x-data="{
                  _name: `{{ iam()->team()->name }}`,
                  _logo: `{{ iam()->team()->getLogo() }}`,
               }">
                  <div class="flex-grow flex-shrink basis-auto pointer-events-none min-w-0" @set-team-name.window="_name = $event.detail;" @updated-workspace-logo.window="_logo = $event.detail[0];">
                     <div class="w-full">
                        <div class="flex items-center mr-4">
                           <div class="--avatar">
                              <div class="w-full h-full --icon" >
                                 <img :src="_logo" class="w-full h-full rounded-full !object-cover" alt="">
                              </div>
                           </div>

                           <div class="--text">
                              <p x-text="_name"></p>
                           </div>
                        </div>
                     </div>
                  </div>

                  <div class="flex self-center shrink ml-2">
                     {!! __i('Arrows, Diagrams', 'Arrow.2', 'h-6') !!}
                  </div>
               </div>
               </a>
         </div>
         <div class="sidebar-search" @click="$dispatch('open-modal', 'search-sites-modal');">
            <div class="--icon">
               {!! __i('interface-essential', 'search.1') !!}
            </div>
            <input type="text" placeholder="{{ __('Jump to') }}">
            <div class="--right-element">
               <p>{{ __('Ctrl+k') }}</p>
            </div>
         </div>
         <div class="flex flex-col items-center mt-4 mb-2">
            <x-x.href class="sidebar-item" href="{{ route('console-index') }}">
               <div class="--inner">
                  {!! __icon('interface-essential', 'dashboard.3') !!}
                  <p>{{ __('Console') }}</p>

                  <span class="dot-button ml-auto !hidden">
                        <i class="fi fi-rr-plus text-[10px]"></i>
                  </span>
               </div>
            </x-x.href>

            <x-x.href class="sidebar-item" href="{{ route('console-sites-index') }}">
               <div class="--inner">
                  {!! __icon('interface-essential', 'browser-internet-web-network-window-app-icon') !!}
                  <p>{{ __('All Sites') }}</p>

                  <span class="dot-button ml-auto !hidden">
                        <i class="fi fi-rr-plus text-[10px]"></i>
                  </span>
               </div>
            </x-x.href>
            <a class="sidebar-item" href="{{ link_in_bio_route() }}">
               <div class="--inner">
                  {!! __icon('interface-essential', 'browser-web-link') !!}
                  <p>{{ __('Link in Bio') }}</p>

                  <span class="dot-button ml-auto !hidden">
                        <i class="fi fi-rr-plus text-[10px]"></i>
                  </span>
               </div>
            </a>

            <div class="w-full --divider-wrapper">
               <div class="flex items-center justify-between">
                  <p class="text-[color:var(--yena-colors-gray-500)] text-sm text-left mb-3 mt-4">{{ __('Business Growth') }}</p>
               </div>
            </div>

            <x-x.href class="sidebar-item" href="{{ route('console-crm-index') }}">
               <div class="--inner">
                  {!! __icon('Business, Products', 'business-customer-service') !!}
                  <p>{{ __('CRM') }}</p>
               </div>
            </x-x.href>

            <x-x.href class="sidebar-item" href="{{ route('console-email-index') }}">
               <div class="--inner">
                  {!! __icon('emails', 'email-marketing') !!}
                  <p>{{ __('Email Marketing') }}</p>
               </div>
            </x-x.href>

            <x-x.href class="sidebar-item" href="{{ route('console-instagram-index') }}">
               <div class="--inner">
                  {!! __icon('social-media', 'instagram-social-media') !!}
                  <p>{{ __('Instagram') }}</p>
               </div>
            </x-x.href>

            <x-x.href class="sidebar-item" href="{{ route('console-social-index') }}">
               <div class="--inner">
                  {!! __icon('social-media', 'social-media-share') !!}
                  <p>{{ __('Social Media') }}</p>
               </div>
            </x-x.href>

            <x-x.href class="sidebar-item" href="{{ route('console-analytics-index') }}">
               <div class="--inner">
                  {!! __icon('Business, Products', 'business-report-chart') !!}
                  <p>{{ __('Analytics') }}</p>
               </div>
            </x-x.href>

            <x-x.href class="sidebar-item" href="{{ route('console-workspace-index') }}">
               <div class="--inner">
                  {!! __icon('interface-essential', 'workspace') !!}
                  <p>{{ __('Workspace') }}</p>
               </div>
            </x-x.href>

            <div class="w-full --divider-wrapper">
               <div class="flex items-center justify-between">
                  <p class="text-[color:var(--yena-colors-gray-500)] text-sm text-left mb-3 mt-4">{{ __('Monetize') }}</p>
               </div>
            </div>

            <x-x.href class="sidebar-item" href="{{ route('console-wallet-index') }}">
               <div class="--inner">
                  {!! __icon('money', 'Wallet-MAIN') !!}
                  <p>{{ __('Wallet') }}</p>
               </div>
            </x-x.href>

            <x-x.href class="sidebar-item" href="{{ route('console-audience-index') }}">
               <div class="--inner">
                  {!! __icon('emails', 'email-mail-letter') !!}
                  <p>{{ __('Leads') }}</p>
               </div>
            </x-x.href>

            <x-x.href class="sidebar-item" href="{{ route('console-store-index') }}">
               <div class="--inner">
                  {!! __icon('Building, Construction', 'store') !!}
                  <p>{{ __('Store') }}</p>
               </div>
            </x-x.href>

            <x-x.href class="sidebar-item" href="{{ route('console-courses-index') }}">
               <div class="--inner">
                  {!! __icon('Content Edit', 'Book, Open.4') !!}
                  <p>{{ __('Courses') }}</p>
               </div>
            </x-x.href>
            <x-x.href class="sidebar-item" href="{{ route('console-donations-index') }}">
               <div class="--inner">
                  {!! __icon('custom', 'settings-pay-1') !!}
                  <p>{{ __('Donations') }}</p>
               </div>
            </x-x.href>

            <x-x.href class="sidebar-item" href="{{ route('console-booking-index') }}">
               <div class="--inner">
                  {!! __icon('Cleaning, Housekeeping', 'calendar-schedule') !!}
                  <p>{{ __('Booking') }}</p>
               </div>
            </x-x.href>
            <a class="sidebar-item !hidden" href="{{ mediakit_in_bio_route() }}">
               <div class="--inner">
                  {!! __icon('shopping-ecommerce', 'store-shopping-basket') !!}
                  <p>{{ __('Mediakit') }}</p>
               </div>
            </a>
            {{-- <x-x.href class="sidebar-item">
               <div class="--inner">
                  {!! __icon('interface-essential', 'magic-wand-mini') !!}
                  <p>{{ __('AI Brand Outreach') }}</p>
               </div>
            </x-x.href>
            <x-x.href class="sidebar-item">
               <div class="--inner">
                  {!! __icon('shopping-ecommerce', 'invoice-list') !!}
                  <p>{{ __('W-9 Generator') }}</p>
               </div>
            </x-x.href> --}}
            <x-x.href class="sidebar-item" href="{{ route('console-invoicing-index') }}">
               <div class="--inner">
                  {!! __icon('Payments Finance', 'Invoice, Accounting.1') !!}
                  <p>{{ __('Invoicing') }}</p>
               </div>
            </x-x.href>
            <x-x.href class="sidebar-item" href="{{ route('console-shortener-index') }}">
               <div class="--inner">
                  {!! __icon('interface-essential', 'attachment-link.4') !!}
                  <p>{{ __('Link Shortener') }}</p>
               </div>
            </x-x.href>
            <div class="w-full pt-1 pb-3 border-[var(--yena-colors-gray-200)]">
               <hr class="w-full opacity-[0.6] border-b border-solid">
            </div>
            <x-x.href class="sidebar-item" href="{{ route('console-messages-index') }}">
               <div class="--inner">
                  {!! __icon('Servers Databases', 'server-databases-connect') !!}
                  <p>{{ __('Messages') }}</p>
               </div>
            </x-x.href>
            <x-x.href class="sidebar-item" href="{{ route('console-qrcode-index') }}">
               <div class="--inner">
                  {!! __icon('shopping-ecommerce', 'Qr code.1') !!}
                  <p>{{ __('QrCode') }}</p>
               </div>
            </x-x.href>

            <div class="--folders w-full">
               <livewire:components.console.sidebar.folders lazy>
            </div>
            
            <div class="w-full pt-1 pb-3 border-[var(--yena-colors-gray-200)]">
               <hr class="w-full opacity-[0.6] border-b border-solid">
            </div>
            <x-x.href class="sidebar-item" href="{{ route('console-templates-index') }}">
               <div class="--inner">
                  {!! __icon('interface-essential', 'thunder-lightning-notifications') !!}
                  <p>{{ __('Templates') }}</p>
               </div>
            </x-x.href>
            {{-- <x-x.href class="sidebar-item">
               <div class="--inner">
                  {!! __icon('Content Edit', 'open-book') !!}
                  <p>{{ __('Inspiration') }}</p>
               </div>
            </x-x.href> --}}
            <x-x.href class="sidebar-item" href="{{ route('console-trash-index') }}">
               <div class="--inner">
                  {!! __icon('interface-essential', 'trash-bin-delete') !!}
                  <p>{{ __('Trash') }}</p>
               </div>
            </x-x.href>
         </div>

         <div class="w-full pt-1 pb-3 border-[var(--yena-colors-gray-200)]">
            <hr class="w-full opacity-[0.6] border-b border-solid">
         </div>

         <x-x.href class="sidebar-item !hidden" href="{{ route('console-settings-index') }}">
            <div class="--inner">
               {!! __icon('Users', 'single-user-add-plus_1') !!}
               <p>{{ __('Settings') }}</p>
            </div>
         </x-x.href>

         {{-- <x-x.href class="sidebar-item" href="{{ route('console-settings-account') }}">
            <div class="--inner">
               {!! __icon('Users', 'single-user-add-plus_1') !!}
               <p>Invite to workspace</p>
            </div>
         </x-x.href> --}}
         <x-x.href class="sidebar-item" href="{{ route('console-upgrade-index') }}">
            <div class="--inner">
               {!! __icon('Delivery', 'Delivery, Shipment, Packages.4') !!}
               <p>{{ __('Upgrade') }}</p>
            </div>
         </x-x.href>
         <x-x.href class="sidebar-item" href="{{ route('console-settings-index') }}">
            <div class="--inner">
               {!! __icon('--ie', 'settings.12') !!}
               <p>{{ __('Settings & Workspace') }}</p>
            </div>
         </x-x.href>
         {{-- <x-x.href class="sidebar-item">

            <div class="--inner">
               {!! __icon('money', 'Coins') !!}
               <p>245 credit</p>
            </div>
         </x-x.href> --}}

         <a class="sidebar-item" target="_blank" href="mailto:{{ config('app.APP_EMAIL') }}">
            <div class="--inner">
               {!! __icon('Messages Chat', 'Chat, Messages, Bubble.12') !!}
               <p>{{ __('Contact us') }}</p>
            </div>
         </a>

         @if (iam()->isAdmin())
         <div class="w-full pt-1 pb-3 border-[var(--yena-colors-gray-200)]">
            <hr class="w-full opacity-[0.6] border-b border-solid">
         </div>

         <a class="sidebar-item" href="{{ route('console-admin-users-index') }}">
            <div class="--inner">
               {!! __icon('Users', 'single-user-add-plus_1') !!}
               <p>{{ __('Users') }}</p>
            </div>
         </a>
         <a class="sidebar-item" href="{{ route('console-admin-bio-index') }}">
            <div class="--inner">
               {!! __icon('interface-essential', 'browser-web-link') !!}
               <p>{{ __('Bio Pages') }}</p>
            </div>
         </a>
         <a class="sidebar-item" href="{{ route('console-admin-bio-templates-index') }}">
            <div class="--inner">
               {!! __icon('interface-essential', 'thunder-lightning-notifications') !!}
               <p>{{ __('Bio Templates') }}</p>
            </div>
         </a>
         <a class="sidebar-item" href="{{ route('console-admin-sites-index') }}">
            <div class="--inner">
               {!! __icon('--ie', 'browser-internet-web-network-window-app-icon') !!}
               <p>{{ __('Sites') }}</p>
            </div>
         </a>
         <a class="sidebar-item" href="{{ route('console-admin-templates-index') }}">
            <div class="--inner">
               {!! __icon('interface-essential', 'thunder-lightning-notifications') !!}
               <p>{{ __('Templates') }}</p>
            </div>
         </a>
         <a class="sidebar-item" href="{{ route('console-admin-website-index') }}">
            <div class="--inner">
               {!! __icon('Internet, Network', 'Browser, Internet, Web, Network, Grid') !!}
               <p>{{ __('My Website') }}</p>
            </div>
         </a>
         <a class="sidebar-item" href="{{ route('console-admin-plans-index') }}">
            <div class="--inner">
               {!! __icon('Delivery', 'Delivery, Shipment, Packages.4') !!}
               <p>{{ __('Plans') }}</p>
            </div>
            </a>
         <a class="sidebar-item" href="{{ route('console-admin-payments-index') }}">
            <div class="--inner">
               {!! __icon('Payments Finance', 'Credit cards') !!}
               <p>{{ __('Payments') }}</p>
            </div>
            </a>
         <a class="sidebar-item" href="{{ route('console-admin-languages-index') }}">
            <div class="--inner">
               {!! __icon('--ie', 'language-translate') !!}
               <p>{{ __('Translation') }}</p>
            </div>
            </a>
         <a class="sidebar-item" href="{{ route('console-admin-settings-index') }}">
            <div class="--inner">
               {!! __icon('--ie', 'settings.10') !!}
               <p>{{ __('Settings') }}</p>
            </div>
         </a>
         @endif

         <div class="flex-1"></div>

         <div class="flex items-end justify-center">
            <div class="">
               <img src="{{ logo_icon() }}" class="h-10 w-10 object-contain" alt=" " width="36" class="block">
            </div>
         </div>
         {{-- <div class="upgrade-plan [!box-shadow:var(--yena-shadows-base)] !border !border-solid [border-image:none] !border-[var(--yena-colors-trueblue-100)] !p-3">
            <div class="--header">
               <span class="--plan-card ![background:#000000]">{{ __('Pro') }}</span>
               <span class="--plan-text">{{ __('Upgrade your plan') }}</span>
            </div>

            <div class="--text">{{ __('Unlock unlimited possibilities with our pro plan.') }}</div>

            <div class="--button">
               <x-x.href href="{{ route('console-upgrade-index') }}">{{ __('View Plans') }}</x-x.href>
            </div>
         </div> --}}
      
      </div>
   </div>


   
   
   @script
   <script>
       Alpine.data('sidebar__menu', () => {
          return {
            teams: @entangle('teams'),
            is_set_team: @entangle('is_set_team'),
            team: @entangle('team'),
            userTeam: @entangle('userTeam'),
            init(){
               var $this = this;
               document.addEventListener('livewire:navigating', (e) => {
                  $this.$store.app.isShortSidebar = false;
               });
            }
          }
       });
   </script>
   @endscript
</div>
