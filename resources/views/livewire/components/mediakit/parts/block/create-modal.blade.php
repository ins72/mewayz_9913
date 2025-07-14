
<?php

    use App\Models\Section;
    use App\Models\SectionItem;
    use function Livewire\Volt\{state, mount};


    state(['site']);
    $createSection = function($section){
        $this->skipRender();

        $_section = new Section;
        $_section->fill($section);
        $_section->site_id = $this->site->id;
        $_section->page_id = $this->site->current_edit_page;
        $_section->published = 1;
        $_section->uuid = __a($section, 'uuid');
        $_section->save();

        if(is_array($items = __a($section, 'items'))){
           foreach ($items as $key => $value) {
                $_item = new SectionItem;
                $_item->fill($value);
                $_item->section_id = $_section->uuid;
                $_item->uuid = __a($value, 'uuid');
                $_item->save();
           }
        }

        $this->js('$store.builder.savingState = 2');
    };

    $generateSvg = function($svg){
        $svg = explode('---', $svg);
        $_svg = null;

        if(!empty($svg[0]) && !empty($svg[1])){
            $_svg = __i($svg[0], $svg[1]);
        }
        return $_svg;
    };
?>
<div class="w-[100%]" >
    <div x-data="builder__new_block" wire:ignore>
        <div class="pl-[45px] pr-[45px] py-[48px]">
            <div class=" pl-[15px] pr-[15px] py-0">
                <div class="text-[#000000] text-2xl font-medium pb-[8px]"><span>{{__('Add content')}}</span></div>
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-[color:#4F4F4F] text-[13px] font-normal">{{__('Select from our wide variety of links and contact info below.')}}</span>
                    </div>
                    <div>

                        <div class="yena-form-group">
                            <div class="--left-element">
                                {!! __i('interface-essential', 'search.1', 'w-5 h-5') !!}
                            </div>
            
                            <input type="text" placeholder="{{ __('Search...') }}">
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
        
            <div>

                <div class="pl-[15px] pr-[15px] py-0 h-[492px] overflow-auto flex flex-col gap-4">
                    <template x-for="(value, key) in blocks" :key="key">
                        <div>
                            <div class="pb-[12px]">
                                <span class="text-[#4F4F4F] text-[13px] font-medium" x-text="value.name"></span>
                            </div>

                            <div class="grid gap-y-[12px] gap-x-[12px] grid-cols-3">
                                <template x-for="(item, index) in value.items" :key="index">
                                    <div class="h-[72px] flex px-[15px] py-[10px] relative [transition:box-shadow_0.25s_ease-out] items-center rounded-[10px] justify-between bg-[#F7F7F7] cursor-pointer hover:bg-[rgb(255,_255,_255)] hover:[box-shadow:rgba(0,_0,_0,_0.2)_0px_8px_20px]" @click="createSection(index, item)" :class="{
                                        'opacity-40': !item.function
                                    }">
                                        <div class="flex items-center">
                                            <div class="w-[42px] h-[42px] [flex:0_0_42px] [box-shadow:inset_0px_-1px_1px_rgba(0,_0,_0,_0.03)] rounded-[8px] flex items-center justify-center" :style="{
                                                'background': item.color,
                                                'color': $store.builder.getContrastColor(item.color),
                                              }" x-html="item['ori-icon-svg']"></div>

                                            <span class="text-[13px] font-semibold pl-[12px]" x-text="item.name"></span>
                                        </div>

                                        <div class="w-[52px] h-[32px] flex items-center rounded-[100px] justify-center bg-[#FFFFFF] [transition:background-color_0.2s_ease-out]">
                                            <i class="fi fi-rr-plus"></i>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    
    
    </div>
    
    @script
    <script>
       Alpine.data('builder__new_block', () => {
          return {
             page_name: '',
             blocks: {!! collect(generate_section())->toJson() !!},
 
             createSection(section, $item){
                let $this = this;
                let $items = [];
                let count = this.sections.length + 1;

                if($item.function.create.items.length > 0){
                    $item.function.create.items.forEach((_i, index) => {
                        let $newItem = {
                            uuid:  $this.$store.builder.generateUUID(),
                            ..._i
                        };

                        $items.push($newItem);
                    });

                    $item.function.create.items = $items;
                }

                let $new = {
                    uuid: $this.$store.builder.generateUUID(),
                    section: section,
                    page_id: $this.currentPage().uuid,
                    published: 1,
                    position: count,
                    ...$item.function.create,
                };
                $this.sections.push($new);
                $this.$wire.createSection($new);
                $this.$dispatch('close');
                
                // let $_item = {
                //     uuid: $this.$store.builder.generateUUID(),
                //     section: "image",
                //     settings: {
                //         create: true,
                //     },
                //     content: {
                //         title: "Image"
                //     },
                // };


                // console.log($this.sections)
                // console.log($item, $new)

                // this.$store.builder.executeFunctionByName($item.function, 'create', [this]);
             },
 
             init(){
                
             }
          }
       });
    </script>
    @endscript
 </div>