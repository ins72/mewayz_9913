<?php
    use Carbon\Carbon;
    use Livewire\Component;
    use Carbon\CarbonPeriod;
    use App\Models\BookingService;
    use App\Yena\SandyAudience;
    use App\Yena\YenaBook;
    use App\Yena\BookingTime;
    use App\Models\BookingAppointment;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, mount, on, uses};

    uses([ToastUp::class]);
    state([
        'service',
        'redirect',
    ]);

    state([
        'date'          => '',
        'date_status'   => '',
        'dates'         => '',
        'times'         => '',
        'time'          => '',
        '_service'       => [],
        'total_booking' => [],
        'timevalue'     => '',
        'available'     => '',
    ]);

    state([
        'weekdays' => ['Mon', 'Tue', 'Wed', "Thu", 'Fri', 'Sat', 'Sun'],
    ]);

    state([
        'user' => [],
    ]);
    state([
        'gallery' => [],
    ]);

    mount(function(){
        $this->date = Carbon::now();
        // $this->bio = $this->user;

        // $this->services = BookingService::where('user_id', $this->user->id)->orderBy('position', 'ASC')->orderBy('id', 'DESC')->get();

        $this->user = $this->service->user;
        $this->gallery = $this->service->gallery;


        $this->refresh();
        $this->setDates($this->date);
    });

    $refresh = function(){
        $this->total_booking = BookingAppointment::where('user_id', $this->user->id)->where('appointment_status', '!=' , 2)->count();
        $this->timeClass = new BookingTime($this->user->id);

        $workhours = $this->service->booking_workhours ?? $this->user->booking_workhours;
        

        $day_id = $this->timeClass->get_day_id(date('l', strtotime($this->date)));
        $start_time = $this->timeClass->format_minutes(ao($workhours, "$day_id.from"));
        $end_time = $this->timeClass->format_minutes(ao($workhours, "$day_id.to"));
        
        $this->timevalue = implode('-', [$start_time, $end_time]);

        $this->available = $this->timeClass->check_workday(date('l', strtotime($this->date->format('Y-m-d'))), $this->user->id);
    };

    $setDates = function ($date) {
        $date = Carbon::parse($date);
        // $from = Carbon::now()->toDateString();
        // $from = Carbon::parse($date)->subDays(7)->toDateString();
        // $to = Carbon::parse($date)->addDays(30)->toDateString();

        $startOfMonth = $date->copy()->startOfMonth();
        $dayOfWeek = $startOfMonth->dayOfWeek;
        $from = ($dayOfWeek == Carbon::MONDAY) 
                ? $startOfMonth->toDateString() 
                : $startOfMonth->subDays(($dayOfWeek + 6) % 7)->toDateString();
                
        $to = $date->copy()->endOfMonth()->toDateString();

        // if (Carbon::now()->format('Y-m') == Carbon::parse($date)->format('Y-m')) {
        //     $from = Carbon::now()->toDateString();
        // }

        $this->setTimes($date);
        $this->dates = $this->ranges($from, $to, $date);
    };

    $setTimes = function ($date) {
        $timeClass = new BookingTime($this->user->id);
        $date = Carbon::parse($date);

        $time = $timeClass->get_time($date->format('Y-m-d'), $this->service->booking_time_interval);
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

    $ranges = function ($from, $to, $date) {
        $timeClass = new BookingTime($this->user->id);
        $ranges = CarbonPeriod::create($from, $to);
        $month = [];
        
        foreach ($ranges as $item) {
            $currentDate = $item->toDateString();

            $day_id = $timeClass->get_day_id(date('l', strtotime($currentDate)));
            $date_status = true;
            $workhours = $this->service->booking_workhours ?? $this->user->booking_workhours;

            if (!ao($workhours, "$day_id.enable")) {
                $date_status = false;
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

    $book = function () {
        $this->validate([
            'time' => 'required',
            'date' => 'required',
            'service' => 'required',
        ]);
        $sandybook = new YenaBook($this->user->id);
        $customer = auth()->user();
        if (!$customer = auth()->user()) {
            $this->flashToast('error', __('Please login to proceed.'));
            return redirect(route('login'));
        }

        $price = $this->service->price;
        $this->price = $price;



        $isFree = $price == 0 ? true : false;


        if($isFree){
            $sandybook
                ->setServices([$this->service->id])
                ->setTime($this->time)
                ->setDate($this->date)
                ->setCustomer($customer->id)
                ->save([], false, 1);

            $this->dispatch('close');
            $this->dispatch('refreshCalendar');

            
            $this->refresh();
            $this->flashToast('success', __('Appointment booked successfully'));
            
            \App\Yena\SandyAudience::create_audience($this->user->id, $customer->id);
            return;
        }else{

            $item = [
                'name'          =>    __('Booking Appointment'),
                'description'   =>    __('Booked an appointment for :user', ['user' => $this->user->name]),
            ];
            
            $meta = [
                'user_id'       => $this->user->id,
                'bio_id'        => $this->user->id,
                'item'          => $item,
                // 'date'          => $this->date,
                'date'          => \Carbon\Carbon::parse($this->date)->format('Y-m-d'),
                'time'          => $this->time,
                'services'      => [$this->service->id],
                'service'       => $this->service->id,
                'customer'      => $customer->id
            ];

            $data = [
                'uref'          => md5(microtime()),
                'email'         => $customer->email,
                'price'         => $price,
                'callback'      => route('general-success', [
                    'redirect' => route('out-booking-service-page', ['slug' => $this->service->id])
                ]),
                'currency'      => config('app.wallet.currency'),
                'frequency'     => 'monthly',
                'payment_type'  => 'onetime',
                'meta'          => $meta,
            ];

            //
            
            $call_function = \App\Yena\SandyCheckout::bookUser($this->user, $meta);
            $call = \App\Yena\SandyCheckout::cr(config('app.wallet.defaultMethod'), $data, $call_function);
            
            return $this->js("window.location.replace('$call');");
        }
    };

    $set_day_status = function () {
        $timeClass = new BookingTime($this->user->id);

        $day_id = $timeClass->get_day_id(date('l', strtotime($this->date)));
        $this->date_status = true;
        $workhours = $this->service->booking_workhours ?? $this->user->booking_workhours;

        if (!ao($workhours, "$day_id.enable")) {
            $this->date_status = false;
        }
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
    <div x-data="user_booking_single">
        <div class="yena-single-product">
            <div class="w-[100%] max-w-screen-xl mx-auto px-5 md:!px-[80px] py-10 md:!py-[80px] product-view-container !flex flex-col lg:!flex-row">
                <div class="product-view-col !w-[350px] flex-[0_0_350px]">
                    <div class="wrapper__profile-mentor info-card-container flex-col !bg-transparent !border-0 !p-0 !mb-2">
        
                        <div class="head w-[100%]">
                         <div class="flex flex-wrap items-center justify-between">
                             <div class="flex items-center mb-3">
                                 <img src="{{ $user->getAvatar() }}" class="w-[60px] h-[60px] rounded-[50%] object-cover" alt="">
                                 <div class="ml-3">
                                     <h5 class="font-bold text-[14px] lg:!text-[14px] mb-0">{{ $user->name }}</h5>
                                     <p class="mb-0 medium text-[12px] lg:!text-[12px]">{{ $user->booking_title }}</p>
                                 </div>
                             </div>
                             {{-- <a class="font-bold text-[12px] lg:!text-[12px] btn btn__purple text-white shadow btn__profile" href="/mentor">See Full Profile</a> --}}
                         </div>
                        </div>
                    </div>
                    <div class="product-view-heading">
                       {{-- <div class="label-small">Product</div> --}}
                       <div class="heading-2 product-view-title !text-[26px]">{{ $service->name }}</div>
                       <div class="product-view-wrapper">
                          <div class="paragraph-x-large">{!! $service->getPrice() !!}</div>
                       </div>
                       <div class="txt-price-coundown flex justify-between px-0">
                           {{-- <div class="-text">
                               <h2>{{ __('Total Bookings') }}</h2>
                               <p>{{ nr($total_booking) }}</p>
                           </div> --}}
                           @if ($available)
                           <div class="ctd">
                               <h3>{{ __('Availabilty') }}</h3>
                               <p>{{ $timevalue }}</p>
                           </div>
                           @endif
                       </div>
                       <div class="paragraph-medium product-view-description textarea-content">
                         {!! $service->description !!}
                       </div>
                    </div>
                    <div class="product-view-col" :class="{
                        '!hidden': !gallery || gallery.length == 0
                    }">
                        <template x-if="selectedGallery">
                            <img :src="getMedia(selectedGallery)" x-cloak alt=" " class="product-view-image !h-[230px]">
                        </template>
        
                       <div class="image-cells-container">
                            <template x-for="(item, index) in gallery" :key="index">
                                <button class="image-cells-button image-cells-selected" @click="clickGallery(item)" :class="{
                                    'image-cells-selected': selectedGallery == item,
                                }" type="button">
                                   <img :src="getMedia(item)" alt=" " class="image-cells-image">
                                </button>
                            </template>
                       </div>
                    </div>
                </div>
                <div class="product-view-col">
                   <div class="product-view-heading">
                      {{-- <div class="label-small">Product</div> --}}
                      <div class="heading-2 product-view-title !text-[26px]">{{ __('Select a Date & Time') }}</div>
                      <div class="relative flex items-center gap-3 mt-[6px] mx-[6px] mb-[15px]">
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
                   <form wire:submit="book" class="">
            
                        @php
                            $formatted_date = $date->format('Y-m-d');
                        @endphp
                       <div class="flex items-start flex-col md:!flex-row gap-4 relative">
                        <template x-if="calendarLoading">
                            <div class="absolute h-full w-[100%] flex items-center justify-center z-50">
                                <div class="z-40 absolute bg-white opacity-80 h-full w-[100%]"></div>

                                <div class="loader-ooo relative z-50 transform scale-[0.5]">
                                    <div class="box1 opacity-30"></div>
                                    <div class="box2 opacity-30"></div>
                                    <div class="box3 opacity-30"></div>
                                </div>
                                {{-- <div class="iii-loader-wrapper relative z-50">
                                    <div class="iii-loader">
                                       <div class="iii-roller"></div>
                                       <div class="iii-roller"></div>
                                    </div>
                                    
                                    <div id="iii-loader2" class="iii-loader">
                                       <div class="iii-roller"></div>
                                       <div class="iii-roller"></div>
                                    </div>
                                    
                                    <div id="iii-loader3" class="iii-loader">
                                       <div class="iii-roller"></div>
                                       <div class="iii-roller"></div>
                                    </div>
                                 </div> --}}
                                 {{-- <div class="loader--i15 relative flex items-center justify-center w-40 h-40  z-50">
                                    <div></div><div></div>
                                    <div></div><div></div>
                                    <div></div><div></div>
                                    <div></div><div></div>
                                 </div> --}}
                                {{-- <div class="blobs relative z-50">
                                   <div class="blob-center"></div>
                                   <div class="blob"></div>
                                   <div class="blob"></div>
                                   <div class="blob"></div>
                                   <div class="blob"></div>
                                   <div class="blob"></div>
                                   <div class="blob"></div>
                                </div>
                                <div class="loader-ooo relative z-50 transform !scale-20">
                                   <div class="box1 opacity-30"></div>
                                   <div class="box2 opacity-30"></div>
                                   <div class="box3 opacity-30"></div>
                                 </div> --}}
                            </div>
                        </template>
                        <div class="daily-agent-monthly-calendar-w horizontal-calendar mb-0 md:mb-5 max-w-[500px]">
                            <div class="snd-months !mx-0 !bg-transparent">
                                <div class="snd-monthly-calendar-days-w !block">
            
                                    <div class="snd-monthly-calendar-days !grid !grid-cols-7">

                                        @foreach ($weekdays as $item)
                                            <div class="font-normal text-xs leading-none uppercase text-center">
                                                {{ $item }}
                                            </div>
                                        @endforeach

                                        @foreach($dates as $key => $value)
                                            @php
                                                $attr = '';
                                                if(ao($value, 'date_status')){
                                                    $d = ao($value, 'date');
                                                    $attr = "@click=\"setTimes('$d')\"";
                                                }
                                                $selected = $date->format('Y-m-d') == ao($value, 'date');
                                            @endphp
            
                                            <div class="flex items-center justify-center">
                                                <div class="snd-day snd-day-current week-day-5 is-f rounded-xl relative !w-[45px] !h-[45px] !flex-[0_0_45px] {{ $selected ? '!bg-black' : '' }} {{ !ao($value, 'previous_month') && !ao($value, 'date_status') ? 'disabled !cursor-default': '' }}  {{ ao($value, 'previous_month') ? '!opacity-0 pointer-events-none' : '' }}" {!! $attr !!}>
                                                    {{-- <div class="snd-day-weekday no-disabled-btn">{{ ao($value, 'week') }}</div> --}}
                                                    <div class="snd-day-box w-[100%] no-disabled-btn outline-none outline-0">
                                                        <div class="snd-day-number flex justify-center items-center !text-[19px] {{ $selected ? '!text-white' : '' }}">{{ ao($value, 'day') }}</div>
    
                                                        {{-- {{ ao($value, 'date') }} --}}
    
                                                        {{-- @if ($selected)
                                                        <div class="transform -translate-x-1/2 left-2/4 absolute -bottom-[3px] h-[4px] w-[4px] rounded-full !bg-white"></div>
                                                        @endif --}}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                                
                        @if(!ao($dates, "$formatted_date.date_status"))
                        
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
                        <div class="time-slots-flex flex md:!flex-col max-h-[400px] overflow-x-auto md:!overflow-y-auto gap-2 md:!w-[13rem] md:!px-4 max-w-full">
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
                        @if(!ao($dates, "$formatted_date.date_status"))
                
                            <div class="flex flex-col my-4">
                                {!! __i('--ie', 'alarm-clock-time', 'w-16 h-16 mb-3 text-black') !!}
            
                                <div class="text-xl font-bold mt-5-">{{ __('This date is not active') }}</div>
                            </div>
                        
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
                           <div class="mt-1 bg-red-200 font--11 p-1 px-2 rounded-md">
                               <div class="flex items-center">
                                   <div>
                                       <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                   </div>
                                   <div class="flex-grow ml-1 text-xs">{{ str_replace('create.', '', $error) }}</div>
                               </div>
                           </div>
                       @endif
                       @if(ao($dates, "$formatted_date.date_status"))
                       <button class="button product-view-button mt-1 w-[100%]">{{ __('Book') }}</button>
                       @endif
                   </form>
                   
                   <div class="product-view-wrapper">
                      <div class="share-share">
                         <div class="paragraph-medium share-title">{{ __('Share:') }}</div>
                         <div class="socials-socials share-socials items-center">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ route('out-booking-service-page', ['slug' => $service->id]) }}" target="_blank" rel="noopener noreferrer" class="socials-social flex">
                                <i class="ph ph-facebook-logo text-2xl"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ route('out-booking-service-page', ['slug' => $service->id]) }}" target="_blank" rel="noopener noreferrer" class="socials-social flex">
                                <i class="ph ph-x-logo text-2xl"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ route('out-booking-service-page', ['slug' => $service->id]) }}" target="_blank" rel="noopener noreferrer" class="socials-social flex">
                                <i class="ph ph-whatsapp-logo text-2xl"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ route('out-booking-service-page', ['slug' => $service->id]) }}" target="_blank" rel="noopener noreferrer" class="socials-social flex">
                                <i class="ph ph-linkedin-logo text-2xl"></i>
                            </a>
                         </div>
                         
                      </div>
                   </div>
                </div>
             </div>
             
            
        </div>
    </div>
    @script
    <script>
        Alpine.data('user_booking_single', () => {
           return {
            oldSelectedGallery: null,
            selectedGallery: null,
            gallery: @entangle('gallery'),
            gs: '{{ gs('media/booking/image') }}',
            calendarLoading: false,
            getMedia(media){
                return this.gs +'/'+ media;
            },

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

            clickGallery(item){
                if(this.oldSelectedGallery == item){
                    this.selectedGallery = null;
                    this.oldSelectedGallery = null;
                    return;
                }
                
                this.selectedGallery = item;
                this.oldSelectedGallery = item;
            },
            
            init(){
               let $this = this;
            },
           }
        });
    </script>
    @endscript
</div>