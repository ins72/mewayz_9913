<?php
    use App\Yena\BookingTime;
    use App\Livewire\Actions\ToastUp;

    use function Livewire\Volt\{state, mount, placeholder, rules, uses};
    placeholder('
        <div class="p-0 w-full mt-0">
            <div class="--placeholder-skeleton w-full h-[30px] rounded-sm"></div>
            <div class="--placeholder-skeleton w-full h-[30px] rounded-sm mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[30px] rounded-sm mt-1"></div>
        </div>
    ');

    uses([ToastUp::class]);
    state([
        'user' => fn () => iam(),

        'weekdays' => [],
    ]);

    rules(fn() => [
        'user.booking_hour_type' => '',
        'user.booking_title' => '',
        'user.booking_description' => '',
        'user.booking_time_interval' => '',
        // 'user.booking_workhours' => '',
        'weekdays.*.enable' => '',
        'weekdays.*.start_time' => '',
        'weekdays.*.end_time' => '',
    ]);

    mount(function(){
        $this->initWeekdays();
    });

    $initWeekdays = function(){
        $timeClass = new BookingTime($this->user->id);
        $times = $timeClass->get_days_array();

        foreach ($times as $key => $value) {
            try {
                $start_time = date('H:i', mktime(0, ao($this->user->booking_workhours, "$key.from")));
                $end_time = date('H:i', mktime(0, ao($this->user->booking_workhours, "$key.to")));
            } catch (\Throwable $th) {
                $start_time = \Carbon\Carbon::now()->format('H:i');
                $end_time = \Carbon\Carbon::now()->format('H:i');
            }

            $this->weekdays[$key] = [
                'enable' => ao($this->user->booking_workhours, "$key.enable"),
                'day_name' => $value,
                'start_time' => $start_time,
                'end_time' => $end_time,
                // 'processedTime' => $timeClass->minutes_to_hours_and_minutes()
            ];
        }

        // dd($this->weekdays);
    };

    $saveSettings = function(){

        $hours = $this->user->booking_workhours;
        foreach ($this->weekdays as $key => $value) {
            $value['from'] = hour2min(\Carbon\Carbon::parse($value['start_time'])->format('H:i A'));
            $value['to'] = hour2min(\Carbon\Carbon::parse($value['end_time'])->format('H:i A'));
            $hours[$key] = $value;
        }

        // dd($hours);

        $this->user->booking_workhours = $hours;
        $this->user->save();
        $this->initWeekdays();

        $this->dispatch('refreshCalendar');
        $this->flashToast('success', __('Saved'));
    };
?>
<div>


    <div x-data="console__booking_settings">
        <div>
            {{-- <livewire:components.console.booking.services :key="uukey('app', 'booking-page-services')"> --}}



            <form wire:submit="saveSettings">

                <div class="flex flex-col gap-4 {{-- max-h-[calc(100vh_-_185px)] overflow-y-auto --}}">
                                
                    <div class="form-input">
                        <input type="text" placeholder="{{ __('Your Title: ex, Fashion designer, barber, etc') }}" wire:model="user.booking_title">
                    </div>
                    <div class="form-input !bg-transparent">
                        <div
                           x-ref="quillEditor"
                           x-init="
                              quill = new window.Quill($refs.quillEditor, {theme: 'snow'});
                              quill.on('text-change', function () {
                                 description = quill.root.innerHTML;
                              });
      
                              quill.root.innerHTML = description;
                           "
                        >
                        
                        </div>
                        {{-- <textarea rows="3" name="settings[description]" placeholder="{{ __('About') }}" wire:model="user.booking_description"></textarea> --}}
                        {{-- <p class="font-9 italic mt-3">{{ __('Give a descriptive info about your booking service.') }}</p> --}}
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="font-bold">{{ __('Time format') }}</p>
                        <div class="p-[4px] flex items-center">
                            <span class="relative top-[1px]">
                                <a class="cursor-pointer">
                                    {!! __i('--ie', 'alarm-clock-time', 'w-5 h-5 text-black') !!}
                                </a>
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:!grid-cols-2 gap-3">
                        <button class="yena-button-o !h-auto !min-h-[38px] !items-center !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ $user->booking_hour_type == '12' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('user.booking_hour_type', '12')">
                            <div>
                                <div class="bg-[#f7f3f2] w-8 h-8 rounded-lg flex items-center justify-center">
                                    {!! __i('--ie', 'alarm-clock-time', 'w-5 h-5 text-black') !!}
                                </div>
                            </div>
        
                            <div class="flex flex-col">
                                <p class="text-sm font-bold text-[var(--yena-colors-gray-800)]">{{ __('12 Hours') }}</p>
                            </div>
                        </button>
                        <button class="yena-button-o !h-auto !min-h-[38px] !items-center !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ $user->booking_hour_type == '24' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('user.booking_hour_type', '24')">
                          <div>
                             <div class="bg-[#f7f3f2] w-8 h-8 rounded-lg flex items-center justify-center">
                                {!! __i('--ie', 'alarm-clock-time.1', 'w-5 h-5 text-black') !!}
                             </div>
                          </div>
          
                          <div class="flex flex-col">
                             <p class="text-sm font-bold text-[var(--yena-colors-gray-800)]">{{ __('24 Hours') }}</p>
                          </div>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="font-bold">{{ __('Time Interval') }}</p>
                        <div class="p-[4px] flex items-center">
                            <span class="relative top-[1px]">
                                <a class="cursor-pointer">
                                    {!! __i('--ie', 'alarm-clock-time-timer-fast.2', 'w-5 h-5 text-black') !!}
                                </a>
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:!grid-cols-2 gap-3">
                        <button class="yena-button-o !h-auto !min-h-[38px] !items-center !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ $user->booking_time_interval == '15' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('user.booking_time_interval', '15')">
                            <div>
                                <div class="bg-[#f7f3f2] w-8 h-8 rounded-lg flex items-center justify-center">
                                    {!! __i('Photo Edit', 'Timer, 10 Seconds', 'w-5 h-5 text-black') !!}
                                </div>
                            </div>
        
                            <div class="flex flex-col">
                                <p class="text-sm font-bold text-[var(--yena-colors-gray-800)]">{{ __('15 Min') }}</p>
                            </div>
                        </button>
                        <button class="yena-button-o !h-auto !min-h-[38px] !items-center !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ $user->booking_time_interval == '45' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('user.booking_time_interval', '45')">
                          <div>
                             <div class="bg-[#f7f3f2] w-8 h-8 rounded-lg flex items-center justify-center">
                                {!! __i('Photo Edit', 'Timer, 10 Seconds', 'w-5 h-5 text-black') !!}
                             </div>
                          </div>
          
                          <div class="flex flex-col">
                            <p class="text-sm font-bold text-[var(--yena-colors-gray-800)]">{{ __('45 Min') }}</p>
                          </div>
                        </button>
                        <button class="yena-button-o !h-auto !min-h-[38px] !items-center !p-1 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] col-span-2 {{ $user->booking_time_interval == '75' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('user.booking_time_interval', '75')">
                          <div>
                             <div class="bg-[#f7f3f2] w-8 h-8 rounded-lg flex items-center justify-center">
                                {!! __i('Photo Edit', 'Timer, 10 Seconds', 'w-5 h-5 text-black') !!}
                             </div>
                          </div>
          
                          <div class="flex flex-col">
                            <p class="text-sm font-bold text-[var(--yena-colors-gray-800)]">{{ __('75 Min') }}</p>
                          </div>
                        </button>
                    </div>


                    <div>
                        @foreach($weekdays as $key => $weekday)
                            <div class="weekday-schedule-w !bg-transparent" x-data="{ enabled: @json($weekdays[$key]['enable']) }">
                                <div class="ws-head-w">
                                    <label class="sandy-switch is-koon flex items-center col-span-2">
                                        <input class="sandy-switch-input" name="weekday[{{ $key }}][enable]" value="1" wire:model="weekdays.{{ $key }}.enable" @input="$event.target.checked ? enabled = true : enabled = false" type="checkbox">
                                        <span class="sandy-switch-in"><span class="sandy-switch-box"></span></span>
                                    </label>
                                    <div class="ws-head">
                                        <div class="ws-day-name flex flex-col">
                                            <span class="font-bold text-sm">{{ $weekday['day_name'] }}</span>
                                            <span class="text-xs">{{ $weekday['start_time'] }} - {{ $weekday['end_time'] }}</span>
                                        </div>
                                        <div class="bg-g-gray w-8 h-8 flex items-center justify-center rounded-full ml-auto wp-edit-icon !hidden">
                                            eee
                                            {{-- {!! orion('edit-1', 'w-3 h-3') !!} --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="weekday-schedule-form" :class="{
                                    '!flex': enabled
                                }">
                                    <div class="ws-period grid grid-cols-1 gap-4 !border-0 !bg-transparent !w-full">
                                        <div class="wj-time-group wj-time-input-w as-period border border-solid koon-grey-border-color rounded-xl px-5 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !w-full">
                                            <label for="weekday[{{ $key }}][from]" class="font-normal">{{ __('Start') }}</label>
                    
                                            <div class="wj-time-input-fields">
                                                <input type="time" placeholder="HH:MM" id="weekday[{{ $key }}][from]"
                                                    name="weekday[{{ $key }}][from]" wire:model="weekdays.{{ $key }}.start_time"
                                                    class="wj-form-control wj-mask-time hourpicker !max-w-full !w-44 text-xs rounded-full koon-grey-bg">
                                            </div>
                                        </div>
                    
                                        <div class="wj-time-group wj-time-input-w as-period border border-solid koon-grey-border-color rounded-xl px-5 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !w-full">
                                            <label for="weekday[{{ $key }}][to]" class="font-normal">{{ __('Finish') }}</label>
                    
                                            <div class="wj-time-input-fields">
                                                <input type="time" placeholder="HH:MM" id="weekday[{{ $key }}][to]"
                                                    name="weekday[{{ $key }}][to]" wire:model="weekdays.{{ $key }}.end_time"
                                                    class="wj-form-control wj-mask-time hourpicker !max-w-full !w-44 text-xs rounded-full koon-grey-bg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                
                <button class="yena-button-stack !w-full mt-4" type="submit">{{ __('Save') }}</button>
            </form>


        </div>
    </div>

    @script
        <script>
            Alpine.data('console__booking_settings', () => {
                return {
                    page: '-',
                    description: @entangle('user.booking_description'),
                }
            });
        </script>
    @endscript
</div>