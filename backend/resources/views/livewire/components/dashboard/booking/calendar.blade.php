<?php
    use App\Yena\BookingTime;
    use App\Models\BookingService;
    use App\Models\BookingWorkingBreak;
    use App\Models\BookingAppointment;
    use Carbon\Carbon;
    use Carbon\CarbonPeriod;
    use function Livewire\Volt\{state, mount, rules, on, placeholder};

    placeholder('
        <div class="p-5 w-full mt-1 bg-white rounded-xl">
            <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
            <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
        </div>
    ');

    on([
        'refreshCalendar' => fn () => $this->refresh(),
    ]);
    state([
        'date' => null,
        'booking' => null,
        'calendar_head' => null,
        'config' => null,
    ]);

    state([
        'user' => fn() => iam(),
    ]);

    rules(fn() => [
        'services.*.name' => 'required',
        'services.*.duration' => 'required|numeric',
        'services.*.price' => 'required|numeric',
    ]);

    mount(function(){
        $this->date = Carbon::now();

        $this->config = [
            'interval' => $this->user->booking_time_interval ?? 15
        ];

        $this->refresh();
    });
    
    $create_break = function($date, $start_time, $end_time) {
        $time = implode('-', [$start_time, $end_time]);
        
        $break = new BookingWorkingBreak;
        $break->user_id = $this->user->id;
        $break->date = $date;
        $break->time = $time;
        $break->save();
        
        $this->refresh();
    };

    $remove_break = function($breaks) {
        $breaks = json_decode($breaks);
        BookingWorkingBreak::where('user_id', $this->user->id)->whereIn('id', $breaks)->delete();
        
        $this->refresh();
    };

    $create = function() {
        $this->validate([
            'break_date' => 'required',
            'break_start_time' => 'required',
            'break_end_time' => 'required',
        ]);

        $save = function($date) {
            $start_time = hour2min($this->break_start_time);
            $end_time = hour2min($this->break_end_time);
            $time = implode('-', [$start_time, $end_time]);
            
            $break = new BarbersWorkingBreak;
            $break->user_id = $this->user_id;
            $break->date = $date;
            $break->time = $time;
            $break->save();

            return $break;
        };

        $breaks = explode(',', $this->break_date);
        if (!empty($breaks[1])) {
            $period = CarbonPeriod::create($breaks[0], $breaks[1]);
            
            foreach ($period as $d) {
                $save($d->format('Y-m-d'));
            }

            return back();
        }

        $save($breaks[0]);

        $this->refresh();
    };

    $sort = function($list) {
        foreach ($list as $key => $value) {
            $value['value'] = (int) $value['value'];
            $value['order'] = (int) $value['order'];
            $update = BookingService::find($value['value']);
            $update->position = $value['order'];
            $update->save();
        }
        
        $this->refresh();
    };

    $edit = function($id, $index) {
        foreach ($this->services as $item) {
            $item->save();
        }
    };

    $delete = function($id) {
        if (!$delete = BookingService::where('id', $id)->where('user_id', $this->user->id)->first()) {
            return false;
        }
        $delete->delete();
        
        $this->refresh();
    };

    $change_date = function($date) {
        $this->date = Carbon::parse($date);
        $this->refresh();

        return $this->date->format('F, Y');
    };

    $refresh = function() {
        $this->booking = BookingAppointment::where('user_id', $this->user->id)
                                ->where('appointment_status', '!=', 2)
                                ->whereDate('date', $this->date->toDateString())
                                ->orderBy('id', 'DESC')
                                ->get();

        $this->calendar_head = $this->generate_daily_monthly_calender($this->date);
    };

    $generate_daily_monthly_calender = function($date, $settings = []) {
        $timeClass = new BookingTime($this->user->id);

        $day_id = $timeClass->get_day_id(date('l', strtotime($date)));
        $slot = $timeClass->get_timeslot_by_day($day_id, $this->user->id);

        $carbon_date_start_day = Carbon::parse($date)->startOfMonth()->format('j');
        $carbon_date_end_day = Carbon::parse($date)->endOfMonth()->format('j');

        $barbers = [];

        $interval = ao($this->config, 'interval');

        $data = [];

        for ($i=$carbon_date_start_day; $i <= $carbon_date_end_day; $i++) {
            $format = Carbon::parse($date)->startOfMonth()->format('Y-m-') . $i;
            $format = Carbon::parse($format);
            $active = false;

            $from = $timeClass->min_time_all($format->toDateString());
            $to = $timeClass->max_time_all($format->toDateString());

            $slots = $timeClass->get_time_slots($interval, $from, $to);

            $appointments = BookingAppointment::where('user_id', $this->user->id)->whereDate('date', $format->toDateString());

            $appointments = $appointments->get();

            $day_status = '';

            $data[$i] = [
                'day' => $format->format('D'),
                'day_digit' => $i,
                'active' => $active,
                'week_day' => $format->format('N'),
                'date' => $format->format('Y-m-d'),
                'day_status' => $day_status
            ];
        }

        return $data;
    };
?>

<div>

    <div x-data="calendar_alpine">
        <div>
            {{-- @section('footerJS')
        
            <script>
                
            jQuery('.snd-months').animate({scrollLeft: jQuery('.snd-day.selected').position().left - 20}, 500);
            </script>
            @stop --}}
        
            <script>
                // function calendar_alpine_head() {
                //     return {
        
                //         title: "{{ __('Select Month') }}",
                //         init(){
                //             var _this = this;
                            
                //             this.$nextTick(() => {
                //                 var month_picker = this.$refs.month_picker;
                                
                //                 var datepicker = jQuery(month_picker).datepicker({
                //                     language: 'en',
                //                     dateFormat: 'yyyy-mm',
                //                     autoClose: true,
                //                     timepicker: false,
                //                     toggleSelected: false,
                //                     classes: 'card-shadow border-0 p-5 rounded-2xl',
                //                     range: false,
                //                     view: 'months',
                //                     minView: 'months',
                //                     onSelect: (formatted_date, date) => {
                //                        this.$wire.change_date(formatted_date).then(result => {
                //                         _this.title = result;
                //                        });
                //                     }
                //                 });
        
                //             });
                //         }
                //     }
                // }
            </script>
            
            @php
                $timeClass = (new BookingTime($user->id));
                $day_id = $timeClass->get_day_id(date('l', strtotime($date)));
                $slot = $timeClass->get_timeslot_by_day($day_id, $user->id);
        
                $calender_start_time = ao($slot, 'from');
                $calender_end_time = ao($slot, 'to');
                $interval = $user->booking_time_interval;
                $p_height = 2000;//$barber->calendar_day_min_height();
                $total_periods = floor(($calender_end_time - $calender_start_time) / $interval) + 1;
                $period_height = floor($p_height / $total_periods);
                $period_css = (($total_periods * 20) < $p_height) ? "height: {$period_height}px;" : '';
            @endphp
        
            <div class="mt-0 mb-5">
                <label class="search-filter w-full" for="month-selector">
                    <input class="-search-input" type="text" name="query" value="{{ request()->get('query') }}" placeholder="{{ __('Search') }}" readonly="true" id="month-selector" wire:ignore x-ref="month_picker">
            
                    <div class="-filter-btn filter-open">
                        {!! __i('emails', 'calendar-schedule', 'w-5 h-5') !!}
                    </div>
                </label>
            </div>
        
        
            <div class="wj-bookings-daily card">
                <div class="daily-agent-monthly-calendar-w horizontal-calendar mb-0 md:mb-5">
        
        
                    <div class="snd-months overflow-x-auto mx-0">
                        <div class="snd-monthly-calendar-days-w !block">
                            
                            <div class="snd-monthly-calendar-days">
        
                                @foreach ($calendar_head as $key => $value)
                                    <div class="snd-day snd-day-current flex flex-col items-center justify-center week-day-{{ ao($value, 'week_day') }} is-f {{ $date->format('Y-m-d') == ao($value, 'date') ? 'selected' : '' }}" wire:click="change_date('{{ ao($value, 'date') }}')">
                                        <button class="snd-day-weekday no-disabled pt-0">{{ ao($value, 'day') }}</button>
                                        <button class="snd-day-box w-full no-disabled outline-none outline-0 flex items-center justify-center">
                                            <div class="snd-day-number font-black text-3xl">{{ ao($value, 'day_digit') }}</div>
                                        </button>
                                        <button class="snd-day-weekday no-disabled pt-0">{{ \Carbon\Carbon::parse(ao($value, 'date'))->format('Y') }}</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="daily-agent-calendar-w ">
                    <div class="calendar-daily-agent-w">
                        @if ($timeClass->check_workday(date('l', strtotime($date)), $user->id))
                        <div class="calendar-hours">
                            <div class="ch-hours">
                                <div class="ch-filter">
                                    <div class="ch-filter-trigger"></div>
                                </div>
                                            
                                @for ($minutes = $calender_start_time; $minutes <= $calender_end_time; $minutes+= $interval)
                                @php
                                
                                $period_class = 'chh-period';
                                $period_class   .= (($minutes == $calender_end_time) || (($minutes + $interval) > $calender_end_time)) ? ' last-period' : '';
                                $period_class   .= (($minutes % 60) == 0) ? ' chh-period-hour' : ' chh-period-minutes';
                                @endphp
                                <div class="{{ $period_class }}" style="{{ $period_css }}"><span>{{ str_replace(' ', '', $timeClass->format_minutes($minutes)) }}</span></div>
                                @endfor
                            </div>
        
                            <div class="ch-agents">
                                <div class="da-head-agents">
                                    <div class="da-head-agent">
                                        <div class="da-head-agent-avatar"
                                            style="background-image: url({{ iam()->getAvatar() }});">
                                        </div>
                                        <div class="da-head-agent-name">{{ $user->name }}</div>
                                    </div>
                                </div>
                                <div class="da-agents-bookings">
                                    <div class="da-agent-bookings-and-periods">
                                        <div class="ch-day-periods">
                                            
                                            @php
                                                list($work_start_time, $work_end_time) = $timeClass->work_time_to_min($date->format('Y-m-d'), $user->id);
                                            @endphp
                                            
                                            @for ($minutes = $calender_start_time; $minutes <= $calender_end_time; $minutes += $interval)
        
        
                                            @php
                                                
                                                $start_time = $minutes;
                                                $end_time = $start_time + $interval;
                                                
                                                $nice_start_time = $timeClass->format_minutes($start_time);
                                                $nice_end_time = $timeClass->format_minutes($end_time);
        
        
                                                $period_class   = 'chd-period';
                                                $period_class   .= (($minutes == $calender_end_time) || (($minutes + $interval) > $calender_end_time)) ? ' last-period' : '';
                                                $period_class   .= (($minutes % 60) == 0) ? ' chd-period-hour' : ' chd-period-minutes';
        
                                                // Check breaks
                                                $check_breaks = $timeClass->check_break_time(implode('-', [$start_time, $end_time]), $date->format('Y-m-d'), $user->id);
        
                                                if($check_breaks){
                                                    $period_class .= ' chd-period-off';
                                                    $period_class = str_replace('booking-popup-open', '', $period_class);
                                                }
                                            @endphp
                                            
                                            <div class="{{ $period_class }}" style="{{ $period_css }}">
        
                                                @if ($check_breaks)
                                                    
                                                    <form x-on:submit.prevent="remove_break('{{ json_encode($check_breaks) }}')" method="post" class="absolute right-0 left-0 top-0 bottom-0">
                                                        
                                                    <button class="text-red-400 h-full w-full text-remove no-disabled-btn"></button>
                                                    </form>
        
                                                    @else
                                                    <form x-on:submit.prevent="create_break('{{ $date->format('Y-m-d') }}', '{{ $start_time }}', '{{ $end_time }}')" method="post" class="absolute right-0 left-0 top-0 bottom-0">
        
                                                    <button class="text-red-400 h-full w-full text-remove no-disabled-btn"></button>
                                                    </form>
                                                @endif
                                                
                                                <div class="chd-period-minutes-value">{{ $timeClass->format_minutes($minutes) }}</div>
                                            </div>
        
        
        
                                            @endfor
                                            
                                            <div class="da-agent-bookings">
                                                @foreach ($booking as $key => $appt)
                                                    @php
                                                    $timeClass = (new BookingTime($user->id));
                                                    $day_id = $timeClass->get_day_id(date('l', strtotime($date)));
                                                    $slot = $timeClass->get_timeslot_by_day($day_id, $user->id);
                                                
                                                    $calender_start_time = ao($slot, 'from');
                                                    $calender_end_time = ao($slot, 'to');
                                                    
                                                    $total_time = $calender_end_time - $calender_start_time;
                                                    $total_time = $total_time <= 0 ? 60 : $total_time;
                                                    $calendar_total_time = $total_time;
                                                
                                                    $start_time = $appt->start_time;
                                                    $end_time = $appt->end_time;
                                                
                                                    $booking_duration = $end_time - $start_time;
                                                
                                                
                                                
                                                    if($booking_duration <= 0){
                                                        
                                                        $services_duration = 0;
                                                
                                                        foreach ($appt->services as $item) {
                                                            $services_duration += ao($item->settings, 'service_duration');
                                                        }
                                                        $booking_duration = ($services_duration > 0) ? $services_duration : 60;
                                                    }
                                                
                                                    $booking_duration_percent = 0;
                                                    $booking_start_percent = 0;
                                                
                                                    try {
                                                        $booking_duration_percent = $booking_duration * 100 / $calendar_total_time;
                                                    } catch (\Throwable $th) {
                                                        //throw $th;
                                                    }
                                                    
                                                    try {
                                                        $booking_start_percent = ($start_time - $calender_start_time) / ($calender_end_time - $calender_start_time) * 100;
                                                        if($booking_start_percent < 0) $booking_start_percent = 0;
                                                    } catch (\Throwable $e) {
                                                    }
                                                
                                                    $appt_link = '#';
                                                
                                                    if (isset($link)) {
                                                        $appt_link = $link;
                                                    }
                                                
                                                @endphp
                                                
                                                <a href="{{ $appt_link }}" class="ch-day-booking status-approved" style="top: {{ $booking_start_percent }}%; height: {{ $booking_duration_percent }}%;">
                                                    <div class="ch-day-booking-i">
                                                        <div class="booking-service-name truncate">{{ $appt->services_name }}</div>
                                                        <div class="booking-time">{{ $timeClass->format_minutes($start_time) }} - {{ $timeClass->format_minutes($end_time) }}</div>
                                                    </div>
                                                </a>
                                                @endforeach
                                            </div>
        
                                        </div>
                                        <div class="da-agent-bookings">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        @else
                                
                        <div class="flex flex-col items-center rounded-xl border-2 border-dotted border-gray-200 p-4">
                            <img class="lozad w-20" alt="" src="{{ gs('assets/image/emoji/Yellow-1/Confused.png') }}">
                            <div class="text-xl font-bold mt-5-">{{ __('Not Available') }}</div>
                            <div class="w-3/4 mt-3 text-center">
                            <div class="text-sm text-gray-400 text-center">{{ __('You have not set any working hours for this day.') }}</div>
                            {{-- <a href="{{ route('sandy-blocks-booking-mix-settings') }}" class="sandy-expandable-btn mt-5 mx-auto"><span>{{ __('Edit Working Hours') }}</span></a> --}}
                            </div>
                        </div>
                        @endif
        
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    @script
        <script>
            Alpine.data('calendar_alpine', () => {
                return {
                    create_break(date, start_time, end_time){
                            var _this = this;
                            $.confirm('{{ __("Do you want to add a break for this time?") }}', {
                                title: '{{ __("Add break for the selected time slot") }}',
                                cancelButton:'{{ __("Cancel") }}',
                                ConfirmbtnClass: 'text-green-400',
                                confirmButton: '{{ __("Yes, Add Break") }}',
                                callEvent:function(){
                                    _this.$wire.create_break(date, start_time, end_time);
                                },
                                cancelEvent:function(){
                                    return false;
                                }
                            });
                        },
        
                        remove_break(breaks){
                            
                            var _this = this;
                            $.confirm("{{ __('Do you want to remove this time break?') }}", {
                                title: "{{ __('Remove Break') }}",
                                cancelButton: '{{ __("Cancel") }}',
                                ConfirmbtnClass: 'text-red-400',
                                confirmButton: '{{ __("Yes, Remove") }}',
                                callEvent:function(){
                                    _this.$wire.remove_break(breaks);
                                },
                                cancelEvent:function(){
                                    return false;
                                }
                            });
                        }
                }
            });
        </script>
    @endscript
</div>