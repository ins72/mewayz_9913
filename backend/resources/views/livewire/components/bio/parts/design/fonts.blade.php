<div>

    <div class="grid grid-cols-2 gap-2">
        @foreach(fonts() as $key => $value)
        <label class="sandy-big-checkbox koon-checkbox">
           <input type="radio" name="font" x-model="page.settings.font" value="{{ $key }}" class="sandy-input-inner">
           <div class="checkbox-inner !flex-row !rounded-[14px] !h-[56px] items-center !justify-start !p-[8px]">
              <div>
                 <div class="bg-[#000] rounded-[12px] flex justify-center items-center w-10 h-10 text-white {{ slugify($key) }}-font-preview">Aa</div>
              </div>
              <div class="content ml-2">
                 <h1 class="font-bold text-sm {{ slugify($key) }}-font-preview">{{ $key }}</h1>
              </div>
           </div>
        </label>
        @endforeach
    </div>
</div>