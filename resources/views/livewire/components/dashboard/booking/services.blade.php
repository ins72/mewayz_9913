<?php
    use App\Models\BookingService;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, placeholder, mount, rules, updated, uses, usesFileUploads};

    uses([ToastUp::class]);

    placeholder('
        <div class="p-0 w-full mt-0">
            <div class="--placeholder-skeleton w-full h-[30px] rounded-sm"></div>
            <div class="--placeholder-skeleton w-full h-[50px] rounded-sm mt-2"></div>
            <div class="--placeholder-skeleton w-full h-[50px] rounded-sm mt-1"></div>
            <div class="--placeholder-skeleton w-full h-[50px] rounded-sm mt-1"></div>
        </div>
    ');

    usesFileUploads();

    state([
        'user' => fn() => iam(),
        'services' => null,
        'service_name' => null,
        'service_duration' => null,
        'service_price' => null
    ]);

    state([
        'userArray' => [],
    ]);

    rules(fn() => [
        'services.*.name' => 'required',
        'services.*.duration' => 'required|numeric',
        'services.*.price' => 'required|numeric',
    ]);

    mount(function(){
        $this->refresh();
    });
    
    $save_new = function() {
        $settings = [
            'duration' => $this->service_duration
        ];
        $service = new BookingService;
        $service->user_id = $this->user->id;
        $service->name = $this->service_name ?? __('My New Booking Service');
        $service->price = $this->service_price ?? 0;
        $service->duration = $this->service_duration ?? 0;
        $service->save();

        $this->refresh();
    };

    $create = function() {
        $settings = [
            'duration' => '30'
        ];
        $service = new BookingService;
        $service->user_id = $this->user->id;
        $service->name = __('My New Booking Service');
        $service->price = 200;
        $service->duration = 60;
        $service->save();

        $this->refresh();

        $this->flashToast('success', __('Service created successfully'));
    };

    $sort = function($list) {
        $this->skipRender();
        foreach ($list as $key => $value) {
            $value['value'] = (int) $value['value'];
            $value['order'] = (int) $value['order'];
            $update = BookingService::find($value['value']);
            $update->position = $value['order'];
            $update->save();
        }
        
        $this->refresh();
    };

    $edit = function($id) {
        foreach ($this->services as $item) {
            if($item->id == $id) $item->save();
        }

        $this->dispatch('refreshCalendar');
        $this->flashToast('success', __('Service updated successfully'));
    };

    $delete = function($id) {
        if (!$delete = BookingService::where('id', $id)->where('user_id', $this->user->id)->first()) {
            return false;
        }
        if (!empty($delete->gallery) && is_array($delete->gallery)) {
            foreach ($delete->gallery as $key => $value) {
                storageDelete('media/booking/image', $value);
            }
        }
        $delete->delete();
        $this->refresh();
        
        $this->dispatch('refreshCalendar');
        $this->flashToast('success', __('Service deleted successfully'));
    };

    $refresh = function() {
        $services = BookingService::where('user_id', $this->user->id)
                    ->orderBy('position', 'ASC')
                    ->orderBy('id', 'DESC')
                    ->get();

        $this->services = $services;

        $this->userArray = $this->user->toArray();
    };
?>

<div>
    <div x-data="console__booking_services">
        <div x-cloak x-show="__page == 'create'">
            <form class="flex flex-col" @submit.prevent="$wire.save_new(); __page='-'">
                <div class="flex justify-between items-center">
                    <div class="bg-white mb-2 w-10 h-10 rounded-lg flex items-center justify-center ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] cursor-pointer" @click="__page='-'">
                        <i class="ph ph-caret-left text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold">{{ __('Create Service') }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div class="form-input w-full !bg-transparent">
                        <input type="text" wire:model="service_name" placeholder="{{ __('Service Name') }}">
                    </div>
                    <div class="form-input w-full !bg-transparent">
                        <input type="number" wire:model="service_duration" placeholder="{{ __('Duration (minutes)') }}">
                    </div>
                    <div class="form-input w-full !bg-transparent">
                        <input type="text" wire:model="service_price" placeholder="{{ __('Service Price') }}">
                    </div>
                </div>

                <button class="yena-button-stack !w-full mt-4" type="submit">{{ __('Save') }}</button>
            </form>
        </div>

        <div x-cloak x-show="__page=='-'">
            <button class="yena-button-stack !w-full mb-4" type="button" @click="__page='create'">{{ __('Create Service') }}</button>

            <div class="gap-4 flex flex-col book-services {{ $services->isEmpty() ? 'hidden' : '' }}" wire:sortable="sort">
                @foreach($services as $item)
                    <div class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !flex-col !p-3 !bg-[#ffffffa3] !text-left gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)]" x-data="{is_delete:false,share: false}" type="button" wire:sortable.item="{{ $item->id }}"
                    wire:key="services-mix-booking-{{ $item->id }}">
                      <div class="flex items-center justify-between w-[100%]">
                        <div class="flex items-center w-full">
                            <div class="flex flex-col">
                                <p class="text-sm font-bold text-[var(--yena-colors-gray-800)] truncate">{{ $item->name }}</p>
                                <p class="text-xs text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)]">{!! iam()->price($item->price) !!} + <span>{{ $item->duration }}</span>{{ __('min') }}</p>
                             </div>
                             <div class="flex items-center ml-auto gap-2">
                                 {{-- <p class="drag handle pointer-events-auto cursor-pointer w-6 h-6 flex items-center justify-center rounded-md bg-[var(--yena-colors-gray-100)]" wire:sortable.handle>
                                     <i class="ph ph-arrows-out-cardinal text-black"></i>
                                 </p> --}}
                                 <a class="pointer-events-auto cursor-pointer w-6 h-6 flex items-center justify-center rounded-md bg-[var(--yena-colors-gray-100)]"  @click="$dispatch('open-modal', 'edit-booking-modal'); $dispatch('registerService', {id: '{{ $item->id }}'})">
                                     <i class="ph ph-pencil-line text-black"></i>
                                 </a>
                                 <a class="pointer-events-auto cursor-pointer w-6 h-6 flex items-center justify-center rounded-md bg-[var(--yena-colors-gray-100)]" @click="$event.stopPropagation(); is_delete=true;">
                                     <i class="ph ph-trash text-black"></i>
                                 </a>
                             </div>
                          </div>
                      </div>
                          
                      <div class="card-button p-3 flex gap-2 bg-[var(--yena-colors-gray-100)] w-full rounded-lg !hidden" x-cloak @click="$event.stopPropagation();" :class="{
                        '!hidden': !is_delete
                       }">
                        <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
      
                        <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-full" @click="$wire.delete('{{ $item->id }}'); is_delete=false;">{{ __('Yes, Delete') }}</button>
                     </div>
                      <a class="sandy-button !bg-black py-2 flex-grow w-[100%] flex justify-center items-center !text-white rounded-lg" :class="{
                        '!hidden': is_delete
                       }" @click="$clipboard('{{ route('out-booking-service-page', ['slug' => $item->id]) }}'); $el.innerText = window.builderObject.copiedText;">
                          <div class="--sandy-button-container">
                              <span class="text-xs">{{ __('Share') }}</span>
                          </div>
                      </a>
                   </div>
                @endforeach
                  
            </div>
        </div>
        
        
        {{-- @foreach ($services as $index => $item)
        <div x-cloak x-show="__page == 'edit::{{ $item->id }}'">
            <form class="flex flex-col" wire:submit.prevent="edit({{ $item->id }})">
                <div class="flex justify-between items-center">
                    <div class="bg-white mb-2 w-10 h-10 rounded-lg flex items-center justify-center ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] cursor-pointer" @click="__page='-'">
                        <i class="ph ph-caret-left text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold">{{ __('Edit Service') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="form-input !bg-transparent">
                        <label>{{ __('Service Name') }}</label>
                        <input type="text" wire:model="services.{{ $index }}.name">
                    </div>
                    <div class="form-input !bg-transparent">
                        <label>{{ __('Duration (minutes)') }}</label>
                        <input type="text" wire:model="services.{{ $index }}.duration">
                    </div>
                    <div class="form-input !bg-transparent">
                        <label>{{ __('Service Price') }}</label>
                        <input type="text" wire:model="services.{{ $index }}.price">
                    </div>
                </div>

                <button class="yena-button-stack !w-full mt-4" type="submit">{{ __('Save') }}</button>
            </form>
        </div>
        @endforeach --}}

        
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
            <div class="mb-1 bg-red-200 text-[11px] p-1 px-2 rounded-md">
            <div class="flex items-center">
                <div>
                    <i class="fi fi-rr-cross-circle flex text-xs"></i>
                </div>
                <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
            </div>
            </div>
        @endif
    </div>

    @script
        <script>
            Alpine.data('console__booking_services', () => {
                return {
                    __page: '-',
                    user: @entangle('userArray'),

                    init(){
                        // console.log(this.user)
                    }
                }
            });
        </script>
    @endscript
</div>