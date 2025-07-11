<?php
    use Carbon\Carbon;
    use Livewire\Component;
    use Carbon\CarbonPeriod;
    use App\Models\BookingService;
    use App\Yena\SandyAudience;
    use App\Yena\YenaBook;
    use App\Yena\BookingTime;
    use App\Models\BookingAppointment;
    use function Livewire\Volt\{state, mount, on};

    state([
        'user',
        'redirect',
    ]);

    state([
        'date'          => '',
        'date_status'   => '',
        'services'      => '',
        'dates'         => '',
        'times'         => '',
        'time'          => '',
        'service'       => [],
        'total_booking' => [],
        'timevalue'     => '',
        'available'     => '',
    ]);

    state([
        'gallery' => function(){
            return $this->user->booking_gallery;
        },
    ]);

    mount(function(){
        $this->date = Carbon::now();
        // $this->bio = $this->user;

        $this->services = BookingService::where('user_id', $this->user->id)->orderBy('position', 'ASC')->orderBy('id', 'DESC')->get();


        $this->refresh();
        $this->setDates($this->date);
    });

    $refresh = function(){
      $this->total_booking = BookingAppointment::where('user_id', $this->user->id)->where('appointment_status', '!=' , 2)->count();
      $this->timeClass = new BookingTime($this->user->id);

        

        $day_id = $this->timeClass->get_day_id(date('l', strtotime($this->date)));
        $start_time = $this->timeClass->format_minutes(ao($this->user->booking_workhours, "$day_id.from"));
        $end_time = $this->timeClass->format_minutes(ao($this->user->booking_workhours, "$day_id.to"));
        
        $this->timevalue = implode('-', [$start_time, $end_time]);

        $this->available = $this->timeClass->check_workday(date('l', strtotime($this->date->format('Y-m-d'))), $this->user->id);
    };

    $setDates = function ($date) {
        $date = Carbon::parse($date);
        $from = Carbon::now()->toDateString();
        $to = Carbon::parse($date)->addDays(30)->toDateString();

        if (Carbon::now()->format('Y-m') == Carbon::parse($date)->format('Y-m')) {
            $from = Carbon::now()->toDateString();
        }

        $this->setTimes($this->date);
        $this->dates = $this->ranges($from, $to);
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

        $price = 0;

        foreach ($this->service as $key => $value) {
            $service = BookingService::find($value);
            $price += $service->price;
        }
        $this->price = $price;



        $isFree = $price == 0 ? true : false;


        if($isFree){
            $sandybook
                ->setServices($this->service)
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
                'services'      => $this->service,
                'customer'      => $customer->id
            ];

            $data = [
                'uref'          => md5(microtime()),
                'email'         => $customer->email,
                'price'         => $price,
                'callback'      => route('general-success', ['redirect' => $this->redirect]),
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

        if (!ao($this->user->booking_workhours, "$day_id.enable")) {
            $this->date_status = false;
        }
    };

    $ranges = function ($from, $to) {
        $timeClass = new BookingTime($this->user->id);
        $ranges = CarbonPeriod::create($from, $to);
        $month = [];
        
        foreach ($ranges as $item) {
            $date = $item->toDateString();

            $day_id = $timeClass->get_day_id(date('l', strtotime($date)));
            $date_status = false;
            
            if (!ao($this->user->booking_workhours, "$day_id.enable")) {
                $date_status = true;
            }

            $month[$item->toDateString()] = [
                'date'          => $item->toDateString(),
                'week'          => $item->format('D'),
                'day'           => $item->format('j'),
                'date_status'   => $date_status,
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
?>
<div>
    <div x-data="user_booking_single" class="yena-single-product">
        <div>
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
            <div class="product-view-col">
                <template x-if="selectedGallery">
                    <img :src="getMedia(selectedGallery)" x-cloak alt=" " class="product-view-image">
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
            <div class="txt-price-coundown flex justify-between px-0">
                <div class="-text">
                    <h2>{{ __('Total Bookings') }}</h2>
                    <p>{{ nr($total_booking) }}</p>
                </div>
                @if ($available)
                <div class="ctd">
                    <h3>{{ __('Availabilty') }}</h3>
                    <p>{{ $timevalue }}</p>
                </div>
                @endif
            </div>
        
            <div class="description text-base my-5 textarea-content">{!! $user->booking_description !!}</div>
        </div> 

        <form wire:submit="book" >

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


            <div class="daily-agent-monthly-calendar-w horizontal-calendar mb-0 md:mb-5 mt-5">


                <div class="snd-months overflow-x-auto mx-0 !bg-transparent">
                    <div class="snd-monthly-calendar-days-w !block">

                        <div class="snd-monthly-calendar-days">
                            @foreach($dates as $key => $value)

                                @php
                                    $attr = '';
                                    if(!ao($value, 'date_status')){
                                        $d = ao($value, 'date');
                                        $attr = "wire:click=\"setTimes('$d')\"";
                                    }
                                @endphp

                                <div class="snd-day snd-day-current week-day-5 is-f rounded-xl {{ $date->format('Y-m-d') == ao($value, 'date') ? 'selected' : '' }} {{ ao($value, 'date_status') ? 'disabled cursor-default': '' }}" {!! $attr !!}>
                                    <div class="snd-day-weekday no-disabled-btn">{{ ao($value, 'week') }}</div>
                                    <div class="snd-day-box w-[100%] no-disabled-btn outline-none outline-0">
                                        <div class="snd-day-number">{{ ao($value, 'day') }}</div>
                                        {{-- <div class="day-status snd-day-status w-1 rounded-full m-auto">

                                        </div> --}}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            @php
                $formatted_date = $date->format('Y-m-d');
            @endphp
            @if(ao($dates, "$formatted_date.date_status"))

                <div class="flex flex-col">
                    {!! __i('--ie', 'alarm-clock-time', 'w-20 h-20 mb-3 text-black') !!}

                    <div class="text-xl font-bold mt-5-">{{ __('This date is not active') }}</div>
                </div>

            @else
                <div class="time-slots-flex flex overflow-x-auto gap-2 mt-4">

                    @foreach($times as $key => $value)

                        @php
                            $attr = '';
                            if(!ao($value, 'check')){
                                $d = ao($value, 'time_value');
                                $attr = 'wire:click="$set(\'time\', \''.$d.'\')"';
                            }
                        @endphp
                        <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !min-w-[initial] {{ $time == ao($value, 'time_value') ? '!border-[var(--yena-colors-purple-400)]' : '' }} {{ ao($value, 'check') ? '!opacity-50 !bg-[#cccccc] !text-[#666666] [box-shadow:none!important] !cursor-default !pointer-events-none' : '' }}" type="button" {!! $attr !!}>
                            <div class="flex flex-col">
                                <p class="text-xs font-bold text-[var(--yena-colors-gray-800)] truncate">{{ ao($value, 'start_time') }}</p>
                            </div>
                        </button>
                    @endforeach
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
                <div class="mt-5 bg-red-200 font--11 p-1 px-2 rounded-md">
                    <div class="flex items-center">
                        <div>
                            <i class="fi fi-rr-cross-circle flex text-xs"></i>
                        </div>
                        <div class="flex-grow ml-1 text-xs">{{ str_replace('create.', '', $error) }}</div>
                    </div>
                </div>
            @endif
            <button class="yena-button-stack mt-5 w-[100%]">{{ __('Book') }}</button>
        </form>
        
        
    </div>
    @script
    <script>
        Alpine.data('user_booking_single', () => {
           return {
            oldSelectedGallery: null,
            selectedGallery: null,
            gallery: @entangle('gallery'),
            gs: '{{ gs('media/booking/image') }}',
            getMedia(media){
                return this.gs +'/'+ media;
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