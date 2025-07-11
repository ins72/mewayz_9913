<?php
    use App\Models\BookingAppointment;
    use App\Models\BookingService;
    use App\Yena\BookingTime;
   use function Livewire\Volt\{state, mount};

   $getEvents = function($info, $selectedProfile = null){
      $this->skipRender();
      $user_id = iam()->id;
      $timeClass = new BookingTime($user_id);
      $startDate = $info['startDate'];
      $endDate = $info['endDate'];

      $get = BookingAppointment::where('user_id', $user_id)->whereDate('date', '>=', $startDate)
      ->whereDate('date', '<=', $endDate)->orderBy('id', 'DESC');


      $get = $get->get();

      $events = [];

      foreach ($get as $key => $item) {
         $start_time = $timeClass->format_minutes($item->start_time);
         $end_time = $timeClass->format_minutes($item->end_time);

         $date = "$item->date $start_time";

         $media = null;
         foreach ($item->getServices() as $service) {
            if(is_array($service->gallery) && !empty($gallery = $service->gallery[0])){
               $media = gs('media/booking/image', $gallery);
               break;
            }
         }

         $event = [
            'id' => $item->id,
            'title' => $item->title,
            'start' => \Carbon\Carbon::parse($date)->format('Y-m-d H:i'),
            'editable' => 1,
            'extendedProps' => [
               'start_time' => $start_time,
               'end_time' => $end_time,
               'services_name' => $item->services_name,
               'settings' => $item->settings,
               'media' => $media,
               'payee' => $item->payee
            ],
         ];
         
         $events[] = $event;
      }

      return $events;
   };

   $changeEvent = function($cal, $oldEvent){
      $this->skipRender();
      $view = ao($cal, 'view.type');
      $event = ao($cal, 'event');
      $date = ao($event, 'start');
      $user_id = iam()->id;
      // dd($cal, $view, $event);

      $time = false;
      if(!$get = BookingAppointment::where('user_id', $user_id)->where('id', ao($event, 'id'))->first()) return;

      if($view == 'timeGridDay' || $view == 'timeGridWeek'){
         $start_time = hour2min(\Carbon\Carbon::parse($date)->format('H:i A'));
         $services = $get->service_ids;
         $end_time = 0;
         foreach($services as $key => $value){
             if($service = BookingService::where('id', $value)->first()){
                 $end_time += $service->duration;
             }
         }

         $end_time = $start_time + $end_time;
         $time = implode('-', [$start_time, $end_time]);
      }

      $get->date = \Carbon\Carbon::parse($date)->format('Y-m-d');

      if($time) $get->time = $time;
      $get->save();

      $this->dispatch('updateCalendarEvent');
   };
?>
<div>
   <div x-data="app_social_create_post_calendar">
       <div wire:ignore class="bg-[#fff] p-4 rounded-2xl">
         <div x-ref="yenaCalendar"></div>
       </div>

       <template x-ref="eventTemplate">
         <div class="calendar__post__wrapper z-[99] small slide-y-enter-done w-full">
            <article class="calendar__post false calendar__post-cannot-move" style="">
               <header class="post__meta">
                  <template x-if="payee">
                     <div class="flex-[1]">
                        <div class="post-attr flex items-center truncate post-attr-isSelected isHoveredAccountListItem">
                           <div class="flex relative cursor-pointer">
                              <img :src="payee.avatar_json" x-on:error="$el.setAttribute('src', $store.app.randomAvatar(payee.name))" class="w-[38px] h-[38px] [transition:all_.2s_ease-in] object-cover rounded-xl [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] p-0.5" alt="">
                           </div>
                           <div class="post-attr__name !ml-2">
                              <span class="text-color-headline text-[11px]" x-text="payee.name"></span>
                              {{-- <span class="block text-[13px] leading-[19.2px] font-normal text-[#323b43] [word-wrap:break-word]"></span> --}}
   
                              <div><small class="text-color-descriptive"><b></b></small></div>
                           </div>
                        </div>
                     </div>
                  </template>
                  <div class="post__meta__time flex">
                     <i class="meta__icon ph ph-clock-afternoon"></i>
                     <time class="meta__time" x-text="start_time"></time>
                  </div>
               </header>
               <div class="post__content pl-[15px] pr-[15px] flex items-center justify-center justify-between">
                  <div class="content__media photo" :class="{
                     '!hidden': !media,
                     '!m-0 !h-[90px]': !services_name,
                  }">
                     <img class="w-full h-full object-cover" :src="media">
                  </div>
                  <template x-if="services_name">
                     <span class="default-tooltip content__text w-full truncate" x-text="services_name +' | '+ (payee ? payee.email : '')"></div>
                  </template>
                  {{-- <footer class="post__footer flex justify-between">
                     <span class="default-tooltip "><i class="text-yellow-700 icon-note font-20"></i></span>
                  </footer> --}}
            </article>
         </div>
       </template>


       
       <div x-data="{popup:false}" x-modelable="popup" x-model="changeEventPrompt" wire:ignore>
         <x-console.popup title="Change Appointment?" description="This Appointment will be rescheduled to the selected date.">
            <button class="btn btn-medium neutral !bg-gray-100 !text-black !h-[calc(var(--unit)*_4)]" type="button" @click="popup=false; changeEvent.revert()">{{ __('Cancel') }}</button>

            <button class="btn btn-medium !text-white !h-[calc(var(--unit)*_4)]" @click="$wire.changeEvent(changeEvent, changeEvent.oldEvent); popup = false;">{{ __('Yes, Change') }}</button>
         </x-console.popup>
       </div>
   </div>
 
   
   @script
   <script>
       Alpine.data('app_social_create_post_calendar', () => {
          return {
               calendar: null,
               changeEventPrompt: false,
               changeEvent: [],


               initCalendar(){
                  let $this = this;
                  $this.calendar = new Calendar($this.$refs.yenaCalendar, {
                     allDaySlot: !1,
                     nowIndicator: !0,
                     aspectRatio: 1.65,
                     progressiveEventRendering: !0,
                     scrollTime: "07:00:00",
                     snapDuration: "00:05:00",
                     plugins: [ dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin ],
                     initialDate: new Date(),
                     initialView: $this.$store.builder.detectMobile() ? 'timeGridDay' : 'dayGridMonth',
                     selectable: true,
                     unselectAuto: false,
                     editable: true,
                     dayPopoverFormat: {
                           weekday: "short",
                           month: "short",
                           day: "numeric"
                     },
                     buttonIcons: {
                        prev: " ph ph-caret-left",
                        next: " ph ph-caret-right"
                     },
                     headerToolbar: {
                        left: 'today,prev,next title',
                        right: 'timeGridDay,timeGridWeek,dayGridMonth'
                     },
                     datesSet: function(e){
                        // console.log(e);

                        $this.hoverFunction();
                     },
                     eventContent: function( info ) {
                           let template = $this.$refs.eventTemplate.innerHTML;
                           
                           // let alpineArray = Object.entries(info.event.extendedProps).map(([key, value]) => {
                           //    return `${key}: ${typeof value === 'object' ? JSON.stringify(value).replace(/"/g, '&quot;') : `'${value}'`}`;
                           // }).join(', ');
                           // let html = template.replace('<div', `<div x-data="{ ${alpineArray} }"`);

                           let eventProps = {
                              start: info.event.start,
                              ...info.event.extendedProps
                           };


                           // Create a dynamic function name for x-data
                           const functionName = `alpine_function_${info.event.id}`;

                           // Define the function in the global scope
                           window[functionName] = () => {
                                 return { 
                                    ...Object.fromEntries(
                                       Object.entries(eventProps).map(([key, value]) => [
                                             key, 
                                             typeof value === 'object' ? value : value
                                       ])
                                    )
                                 };
                           };

                           let html = template.replace('<div', `<div x-data="${functionName}()"`);
                           
                           // console.log(functionName)

                           return {html: html};
                     },
                     events: function( fetchInfo, successCallback, failureCallback ) {
                        let info = {
                           startDate: fetchInfo.start,
                           endDate: fetchInfo.end,
                        }

                        $this.$wire.getEvents(info, $this.selectedSocial).then(r => {
                           successCallback(r);
                        });
                     },
                     select: (info) => {
                        $this.$dispatch('open-modal', 'create-booking-modal');
                        $this.$dispatch('bookingSetDate', {
                           date: info.startStr
                        });
                        $this.hoverFunction();
                        // $this.openCreate(info.startStr);
                     },
                     eventDrop: function(info){
                        $this.changeEventPrompt = true;
                        $this.changeEvent = info;

                        // $this.showEventChangePrompt();
                        // $this.$wire.changeEvent(info.event, info.oldEvent);


                        // if (confirm("Are you sure about this change?")) {
                        //       // reschedule event
                        //       @this.eventDrop(info.event, info.oldEvent)
                        // } else {
                        //       info.revert();
                        // }
                     },
                     eventClick: (info) => {
                           // if (confirm('Are you sure you want to remove this event?')) {
                           //    const index = this.getEventIndex(info)

                           //    this.events.splice(index, 1)

                           //    this.warebares.refetchEvents()
                           // }
                     },
                     eventChange: (info) => {
                        // console.log(info.event)
                        // const index = this.getEventIndex(info)

                        // this.events[index].start = info.event.startStr
                        // this.events[index].end = info.event.endStr
                     },
                  });
                  $this.calendar.render();
                  // $this.hoverFunction();

                  document.addEventListener('livewire:navigated', (e) => {
                     setTimeout(() => {
                        $this.calendar.updateSize();
                     }, 1000);
                  });
               },
               showEventChangePrompt(){
                  this.changeEventPrompt = true;
               },
               hoverFunction() {
                     setTimeout(function() {
                        document.querySelectorAll('.fc-daygrid-day-frame').forEach(function(e) {
                           if (e) {
                           const cellHoverElementsContainer = document.createElement('div');
                           cellHoverElementsContainer.className = 'cell-hover-elements-container';
                           //   const count = 24;
                           //   for (let i = 0; i < count; i++) {
                           //     cellHoverElementsContainer.appendChild(document.createElement('div'));
                           //   }
                           
                              cellHoverElementsContainer.appendChild(document.createElement('div'));
                              e.appendChild(cellHoverElementsContainer);
                           }
                        });
                     }, 1000);
               },
               init() {
                   let $this = this;
                   $this.initCalendar();
                   
                              
                  window.addEventListener('updateCalendarEvent', (e) => {
                     $this.calendar.refetchEvents();
                  });
               },
          }
       });
   </script>
   @endscript
 </div>