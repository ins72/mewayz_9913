<div class="w-[100%]">
    <div class="flex flex-col mt-2">
        <div class="text-small-bold !truncate bg-white mb-2 text-center border-b border-t border-solid border-black" x-text="site.name"></div>

        <div class="w-20 h-20 bg-gray-200 p-3 rounded-md mx-auto flex items-center justify-center  !bg-cover !bg-center" :style="{
            'background': site.banner ? `url(${$store.builder.getMedia(site.banner)})` : 'rgb(238 238 238/1)'
        }">
            
            <template x-if="!site.logo">
                <div class="h-14 w-12 bg-gray-200 p-3 border-2 border-white border-solid rounded-lg"></div>
            </template>
            <template x-if="site.logo">
                <img alt=" " class="h-14 w-12 bg-gray-200 border-2 border-white border-solid rounded-lg object-cover" :src="$store.builder.getMedia(site.logo)">
            </template>
        </div>
        <div class="flex flex-col items-center !mt-1">
            <div class="w-16 h-1 bg-gray-200 rounded-full !mt-1"></div>
            <div class="w-20 h-1 bg-gray-200 rounded-full !mt-1"></div>
        </div>
    </div>
    <div class="flex flex-col w-5/6 !mt-4 gap-2 mx-auto">
        <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
        <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
        <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
    </div>
</div>
