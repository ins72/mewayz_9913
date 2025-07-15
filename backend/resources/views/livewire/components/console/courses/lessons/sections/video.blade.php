<div>
    <div x-data="app_course_lession_video">
        <div class="grid grid-cols-1 md:!grid-cols-2 gap-4 mb-5">
            @foreach ($this->video_skel() as $key => $value)
            <label class="sandy-big-checkbox">
                <input type="radio" name="data[type]" x-model="item.lesson_data.type" class="sandy-input-inner" data-placeholder-input="#video-link" data-placeholder="{{ ao($value, 'placeholder') }}" value="{{ $key }}">
                <div class="checkbox-inner rounded-2xl border-2 border-grey-400 !h-[20px]">
                    <div class="checkbox-wrap">
                        <div class="h-6 w-6 rounded-[10px]">
                            <i class="{{ ao($value, 'icon') }}"></i>
                            {!! ao($value, 'svg') !!}
                        </div>
                        <div class="content ml-2 flex items-center">
                            <h1>{{ ao($value, 'name') }}</h1>
                        </div>
                        <div class="icon">
                            <div class="active-dot !w-4 !h-4 !rounded-md">
                                <i class="ph ph-check !text-[10px]"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </label>
            @endforeach
        </div>
        <div class="form-input !bg-transparent">
            <label>{{ __('URL Link') }}</label>
            <input type="text" x-model="item.lesson_data.link">
        </div>
    </div>
    
   @script
   <script>
       Alpine.data('app_course_lession_video', () => {
          return {
            init(){
               let $this = this;

            //    console.log(this.item)


            //    this.$watch('item', (value) => {
            //     console.log(value)
            //    });
            }
          }
       });
   </script>
   @endscript
</div>