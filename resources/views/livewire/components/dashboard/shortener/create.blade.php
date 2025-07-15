<?php
    use App\Models\LinkShortener;

    use App\Livewire\Actions\ToastUp;

    use function Livewire\Volt\{state, mount, placeholder, rules, uses, with};

    uses([ToastUp::class]);

    state([
        'user' => fn() => iam(),
    ]);

    state([
        'link' => '',
        'slug' => null,
    ]);
    mount(function(){

        // $this->refresh();
    });

    $save = function(){
        $this->validate([
            'slug' => 'required|string|min:4|unique:link_shortener,slug',
        ]);

        $link = $this->link;
        $sh = new LinkShortener;
        $sh->user_id = $this->user->id;
        $sh->slug = $this->slug;
        $sh->settings = [
            'link' => $link
        ];
        $sh->link = $link;
        $sh->save();

        $this->flashToast('success', __('Link generated successfully'));

        $this->dispatch('close');
        $this->dispatch('updateLinks');
    };
?>
<div>
        
    <div class="w-full" x-data="console__link_shortener_create">
        <div class="flex flex-col">
        <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross text-sm"></i>
        </a>
    
        <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Link') }}</header>
    
        <hr class="yena-divider">
            <form wire:submit="save" class="px-8 pt-2 pb-6">
                <div class="form-input">
                    <label>{{ __('Link') }}</label>
                    <input type="text" x-model="link" placeholder="{{ __('type your link') }}">
                </div>
                    
                <div class="relative flex w-[100%] isolate mt-2">
                    <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 border-2 border-solid [border-image:initial] border-transparent bg-[#f7f3f2]" placeholder="{{ __('link goes here...') }}" readonly :value="slug">

                    <div class="right-0 h-[3rem] text-[1rem] flex items-center justify-center absolute top-0 z-[1]">
                    <button type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" @click="generateSlug()">{{ __('Random') }}</button>
                    </div>
                </div>
                <div class="input-box !mb-0 mt-2">
                    <label for="text-size">{{ __('Characters') }}</label>
                    <div class="input-group">
                    <input type="range" class="input-small range-slider !rounded-l-none" min="5" max="25" step="1" x-model="_characters" @input="generateSlug">
                    
                    <p class="image-size-value" x-text="_characters  + 'char'"></p>
                    </div>
                </div>
                
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
                <button class="yena-button-stack mt-5 w-full" :disabled="!isValid">{{ __('Save') }}</button>
            </form>
        </div>
    </div>
    @script
      <script>
          Alpine.data('console__link_shortener_create', () => {
            return {
                link: @entangle('link'),
                slug: @entangle('slug'),
                isValid: false,
                _characters: 10,
                getRandomString(length) {
                    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                    let result = '';
                    const charactersLength = characters.length;
                    
                    for (let i = 0; i < length; i++) {
                        result += characters.charAt(Math.floor(Math.random() * charactersLength));
                    }

                    return result;
                },
                checkValidUrl(){
                    let url = this.$store.app.addHttps(this.link);
                    this.isValid = false;
                    
                    if(this.$store.app.isValidUrl(url)){
                        this.isValid = true;
                    }
                },
                generateSlug(){

                    this.slug = this.getRandomString(this._characters);
                },

                init(){
                  let $this = this;
                  $this.$watch('link', (value) => {
                    if($this.link && !$this.slug){
                        $this.generateSlug();
                    }

                    $this.checkValidUrl();
                  });

                //   $this.$wire.getAnalytics().then(r => {
                //     $this.analytics = r;
                //     $this.analyticsLoading = false;
                //   });
                },
            }
          });
      </script>
    @endscript
</div>