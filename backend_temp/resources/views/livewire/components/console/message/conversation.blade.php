


<?php

   use App\Models\UserConversation;
   use App\Models\UserMessage;
   use App\Livewire\Actions\ToastUp;

   use function Livewire\Volt\{state, mount, placeholder, rules, uses, usesFileUploads, on};

   on([
      'setConversation' => function($id){
         $this->_convo_id = $id;
         $this->get();
      }
   ]);
   usesFileUploads();
   uses([ToastUp::class]);
   rules(fn() => [
      "message_image" => 'image|max:5048',
   ]);

   state([
      '_convo_id' => null,
      'conversation' => null,
      'conversation_message' => null,

      'message_type' => 'text',
      'message' => null,
      'link' => null,
      'message_image' => null,

      'priced_message' => false,
      'price' => 0,

      'enable_link' => false,
      'sending_to' => null,
      '_user' => null,

      'per_page' => 7
   ]);

   // state([
   //    'products' => [],
   //    'user' => fn() => iam(), 
   // ]);

   mount(function(){
      $this->get();
   });

   $get = function(){

      $this->conversation = UserConversation::where('id', $this->_convo_id)->first();
      if(!$this->conversation) abort(404);

        $user_1 = $this->conversation->user_1;
        $user_2 = $this->conversation->user_2;
        
        if($user_1 == auth()->user()->id && $user_2 != auth()->user()->id){
            //$this->goo = 'GO 1';
        }else if($user_2 == auth()->user()->id && $user_1 != auth()->user()->id){
            //$this->goo = 'GO 2';
        }else if($user_1 == auth()->user()->id && $user_2 == auth()->user()->id){

            //$this->goo = 'GO 3';
        }else{
            abort(404);
        }

        
        try {
            
            if($this->conversation->last()->from_user_id == auth()->user()->id && $this->conversation->last()->to()->id != auth()->user()->id){
                $this->sending_to   = $this->conversation->last()->to()->id;
                $this->_user = $this->conversation->last()->to();

            }else if ($this->conversation->last()->from_user_id == auth()->user()->id){
                $this->sending_to   = $this->conversation->last()->to()->id;
                $this->_user = $this->conversation->last()->to();
            } else {
                $this->sending_to   = $this->conversation->last()->from()->id;
                $this->_user = $this->conversation->last()->from();
            }
        } catch (\Exception $th) {
            $user_id = null;

            if($this->conversation->user_1 == auth()->user()->id){
                $user_id = $this->conversation->user_2;
            }else if($this->conversation->user_2 == auth()->user()->id){
                $user_id = $this->conversation->user_1;
            }
            

            
            $this->sending_to = $user_id;
            $this->_user = \App\Models\User::find($user_id);
        }
        $this->refresh();
   };

   $load_more = function(){

      $this->per_page + 2;
      $this->refresh();
   };

   $refresh = function(){
      $messages = UserMessage::where('conversation_id', $this->conversation->id)->get()->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
      })->toBase();
      $this->conversation_message = $messages;
  
      UserMessage::where('from_user_id', $this->sending_to)->where('to_user_id', auth()->user()->id)->where('status', 'new')->update(['status' => 'readed']);
   };

   $send = function(){
         $this->validate([
            'message' => 'required'
        ]);


        $sending = \App\Models\User::find($this->sending_to);
        
        $this->enable_link = false;
        $type = $this->message_type;
        $data = [];

        $data['message'] = $this->message;
        $image = null;
        $link = $this->link;

        if(!empty($this->message_image)){
            $filesystem = sandy_filesystem('media/conversation');
            $image = $this->message_image->storePublicly('media/conversation', $filesystem);
            $image = str_replace('media/conversation/', "", $image);
            $this->message_image = '';
        }

        $from_id = $this->conversation->barber_id;

        $new = new UserMessage;
        $new->user_id = auth()->user()->id;
        $new->conversation_id = $this->conversation->id;
        $new->from_user_id = auth()->user()->id;
        $new->to_user_id = $this->sending_to;
        $new->message = $this->message;
        $new->link = $link;
        $new->image = $image;
        $new->created_at = \Carbon\Carbon::now();
        $new->save();
        
        $this->message = '';
        $this->link = '';
        $this->refresh();

        if(UserMessage::where('conversation_id', $this->conversation->id)->count() == 1){
         $this->dispatch('conversationRefresh');
        }

      //   // Notify
      //   if(!\Cache::has('is-online-' . $this->sending_to) && $sending){
      //       $email = new \App\Email;
      //       // Get email template
      //       $template = $email->template('account/new_message', ['user' => $sending]);
      //       // Email array
      //       $mail = [
      //           'to' => $sending->email,
      //           'subject' => __('New Message'),
      //           'body' => $template
      //       ];
    
      //       $email->send($mail);
      //   }
   };
?>
<div>

    <div class="w-full" x-data="app_message_conversation">

      <div class="messages custom-scroll active bg-transparent p-0 lg:p-11">
         <div class="image-overlay opacity-20 z-10" style="background-image: url({{ gs('assets/image/others/pattern', 'pattern-26.svg') }})"></div>



         <div class="contact-details z-50 shadow-none">
            <div class="flex items-center justify-between">
               <div class="col-7 w-7/12">
                  <div class="flex items-center left chat-header p-0">
                     <div class="media-left me-3">
                        <div class="w-12 h-12 block rounded-full" style="background-image: url({{ getAvatar($this->sending_to) }}); background-size: cover; background-position: center center;"></div>
                     </div>
                     <div class="-content flex flex-col">
                        <h5>{{ $_user->name }}</h5>
                        <div class="flex items-center">
                            <div class="online --online-status {{ Cache::has('is-online-' . $_user->id) ? 'online' : 'offline' }}"></div>
                            <div class="--online-status-t">{{ Cache::has('is-online-' . $_user->id) ? __('Online') : __('Offline') }}</div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="lg:hidden">
                 <div class="h-8 w-8 flex items-center justify-center bg-gray-200 rounded-full relative cursor-pointer" @click="show_option=true">
                     {!! __i('interface-essential', 'dots', 'w-5 h-5') !!}
                    </div>
               </div>
            </div>
         </div>
         <div class="contact-chat z-40 relative">
             <div class="px-5 pt-5 mt-0 md:mt-0 bwg-white sandy-messages-o" zzire:poll.keep-alive.1000ms="refresh">
                @foreach($this->conversation_message as $date => $items)
        
        
                    @foreach ($items as $msg)
        
        
                    @php
        
                            $avatar   = null;
                            $name     = null;
                            $userID   = null;
                            $username = null;
                        if ($msg->from_user_id  == Auth::user()->id) {
                            $avatar   = getAvatar($msg->to()->id);
                            $name     = $msg->to()->name;
                            $userID   = $msg->to()->id;
                            $username = $msg->to()->username;
        
                        } else if ($msg->to_user_id  == Auth::user()->id) {
                            $avatar   = getAvatar($msg->from()->id);
                            $name     = $msg->from()->name;
                            $userID   = $msg->from()->id;
                            $username = $msg->from()->username;
                        }
        
                        $chat_msg = ao($msg->message) ? linkText(checkText($msg->message)) : null;
                        
                        $chat_direction = $msg->from()->id == auth()->user()->id ? 'is-right' : '';
                        $_chat_direction = $msg->from()->id == auth()->user()->id ? 'is-right' : '';
        
                        $_has_image_text = !empty($chat_msg) && !empty($msg->image) ? '-has-image-txt' : false;
                    @endphp
                    <div class="-chat-list {{ $_has_image_text }} {{ $chat_direction }} sandy-fancybox" data-fancybox="gallery" data-src="{{ gs('media/conversation', $msg->image) }}" href="{{ gs('media/conversation', $msg->image) }}">
                            <div class="-msg-content">
                            
                                @if (!empty($msg->image))
                                <div class="--msg-img">
                                    <img src="{{ gs('media/conversation', $msg->image) }}" alt="" srcset="">
                                </div>
                                @endif
                            
                                @if (!empty($chat_msg))
                                    <div class="--msg-text">
                                        {!! $chat_msg !!}
                                    </div>
                                @endif
                            
                            
                            
                                <span class="--msg-date">{{ \Carbon\Carbon::parse($msg->created_at
                                    )->format('H:ia') }}
        
                                        @if ($_chat_direction)
                                            @if ($msg->status == 'readed')
                                                <i class="la la-check-double text-blue-500"></i>
                                                @else
                                                <i class="la la-check"></i>
                                            @endif
                                        @endif
                                    </span>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
         </div>
      </div>
 
 
      
      <form class="message-input z-50" wire:submit="send">
         <div class="wrap flex items-center">
            
            
             <input class="textarea bg-transparent h-10 w-full" wire:model="message" rows="3" placeholder="{{ __('Write something...') }}">
            
            <div class="ml-auto flex items-center gap-2">
                <div class="h-8 w-8 flex items-center justify-center bg-gray-200 rounded-full relative z-40">
                 {!! __i('interface-essential', 'image-picture-loading', 'w-5 h-5') !!}
                    <input type="file" wire:model="message_image" class="!absolute !right-0 !top-0 !opacity-0 !w-full !h-full">
                </div>
                <button class="submit h-8 w-8 bg-gray-200 rounded-full flex items-center justify-center relative z-50">
                    {!! __i('interface-essential', 'send', 'w-5 h-5') !!}
                </button>
            </div>
         </div>
         
         <div class="feed-upload flex items-center gap-2 overflow-x-auto">
                         
             @if ($message_image)
                 <div class="upload-wrap mt-2">
                     <img src="{{ $message_image->temporaryUrl() }}" alt="">
                     <span class="remove-file" wire:click="removeMessageImage">
                        <i class="fi fi-rr-cross text-sm"></i>
                     </span>
                 </div>
             @endif
             {{-- @foreach ($medias as $k => $v)
             @endforeach --}}
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
             <div class="mt-5 bg-red-200 font-11 p-1 px-2 rounded-md">
                 <div class="flex items-center">
                     <div>
                         <i class="fi fi-rr-cross-circle flex"></i>
                     </div>
                     <div class="flex-grow ml-1">{{ $error }}</div>
                 </div>
             </div>
         @endif
     
         @if ($priced_message)
         <div class="form-input is-link always-active active mt-2">
             <label class="is-alt-label hidden"></label>
             <div class="is-link-inner bg-white">
             <div class="side-info">
                 <img src="{{ gs('assets/image/others/payme-currency.png') }}" class="w-5 h-5 object-contain" alt="">
             </div>
             <input type="text" name="username" placeholder="{{ __('Points') }}" class="is-alt-input bg-white">
             </div>
         </div>
         @endif
     
         @if ($enable_link)
         <div class="form-input mt-2">
             <input type="text" wire:model="link" placeholder="{{ __('link ...') }}">
         </div>
         @endif
         <p class="mt-2 text-xs text-gray-600 flex items-center gap-1 hidden">
             <i class="fi fi-rr-terminal"></i>
             {{ __('Markdown supported') }}
         </p>
      </form>
      
     {{-- <div class="support-div p-5">
         <div class="support-chat p-0">
             <div class="messages p-0 sm:p-10 rounded-3xl h-full flex-col bg-transparent" >
     
                 <div class="message-list min-w-full mb-0">
                     @if($this->conversation_message->isEmpty())
                         <div class="is-empty p-20 text-center">
                             <img src="{{ gs('assets/image/others', 'empty-fld.png') }}" class="w-half m-auto" alt="">
                             <p class="mt-10 text-lg font-bold">{{ __('No messages found!') }}</p>
                         </div>
                     @endif
     
     
                     @foreach($this->conversation_message as $date => $items) 
                     @endforeach
                 </div>
                 @if(!$errors->isEmpty())
                     @foreach ($errors->all() as $error)
                         
                     <p class="text-xs text-red-400 mb-5">
                         <span class="error">{{ $error }}</span>
                     </p>
                     @endforeach
                 @endif
     
                 <div class="start-end-of-message"></div>
                 
                 
             </div>
         </div>
     </div --}}
     </div>


     @script
     <script>
         Alpine.data('app_message_conversation', () => {
            return {
               init(){
                  var $this = this;
               }
            }
         });
     </script>
     @endscript
</div>