<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
        @php
            $appName = config('app.name', 'Laravel');
        @endphp
        <title>{{ isset($title) ? "$appName - $title" : $appName }}</title>

        <!-- Favicon -->
        <link href="{{ favicon() }}" rel="shortcut icon" type="image/png" />

        <!-- Scripts -->
        @vite([
            'resources/sass/create.scss',
            'resources/css/app.css',
            'resources/sass/app.scss',
            'resources/sass/dashboard/dashboard.scss',
            'resources/sass/dashboard/dashboard.sidebar.scss',
            'resources/sass/console/console.placeholder.scss',
            'resources/js/app.js',
            'resources/js/moreUtils.js',
        ])
        @vite([
            'resources/sass/dashboard/community.scss',
        ])

        {!! isset($meta) ? $meta : '' !!}

        <script type="text/javascript" src="{{ gs('assets/js/navigate.turbo.js') }}" defer></script>

        @livewireStyles
    </head>
    <body class="font-sans text-gray-900 antialiased console-body bg-gray-50 dark:bg-gray-900 dark:text-gray-100" x-data="appData">
        
        <div id="app">

            @persist('app-utils')
               <div wire:ignore>
                    <livewire:components.console.utils.logout>
                    <livewire:components.console.utils.toast lazy="on-load">
               </div>
            @endpersist
            
            @persist('app-toolbar')
               {{-- <livewire:components.console.sidebar.toolbar lazy> --}}
            @endpersist

            <div class="yena-app-wrapper flex w-full h-full relative">
               <div class="yena-sidebar" :class="{
                  '!transform !translate-x-0 !translate-y-0': $store.app.layoutSidebar,
                  'is-short': $store.app.isShortSidebar,
               }">
                  @persist('sidebar-menu')
                     <livewire:components.console.sidebar.menu zzlazy>
                  @endpersist
               </div>

               <div class="yena-root-main">
                @persist('mobile-header-toolbar')
                    <livewire:components.console.header.menu :key="uukey('app', 'console.header.menu')">
                @endpersist

                  <div class="yena-container !px-0" id="yenaApp">
                     {{ $slot }}
                  </div>
               </div>
            </div>
            @stack('below-slot')
        </div>



        @persist('search-modal')
        <x-modal name="search-sites-modal" :show="false" removeoverflow="true" maxWidth="2xl">
            <livewire:components.console.sidebar.search :key="uukey('app', 'sites-search')">
         </x-modal>
        @endpersist


        @persist('language-modal')
        <x-modal name="language-modal" :show="false" removeoverflow="true" maxWidth="lg">
            <livewire:components.console.sidebar.language :key="uukey('app', 'app-language')">
         </x-modal>
        @endpersist

        <div class="fixed bottom-4 right-4 z-[var(--yena-zIndices-overlay)]" x-data="{ tippy: {
            content: () => $refs.template.innerHTML,
            allowHTML: true,
            appendTo: document.body,
            maxWidth: 360,
            interactive: true,
            trigger: 'click',
            animation: 'scale',
         } }">
            <template x-ref="template">
               <div class="yena-menu-list !w-full">
         
                  <a href="{{ config('app.HELPCENTER_URL') }}" class="yena-menu-list-item">
                     <div class="--icon">
                        <i class="ph ph-magnifying-glass text-lg flex"></i>
                     </div>
                     <span>{{ __('Help center') }}</span>
                  </a>
                  <a href="mailto:{{ config('app.APP_EMAIL') }}" class="yena-menu-list-item">
                     <div class="--icon">
                        {!! __icon('interface-essential', 'document-text-edit', 'w-5 h-5') !!}
                     </div>
                     <span>{{ __('Contact support') }}</span>
                  </a>
                  <hr class="--divider">
                  <a @click="$dispatch('open-modal', 'language-modal');" class="yena-menu-list-item">
                     <div class="--icon">
                        <i class="ph ph-globe text-lg flex"></i>
                     </div>
                     <span>{{ __('Language') }}</span>
                  </a>
              </div>
            </template>
            <div class="yena-button-stack ml-4 !h-[40px] !w-[40px] cursor-pointer !rounded-full" x-tooltip="tippy">
                <i class="ph ph-question-mark text-xl"></i>
            </div>
        </div>
        <script>
            var object = {
                mediaUrl: "{{ gs('media/site/images') }}",
                baseUrl: "{{ config('app.url') }}",
                currentBaseUrl: "{{ url('/') }}",
                sitePrefix: "{{ config('app.site_prefix') }}", 
                copiedText: "{{ __('Copied') }}",
            };

            window.builderObject = object;

            window.consoleAuth = true;
            window.siteManifestResources = {!! collect(get_vite_site_resources())->toJson() !!};
        </script>
        @stack('scripts')

        @livewireScriptConfig

        @vite([
         'resources/js/yenaWire.js'
        ])

        {{-- <script>
            document.addEventListener('navigateTurbo:ready', () => {
                navigateTurbo.init({

                    ...window.turboNavigate,
                    routes: [
                        '/console',
                        '/console/sites',
                        '/console/templates',
                        '/console/upgrade',
                        '/console/trash',
                        '/console/create',
                        '/console/settings',
                        '/console/upgrade/view/{_id}',
                    ],
                    prefetch: [
                        '/console',
                        '/console/sites',
                        '/console/templates',
                        '/console/upgrade',
                        '/console/trash',
                        '/console/create',
                        '/console/settings',
                        '/console/upgrade/view/{_id}',
                    ]
                })
            })
        </script> --}}
    </body>
</html>
