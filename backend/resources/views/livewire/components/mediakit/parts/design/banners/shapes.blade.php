<div class="w-[100%]">
    <div class="flex flex-col p-2">
        <div class="w-[100%] h-10 bg-gray-200 p-3 rounded-t-xl !bg-cover !bg-center" style="-webkit-clip-path: url(#_curve);" :style="{
            'background': site.banner ? `url(${$store.builder.getMedia(site.banner)})` : 'rgb(238 238 238/1)'
        }"></div>

        <div class="flex items-center -mt-5 relative overflow-hidden justify-center">

            <template x-if="!site.logo">
                <div class="h-10 w-10 bg-gray-200 p-3 border-2 border-white border-solid rounded-full"></div>
            </template>
            <template x-if="site.logo">
                <img alt=" " class="h-10 w-10 bg-gray-200 border-2 border-white border-solid rounded-full object-cover" :src="$store.builder.getMedia(site.logo)">
            </template>
        </div>
        <div class="flex flex-col items-center !mt-1 justify-center">
            <div class="text-small-bold !truncate" x-text="site.name"></div>
            <div class="w-16 h-1 bg-gray-200 rounded-full !mt-1"></div>
            <div class="w-20 h-1 bg-gray-200 rounded-full !mt-1"></div>
        </div>
        <div class="flex flex-col w-5/6 !mt-4 gap-2 mx-auto">
            <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
            <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
            <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
        </div>
    </div>
    <svg width="0" height="0">
        <defs>
            <clipPath id="_curve" clipPathUnits="objectBoundingBox">
                <path d="M 0,1 L 0,0 L 1,0 L 1,1 C .65 .8, .35 .8, 0 1 Z"></path>
            </clipPath>
        </defs>
    </svg>
</div>
