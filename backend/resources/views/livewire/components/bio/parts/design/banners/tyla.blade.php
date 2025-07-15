<div class="w-[100%]">
    <div class="flex flex-col p-2">
        <div class="w-[100%] h-[3.75rem] p-3 mt-0 rounded-t-xl bg-gray-200 !bg-cover !bg-center" :style="{
            'background': site.banner ? `url(${$store.builder.getMedia(site.banner)})` : 'rgb(238 238 238/1)'
        }"></div>


        <div class="flex flex-col items-center -mt-3">

            <template x-if="!site.logo">
                <div class="h-7 w-7 bg-gray-200 p-3 border-2 border-white border-solid rounded-full"></div>
            </template>
            <template x-if="site.logo">
                <img alt=" " class="h-7 w-7 bg-gray-200 border-2 border-white border-solid rounded-full object-cover" :src="$store.builder.getMedia(site.logo)">
            </template>
            
            <div class="text-small-bold !truncate" x-text="site.name"></div>
            <div class="w-14 h-1 bg-gray-200 rounded-full !mt-1"></div>
            <div class="w-10 h-1 bg-gray-200 rounded-full !mt-1"></div>
            
            <div class="flex flex-col w-5/6 !mt-4 gap-2 mx-auto">
                <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
                <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
                <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
            </div>

        </div>
    </div>
</div>
