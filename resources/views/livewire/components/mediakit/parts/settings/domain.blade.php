
<?php
    use App\Models\SiteDomain;
    use App\Models\BioSiteDomain;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, mount, placeholder, uses};

    uses([
        ToastUp::class,
    ]);
    state([
      'site'
    ]);
    state([
        'domain' => null,
        'hasDomain' => false,
    ]);

    state([
        'protocol' => 'https',
        'host' => '',
    ]);

    mount(function() {
        $this->getDomain();
    });

    $getDomain = function(){
        $this->domain = BioSiteDomain::where('site_id', $this->site->id)->first();
        $this->hasDomain = $this->domain ? true : false;
        // $this->skipRender();
    };
    
    $getDomainStatus = function(){
        // if(!$domain = Domain::where('user', $this->page->id)->where('id', $domain_id)->first()) return false;
        if(!$domain = $this->domain) return false;

        $is_connected = $domain->is_connected;
        //
        $records = dns_get_record($domain->host, DNS_CNAME);

        foreach (dns_get_record($domain->host, DNS_CNAME) as $item) {
            if(ao($item, 'target') == config('app.BIO_DOMAIN_CNAME')){
                $is_connected = true;
            }
        }

        foreach (dns_get_record($domain->host, DNS_A) as $item) {
            if(ao($item, 'ip') == config('app.BIO_DOMAIN_IP')){
                $is_connected = true;
            }
        }

        $domain->is_connected = $is_connected;
        $domain->save();

        $this->domain = $domain;
        return $this->domain;
    };

    $deleteDomain = function(){
        if(!$this->domain){
            return [
                'status' => 'error',
                'response' => __('Domain doesnt exists')
            ];
        }
        
        $this->domain->delete();
        $this->getDomain();

        $this->hasDomain = false;
        $this->skipRender();
    };

    $createDomain = function($host = null, $protocol = null){
        // if(!__o_feature('feature.custom_domain')) abort(404);
        $this->skipRender();

        if(empty($host) || empty($protocol)){
            return [
                'status' => 'error',
                'response' => __('Host & Protocol is required')
            ];
        }

        if(!__o_feature('feature.custom_domain', $this->site->user)){
            return [
                'status' => 'error',
                'response' => __('Please upgrade to connect domain.')
            ];
        }

        // Strip www. host
        $host = str_replace('www.', '', $host);

        // Make url valid
        $valid_url = "$protocol://$host";
        $valid_url = strtolower($valid_url);

        // Validate url
        if (!validate_url($valid_url)) {
            return [
                'status' => 'error',
                'response' => __('Domain is not valid')
            ];
        }

        // Check if url is same as app url
        if(parse($valid_url, 'host') == parse(url('/'), 'host')){
            return [
                'status' => 'error',
                'response' => __('Domain cannot be app url')
            ];
        }


        // Check if domain already exisits incase we want to change this to multi-domain support
        $host = parse($valid_url, 'host');

        if (SiteDomain::where('host', $host)->exists() || BioSiteDomain::where('host', $host)->exists()) {
            return [
                'status' => 'error',
                'response' => __('Domain already exists')
            ];
        }

        
        $create = new BioSiteDomain;
        $create->site_id = $this->site->id;
        $create->is_active = 1;
        $create->scheme = $protocol;
        $create->host = parse($valid_url, 'host');
        $create->save();

        $this->getDomain();
        $this->getDomainStatus();

        return [
            'status' => 'success',
            'response' => $this->domain
        ];
    };
?>
<div class="website-section">
    <div class="design-navbar">
        <ul >
            <li class="close-header !flex">
              <a @click="__page='-'">
                <span>
                    {!! __i('Arrows, Diagrams', 'Arrow.8', '!w-6 !h-6') !!}
                </span>
              </a>
           </li>
           <li class="!pl-0">{{ __('Domain') }}</li>
           <li></li>
        </ul>
     </div>
     <div class="container-small p-[var(--s-2)] pb-[150px]">

        <template x-if="!__o_feature('feature.custom_domain')">
            <div class="flex flex-col justify-center items-center px-[20px] py-[60px]">
                {!! __i('--ie', 'earth-globe-more-setting', 'w-14 h-14') !!}
                <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                    {!! __t('Upgrade your site plan to use <br> your domain name instead of <br>', ['site' => str_replace(['http://', 'https://'], '', $this->site->getAddress())]) !!} <span x-text="$store.builder.generateSiteLink(site).replace(/http|https|:\/\//g, '')"></span>
                </p>
                <button type="button" @click="$dispatch('open-modal', 'upgrade-modal')" class="btn btn-large mt-3 !h-[40px] !border-none !transition-none">{{ __('Upgrade') }}</button>
            </div>
        </template>
      <div wire:ignore x-show="__o_feature('feature.custom_domain')">
         <div x-data="builder__settings_domain">
            
            <template x-if="!hasDomain">
               <form @submit.prevent="create_domain" class="">
                 <div class="flex flex-col gap-3">
                     {{-- <div class="text-xl font-extrabold tracking-[-1px]">{{ __('Domain') }}</div> --}}
                    <div class="flex flex-col justify-center items-center px-[20px] pt-[60px]">
                        {!! __i('--ie', 'earth-globe-more-setting', 'w-14 h-14') !!}
                        <p class="mt-3 text-[var(--c-mix-3)] text-center text-[var(--t-m)]">
                            {!! __t('Elevate your site & drive more clicks by connecting your personalized domain.') !!}
                        </p>
                    </div>
                     {{-- <div class="text-xs mt-0">
                         {{ __('Elevate your page with a personalized domain by upgrading to Link in Bio Pro. Drive more clicks to your page while owning your online presence.') }}
                     </div> --}}
                 </div>
  
                 <div class="custom-content-input border-2 border-dashed mb-1">
                    <label class="h-10 !flex items-center px-5">
                       <select name="protocol" x-model="protocol" class="text-sm border-0">
                          <option value="https">{{ __('https://') }}</option>
                          <option value="http">{{ __('http://') }}</option>
                       </select>
                    </label>
                    <input type="text" x-model="host" placeholder="domain" class="w-[100%] !bg-gray-100">
                 </div>

                   <template x-if="backendError">
                      <div class="bg-red-200 text-[11px] p-1 px-2 mb-1 rounded-full">
                       <div class="flex items-center">
                          <div>
                             <i class="fi fi-rr-cross-circle flex text-xs"></i>
                          </div>
                          <div class="flex-grow ml-1 text-xs !text-black" x-text="backendError"></div>
                       </div>
                      </div>
                   </template>
                                      
  
                 <button class="yena-button-stack w-[100%]">
                   <span :class="{
                       'hidden': !buttonLoader,
                       'flex': buttonLoader
                   }">
                       <span class="loader-o20 !text-[9px] mx-auto !text-black"></span>
                   </span>
                   <span x-show="!buttonLoader">{{ __('Save') }}</span>
                 </button>
              </form>
           </template>

           <template x-if="hasDomain && domain">
               <ul class="grid grid-cols-1 gap-3">
                   <li>
                      <div class="flex flex-col space-y-3 rounded-lg border border-gray-200 bg-white p-5">
                         <div class="flex h-11 w-[100%] items-center justify-start space-x-1.5 rounded-t-lg border bg-white px-3">
                            <span class="h-3 w-3 flex-none rounded-full bg-red-400"></span>
                            <span class="h-3 w-3 flex-none rounded-full bg-yellow-400 ml-1"></span>
                            <span class="h-3 w-3 flex-none rounded-full bg-green-400 ml-1"></span>


                            <div class="flex flex-grow items-center pl-2">
                               <a :href="domain.scheme+'://'+domain.host" target="_blank" rel="noreferrer" class="flex h-7 w-[100%] items-center gap-2 rounded-md bg-gray-100 px-2 text-sm">

                                   <template x-if="domain.scheme === 'http'">
                                       <i class="fi fi-rr-unlock text-xs text-red-600"></i>
                                   </template>
                                   <template x-if="domain.scheme === 'https'">
                                       <i class="fi fi-rr-lock text-xs text-green-600"></i>
                                   </template>
                                  
                                  <span class="flex items-center" x-text="domain.host"></span>

                                   {!! __i('interface-essential', 'share-arrow-sqaure', 'h-3 w-3') !!}
                                  
                                   <template x-if="!domain.is_connected">
                                       <span class="loader-o20 !text-[9px] !text-black ml-auto"></span>
                                   </template>
                               </a>
                            </div>
                         </div>
                         <div class="h-14 w-[100%] border-t-0 bg-gradient-to-b from-gray-100 to-white"></div>
                         <div class="flex p-0">
                            <div>
                               <template x-if="domain.is_connected">
                                   <span class="rounded-full bg-green-500 px-3 py-0.5 text-xs text-white">{{ __('Connected') }}</span>
                               </template>
                               <template x-if="!domain.is_connected">
                                   <span class="rounded-full bg-red-500 px-3 py-0.5 text-xs text-white">{{ __('Not Connected') }}</span>
                               </template>
                            </div>
                            <div class="ml-auto">
                               <div wire:ignore="">
                                   <a class="pointer-events-auto cursor-pointer w-6 h-6 flex justify-center items-center bg-red-400 rounded-lg" @click="delete_domain">
                                       {!! __i('interface-essential', 'trash-delete-remove', 'text-white w-3 h-3') !!}
                                   </a>
                               </div>
                            </div>
                         </div>
                         <template x-if="!domain.is_connected">
                           <div class="border-t border-gray-200 pt-5 mt-5" x-data="{cname: false}">
                              <div class="flex justify-start">
                                 <button class="ease border-b-2 pb-1 text-sm transition-all duration-150 outline-none border-white text-gray-400" :class="{'border-black text-black': !cname, 'border-white text-gray-400': cname}" @click="cname = false">A Record</button>
                                 <button class="ease border-b-2 pb-1 text-sm transition-all duration-150 ml-4 outline-none border-black text-black" :class="{'border-black text-black': cname, 'border-white text-gray-400': !cname}" @click="cname = true">CNAME Record</button>
                              </div>


                              <div class="my-3 text-left" x-show="!cname">
                               <p class="my-5 text-sm">{{ __('To configure your domain') }} (<span class="inline-block rounded-md bg-blue-100 px-1 py-0.5 font-mono text-blue-900" x-text="domain.host"></span>), {{ __('set the following A record on your DNS provider to continue:') }}</p>
                               <div class="block lg:flex items-center justify-start rounded-md bg-gray-50 p-2">
                                   <div>
                                       <p class="text-sm font-bold">{{ __('Type') }}</p>
                                       <p class="mt-2 font-mono text-sm">A</p>
                                   </div>
                                   <div class="lg:ml-10">
                                       <p class="text-sm font-bold">{{ __('Name') }}</p>
                                       <p class="mt-2 font-mono text-sm">@</p>
                                   </div>
                                   <div class="lg:ml-10">
                                       <p class="text-sm font-bold">{{ __('Value') }}</p>
                                       <p class="mt-2 font-mono text-sm">{{ config('app.BIO_DOMAIN_IP') }}</p>
                                   </div>
                                   <div class="lg:ml-10">
                                       <p class="text-sm font-bold">{{ __('TTL') }}</p>
                                       <p class="mt-2 font-mono text-sm">86400</p>
                                   </div>
                               </div>
                               <p class="mt-5 text-sm">
                                 {!! __t('Note: for TTL, if <span class="inline-block rounded-md bg-blue-100 px-1 py-0.5 font-mono text-blue-900">86400</span> is not available, set the highest value possible. Also, domain propagation can take anywhere between 1 hour to 12 hours.') !!}
                               </p>
                               </div>

                               <div class="my-3 text-left" x-show="cname">
                                   <p class="my-5 text-sm">{{ __('To configure your domain') }} (<span class="inline-block rounded-md bg-blue-100 px-1 py-0.5 font-mono text-blue-900" x-text="domain.host"></span>), {{ __('set the following CNAME record on your DNS provider to continue:') }}</p>
                                   <div class="block lg:flex items-center justify-start rounded-md bg-gray-50 p-2">
                                       <div>
                                           <p class="text-sm font-bold">{{ __('Type') }}</p>
                                           <p class="mt-2 font-mono text-sm">CNAME</p>
                                       </div>
                                       <div class="lg:ml-10">
                                           <p class="text-sm font-bold">{{ __('Name') }}</p>
                                           <p class="mt-2 font-mono text-sm">www</p>
                                       </div>
                                       <div class="lg:ml-10">
                                           <p class="text-sm font-bold">{{ __('Value') }}</p>
                                           <p class="mt-2 font-mono text-sm">{{ config('app.BIO_DOMAIN_CNAME') }}</p>
                                       </div>
                                       <div class="lg:ml-10">
                                           <p class="text-sm font-bold">{{ __('TTL') }}</p>
                                           <p class="mt-2 font-mono text-sm">86400</p>
                                       </div>
                                   </div>
                                   <p class="mt-5 text-sm">
                                     {!! __t('Note: for TTL, if <span class="inline-block rounded-md bg-blue-100 px-1 py-0.5 font-mono text-blue-900">86400</span> is not available, set the highest value possible. Also, domain propagation can take anywhere between 1 hour to 12 hours.') !!}
                                   </p>
                               </div>
                           </div>
                         </template>
                         
                         <div class="flex justify-end gap-2 items-center">
                            <a class="yena-button-stack w-[100%]" @click="refresh_domain">
                              <span :class="{
                                  'hidden': !buttonLoader,
                                  'flex': buttonLoader
                              }">
                                  <span class="loader-o20 !text-[9px] mx-auto !text-black"></span>
                              </span>
                              <span x-show="!buttonLoader">{{ __('Refresh') }}</span>
                           </a>
                         </div>
                      </div>
                   </li>
                </ul>
           </template>
           
         </div>
         
      </div>
     </div>
     @script
     <script>
      Alpine.data('builder__settings_domain', () => {
         return {
            autoSaveTimer: null,
            domainStatus: null,
            domain: {!! collect($domain)->toJson() !!},
            hasDomain: @entangle('hasDomain'),


            // Utils
            buttonLoader: false,
            backendError: null,
            // buttonError: false,


            // Model
            host: '',
            protocol: 'https',

            // Methods
            create_domain(){
                var $this = this;
                $this.buttonLoader = true;
                $this.backendError = null;

                // Do livewire stuff
                $this.$wire.createDomain($this.host, $this.protocol).then(r => {
                    $this.buttonLoader = false;

                    // If error exists
                    if(r.status == 'error'){
                        $this.backendError = r.response;
                        return;
                    }

                    $this.domain = r.response;
                    // console.log(r)
                });
            },

            delete_domain(){
                var $this = this;
                // $this.buttonLoader = true;

                // Do livewire stuff
                $this.$wire.deleteDomain().then(r => {
                    $this.domain = null;
                });
            },

            refresh_domain(){
                var $this = this;
                $this.buttonLoader = true;

                // Do livewire stuff
                $this.$wire.getDomainStatus().then(r => {
                    $this.buttonLoader = false;


                    $this.domain = r;
                });
            },

            init(){
               var $this = this;
               
               
            }
         }
      });
     </script>
     @endscript
</div>