<?php
    use Carbon\Carbon;
    use Livewire\Component;
    use Carbon\CarbonPeriod;
    use App\Models\BookingService;
    use App\Yena\SandyAudience;
    use App\Yena\YenaBook;
    use App\Yena\BookingTime;

    use App\Livewire\Actions\ToastUp;

    use function Livewire\Volt\{state, mount, placeholder, rules, uses, on};

    uses([ToastUp::class]);

    state([
        'user' => fn() => iam(),
        // 'bio' => '',
        'date' => '',
        'date_status' => '',
        'services' => '',
        'dates' => '',
        'times' => '',
        'time' => '',
        'service' => []
    ]);
    state([
        'weekdays' => ['Mon', 'Tue', 'Wed', "Thu", 'Fri', 'Sat', 'Sun'],
    ]);

    on([
        'bookingSetDate' => function($date){
            $this->date = Carbon::parse($date);
            $this->setDates($this->date);
        }
    ]);

    mount(function(){
        $this->date = Carbon::now();
        // $this->bio = $this->user;

        $this->services = BookingService::where('user_id', $this->user->id)->orderBy('position', 'ASC')->orderBy('id', 'DESC')->get();

        $this->timeClass = new BookingTime($this->user->id);

        $this->setDates($this->date);

        // $this->refresh();
    });

    $setDates = function ($date) {
        $date = Carbon::parse($date);
        $startOfMonth = $date->copy()->startOfMonth();
        $dayOfWeek = $startOfMonth->dayOfWeek;
        $from = ($dayOfWeek == Carbon::MONDAY) 
                ? $startOfMonth->toDateString() 
                : $startOfMonth->subDays(($dayOfWeek + 6) % 7)->toDateString();
                
        $to = $date->copy()->endOfMonth()->toDateString();
        $this->setTimes($date);
        $this->dates = $this->ranges($from, $to, $date);
    };

    $setTimes = function ($date) {
        $timeClass = new BookingTime($this->user->id);
        $date = Carbon::parse($date);

        $time = $timeClass->get_time($date->format('Y-m-d'));
        if (empty(ao($time, 'times'))) {
            $this->times = [];
            return false;
        }
        $return = [];

        foreach (ao($time, 'times') as $key => $time_slot) {
            $check =  $timeClass->check_time(ao($time_slot, 'time_value'), ao($time, 'date'));
            $return[$key] = $time_slot;
            $return[$key]['check'] = $check;
        }

        $this->set_day_status();

        $this->date = $date;
        $this->times = $return;
    };

    $book = function () {
        // $this->validate();
        $sandybook = new YenaBook($this->user->id);

        $price = 0;

        foreach ($this->service as $key => $value) {
            $service = BookingService::find($value);
            $price += $service->price;
        }
        $this->price = $price;
        // SandyAudience::create_audience($this->user->id, $this->user->id);

        $sandybook
                ->setServices($this->service)
                ->setTime($this->time)
                ->setDate($this->date)
                ->setCustomer($this->user->id)
                ->save([], false, 1);

        // $redirect = route('sandy-blocks-booking-page-booking-success', ['_user' => $this->user->id]);
        // return redirect($redirect);

        $this->dispatch('close');
        $this->dispatch('updateCalendarEvent');
        $this->dispatch('refreshCalendar');

        
        $this->flashToast('success', __('Appointment booked successfully'));
    };

    $set_day_status = function () {
        $timeClass = new BookingTime($this->user->id);

        $day_id = $timeClass->get_day_id(date('l', strtotime($this->date)));
        $this->date_status = true;

        if (!ao($this->user->booking_workhours, "$day_id.enable")) {
            $this->date_status = false;
        }
    };

    $ranges = function ($from, $to, $date) {
        $timeClass = new BookingTime($this->user->id);
        $ranges = CarbonPeriod::create($from, $to);
        $month = [];
        
        foreach ($ranges as $item) {
            $currentDate = $item->toDateString();

            $day_id = $timeClass->get_day_id(date('l', strtotime($currentDate)));
            $date_status = false;
            $workhours = $this->service->booking_workhours ?? $this->user->booking_workhours;

            if (!ao($workhours, "$day_id.enable")) {
                $date_status = true;
            }

            $month[$item->toDateString()] = [
                'date'          => $item->toDateString(),
                'week'          => $item->format('D'),
                'day'           => $item->format('j'),
                'date_status'   => $date_status,
                'previous_month'=> ($item->month != $date->month), // Indicate if the date is from the previous month
            ];
        }

        return $month;
    };

    $setService = function($service_id){

        $services = $this->service;

        if(in_array($service_id, $services)){
            foreach ($services as $key => $value) {
                if($value == $service_id){
                    unset($services[$key]);
                }
            }
        }
        
        if(!in_array($service_id, $this->service)){
            $services[] = $service_id;
        }


        $this->service = $services;
    };

    $setPreviousMonth = function(){
        if(isPreviousMonthPast($this->date)) return;
        $date = Carbon::parse($this->date);

        $newDate = $date->subMonths(1);
        $this->date = $newDate;
        $this->setDates($newDate);
    };

    $setNextMonth = function(){
        $date = Carbon::parse($this->date);
      


        $newDate = $date->addMonths(1);
        $this->date = $newDate;
        $this->setDates($newDate);
    };
?>
<div>
        
    <div class="w-full" x-data="console_booking_single">
        <div class="flex flex-col">
        <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross text-sm"></i>
        </a>
    
        <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Appointment') }}</header>
    
        <hr class="yena-divider">
    
        <form wire:submit="book" class="px-8 pt-2 pb-6">

            <div class="grid grid-cols-1 md:!grid-cols-2 gap-3">
                @foreach($services as $item)
                    <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-start !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ in_array($item->id, $service) ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="setService('{{ $item->id }}')">
                      {{-- <div>
                         <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                            {!! __i('Maps, Navigation', 'pin-location-hand-select', 'w-5 h-5 text-black') !!}
                         </div>
                      </div> --}}
 
                      <div class="flex flex-col">
                         <p class="text-xl font-bold text-[var(--yena-colors-gray-800)] truncate">{{ $item->name }}</p>
                         <p class="text-xs text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)]">{!! iam()->price($item->price) !!} + <span>{{ $item->duration }}</span>{{ __('min') }}</p>
                      </div>
                   </button>
                @endforeach
            </div>


            <div>
                <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                   <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Select a Date & Time') }}</span>
                   <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                </div>
                <div class="relative flex items-center gap-3 mt-[6px] mb-[18px]">
                  <div class="">
                      <div class="relative z-[1] inline-flex justify-center items-center w-[38px] h-[38px] rounded-[50%] text-[#0069ff] bg-[#0069ff11] cursor-pointer {{ isPreviousMonthPast($date) ? 'opacity-20 pointer-events-none !cursor-default' : '' }}" @click="setPreviousMonth">
                          <i class="ph ph-caret-left"></i>
                      </div>
                  </div>
                  <div class="flex justify-center my-[0] font-normal text-base">
                      {{ \Carbon\Carbon::parse($date)->format('M Y') }}
                  </div>

                  <div class="">
                      <div class="relative z-[1] inline-flex justify-center items-center w-[38px] h-[38px] rounded-[50%] text-[#0069ff] bg-[#0069ff11] cursor-pointer" @click="setNextMonth">
                          <i class="ph ph-caret-right"></i>
                      </div>
                  </div>
                </div>
             </div>
             @php
                 $formatted_date = $date->format('Y-m-d');
             @endphp
             <div class="flex items-start flex-col md:!flex-row gap-4 relative">
                <template x-if="calendarLoading">
                    <div class="absolute h-full left-0 top-0 w-[100%] flex items-center justify-center z-50">
                        <div class="z-40 absolute bg-white opacity-80 h-full w-[100%]"></div>

                        <div class="loader-ooo relative z-50 transform scale-[0.5]">
                            <div class="box1 opacity-30"></div>
                            <div class="box2 opacity-30"></div>
                            <div class="box3 opacity-30"></div>
                        </div>
                    </div>
                </template>
                <div class="daily-agent-monthly-calendar-w horizontal-calendar mb-0 md:mb-5 mt-5">
                    <div class="snd-months !mx-0 !bg-transparent">
                        <div class="snd-monthly-calendar-days-w !block">
    
                            <div class="snd-monthly-calendar-days !grid !grid-cols-7 !gap-[9px]">
    
                                @foreach ($weekdays as $item)
                                    <div class="font-normal text-xs leading-none uppercase text-center">
                                        {{ $item }}
                                    </div>
                                @endforeach
    
                                @foreach($dates as $key => $value)
                                    @php
                                        $attr = '';
                                        if(!ao($value, 'date_status')){
                                            $d = ao($value, 'date');
                                            $attr = "@click=\"setTimes('$d')\"";
                                        }
                                        $selected = $date->format('Y-m-d') == ao($value, 'date');
                                    @endphp
    
                                    <div class="flex items-center justify-center">
                                        <div class="snd-day snd-day-current week-day-5 is-f rounded-xl relative !w-[45px] !h-[45px] !flex-[0_0_45px] {{ $selected ? '!bg-black' : '' }} {{ !ao($value, 'previous_month') && ao($value, 'date_status') ? 'disabled cursor-default': '' }}  {{ ao($value, 'previous_month') ? '!opacity-0 pointer-events-none' : '' }}" {!! $attr !!}>
                                            {{-- <div class="snd-day-weekday no-disabled-btn">{{ ao($value, 'week') }}</div> --}}
                                            <div class="snd-day-box w-[100%] no-disabled-btn outline-none outline-0">
                                                <div class="snd-day-number flex justify-center items-center !text-[19px] {{ $selected ? '!text-white' : '' }}">{{ ao($value, 'day') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @if(ao($dates, "$formatted_date.date_status"))
                
                <div class="time-slots-flex flex md:!flex-col max-h-[350px] overflow-x-auto md:!overflow-y-auto gap-2 md:!w-[13rem] md:!px-4 max-w-full">
                    @for ($i = 0; $i < 7; $i++)
                    <button class="border-2 border-solid !h-auto !min-h-[44px]  !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] text-center gap-2 flex items-center justify-center rounded-lg !opacity-50 !bg-[#cccccc] !text-[#666666] [box-shadow:none!important] !cursor-default !pointer-events-none" type="button">
                        <div class="flex flex-col">
                            <p class="text-xs font-bold text-[var(--yena-colors-gray-800)] truncate">{{ __('Unavailable') }}</p>
                        </div>
                    </button>
                    @endfor
                </div>
    
                @else
                <div class="time-slots-flex flex md:!flex-col max-h-[350px] overflow-x-auto md:!overflow-y-auto gap-2 md:!w-[13rem] md:!px-4 max-w-full">
                    @foreach($times as $key => $value)
    
                        @php
                            $attr = '';
                            if(!ao($value, 'check')){
                                $d = ao($value, 'time_value');
                                $attr = 'wire:click="$set(\'time\', \''.$d.'\')"';
                            }
                        @endphp
                        <button class="border-2 border-solid !h-auto !min-h-[44px]  !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] text-center gap-2 flex items-center justify-center rounded-lg {{ $time == ao($value, 'time_value') ? '!border-[var(--yena-colors-purple-400)]' : '' }} {{ ao($value, 'check') ? '!opacity-50 !bg-[#cccccc] !text-[#666666] [box-shadow:none!important] !cursor-default !pointer-events-none' : '' }}" type="button" {!! $attr !!}>
                            <div class="flex flex-col">
                                <p class="text-xs font-bold text-[var(--yena-colors-gray-800)] truncate">{{ ao($value, 'start_time') }}</p>
                            </div>
                        </button>
                    @endforeach
                </div>
    
                @endif
             </div>

            @php
                $formatted_date = $date->format('Y-m-d');
            @endphp
            @if(ao($dates, "$formatted_date.date_status"))

                <div class="flex flex-col my-4">
                    {!! __i('--ie', 'alarm-clock-time', 'w-16 h-16 mb-3 text-black') !!}

                    <div class="text-xl font-bold mt-5-">{{ __('This date is not active') }}</div>
                </div>

            @else

            @endif
            
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
        </form>
        </div>
    </div>
    @script
    <script>
        Alpine.data('console_booking_single', () => {
           return {
            calendarLoading: false,

            setTimes(d){
                let $this = this;
                $this.calendarLoading = true;

                $this.$wire.setTimes(d).then(r => {
                    $this.calendarLoading = false;
                });
            },

            setPreviousMonth(){
                let $this = this;
                $this.calendarLoading = true;

                $this.$wire.setPreviousMonth().then(r => {
                    $this.calendarLoading = false;
                });
            },

            setNextMonth(){
                let $this = this;
                $this.calendarLoading = true;

                $this.$wire.setNextMonth().then(r => {
                    $this.calendarLoading = false;
                });
            },
            
            init(){
               let $this = this;
            },
           }
        });
    </script>
    @endscript
</div>