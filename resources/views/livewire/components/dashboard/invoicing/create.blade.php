<?php
    use App\Models\Invoice;

    use App\Livewire\Actions\ToastUp;

    use function Livewire\Volt\{state, mount, placeholder, rules, uses, with};

    uses([ToastUp::class]);

    state([
        'user' => fn() => iam(),
    ]);

    state([
        'due' => null,
    ]);
    mount(function(){

        // $this->refresh();
    });

    $save = function(){
        $this->validate([
            'due' => 'required',
        ]);

        $sh = new Invoice;
        $sh->user_id = $this->user->id;
        $sh->slug = str()->random(10);
        $sh->data = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'invoice_no' => (Invoice::where('user_id', $this->user->id)->count() + 1)
        ];
        $sh->payer = [
            'name' => __('New Payer'),
        ];
        $sh->save();

        // $this->flashToast('success', __('Link generated successfully'));

        $this->dispatch('close');
        $this->dispatch('updateInvoice');

        redirect(route('dashboard-invoicing-edit', ['slug' => $sh->slug]));
    };
?>
<div>
        
    <div class="w-full" x-data="console__invoice_create">
        <div class="flex flex-col">
        <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
            <i class="fi fi-rr-cross text-sm"></i>
        </a>
    
        <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Invoice') }}</header>
    
        <hr class="yena-divider">
            <form wire:submit="save" class="px-8 pt-2 pb-6">
                {{-- <div class="form-input">
                    <label>{{ __('Link') }}</label>
                    <input type="text" x-model="link" placeholder="{{ __('type your link') }}">
                </div> --}}
                <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] mb-4">
                    <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Invoice due date') }}</span>
                    <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                </div>
                <div class="flex items-center gap-3 mb-3">
                    <button class="yena-button-o !bg-[var(--yena-colors-trueblue-50)] !rounded-full !text-black" :class="{
                        '!bg-black !text-white': selectedPresetDue == 30
                    }" type="button" @click="addDue(30)">
                        {{ __('in 30 days') }}
                     </button>
                     <button class="yena-button-o !bg-[var(--yena-colors-trueblue-50)] !rounded-full !text-black" :class="{
                         '!bg-black !text-white': selectedPresetDue == 45
                     }" type="button" @click="addDue(45)">
                         {{ __('in 45 days') }}
                      </button>
                      <button class="yena-button-o !bg-[var(--yena-colors-trueblue-50)] !rounded-full !text-black" :class="{
                          '!bg-black !text-white': selectedPresetDue == 60
                      }" type="button" @click="addDue(60)">
                          {{ __('in 60 days') }}
                       </button>
                       <button class="yena-button-o !bg-[var(--yena-colors-trueblue-50)] !rounded-full !text-black" :class="{
                           '!bg-black !text-white': selectedPresetDue == 90
                       }" type="button" @click="addDue(90)">
                           {{ __('in 90 days') }}
                        </button>
                </div>
                <div class="form-input">
                    <input type="date" @input="selectedPresetDue=null" x-model="due" placeholder="{{ __('Your due date') }}">
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
                <button class="yena-button-stack mt-5 w-full" :disabled="!due">{{ __('Save') }}</button>
            </form>
        </div>
    </div>
    @script
      <script>
          Alpine.data('console__invoice_create', () => {
            return {
                due: @entangle('due'),
                selectedPresetDue: null,
                addDue(days = 30){
                    let currentDate = new Date();
                    currentDate.setDate(currentDate.getDate() + days);

                    // Convert back to a string in yyyy-mm-dd format
                    let year = currentDate.getFullYear();
                    let month = ('0' + (currentDate.getMonth() + 1)).slice(-2); // Months are zero-based
                    let day = ('0' + currentDate.getDate()).slice(-2);
                    this.selectedPresetDue = days;

                    this.due = `${year}-${month}-${day}`;
                },

                init(){
                  let $this = this;
                },
            }
          });
      </script>
    @endscript
</div>