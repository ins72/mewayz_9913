

<?php
   use App\Models\UserConversation;
   use App\Models\UserMessage;

   use function Livewire\Volt\{state, mount, placeholder, on, usesPagination, with};
   
   with(fn () => ['messages' => fn() => $this->get()]);

   mount(function(){
      $this->getProducts();
   });

   state([
      'conversation_id' => null,
      'conversation' => [],
   ]);

   on([
      'conversationRefresh' => function(){
         $this->messages = $this->get();
      },
      'setConversation' => function($id){
         $this->conversation_id = $id;
         // $this->_setConversation($id);
      }
   ]);

   mount(function(){
      // $this->get();

      // dd(UserConversation::has('messages')->where('user_1', iam()->id)->orWhere('user_2', iam()->id)->orderBy('updated_at', 'DESC')->orderBy('id', 'DESC')->paginate(2));
      // dd($this->messages);

   });

   $_setConversation = function($id){
      $conversation = false;
      if ($conversation_id = $id) {
            $conversation = UserConversation::find($conversation_id);
            if(!$conversation) $conversation = false;
    
            $user_1 = $conversation->user_1;
            $user_2 = $conversation->user_2;
            
            if($user_1 == auth()->user()->id && $user_2 != auth()->user()->id){
                //$this->goo = 'GO 1';
            }else if($user_2 == auth()->user()->id && $user_1 != auth()->user()->id){
                //$this->goo = 'GO 2';
            }else if($user_1 == auth()->user()->id && $user_2 == auth()->user()->id){
    
                //$this->goo = 'GO 3';
            }else{
                $conversation = false;
            }
      }

      $this->conversation = $conversation;
   };

   $get = function(){
      return UserConversation::has('messages')->where('user_1', iam()->id)->orWhere('user_2', iam()->id)->orderBy('updated_at', 'DESC')->orderBy('id', 'DESC')->paginate(10);
   };
?>
<div>
   <style>
      .yena-root-main, .yena-container{
         padding: 0 !important;
      }
   </style>
    
    <div x-data="app_messaging">
      <div class="chitchat-container sidebar-toggle mobile-menu rounded-xl [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] overflow-hidden">
         <aside class="chitchat-left-sidebar left-disp overflow-auto hidden fixed right-0 bottom-0 h-full z-50 lg:!h-screen lg:!block lg:!relative" :class="{'hidden': !show_option}">
            <div class="recent-default dynemic-sidebar active w-full">
               <div class="chat custom-scroll">
                  <div class="theme-title">
                     <div class="flex">
                         <a class="button-reset py-1 px-1.5 -ml-1.5 mr-0 mb-5 rounded-md hover:bg-gray-100 outline-none cursor-pointer lg:hidden" x-on:click="show_option = false">
                         
                            <div class="flex items-center flex-row space-x-2">
                               <div class="inline-flex shrink-0 text-element-30 transition-colors">
                                  <i class="fi fi-rr-cross text-sm flex"></i>
                               </div>
                               <span class="text-0.75 font-semibold text-text">{{ __('Close') }}</span>
                            </div>
                         </a>
                      </div>
                     <div class="media flex justify-between items-center">
                        <div>
                           <h2>{{ __('Chat') }}</h2>
                           <h4>{{ __('Start New Conversation') }}</h4>
                        </div>
                        <div>
                         
                           <a class="yena-button-stack cursor-pointer !rounded-full py-0.5" @click="$dispatch('open-modal', 'create-message-modal')">
                              <span class="flex items-center justify-between">
                                  <span class="text-white">{{ __('Start') }}</span>
                              </span>
                          </a>
                        </div>
                     </div>
                  </div>
                  <div class="theme-tab tab-sm chat-tabs">
                     <ul class="chat-main">
                         @foreach ($messages as $msg)
                     
                         @php
                             $avatar = null;
                             $name = null;
                             $userID = null;
                             $username = null;
                             $verified_id = null;
                             $active_status_online = false;
                             $icon = null;
                     
                             $unread = false;
                             
                                 
                                 try {
                     
                                         if($msg->last()->from_user_id == auth()->user()->id && $msg->last()->to()->id != auth()->user()->id){
                                         $avatar   = $msg->last()->to()->getAvatar();
                                         $name     = $msg->last()->to()->hide_name == 'yes' ? $msg->last()->to()->username : $msg->last()->to()->name;
                                         $userID   = $msg->last()->to()->id;
                                         $username = $msg->last()->to()->username;
                                         $verified_id = $msg->last()->to()->verified_id;
                                         $active_status_online = $msg->last()->to()->active_status_online == 'yes' ? true : false;
                                         $icon     = $msg->last()->status == 'readed' ? '<i class="la la-check-double --ico text-blue-500"></i>' : '<i class="la la-check --ico"></i>';
                     
                                     }else if ($msg->last()->from_user_id == auth()->user()->id){
                                         $avatar   = $msg->last()->to()->getAvatar();
                                         $name     = $msg->last()->to()->hide_name == 'yes' ? $msg->last()->to()->username : $msg->last()->to()->name;
                                         $userID   = $msg->last()->to()->id;
                                         $username = $msg->last()->to()->username;
                                         $verified_id = $msg->last()->to()->verified_id;
                                         $active_status_online = $msg->last()->to()->active_status_online == 'yes' ? true : false;
                                         $icon = null;
                                     } else {
                                         $avatar   = $msg->last()->from()->getAvatar();
                                         $name     = $msg->last()->from()->hide_name == 'yes' ? $msg->last()->from()->username : $msg->last()->from()->name;
                                         $userID   = $msg->last()->from()->id;
                                         $username = $msg->last()->from()->username;
                                         $verified_id = $msg->last()->from()->verified_id;
                                         $active_status_online = $msg->last()->from()->active_status_online == 'yes' ? true : false;
                                         $icon = null;
                     
                                         $msg->last()->status !== 'readed' ? $unread = true : $unread = false;
                                     }
                                 } catch (\Exception $th) {
                                     $user_id = null;
                     
                                     if($msg->user_1 == auth()->user()->id){
                                         $user_id = $msg->user_2;
                                     }else if($msg->user_2 == auth()->user()->id){
                                         $user_id = $msg->user_1;
                                     }
                                     
                                     $_user = \App\Models\User::find($user_id);
                                     if(!$_user) continue;
                     
                                     $avatar   = $_user->getAvatar();
                                     $name     = $_user->hide_name == 'yes' ? $_user->username : $_user->name;
                                     $userID   = $_user->id;
                                     $username = $_user->username;
                                     $verified_id = $_user->verified_id;
                                     $active_status_online = $_user->active_status_online == 'yes' ? true : false;
                                     $icon = null;
     
                                     if($user_id == null) continue;
                                 }
                         @endphp
                         <li class="cursor-pointer {{ $conversation_id == $msg->id ? 'active' : '' }}" >
                           <a class="chat-box block" @click="$dispatch('setConversation', {
                              id: '{{ $msg->id }}'
                           })">
                             <div class="profile {{ Cache::has('is-online-' . $userID) ? 'online' : 'offline' }} bg-size block" style="background-image: url({{ $avatar }}); background-size: cover; background-position: center center;">
                             
                             </div>
                             <div class="details">
                               <h5>{{ $name }}</h5>
                               <h6>{{ $msg->last() ? $msg->last()->message : '' }}</h6>
                             </div>
                             <div class="date-status">
                               <h6>{{ $msg->last() ? \Carbon\Carbon::parse($msg->last()->created_at)->toFormattedDateString() : '' }}</h6>
                               
                               {!! $icon !!}
                             </div>
                           </a>
                         </li>
                         @endforeach
                       </ul>
                  </div>
               </div>
            </div>
         </aside>
         <div class="chitchat-main opacity--0 overflow-hidden">
     
            <div class="chat-content active h-full">
     
             @if ($conversation_id)
                 <div>
                  <livewire:components.console.message.conversation :key="uukey('app', 'message-page-single-conversation')" :_convo_id="$conversation_id">
                 </div>
                 @else
                 <div class="no-record z-50 mt-52">
                     <div class="rounded-3xl block p-5">
                     <div class="text-center flex justify-center flex-col items-center">
             
                         {!! __i('interface-essential', 'chat-message copy', 'w-16 h-16') !!}
                         <div class="text-xl font-bold mt-5">{{ __('Start Conversation') }}</div>
                         <div class="w-3/4 mt-3">
                         <div class="text-sm text-gray-400">{{ __('No conversation selected, select a chat to continue or start a new conversation.') }}</div>
                 
                         <a class="mt-5 yena-button-stack w-72 !mx-auto cursor-pointer" @click="show_option=true">{{ __('Select conversation') }}</a>
                         </div>
                     </div>
                     </div>
                 </div>
             @endif
     
     
            </div>
         </div>
      </div>
    
    
          
    
        <template x-teleport="body">
            <x-modal name="create-message-modal" :show="false" removeoverflow="true" maxWidth="xl" >
               <livewire:components.console.message.create-modal :key="uukey('app', 'message-page-create')">
            </x-modal>
         </template>
    </div>


   @script
   <script>
       Alpine.data('app_messaging', () => {
          return {
            show_option: false,
            init(){
               var $this = this;
            }
          }
       });
   </script>
   @endscript
</div>