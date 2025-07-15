
<?php

    use App\Models\Addon;
    use App\Models\AddonsDb;
    use function Livewire\Volt\{state, mount};

    state(['generate_addons']);

    // state([
    //     'bladeTemplate' => null,
    // ]);

    
    $getDatabase = function($addon){
        $database = AddonsDb::where('addon', __a($addon, 'uuid'))->where('site_id', __s()->id)->get()->toArray();

        return $database;
    };
?>
<div class="w-[100%]" >
    <div x-data="builder__addon_database">

        <div x-show="__page.startsWith('databaseAddon::')">
            <div class="website-section !block" wire:ignore>
                <div>
                    <div class="--navbar">
                        <ul >
                            <li class="close-header !flex">
                                <a @click="reverseAddonPage()">
                                    <span>
                                        {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                                    </span>
                                </a>
                            </li>
                            <li class="!pl-0">{{ __('Database') }}</li>
                            <li class="!flex items-center !justify-center">
                                <button class="btn btn-save !w-[24px] !bg-[var(--c-red)] !text-[var(--c-light)] !rounded-[var(--r-full)] p-0 !flex" type="button" @click="deleteAddon(itemAddon)">{!! __i('interface-essential', 'trash-bin-delete', 'w-4 h-4') !!}</button>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mt-4">
                        <div class="container-small tab-content-box pb-10">
                            @foreach($generate_addons as $key => $item)
                               @php
                                  if(!$_name = __a($item, 'components.databaseView')) continue;
                    
                                  $_name = str_replace('/', '.', $_name);
                                  $component = "livewire::addons.$key.$_name";
                               @endphp
                               <template x-if="__page=='databaseAddon::{{ $key }}'">
                                  <div wire:ignore>
                                     <x-dynamic-component :component="$component"/>
                                  </div>
                               </template>
                            @endforeach
                        </div>
        
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    @script
    <script>
       Alpine.data('builder__addon_database', () => {
          return {
            database: [],
            addon: null,
            
            init(){
                let $this = this;

                window.addEventListener("openDatabase", (event) => {
                    $this.database = [];
                    let $detail = event.detail;

                    $this.addon = $detail;
                    $this.__page=`databaseAddon::${$detail.addon}`;

                    $this.$nextTick(() => {
                        $this.$wire.getDatabase($detail).then(r => {
                            $this.database = r;
                        }); 
                    });
                });


                window.addEventListener("addon::databaseAdded", (event) => {
                    let $detail = event.detail[0];

                    if($detail.addon.uuid == $this.addon.uuid){
                        $this.database.push($detail.database);
                    }
                });
                
            }
          }
       });
    </script>
    @endscript
 </div>