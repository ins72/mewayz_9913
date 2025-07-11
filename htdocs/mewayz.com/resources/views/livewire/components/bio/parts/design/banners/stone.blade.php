<div class="w-[100%]">
    <div class="flex flex-col mt-1">
        <div class="w-32 h-32 bg-gray-200 p-3 rounded-md mx-auto flex items-center justify-center flex-col !bg-cover !bg-center !truncate stone-preview-o" :style="{
            'background': site.banner ? `url(${$store.builder.getMedia(site.banner)})` : 'rgb(238 238 238/1)'
        }">
            {{-- <div class="text-small-bold !truncate bg-gray-200" x-text="site.name"></div> --}}
            {{-- <div class="w-[100%] h-1 bg-gray-200 rounded-full mb-1"></div> --}}
            
            <template x-if="!site.logo">
                <div class="h-8 w-8 bg-gray-200 p-3 border-2 border-white border-solid rounded-full mt-auto relative z-10"></div>
            </template>
            <template x-if="site.logo">
                <img alt=" " class="h-8 w-8 bg-gray-200 border-2 border-white border-solid rounded-full mt-auto object-cover relative z-10" :src="$store.builder.getMedia(site.logo)">
            </template>

            <div class="flex flex-col items-center !mt-1 px-4 relative z-10">
                <div class="w-10 h-1 bg-gray-200- bg-gray-200 rounded-full !mt-1"></div>
                <div class="w-16 h-1 bg-gray-200 rounded-full !mt-1"></div>
            </div>
        </div>
        <div class="flex flex-col w-5/6 !mt-4 gap-2 mx-auto">
            <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
            <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
            <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
        </div>
    </div>
</div>
