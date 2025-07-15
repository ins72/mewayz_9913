<div class="py-2 px-2 w-[100%]">
    <div class="flex flex-row items-center">
        <template x-if="!site.logo">
            <div class="h-7 w-7 bg-gray-200 p-3 border-2 border-white border-solid rounded-full"></div>
        </template>
        <template x-if="site.logo">
            <img alt=" " class="h-7 w-7 bg-gray-200 border-2 border-white border-solid rounded-full object-cover" :src="$store.builder.getMedia(site.logo)">
        </template>

        <div class="flex flex-col ml-2">
            <div class="text-small-bold !truncate" x-text="site.name"></div>
            <div class="w-10 h-1 bg-gray-300 rounded-full !mt-1"></div>
        </div>

    </div>
    <div class="flex flex-col items-start !mt-1">

        <div class="w-[100%] h-1 bg-gray-200 rounded-full !mt-1"></div>
        <div class="w-[100%] h-1 bg-gray-200 rounded-full !mt-1"></div>
        <div class="w-[100%] h-1 bg-gray-200 rounded-full !mt-1"></div>
    </div>
    <div class="flex flex-col w-5/6 !mt-4 gap-2 mx-auto">
        <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
        <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
        <div class="bg-gray-200 flex w-[100%] h-5 border border-solid border-gray-200 rounded-4"></div>
    </div>
</div>