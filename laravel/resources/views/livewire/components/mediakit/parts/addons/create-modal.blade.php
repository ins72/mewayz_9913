
<?php

    use App\Models\Addon;
    use function Livewire\Volt\{state, mount};

    state(['generate_addons']);

    $createAddon = function($_addon){
        $slug = \Str::random(4);
        
        $addon = new Addon;
        $addon->fill($_addon);
        $addon->site_id = __s()->id;
        $addon->slug = $slug;
        $addon->save();
        
        $this->js('$store.builder.savingState = 2');
    };
?>
<div class="w-[100%]" >
    <div x-data="builder__new_block" wire:ignore>
        <div class="pl-[45px] pr-[45px] py-[48px]">
            <div class=" pl-[15px] pr-[15px] py-0">
                <div class="text-[#000000] text-2xl font-medium pb-[8px]"><span>{{__('Available Addon\'s')}}</span></div>
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[color:#4F4F4F] text-[13px] font-normal">{{__('Get a better understanding of your audience.')}}</span>
                    </div>
                    <div>

                        <div class="yena-form-group">
                            <div class="--left-element">
                                {!! __i('interface-essential', 'search.1', 'w-5 h-5') !!}
                            </div>
            
                            <input type="text" placeholder="{{ __('Find or create a new folder') }}">
                            <div class="--right-element !hidden" wire:loading.class.remove="!hidden" wire:target="query">
                                <div class="yena-spinner !w-4 !h-4 !border-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-5 right-5 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
                <i class="fi fi-rr-cross-small"></i>
            </a>
        
            <div class="mt-10">

                <div class="pl-[15px] pr-[15px] py-0 h-[492px] overflow-auto flex flex-col gap-4">
                    <div class="grid gap-y-[12px] gap-x-[12px] grid-cols-2">
                        <template x-for="(item, key) in generate_addons" :key="key">
                            <div>
                                <div class="h-[72px] flex px-[15px] py-[10px] relative [transition:box-shadow_0.25s_ease-out] items-center rounded-[10px] justify-between bg-[#F7F7F7] cursor-pointer hover:bg-[rgb(255,_255,_255)] hover:[box-shadow:rgba(0,_0,_0,_0.2)_0px_8px_20px]" @click="createAddon(key, item)">
                                    <div class="flex items-center">
                                        <div class="w-[42px] h-[42px] [box-shadow:inset_0px_-1px_1px_rgba(0,_0,_0,_0.03)] rounded-[8px] flex items-center justify-center p-2" :style="{
                                            'background': item.icon.color,
                                            'color': $store.builder.getContrastColor(item.icon.color),
                                          }" x-html="item.icon.svg"></div>
    
                                        <div class="flex flex-col pl-[12px]">
                                            <span class="text-[13px] font-semibold" x-text="item.name"></span>
                                            <p class="text-[10px] truncate" x-text="item.description"></p>
                                        </div>
                                    </div>
    
                                    <div class="w-[52px] h-[32px] flex items-center rounded-[100px] justify-center bg-[#FFFFFF] [transition:background-color_0.2s_ease-out]">
                                        <i class="fi fi-rr-plus"></i>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    
    
    </div>
    
    @script
    <script>
       Alpine.data('builder__new_block', () => {
          return {
             generate_addons: @entangle('generate_addons'),
 
             init(){
                
             }
          }
       });
    </script>
    @endscript
 </div>