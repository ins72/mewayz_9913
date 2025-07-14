
<?php
   use App\Livewire\Actions\ToastUp;

   use App\Yena\YenaMail;
   use App\Models\YenaTeamsInvite;
   use App\Yena\Teams;
   use function Livewire\Volt\{state, mount, placeholder, updated, uses, on};

   uses([ToastUp::class]);
   mount(function(){
      $this->initGet();
      $this->getInvites();
   });

   on([
      'refreshWorkspace' => function(){
         $this->initGet();
      },
   ]);

   state([
      'team' => [],
      'teamArray' => [],
      'invites' => [],
      'invitesArray' => [],
      'teamRoute' => '',
   ]);

   updated([
      'query' => function(){
         $this->getFolders();
      }
   ]);

   $initGet = function(){
      $this->team = Teams::init();
      $this->teamArray = $this->team->toArray();

      $this->teamRoute = route('console-team-join', ['slug' => $this->team->slug]);
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
      YenaTeamsInvite::where('team_id', $this->team->id)->where('uuid', $uuid)->delete();

      // $this->getInvites();
   };

   placeholder('
   <div class="p-4 w-full mt-1">
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
      <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
   </div>');
?>


<div class="w-full">
   <div wire:ignore>
      <div x-data="workspace_team">
         <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-1 right-1 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross-small"></i>
         </a>
         <header class="flex pt-4 px-6 flex-initial text-3xl font-black">{{ __('Workspace settings') }}</header>
   
         <hr class="yena-divider my-4">
         
   
         <div class="px-6 pb-4">
            <div class="flex flex-col">
               <form @submit.prevent="saveTeam">
                  <div class="flex items-start gap-4">
                     <div class="w-full">
                        <div class="yena-form-group">
                           <input type="text" x-model="team.name" @input="showTeamSave=true" class="!pl-4 md:!w-1/2" placeholder="{{ __('Workspace name') }}">
                        </div>
                     </div>
         
                     <div class="flex flex-col md:flex-row gap-2" :class="{
                        '!hidden': !showTeamSave
                     }">
                        <button class="yena-button-stack ![background:black] !text-white">{{ __('Save') }}</button>
                        <button class="yena-button-stack" type="button" @click="showTeamSave=false">{{ __('Cancel') }}</button>
                     </div>
                  </div>
               </form>
               <div class="flex gap-2 mt-2 items-center">
                  <p class="[font-size:var(--yena-fontSizes-sm)] text-[color:var(--yena-colors-gray-500)]">{{ __('Eg: your team or company name') }}</p>
               </div>
            </div>
         
            <div class="mt-12">
   
               <div class="flex gap-2 mb-4 items-center">
                  <p class="text-xl font-semibold">{{ __('Invite others to this workspace') }}</p>
               </div>
               <p class="mb-[var(--yena-space-2)] [font-size:var(--yena-fontSizes-sm)] text-[color:var(--yena-colors-gray-500)]">{{ __('Invite by email address') }}</p>
         
               <div class="flex flex-col mb-5">
                  <form @submit.prevent="inviteTeam">
                     <div class="flex items-start gap-4">
                        <div class="w-full">
                           <div class="yena-form-group">
                              <div class="--left-element">
                                 {!! __i('interface-essential', 'search.1', 'w-5 h-5') !!}
                              </div>
            
                              <input type="text" x-model="add_email" @input="checkValidEmail" placeholder="{{ __('Add people') }}">
                              {{-- <div class="--right-element !hidden" wire:loading.class.remove="!hidden" wire:target="query">
                                 <div class="yena-spinner !w-4 !h-4 !border-2"></div>
                              </div> --}}
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
               <p class="text-sm text-[var(--yena-colors-gray-500)] my-2">{{ __('Or you can invite them to join as a member with this secret invite link. Only admins can see this link.') }}</p>
               <div class="relative flex w-[100%] isolate mb-4">
                  <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 border-2 border-solid [border-image:initial] border-transparent bg-[#f7f3f2]" readonly :value="teamRoute" placeholder="{{ __('link goes here...') }}">
         
                  <div class="right-0 h-[3rem] text-[1rem] flex items-center justify-center absolute top-0 z-[1]">
                     <button type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" @click="$clipboard(teamRoute); $el.innerText = window.builderObject.copiedText;">{{ __('Copy') }}</button>
                  </div>
                </div>
         
         
               <div class="w-full flex items-center relative overflow-hidden bg-[#feebc8] px-4 pt-[var(--yena-space-3)] pb-[var(--yena-space-3)] mt-[var(--yena-space-2)] text-[var(--yena-fontSizes-sm)] rounded-[4px]">
                  <span class="text-[#c05621] flex-shrink-0 w-[var(--yena-sizes-5)] h-[var(--yena-sizes-6)] self-start mr-3 flex items-center justify-center">
                     <i class="fi fi-rr-triangle-warning"></i>
                  </span>
                  
                  <div class="flex flex-col gap-2">
                     <p class="yena-text">
                        {!! __t('<strong>Heads up! This is a special link.</strong> Anyone with this link can join your workspace and will be able to view gammas that are not private.') !!}
                     </p>
                  </div>
               </div>
   
   
               <div class="relative block mt-5">
                  <div class="flex justify-start flex-row mb-[var(--yena-space-4)] gap-2">
                     <button class="yena-button-o" :class="{
                        '!text-[rgb(26,_6,_153)] !bg-[rgb(213,_206,_255)]':_page=='members'
                     }" type="button" @click="_page='members'">
                        <div class="mr-2">
                           {!! __i('Users', 'User,Profile.1', 'h-[1rem]') !!}
                        </div>
                        <span>{{ __('Active members') }} <span>(0)</span></span>
                     </button>
   
                     <button class="yena-button-o" :class="{
                        '!text-[rgb(26,_6,_153)] !bg-[rgb(213,_206,_255)]':_page=='invites'
                     }" type="button" @click="_page='invites'">
                        <div class="mr-2">
                           {!! __i('emails', 'email-mail-letter', 'h-[1rem]') !!}
                        </div>
                        <span>{{ __('Invitations') }} <span x-text="'(' + (invites.length) + ')'"></span></span>
                     </button>
                  </div>
   
                  <div class="w-[100%]">
                     <div x-show="_page=='members'">
                        <div class="block whitespace-nowrap max-w-full">
                           <table class="border-collapse w-[var(--yena-sizes-full)] whitespace-normal yena-table-o">
                              <thead>
                                 <tr>
                                    <th class="head-th">{{ __('Name') }}</th>
                                    <th class="head-th">{{ __('Join date') }}</th>
                                    <th class="head-th">{{ __('Role') }}</th>
                                 </tr>
                              </thead>
   
                              <tbody>
                                 <tr>
                                    <td class="body-td">
                                       <div class="flex items-center flex-row gap-2">
                                          <span class="yena-avatar">
                                             <img src="https://lh3.googleusercontent.com/a/ACg8ocIfj-FCgS0C0oMhZ-7IBAimDbMwSyK8FomVZ9d0_6f8yyLO=s96-c" alt="Jeff Jola" class="">
                                          </span>
                                          <div class="flex flex-col gap-0">
                                             <p class="yena-text">Jeff Jola (you)</p>
                                             <div class="flex items-center flex-row mt-[var(--yena-space-1)] gap-2">
                                                <p class="[font-size:var(--yena-fontSizes-xs)] text-[color:var(--yena-colors-gray-500)]">jeffjola@gmail.com</p>
                                             </div>
                                          </div>
                                       </div>
                                    </td>
                                    <td class="body-td whitespace-nowrap">Sep 17, 2023, 8:36 PM</td>
                                    <td class="body-td  w-[var(--yena-sizes-32)]"></td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div x-show="_page=='invites'">
                        <div class="block whitespace-nowrap max-w-full" :class="{
                           '!hidden': invites.length == 0,
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
                                          <button class="yena-button-stack" @click="removeInvite(item)">{{ __('Revoke') }}</button>
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

   
   @script
   <script>
       Alpine.data('workspace_team', () => {
          return {
            team: @entangle('teamArray').live,
            invites: @entangle('invitesArray').live,
            teamRoute: @entangle('teamRoute').live,
            users: [],

            _page: 'members',
            
            showTeamSave: false,
            addPeople:false,
            add_email:'',
            isValidEmail:false,

            inviteError: false,

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
               this._page = 'invites';
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
               var $this = this;


               // console.log(window.moment().format("MMM d, Y HH:mm A"))
            }
          }
       });
   </script>
   @endscript
</div>