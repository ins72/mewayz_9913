
<?php

    use App\Models\Addon;
    use function Livewire\Volt\{state, mount};


    state(['site']);
    // state(['generate_addons']);

    state([
        'bladeTemplate' => null,
    ]);

    $createAddon = function($_addon){
        $slug = \Str::random(4);
        
        $addon = new Addon;
        $addon->fill($_addon);
        $addon->site_id = $this->site->id;
        $addon->slug = $slug;
        $addon->save();
        
        $this->js('$store.builder.savingState = 2');
    };

    $generateBlade = function($addon){

        $addon_key = __a($addon, 'addon');

        $_name = str_replace('/', '.', __a($addon, 'config.components.databaseView'));
        $component = "livewire::addons.$addon_key.$_name";

        $this->bladeTemplate = \Blade::render("<x-livewire::addons.$addon_key.$_name/>");
        
        

        return ;
    };
?>
<div class="w-[100%]" >

    <div x-data="builder__addon_database">
        {!! $bladeTemplate !!}
    </div>
    
    @script
    <script>
       Alpine.data('builder__addon_database', () => {
          return {
            
            _blade_template(addon){


                this.$wire.generateBlade(addon);
            },
            
            init(){
                var $this = this;

                window.addEventListener("openDatabase", (event) => {
                    $this._blade_template(event.detail);
                });
                
            }
          }
       });
    </script>
    @endscript
 </div>