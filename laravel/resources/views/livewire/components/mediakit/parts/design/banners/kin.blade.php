<div class="w-[100%]">
    <div class="flex flex-col p-2">
        <div class="w-[100%] h-10 bg-gray-200 p-3 rounded-t-xl !bg-cover !bg-center" :style="{
            'background': site.banner ? `url(${$store.builder.getMedia(site.banner)})` : 'rgb(238 238 238/1)'
        }"></div>

        <div class="flex items-center -mt-5 relative overflow-hidden justify-center">

            <div class="h-10 w-10 bg-gray-200 p-3 border-2 border-white border-solid rounded-lg absolute -left-3 !bg-cover !bg-center" :style="{
                'background': site.logo ? `url(${$store.builder.getMedia(site.logo)})` : 'rgb(238 238 238/1)'
            }"></div>

            <div class="h-10 w-10 bg-gray-200 p-3 border-2 border-white border-solid rounded-full !bg-cover !bg-center" :style="{
                'background': site.logo ? `url(${$store.builder.getMedia(site.logo)})` : 'rgb(238 238 238/1)'
            }"></div>

            <div class="h-10 w-10 bg-gray-200 p-3 border-2 border-white border-solid rounded-lg absolute -right-3 !bg-cover !bg-center" :style="{
                'background': site.logo ? `url(${$store.builder.getMedia(site.logo)})` : 'rgb(238 238 238/1)'
            }">
            </div>
        </div>
        <div class="flex flex-col items-start !mt-1">
            <div class="text-small-bold !truncate" x-text="site.name"></div>
            <div class="w-16 h-1 bg-gray-200 rounded-full !mt-1"></div>
            <div class="w-20 h-1 bg-gray-200 rounded-full !mt-1"></div>
        </div>
        <div class="flex flex-col w-5/6 !mt-4 gap-2 w-[100%]">
            <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
            <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
            <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
        </div>
    </div>
</div>