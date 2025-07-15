<?php
   use App\Livewire\Actions\ToastUp;

   use App\Yena\YenaMail;
   use App\Models\YenaTeamsInvite;
   use App\Models\YenaTeamsUserTable;
   use App\Yena\Teams;
   use function Livewire\Volt\{state, mount, placeholder, updated, rules, uses, usesFileUploads, on};

   uses([ToastUp::class]);
   usesFileUploads();
   mount(function(){
      $this->initGet();
      $this->getInvites();
   });
   rules(fn () => [
      'user.name' => 'required',
      'user.title' => '',
      'user.email' => 'required|email|unique:users,email,'.$this->user->id,
   ]);

   on([
      'refreshWorkspace' => function(){
         $this->initGet();
      },
   ]);

   state([
      'logo' => '',
   ]);
   state([
      'user' => fn() => iam()->get_original_user(),
      'avatar' => null,
   ]);
   state([
      'team' => [],
      'teamUsers' => [],
      'teamArray' => [],
      'invites' => [],
      'invitesArray' => [],
      'get_original_user' => [],
      'ce' => false,
      'teamRoute' => '',
      'logoExists' => false,
   ]);

   updated([
      'query' => function(){
         $this->getFolders();
      },
      'logo' => function(){
         if(!Teams::permission('ce')) return;
         if(!empty($this->logo)){
               $this->validate([
                  'logo' => 'image|mimes:jpeg,png,jpg,gif|max:5048'
               ]);
               storageDelete('media/team/logo', $this->team->logo);


               $filesystem = sandy_filesystem('media/team/logo');
               $logo = $this->logo->storePublicly('media/team/logo', $filesystem);
               $logo = str_replace("media/team/logo/", "", $logo);
               $this->team->logo = $logo;
         }

         $this->logoExists = !empty($this->team->logo) && mediaExists('media/team/logo', $this->team->logo) ? true : false;

         $this->team->save();
         $this->dispatch('updated-workspace-logo', gs('media/team/logo', $this->team->logo));
         $this->flashToast('success', __('Changes saved successfully'));
      },
   ]);
   $resetLogo = function(){
    $this->logo = null;
    
    
    storageDelete('media/team/logo', $this->team->logo);
    $this->team->logo = null;
    $this->team->save();
    
    $_logo = $this->team->getLogo();
    $this->dispatch('updated-workspace-logo', $_logo);

    $this->logoExists = !empty($this->team->logo) && mediaExists('media/team/logo', $this->team->logo) ? true : false;
   };

   $initGet = function(){
      $this->team = Teams::init();
      $this->teamArray = $this->team->toArray();

      $this->teamRoute = route('console-team-join', ['slug' => $this->team->slug]);

      $this->get_original_user = collect(iam()->get_original_user());
      $this->ce = Teams::permission('ce');
      $this->getMembers();
      
      $this->logoExists = !empty($this->team->logo) && mediaExists('media/team/logo', $this->team->logo) ? true : false;
   };

   $getMembers = function(){
      $teams = [];
      
      $team_users = \App\Models\YenaTeamsUserTable::where('team_id', $this->team->id)->get();
      foreach ($team_users as $item) {
         $teams[] = [
            ...$item->toArray(),
            'user' => $item->user()->first()->append('avatar_json')->toArray(),
         ];
      }

      $this->teamUsers = $teams;
   };

   $updateMember = function($item, $role){
      if(!Teams::permission('ce')) return;

      $this->skipRender();

      if(!$member = YenaTeamsUserTable::where('team_id', $this->team->id)->where('id', ao($item, 'id'))->first()) return;

      $member->can_create = $role == 'member' ? 0 : 1;
      $member->can_update = $role == 'member' ? 0 : 1;
      $member->can_delete = $role == 'member' ? 0 : 1;
      $member->role = $role;
      $member->save();
   };


   $deleteMember = function($item){
      if(!Teams::permission('ce')) return;
      $this->skipRender();
      YenaTeamsUserTable::where('team_id', $this->team->id)->where('id', ao($item, 'id'))->delete();
   };


   $updateTeam = function($data){
      $this->skipRender();
      if(!Teams::permission('ce')) return;

      $team = $this->team;
      $team->fill($data);

      $team->save();
   };

   $getInvites = function(){
      $this->invites = YenaTeamsInvite::where('team_id', $this->team->id)->orderBy('id', 'ASC')->get();
      $this->invitesArray = $this->invites->toArray();
   };


   $inviteTeam = function($data){
      $this->skipRender();
      if(!Teams::permission('ce')) return;

      $send_email = function($invite){
         $mail = new YenaMail;
         $mail->send([
            'to' => $invite->email,
            'subject' => __('Invitation to Join :workspace', ['workspace' => $this->team->name]),
         ], 'team.invite', [
            'team' => $this->team,
            'invite' => $invite,
            'user' => iam()
         ]);
      };

      if($invite = YenaTeamsInvite::where('team_id', $this->team->id)->where('email', ao($data, 'email'))->first()){
         $send_email($invite);

         return;
      }


      // Check if can invite user
      $accept_token = md5(time());
      $deny_token = md5(str()->random(10));

      $permission = [
          'cc' => 0,
          'ce' => 0,
          'cd' => 0
      ];

      $settings = [
          'permission' => $permission
      ];
      
      $invite = new YenaTeamsInvite;
      $invite->uuid = ao($data, 'uuid');
      $invite->user_id = $this->team->owner_id;
      $invite->team_id = $this->team->id;
      $invite->accept_token = $accept_token;
      $invite->settings = $settings;
      $invite->deny_token = $deny_token;
      $invite->created_at = \Carbon\Carbon::parse(ao($data, 'created_at'));
      $invite->updated_at = \Carbon\Carbon::parse(ao($data, 'created_at'));
      unset($data['date']);



      $invite->fill($data);
      $invite->save();
      $send_email($invite);

      // $this->getInvites();
   };

   $removeInvite = function($uuid){
      $this->skipRender();
      if(!Teams::permission('ce')) return;
      YenaTeamsInvite::where('team_id', $this->team->id)->where('uuid', $uuid)->delete();

      // $this->getInvites();
   };
   $editUser = function(){
      $this->validate();
      // Upload avatar


      if(!empty($this->avatar)){
            $this->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif|max:5048'
            ]);
            storageDelete('media/avatar', $this->user->avatar);


            $filesystem = sandy_filesystem('media/avatar');
            $avatar = $this->avatar->storePublicly('media/avatar', $filesystem);
            $avatar = str_replace("media/avatar/", "", $avatar);
            $this->user->avatar = $avatar;
      }

      $this->user->save();

      $this->flashToast('success', __('Changes saved successfully'));
   };
?>

<div>
   @if (!iam()->fullAccess())
   <x-empty-state :title="__('Cannot access this page.')" :desc="__('Workspace can only be accessed by team member admin or owner.')" image="19.png">
   </x-empty-state>
   @endif

   @if (iam()->fullAccess())
   <div x-data="app_settings">
      <div class="w-full max-w-[80em] h-full">
         <div class="w-full mb-6 max-w-full">
            <div class="block">
               <div class="flex items-center gap-2">

                  <button type="button" class="yena-button-o !gap-2 !text-base" :class="{
                     '!text-[var(--yena-colors-trueblue-500)] !bg-[var(--yena-colors-trueblue-50)]': _page=='profile'
                  }" @click="_page='profile'">
                     <div class="-icon">
                        {!! __i('--ie', 'settings.12', 'w-5 h-5') !!}
                     </div>
                     {{ __('Profile') }}
                  </button>
                  <button type="button" class="yena-button-o !gap-2 !text-base" :class="{
                     '!text-[var(--yena-colors-trueblue-500)] !bg-[var(--yena-colors-trueblue-50)]': _page=='-'
                  }" @click="_page='-'">
                     <div class="-icon">
                        {!! __i('--ie', 'settings.12', 'w-5 h-5') !!}
                     </div>
                     {{ __('Overview') }}
                  </button>
                  <button type="button" class="yena-button-o !gap-2 !text-base" :class="{
                     '!text-[var(--yena-colors-trueblue-500)] !bg-[var(--yena-colors-trueblue-50)]': _page=='members'
                  }" @click="_page='members'">
                     <div class="-icon">
                        {!! __i('Users', 'User,Profile.1', 'w-5 h-5') !!}
                     </div>
                     {{ __('Members') }}
                  </button>
               </div>
            </div>
         </div>

         <div class="flex flex-col gap-0">
            <div x-show="_page=='profile'">
                  
               <form wire:submit="editUser" class="pb-6 lg:w-[50%]">
   
                  <div class="settings__upload" data-generic-preview>
                     <div class="settings__preview relative overflow-hidden">
                        @php
                           $_avatar = iam()->get_original_user()->getAvatar();
                           
                           if($avatar) $_avatar = $avatar->temporaryUrl();
                        @endphp
                        <img src="{{ $_avatar }}" alt="">
                        <div wire:loading.class.remove="!hidden" wire:target="avatar" class="absolute w-full h-full flex items-center justify-center bg-[#00000063] !hidden">
                           <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-7 !h-7"></div></div>
                        </div>
                     </div>
                     <div class="settings__wrap">
                     <div class="text-[2rem] leading-10 font-bold">{{ __('Profile photo') }}</div>
                     <div class="settings__content">{{ __('We recommended an image of at least 80x80. Gifs work too.') }}</div>
                     <div class="settings__file">
                        <input class="settings__input z-50" type="file" wire:model="avatar">
                        <a class="yena-button-stack">{{ __('Choose') }}</a>
                     </div>
                     </div>
                  </div>
               
                  <div class="flex flex-col gap-6 mt-5">
                     <x-input-x wire:model="user.name" label="{{ __('Full Name') }}"></x-input-x>
                     <x-input-x wire:model="user.email" label="{{ __('Email address') }}"></x-input-x>
                     <x-input-x wire:model="user.title" label="{{ __('Title') }}" placeholder="{{ __('Director, md') }}"></x-input-x>
                  </div>
   
                  <button class="yena-button-stack mt-5 w-full">{{ __('Save') }}</button>
               </form>
            </div>
            <div x-show="_page=='-'">
               <div class="flex flex-col gap-10 mt-4">
                  <div class="flex flex-col gap-8 w-full">
                     <div class="text-3xl leading-[1.33] font-bold mb-[calc(var(--yena-space-4)_*_-1)] lg:text-4xl lg:leading-[1.2]">{{ __('Basic info') }}</div>

                     <div class="flex flex-col w-full gap-10 md:flex-row md:gap-24">
                        <div class="flex flex-col justify-start flex-1">
                           <h2 class="leading-[1.33] text-lg font-semibold text-[var(--yena-colors-gray-800)] my-2 lg:leading-[1.2]">
                              <p>{{ __('Workspace logo') }}</p>
                           </h2>
                           <p class="text-sm text-[var(--yena-colors-gray-600)]">{{ __('An image to represent your workspace') }}</p>
                        </div>

                        <div class="flex flex-1 justify-start md:justify-end">
                           <div class="flex flex-row justify-start w-full gap-4">
                              <div class="settings__preview relative !rounded-full">
                                 @php
                                     $_logo = $team->getLogo();
                                     
                                     if($logo) $_logo = $logo->temporaryUrl();
                                 @endphp


                                 <img class="!rounded-full" src="{{ $_logo }}" alt=" " >
                                 <input class="settings__input z-50" type="file" wire:model="logo">


                                 <div class="h-10 w-10 bg-white rounded-full flex items-center justify-center absolute right-8 -bottom-3 cursor-pointer shadow-lg z-[55]" :class="{
                                    '!opacity-50 !pointer-events-none !cursor-default': !ce
                                 }" @click="logoExists ? $wire.resetLogo() : $root.querySelector('.settings__input').click()">
                                    <template x-if="logoExists">
                                        {!! __i('--ie', 'trash-bin-delete', 'w-5 h-5') !!}
                                    </template>
                                    <template x-if="!logoExists">
                                        {!! __i('--ie', 'pen-edit.11', 'w-5 h-5') !!}
                                    </template>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="flex flex-col w-full gap-10 md:flex-row md:gap-24">
                        <div class="flex flex-col justify-start flex-1">
                           <h2 class="leading-[1.33] text-lg font-semibold text-[var(--yena-colors-gray-800)] my-2 lg:leading-[1.2]">
                              <p>{{ __('Workspace name') }}</p>
                           </h2>
                           <p class="text-sm text-[var(--yena-colors-gray-600)]">{{ __('e.g. your team or company name') }}</p>
                        </div>

                        <div class="flex flex-1 justify-start md:justify-end">
                                          
                           <div class="flex flex-col w-full">
                              <form @submit.prevent="saveTeam">
                                 <div class="flex items-start gap-4">
                                    <div class="w-full">
                                       <div class="yena-form-group">
                                          <input type="text" x-model="team.name" @input="showTeamSave=true" class="!pl-4 w-full !bg-white" placeholder="{{ __('Workspace name') }}" :class="{
                                             '!opacity-50 !pointer-events-none !cursor-default': !ce
                                          }">
                                       </div>
                                    </div>
                        
                                    <div class="flex flex-col md:flex-row gap-2" :class="{
                                       '!hidden': !showTeamSave
                                    }">
                                       <button class="yena-button-stack">{{ __('Save') }}</button>
                                       <button class="yena-button-stack !bg-transparent" type="button" @click="showTeamSave=false">{{ __('Cancel') }}</button>
                                    </div>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div x-show="_page=='members'">
               <div class="flex flex-col gap-10 mt-4">
                  <div class="flex flex-col gap-8 w-full">
                     <div class="text-3xl leading-[1.33] font-bold mb-[calc(var(--yena-space-4)_*_-1)] lg:text-4xl lg:leading-[1.2]">{{ __('Manage workspace members') }}</div>

                     <div class="flex flex-col justify-start flex-1">
                        <h2 class="leading-[1.33] text-lg font-semibold text-[var(--yena-colors-gray-800)] my-2 lg:leading-[1.2]">
                           <p>{{ __('Invite others to this workspace') }}</p>
                        </h2>
                     </div>
                  </div>
               </div>
               
               <div>
                  <div class="relative block mt-10">
                     
                     <div class="lg:w-[50%]">
                        <div class="flex md:flex-row flex-col md:items-center gap-2">
                           <button type="button" class="yena-button-o !h-10 !gap-2 !text-base" :class="{
                              '!text-[var(--yena-colors-trueblue-500)] !bg-[var(--yena-colors-trueblue-50)]': inviteType=='email'
                           }" @click="inviteType='email'">
                              <div class="-icon">
                                 {!! __i('emails', 'Mail, Email, Letter.3', 'w-5 h-5') !!}
                              </div>
                              {{ __('Invite with email address') }}
                           </button>
                           @if (iam()->get_original_user()->id == $team->owner_id)
                           <button type="button" class="yena-button-o !h-10 !gap-2 !text-base" :class="{
                              '!text-[var(--yena-colors-trueblue-500)] !bg-[var(--yena-colors-trueblue-50)]': inviteType=='link'
                           }" @click="inviteType='link'">
                              <div class="-icon">
                                 {!! __i('--ie', 'attachment-link-1', 'w-5 h-5') !!}
                              </div>
                              {{ __('Invite with link') }}
                           </button>
                           @endif
                        </div>
   
                        <div x-show="inviteType=='email'" class="pt-4">
                           <p class="mb-[var(--yena-space-2)] [font-size:var(--yena-fontSizes-sm)] text-[color:var(--yena-colors-gray-500)]">{{ __('By adding members, you grant them the ability to edit, create, and share sites within this workspace.') }}</p>
                     
                           <div class="flex flex-col mb-5">
                              <form @submit.prevent="inviteTeam">
                                 <div class="flex items-start gap-4">
                                    <div class="w-full">
                                       <div class="yena-form-group">
                                          <div class="--left-element">
                                             {!! __i('interface-essential', 'search.1', 'w-5 h-5') !!}
                                          </div>
                        
                                          <input type="text" x-model="add_email" @input="checkValidEmail" placeholder="{{ __('Add people') }}" class="!bg-white" :class="{
                                             '!opacity-50 !pointer-events-none !cursor-default': !ce
                                          }">
                                       </div>
                                    </div>
                        
                                    <button class="yena-button-stack w-[300px]" :class="{
                                       '!hidden': !addPeople,
                                       'opacity-40 cursor-not-allowed [box-shadow:var(--yena-shadows-none)] pointer-events-none': !isValidEmail && !inviteError
                                    }">{{ __('Invite') }}</button>
                                 </div>
                              </form>
                              <template x-if="inviteError">
                                 <div class="bg-red-200 text-[11px] p-1 px-2 rounded-md mt-2">
                                    <div class="flex items-center">
                                       <div>
                                          <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                       </div>
                                       <div class="flex-grow ml-1 text-xs" x-text="inviteError"></div>
                                    </div>
                                 </div>
                              </template>
                           </div>
                        </div>
                        @if (iam()->get_original_user()->id == $team->owner_id)
                        <div x-show="inviteType=='link'" class="pt-4">
                           <p class="text-sm text-[var(--yena-colors-gray-500)] my-2">{{ __('Or you can invite them to join as a member with this secret invite link. By adding members, you grant them the ability to edit, create, and share sites within this workspace. Only admins can see this link.') }}</p>
                           <div class="relative flex w-[100%] isolate mb-4">
                              <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 border-2 border-solid [border-image:initial] border-transparent bg-white" readonly :value="teamRoute" placeholder="{{ __('link goes here...') }}">
                     
                              <div class="right-0 h-[3rem] text-[1rem] flex items-center justify-center absolute top-0 z-[1]">
                                 <button type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" @click="$clipboard(teamRoute); $el.innerText = window.builderObject.copiedText;">{{ __('Copy') }}</button>
                              </div>
                           </div>
                        </div>
                        @endif
                     </div>
      
                     <div class="flex md:flex-row flex-col justify-start flex-row mb-[var(--yena-space-4)] gap-2">
                        <button class="yena-button-o !text-base !h-10" :class="{
                           '!text-[var(--yena-colors-trueblue-500)] !bg-[var(--yena-colors-trueblue-50)]':__page=='members'
                        }" type="button" @click="__page='members'">
                           <div class="mr-2">
                              {!! __i('Users', 'User,Profile.1', 'w-5 h-5') !!}
                           </div>
                           <span>{{ __('Active members') }} <span x-text="'(' + (teamUsers.length) + ')'">(0)</span></span>
                        </button>
      
                        <button class="yena-button-o !text-base !h-10" :class="{
                           '!text-[var(--yena-colors-trueblue-500)] !bg-[var(--yena-colors-trueblue-50)]':__page=='invites'
                        }" type="button" @click="__page='invites'">
                           <div class="mr-2">
                              {!! __i('emails', 'email-mail-letter', 'w-5 h-5') !!}
                           </div>
                           <span>{{ __('Invitations') }} <span x-text="'(' + (invites ? invites.length : '0') + ')'"></span></span>
                        </button>
                     </div>
                     <div class="w-[100%]">
                        <div x-show="__page=='members'">
                           <div class="block whitespace-nowrap max-w-full overflow-x-auto">
                              <table class="border-collapse w-[var(--yena-sizes-full)] whitespace-normal yena-table-o">
                                 <thead>
                                    <tr>
                                       <th class="head-th">{{ __('Name') }}</th>
                                       <th class="head-th">{{ __('Join date') }}</th>
                                       <th class="head-th">{{ __('Role') }}</th>
                                    </tr>
                                 </thead>
      
                                 <tbody>
                                    <template x-for="(item, index) in teamUsers">
                                       <tr>
                                          <td class="body-td">
                                             <div class="flex items-center flex-row gap-2">
                                                <span class="yena-avatar !h-[32px] !w-[32px]">
                                                   <img :src="item.user.avatar_json" :alt="item.user.name" class="w-full h-full object-cover">
                                                </span>
                                                <div class="flex flex-col gap-0">
                                                   
                                                   <p x-html="item.user.name + (get_original_user.id == item.user.id ? ' ({{ __('You') }})' : '')"></p>
                                                   <div class="flex items-center flex-row mt-[var(--yena-space-1)] gap-2">
                                                      <p class="[font-size:var(--yena-fontSizes-xs)] text-[color:var(--yena-colors-gray-500)]" x-text="item.user.email"></p>
                                                   </div>
                                                </div>
                                             </div>
                                          </td>
                                          <td class="body-td whitespace-nowrap" x-text="item.created_at_json"></td>
                                          <td class="body-td  w-[var(--yena-sizes-32)]">
                                             

                                             <template x-if="!ce || ce && item.user.id == get_original_user.id">
                                                <button class="yena-button-o !text-[var(--yena-colors-gray-300)] !opacity-50 pointer-events-none" type="button">
                                                   <span x-text="permissionRole[item.role].title" class="capitalize"></span>
                                                   <span class="--icon ml-2 !mr-0">
                                                      <i class="ph ph-caret-down"></i>
                                                   </span>
                                                </button>
                                             </template>

                                             <template x-if="ce && item.user.id !== get_original_user.id">
                                                <button class="yena-button-o !text-[var(--yena-colors-trueblue-500)]" type="button" x-tooltip="tippyRole" >
                                                   <span x-text="permissionRole[item.role].title" class="capitalize"></span>
                                                   <span class="--icon ml-2 !mr-0">
                                                      <i class="ph ph-caret-down"></i>
                                                   </span>
                                                </button>
                                             </template>
                                             
                                          </td>
                                       </tr>
                                    </template>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div x-show="__page=='invites'">
                           <div class="block whitespace-nowrap max-w-full overflow-x-auto" :class="{
                              '!hidden': invites && invites.length == 0,
                           }">
                              <table class="border-collapse w-[var(--yena-sizes-full)] whitespace-normal yena-table-o">
                                 <thead>
                                    <tr>
                                       <th class="head-th">{{ __('Email') }}</th>
                                       <th class="head-th">{{ __('Last sent') }}</th>
                                       <th class="head-th">{{ __('Revoke') }}</th>
                                    </tr>
                                 </thead>
      
                                 <tbody>
                                    <template x-for="(item, index) in invites">
                                       <tr>
                                          <td class="body-td">
                                             <div class="flex items-center flex-row gap-2">
                                                <div class="flex flex-col gap-0">
                                                   <p class="yena-text" x-text="item.email"></p>
                                                </div>
                                             </div>
                                          </td>
                                          <td class="body-td whitespace-nowrap" x-text="item.date"></td>
                                          <td class="body-td  w-[var(--yena-sizes-32)]">
                                             <button class="yena-button-stack" :class="{
                                                '!opacity-50 !pointer-events-none !cursor-default': !ce
                                             }" @click="ce ? removeInvite(item) : ''">{{ __('Revoke') }}</button>
                                          </td>
                                       </tr>
                                    </template>
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>



   <div>
      
      <template x-ref="tippy_role">
         <div class="yena-menu-list !w-[380px] !max-w-full">
            <p class="mx-4 mt-[var(--yena-space-2)] mb-[var(--yena-space-2)] font-semibold text-[var(--yena-fontSizes-sm)] font-[var(--yena-fonts-heading)] normal-case text-[var(--yena-colors-gray-500)] tracking-[0px] ml-[var(--yena-space-2)]">{{ __('Permissions') }}</p>

            <template x-for="(_item, index) in permissionRole" :key="index">
               <a @click="updateMember(item, index); $dispatch('hideTippy');" :class="{
                   '!bg-[var(--yena-colors-gray-100)] !border-[var(--yena-colors-gray-200)]': item.role == index,
               }" class="yena-menu-list-item !items-start">
                  <div class="--icon !mt-1" :class="{
                     'opacity-0': item.role != index
                  }">
                     <i class="ph ph-check"></i>
                  </div>
                  <div>
                     <div class="flex flex-col">
                        <span class="" x-text="_item.title"></span>
                        <span class="text-sm text-[var(--yena-colors-gray-500)] mt-1" x-text="_item.text"></span>
                     </div>
                  </div>
               </a>
            </template>
            <hr class="--divider">
            <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="deleteMember(item)">
               <div class="--icon">
                  {!! __icon('interface-essential', 'trash-bin-delete', 'w-5 h-5') !!}
               </div>
               <span>{{ __('Remove') }}</span>
            </a>
        </div>
     </template>
   </div>
   @endif
   

   @script
   <script>
       Alpine.data('app_settings', () => {
          return {
            _page: 'profile',
            __page: 'members',

            inviteType: 'email',
            team: @entangle('teamArray').live,
            teamUsers: @entangle('teamUsers').live,
            invites: @entangle('invitesArray').live,
            teamRoute: @entangle('teamRoute').live,
            get_original_user: @entangle('get_original_user'),
            ce: @entangle('ce').live,
            logoExists: @entangle('logoExists').live,
            users: [],
            
            showTeamSave: false,
            addPeople:false,
            add_email:'',
            isValidEmail:false,

            inviteError: false,
            permissionRole: {
               admin: {
                  title: '{{ __('Admin') }}',
                  text: '{{ __('Can change workspace settings, assign roles, and invite or remove workspace members.') }}',
               },
               member: {
                  title: '{{ __('Member') }}',
                  text: '{{ __('Cannot change workspace settings or manage workspace members.') }}',
               },
            },
            tippy: {
                allowHTML: true,
                maxWidth: 360,
                interactive: true,
                trigger: 'click',
                animation: 'scale',
            },
            tippyRole: {},

            checkValidEmail(){
               this.isValidEmail = false;
               if(this.add_email.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)){
                  this.isValidEmail = true;
               }

               this.addPeople = true;
               if(this.add_email == ''){
                  this.addPeople = false;
               }
            },

            updateMember(item, role){
               item.role = role;
               this.$wire.updateMember(item, role);
            },

            deleteMember(item){
               let $this = this;
               $this.teamUsers.forEach((e, index) => {
                  if(item.id == e.id){
                     $this.teamUsers.splice(index, 1);
                  }
               });
               
               $this.$wire.deleteMember(item);
            },

            saveTeam(){
               this.$dispatch('set-team-name', this.team.name);
               this.showTeamSave=false;
               this.$wire.updateTeam(this.team);
            },

            checkInviteError(){
               this.inviteError = false;
               if(!this.isValidEmail){
                  this.inviteError = '{{ __('Invalid email') }}';
               }

               let $this = this;


               this.users.forEach((e) => {
                  if($this.add_email == e.email){
                     this.inviteError = '{{ __('Email exists') }}';
                  }
               });
            },
            inviteTeam(){
               this.checkInviteError();
               if(this.inviteError) return;

               let $this = this;
               let $canCreate = true;
               this.invites.forEach((e, index) => {
                  if(e.email == $this.add_email){
                     e.date = window.moment().format("MMM DD, Y HH:mm A");
                     $canCreate = false;
                     this.$wire.inviteTeam(e);
                  }
               });



               if($canCreate){
                  let $invite = {
                     uuid: this.$store.builder.generateUUID(),
                     email: this.add_email,
                     created_at: window.moment(),
                     date: window.moment().format("MMM DD, Y HH:mm A"),
                  };

                  this.invites.push($invite);
                  this.$wire.inviteTeam($invite);
               }

               this.add_email = '';
               this.__page = 'invites';
            },
            removeInvite(item){
               let $this = this;
               this.invites.forEach((e, index) => {
                  if(e.uuid == item.uuid){
                     $this.invites.splice(index, 1);
                  }
               });

               this.$wire.removeInvite(item.uuid);
            },

            init(){
               this.tippyRole = {
                   ...this.tippy,
                   content: this.$refs.tippy_role.innerHTML,
               }
               
            }
          }
       });
   </script>
   @endscript
</div>