<x-layouts.app>
  <x-slot:title>{{ __('Settings') }}</x-slot>



<script>
  function pine(){
    return {

      clicked_menu(){
        var _this = this;
        //_this.$root.querySelector('.sandy-tabs-link').classList.remove('active');
        //_this.$event.currentTarget.classList.add('bg-zinc-200/70');
      },

      init(){
        var _this = this;

        
        _this.$root.querySelectorAll('.sandy-tabs-link').forEach(element => {

          element.addEventListener('click', function(e){
            _this.$root.querySelectorAll('.sandy-tabs-link').forEach(box => {
              box.classList.remove("bg-zinc-200/70");
            });
            
            
            e.currentTarget.classList.add('bg-zinc-200/70');
          });
        });

      }
    }
  }
</script>

<script>
  function __image(image = ''){
    return {
      imageUrl: '',

      selectFile (event) {
          const file = event.target.files[0]
          const reader = new FileReader()
          if (event.target.files.length < 1) {
              return
          }
          reader.readAsDataURL(file)
          reader.onload = () => (this.imageUrl = reader.result)
      },

      init(){
        this.imageUrl = image;
      }
    }
  }
</script>
<style>
  @media(min-width: 992px){
    .min-sid{
      width: 15em !important;
    }
  }

  .form-input{
    background: transparent !important;
  }
</style>

@php
  $payments = new \App\Yena\Payments;
  $payments = $payments->getInstalledMethods();
@endphp

<form method="post" action="{{ route('dashboard-admin-settings-post', ['tree' => 'post']) }}" class="h-full pb-16 pt-8 sm:pt-10" enctype="multipart/form-data" x-data="pine">
  @csrf

  <div class="mb-6 ">
    <div class="flex flex-col mb-4">
      <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center">
        <span class="whitespace-nowrap">{{ __('Admin') }}</span>
      </div>
      <div class="border-b border-solid border-gray-300 w-[100%] my-4 flex"></div>
       
       <div class="flex items-center h-6">
          <h2 class="text-lg font-semibold ">
              {!! __icon('--ie', 'settings.10', 'w-6 h-6 inline-block') !!}
              <span class="ml-2">{{ __('Settings') }}</span>
          </h2>
       </div>
       <div class="flex flex-col gap-4 mt-4 lg:flex-row">
          <button class="cursor-pointer yena-button-stack">
            {{ __('Save Settings') }}
          </button>
       </div>
    </div>
 </div>
  
  <div class="mx-auto w-full max-w-screen-xl px-0 md:px-0 pb-10 pt-8 flex flex-col lg:flex-row lg:space-x-6 mb-10">
    <div class="w-full">
      <div>

        <div class="flex flex-col gap-4 mt-6 lg:mt-0 px-0">
          <div class="sandy-tabs-item" id="general-tab">
            @includeIf('admin.settings.sections.app')
          </div>
          <div class="sandy-tabs-item" id="payments-tab">
            @includeIf('admin.settings.sections.payments')
          </div>
          {{-- <div class="sandy-tabs-item" id="invoice-tab">
            @includeIf('admin.settings.sections.invoice')
          </div> --}}
          <div class="sandy-tabs-item" id="signin-tab">
            @includeIf('admin.settings.sections.social_login')
          </div>
          <div class="sandy-tabs-item !hidden" id="captcha-tab">
            @includeIf('admin.settings.sections.captcha')
          </div>
          <div class="sandy-tabs-item" id="smtp-tab">
            @includeIf('admin.settings.sections.smtp')
          </div>
        </div>
      </div>
    </div>
  </div>

</form>
</x-layouts.app>
