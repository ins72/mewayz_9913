<div class="w-[100%]">
    <div class="flex flex-col mt-2">
        <div class="w-24 h-32 bg-gray-300- bg-white p-3 rounded-md mx-auto flex items-center justify-center flex-col">
            <div class="flex flex-col items-center !mt-1 px-4 mb-2">
                <div class="text-small-bold !truncate !mt-1" x-text="site.name"></div>
                <div class="w-16 h-1 bg-gray-200 rounded-full !mt-1"></div>
            </div>

            <template x-if="!site.logo">
                <div class="h-14 w-12 bg-gray-200 p-3 border-2 border-white border-solid rounded-lg"></div>
            </template>
            <template x-if="site.logo">
                <img alt=" " class="h-14 w-12 bg-gray-200 border-2 border-white border-solid rounded-lg object-cover" :src="$store.builder.getMedia(site.logo)">
            </template>
        </div>
    </div>
    <div class="flex flex-col w-5/6 !mt-4 gap-2 mx-auto">
        <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
        <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
        <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
    </div>
</div>
