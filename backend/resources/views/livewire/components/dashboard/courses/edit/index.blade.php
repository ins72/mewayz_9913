
<?php
    use App\Models\Course;
    use App\Models\CoursesIntro;
    use App\Models\CoursesPerformanceExam;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, mount, placeholder, rules, uses, usesFileUploads};
    uses([ToastUp::class]);
    usesFileUploads();

    state([
        'user' => fn() => iam(),
    ]);

    state([
        'id'
    ]);

    state([
        'course' => null,
        'featured_img' => null,
        'exams' => null,
        'memberships' => null,
        'new_include' => null,
        'intro' => [
            'name' => '',
            'file' => ''
        ],
        'intros' => null
    ]);

    rules(fn() => [
        'course.course_level' => 'string',
        'course.name' => 'string',
        'course.description' => 'string',
        'course.price_type' => 'string',
        'course.price' => 'string',
        'course.settings.enable_intro' => 'string',
        'course.settings.membership.levels' => 'string',
        'course.course_expiry_type' => 'string',
        'course.course_expiry' => 'string',
        'course.course_includes' => 'string',
        'course.settings.enable_exam' => 'string',
        'course.settings.questions_to_pass' => 'string',
        'course.settings.exam_id' => 'string',

        'intros.*.name' => 'string',
    ]);

    mount(function(){
        $this->exams = CoursesPerformanceExam::where('user_id', $this->user->id)->orderBy('id', 'DESC')->get();

        $this->intros();
        $this->refresh();
    });

    $refresh = function(){
        if (!$this->course = Course::where('user_id', $this->user->id)->where('id', $this->id)->first()) {
            abort(404);
        }
    };

    $add_intro = function() {
        $filesystem = sandy_filesystem('media/courses/intro-video');
        $this->validate([
            'intro.file' => 'mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi|max:20048'
        ]);

        $file = '';
        if (!empty(ao($this->intro, 'file'))) {
            $file = $this->intro['file']->storePublicly('media/courses/intro-video', $filesystem);
            $file = str_replace('media/courses/intro-video/', "", $file);
        }

        $intro = new CoursesIntro;
        $intro->user = $this->user->id;
        $intro->course_id = $this->course->id;
        $intro->name = ao($this->intro, 'name');
        $intro->file = $file;
        $intro->save();

        $this->intro = [
            'name' => '',
            'file' => ''
        ];

        $this->intros();
    };

    $remove_intro = function($id) {
        if (!$intro = CoursesIntro::where('user', $this->user->id)->where('id', $id)->first()) return false;

        $filefolder = 'media/courses/intro-video';
        storageDelete($filefolder, $intro->file);
        $intro->delete();

        $this->intros();
    };

    $add_include = function() {
        $includes = $this->course->course_includes;
        $includes[] = $this->new_include;

        $this->course->course_includes = $includes;
        $this->new_include = '';
    };

    $remove_include = function($key) {
        $includes = $this->course->course_includes;
        unset($includes[$key]);

        $this->course->course_includes = $includes;
    };

    $_post = function() {
        $filefolder = 'media/courses/image';
        $filesystem = sandy_filesystem($filefolder);

        $this->validate([
            'course.name' => 'required',
            'course.description' => 'required'
        ]);

        $banner = $this->course->banner;
        if (!empty($this->featured_img)) {
            $this->validate([
                'featured_img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            $banner = $this->featured_img->storePublicly($filefolder, $filesystem);
            $banner = str_replace("$filefolder/", "", $banner);
            storageDelete($filefolder, $this->course->banner);
        }
        $this->course->banner = $banner;

        $this->course->price = (float) str_replace(',', '.', $this->course->price);
        $this->course->save();

        $this->flashToast('success', __('Saved successfully'));
    };

    $intros = function() {
        $this->intros = CoursesIntro::where('user_id', $this->user->id)->get();
    };

?>
<div>
  <div>
    <div x-data="console__edit_course">
 
       <div class="md:flex p-0 md:h-full justify-between gap-4">
          <div class="w-full min-w-0">
             
             <div class="banner">
                <div class="banner__container !bg-white">
                   <div class="banner__preview !right-0 !w-[300px] !top-[4rem]">
                      {!! __icon('Content Edit', 'Book, Open.4') !!}
                   </div>
                   <div class="banner__wrap z-[50]">
                      <div class="banner__title h3 !text-black">{{ __('Edit Course') }}</div>
                      {{-- <div class="banner__text !text-black">{{ __('Power your pages with our Booking App.') }}</div> --}}
                      
                      {{-- <div class="mt-3 flex gap-2">
                         <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-booking-modal')">{{ __('Create Booking') }}</button>
                         

                         <a class="yena-button-stack !rounded-full lg:!hidden" @click="$dispatch('open-modal', 'booking-settings-modal');">{{ __('Settings') }}</a>
                      </div> --}}
                   </div>
                </div>
             </div>

             <div>
                <form wire:submit.prevent="_post" class="p-2 md:!p-5 rounded-xl bg-white">

                    <div class="settings__upload mb-4 !mt-0">
                        <div class="settings__preview !bg-[#f3f3f3] flex items-center justify-center">
                           @php
                               $_avatar = false;

                               if($course->banner){
                                $_avatar = gs('media/courses/image', $course->banner);
                               }
                               
                               if($featured_img) $_avatar = $featured_img->temporaryUrl();
                           @endphp
            
                           @if (!$_avatar)
                              {!! __i('--ie', 'image-picture', 'text-gray-300 w-8 h-8') !!}
                           @endif
                           @if ($_avatar)
                              <img src="{{ $_avatar }}" alt="">
                           @endif
                        </div>
                        <div class="settings__wrap">
                          <div class="text-[2rem] leading-10 font-bold">{{ __('Featured Image') }}</div>
                          <div class="settings__content">{{ __('We recommended an image of at least 80x80. Gifs work too.') }}</div>
                          <div class="settings__file">
                           <input class="settings__input z-50" type="file" wire:model="featured_img">
                           <a class="yena-button-stack">{{ __('Choose') }}</a>
                          </div>
                        </div>
                     </div>

                    <div class="grid grid-cols-1 md:!grid-cols-3 gap-3 mb-4">
                        <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-center !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ $course->course_level == 'beginner' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('course.course_level', 'beginner')">
                              <div>
                                 <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                                    {!! __i('interface-essential', 'light-bulb.1', 'w-5 h-5 text-black') !!}
                                 </div>
                              </div>
         
                              <div class="flex flex-col">
                                 <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('Beginner') }}</p>
                              </div>
                        </button>
                        <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-center !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ $course->course_level == 'advanced' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('course.course_level', 'advanced')">
                              <div>
                                 <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                                    {!! __i('Photo Edit', 'Magic Wand, Photo, Edit', 'w-5 h-5 text-black') !!}
                                 </div>
                              </div>
         
                              <div class="flex flex-col">
                                 <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('Advanced') }}</p>
                              </div>
                        </button>
                        <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-center !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ $course->course_level == 'intermediate' ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('course.course_level', 'intermediate')">
                              <div>
                                 <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                                    {!! __i('Photo Edit', 'Thunder, Lightning', 'w-5 h-5 text-black') !!}
                                 </div>
                              </div>
         
                              <div class="flex flex-col">
                                 <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ __('Intermediate') }}</p>
                              </div>
                        </button>
                    </div>
                    

                    <div>
                        <div class="form-input mb-3">
                            <input type="text" name="name" wire:model="course.name" placeholder="{{ __('Name') }}">
                        </div>
                        <div class="form-input" wire:ignore>
                            <div
                               x-ref="quillEditor"
                               x-init="
                                  quill = new window.Quill($refs.quillEditor, {theme: 'snow'});
                                  quill.on('text-change', function () {
                                     description = quill.root.innerHTML;
                                  });
          
                                  quill.root.innerHTML = description;
                               "
                            >
                            
                            </div>
                        </div>
                        <div class="bg-[var(--yena-colors-gray-100)] p-4 rounded-lg mt-4">
                            <div class="input-box !border border-solid border-[var(--c-mix-1)] !mt-0 !bg-white">
                                <div class="input-group !border-0">
                                   <div class="switchWrapper">
                                      <input class="switchInput !border-0" id="enableIntroVideo" type="checkbox" wire:model="course.settings.enable_intro" value="">
             
                                      <label for="enableIntroVideo" class="switchLabel">{{ __('Enable Intro Video') }}</label>
                                      <div class="slider"></div>
                                   </div>
                                </div>
                             </div>

                        
                             <div class="flex flex-col gap-3">
                                <div class="flex">
                                    <div class="grid grid-cols-12 w-full gap-2 mr-2">
                                        <div class="form-input w-full col-span-8">
                                            <input type="text" class="border-dashed !h-full" wire:model.lazy="intro.name" placeholder="{{ __('Add Intro Name') }}">
                                        </div>
        
                                        <div class="fake-input relative border-dashed has-fancy-r col-span-4">
                                            <input type="file" class="absolute right-0 top-0 opacity-0 w-full h-full" wire:model="intro.file" placeholder="{{ __('Add Image') }}">
        
                                            <p>{{ __('Add Video') }}</p>
        
                                            <div class="fancy-r">
                                            
                                                {!! __i('Video, Movies', 'play-video-cursor', 'w-6 h-6') !!}
                                            </div>
                                            <div wire:loading wire:target="intro.file"
                                                class="text-xs font-bold m-0 absolute -bottom-6 left-0">
                                                {{ __('Processing video...') }}</div>
                                        </div>
                                
                                        @error("intro.file")
                                            <p class="text-xs text-red-400">
                                                <span class="error">{{ $message }}</span>
                                            </p>
                                        @enderror
                                    </div>
                                    <div class="flex items-center">
                                        <a wire:loading.class="disabled" wire:target="intro.file" class="ml-auto my-auto bg-white w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" wire:click="add_intro()">
                                            <i class="fi fi-rr-plus text-gray-400 text-xs font-bold"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-2 {{ $intros->isEmpty() ? 'hidden' : '' }}">
                                    @foreach ($intros as $key => $v)
                                    <div class="flex">
                                        <div class="grid grid-cols-12 w-full gap-2 mr-2">
                                            <div class="form-input w-full col-span-8">
                                                <input type="text" class="" wire:model="intros.{{ $key }}.name" placeholder="{{ __('Add Intro Name') }}">
                                            </div>
                                            
                                            <div class="fake-input relative has-fancy-r col-span-4 disabled">
                        
                                                <p class="truncate pr-5">{{ $v->file }}</p>
                            
                                                <div class="fancy-r">
                                                    {!! __i('Video, Movies', 'Play, Library, Playlist, Slider.1', 'w-6 h-6') !!}
                                                </div>
                                            </div>
                                        </div>
                            
                                        <div class="flex items-center">
                                            <a class="ml-auto my-auto bg-g-gray w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" wire:click="remove_intro({{ $v->id }})">
                                            
                                                {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                             </div>

                        </div>

                        <div class="flex items-center flex-row gap-[var(--yena-space-3)] w-full h-[var(--yena-sizes-10)] my-4">
                           {{-- <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]"> --}}
                           <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Price') }}</span>
                           <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                        </div>

                        {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="sandy-big-checkbox koon-checkbox">
                                <input type="radio" name="price_type" wire:model="course.price_type" checked="" value="1" class="sandy-input-inner">
                                <div class="checkbox-inner">
                                    <div>
                                        <div class="-icon w-12 h-12">
                                            {!! __i('money', 'Coins', 'w-5 h-5 text-white') !!}
                                        </div>
                                    </div>
                                    <div class="content ml-2">
                                        <h1 class="font-bold">{{ __('Fixed') }}</h1>
                                        <p class="font-10">{{ __('Customers can pay for the product.') }}</p>
                                    </div>
                                </div>
                            </label>
                            <label class="sandy-big-checkbox koon-checkbox">
                                <input type="radio" name="price_type" wire:model="course.price_type" value="2" class="sandy-input-inner">
                                <div class="checkbox-inner">
                                    <div>
                                        <div class="-icon w-12 h-12">
                                            {!! __i('Payments Finance', 'invoice-hand', 'w-5 h-5 text-white') !!}
                                        </div>
                                    </div>
                                    <div class="content ml-2">
                                        <h1 class="font-bold">{{ __('Pass') }}</h1>
                                        <p class="font-10">{{ __('Customers access with pass.') }}</p>
                                    </div>
                                </div>
                            </label>
                        </div> --}}

                        @if ($course->price_type == 1)            
                            <div class="grid grid-cols-1 gap-4 mt-3">
                                <div class="form-input">
                                    <input type="number" placeholder="{{ __('Price') }}" name="price" wire:model="course.price">
                                </div>
                            </div>
                        @endif

                        @if ($course->price_type == 2)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                                @foreach ($memberships as $item)
                                <label class="sandy-big-checkbox koon-checkbox">
                                    <input type="checkbox" name="pass[]" wire:model="course.settings.membership.levels" value="{{ $item->id }}" class="sandy-input-inner">
                                    <div class="checkbox-inner py-2">
                                        <div class="h-12 w-12">
                                            <div class="-icon koon-grey-bg">
                                                {!! __i('Payments Finance', 'invoice-hand', 'w-5 h-5') !!}
                                            </div>
                                        </div>
                                        <div class="content ml-2 truncate">
                                            <h1 class="font-bold truncate">{{ $item->name }}</h1>
                                            <p class="font-10">{!! iam()->price($item->price, $user->id) !!}</p>
                                        </div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="bg-[var(--yena-colors-gray-100)] p-4 rounded-lg mt-4">
                        
                            <div class="input-box !border border-solid border-[var(--c-mix-1)] !mt-0 !bg-white">
                                <div class="input-group !border-0">
                                   <div class="switchWrapper">
                                      <input class="switchInput !border-0" id="enableExpiration" type="checkbox" wire:model="course.course_expiry_type" value="">
             
                                      <label for="enableExpiration" class="switchLabel">{{ __('Enable Expiration') }}</label>
                                      <div class="slider"></div>
                                   </div>
                                </div>
                             </div>
    
                            <div class="form-input active">
                                <input type="number" name="expry_days" wire:model="course.course_expiry" placeholder="{{ __('Set Expiry Days') }}">
                            </div>
                        </div>
                        <div class="bg-[var(--yena-colors-gray-100)] p-4 rounded-lg mt-4">
                            <div class="flex items-center flex-row gap-3 w-full h-[var(--yena-sizes-10)] mb-4">
                               {{-- <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]"> --}}
                               <span class="flex-shrink-0 text-[var(--yena-colors-gray-600)]">{{ __('Course Includes or Requirements') }}</span>
                               <hr class="opacity-60 [border-image:initial] border-solid w-full border-[var(--yena-colors-blackAlpha-400)]">
                            </div>

                            <div class="flex flex-col gap-3">
                                        
                                <div class="flex">
                                    <div class="grid grid-cols-1 w-full gap-2 mr-2">
                                        <div class="form-input w-full">
                                            <input type="text" class="border-dashed" wire:model.lazy="new_include" placeholder="{{ __('Add New Includes or Requirements') }}">
                                        </div>
                                    </div>
                        
                                    <div class="flex items-center">
                                        <a class="ml-auto my-auto bg-white w-8 h-8 flex items-center justify-center rounded-md cursor-pointer" wire:click="add_include()">
                                            <i class="fi fi-rr-plus text-gray-400 text-xs font-bold"></i>
                                        </a>
                                    </div>
                                </div>
                        
                        
                                <div class="flex flex-col gap-4">
                                    
                                    @if (!empty($course->course_includes) && is_array($course->course_includes))
                                        @foreach ($course->course_includes as $key => $v)
                                        <div class="flex">
                                            <div class="grid grid-cols-1 w-full gap-2 mr-2">
                                                <div class="form-input w-full">
                                                    <input type="text" class="" wire:model="course.course_includes.{{ $key }}" placeholder="{{ __('Add New Includes or Requirements') }}">
                                                </div>
                                            </div>
                                
                                            <div class="flex items-center">
                                                <a class="ml-auto my-auto bg-white w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" wire:click="remove_include({{ $key }})">
                                                
                                                    {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                                                </a>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="bg-[var(--yena-colors-gray-100)] p-4 rounded-lg mt-4">
                            <div class="input-box !border border-solid border-[var(--c-mix-1)] !mt-0 !bg-white">
                                <div class="input-group !border-0">
                                   <div class="switchWrapper">
                                      <input class="switchInput !border-0" id="enableExam" type="checkbox" wire:model="course.settings.enable_exam" value="">
             
                                      <label for="enableExam" class="switchLabel">{{ __('Enable Exam') }}</label>
                                      <div class="slider"></div>
                                   </div>
                                </div>
                             </div>
                             
                            <div class="pt-5 mt-5 border-t-2 border-gray-200 border-solid">
                                <div class="flex gap-2 mb-3 items-center">
                                <p class="text-lg font-semibold">{{ __('Pass Exam') }}</p>
                                </div>
                                <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <p class="text-gray-500 text-xs lg:text-sm mb-0">{{ __('Input the amount of answered correct questions in order to pass the selected exam. Ex: 4. 4 questions out of 5 has to be answered correctly to pass the exam.') }}</p>
                                </div>
                                <div class="col-span-2">
                                    <div class="form-input w-full">
                                        <input type="number" class="border-dashed-" wire:model.lazy="course.settings.questions_to_pass" placeholder="{{ __('Amount to pass') }}">
                                    </div>
                                </div>
                                </div>
                            </div>
    
                            <div class="grid grid-cols-1 md:!grid-cols-3 gap-4 mt-4">
                                @foreach ($exams as $item)
                                <button class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !items-center !p-3 !bg-[#ffffffa3] hover:border-[var(--yena-colors-purple-400)] border-2 border-solid [border-image:initial] border-[var(--yena-colors-transparent)] shadow-sm !text-left !justify-start gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] {{ ao($course->settings, 'exam_id') == $item->id ? '!border-[var(--yena-colors-purple-400)]' : '' }}" type="button" wire:click="$set('course.settings.exam_id', '{{ $item->id }}')">
                                      <div>
                                         <div class="bg-[#f7f3f2] w-10 h-10 rounded-lg flex items-center justify-center">
                                            {!! __i('custom', 'course-cert-1', 'w-5 h-5 text-black') !!}
                                         </div>
                                      </div>
                 
                                      <div class="flex flex-col">
                                         <p class="text-xl font-bold text-[var(--yena-colors-gray-800)]">{{ $item->name }}</p>
                                         <p class="text-xs text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] whitespace-pre-line">{{ ucfirst($item->level) }}</p>
                                      </div>
                                </button>
                                @endforeach
                            </div>
                        </div>
                        {{-- <a href="" class="sandy-expandable-btn mt-5 koon-grey-bg w-full p-2 font-bold"><span class="text-base flex items-center justify-center">{{ __('Lessons') }}</span></a> --}}
                    </div>

                    @php
                        $error = false;

                        if(!$errors->isEmpty()){
                            $error = $errors->first();
                        }

                        if(Session::get('error._error')){
                            $error = Session::get('error._error');
                        }
                    @endphp
                    @if ($error)
                        <div class="mt-5 bg-red-200 font--11 p-1 px-2 rounded-md">
                            <div class="flex items-center">
                            <div>
                                <i class="fi fi-rr-cross-circle flex text-xs"></i>
                            </div>
                            <div class="flex-grow ml-1 text-xs">{{ str_replace('create.', '', $error) }}</div>
                            </div>
                        </div>
                    @endif
                    <button class="yena-button-stack mt-5 w-full">{{ __('Save') }}</button>
                </form>
             </div>
          </div>
          <div>
            <div class="short-cal min-w-[310px] flex flex-[1] flex-col gap-[12px] p-[12px] border-l border-solid border-gray-50 w-full lg:!w-max lg:!max-w-[310px] mt-4 lg:![margin-top:0]">
              <div class="flex items-center justify-between">
                  <p class="text-color-headline font-bold">{{ __('Lessons') }}</p>
                  <div class="p-[4px] flex items-center">
                      <span class="default-tooltip relative top-[1px]">
                        <a @click="$dispatch('open-modal', 'course-lesson-create-modal');" class="yena-button-stack --primary !text-xs !h-8">
                            {{ __('Add Lesson') }}
                        </a>
                      </span>
                  </div>
              </div>
              
              <div class="flex flex-col gap-2">
                  <div class="calendar-day-view flex items-center justify-center">
                    <div class="h-full w-full flex flex-col bg-[var(--yena-colors-gray-100)] rounded-[10px]">

                        
                        <div class="p-4 w-full">
                            <livewire:components.console.courses.lessons.index :$course :key="uukey('app', 'console.courses.lessons.index')">
                        </div>

                       {{-- <div class="p-4 w-full">
                        <div x-cloak x-show="_page=='-'">
                          <livewire:components.console.booking.settings :key="uukey('app', 'console.booking.settings')">
                        </div>
                        <div x-cloak x-show="_page=='services'">
                          <livewire:components.console.booking.services :key="uukey('app', 'console.booking.services')">
                        </div>
                        <div x-cloak x-show="_page=='gallery'">
                          <livewire:components.console.booking.gallery :key="uukey('app', 'console.booking.gallery')">
                        </div>
                       </div> --}}
                    </div>
                  </div>
              </div>
             
           </div>
          </div>
       </div>
       
      {{-- <x-modal name="booking-settings-modal" :show="false" removeoverflow="true" maxWidth="2xl">

        <div class="w-full">
          <div class="flex flex-col">
             <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
                <i class="fi fi-rr-cross text-sm"></i>
             </a>
       
             <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Settings') }}</header>
       
             <hr class="yena-divider">
       
             <div class="px-8 pt-2 pb-6">
              <div class="flex gap-2 mb-4">
                  <a @click="_page='-'" class="yena-button-stack --primary !text-xs !h-8">
                     {{ __('Settings') }}
                  </a>
                  <a @click="_page='services'" class="yena-button-stack --primary !text-xs !h-8">
                     {{ __('Services') }}
                  </a>
                  <a @click="_page='gallery'" class="yena-button-stack --primary !text-xs !h-8">
                     {{ __('Gallery') }}
                  </a>
              </div>

              <div>
                <div x-cloak x-show="_page=='-'">
                  <livewire:components.console.booking.settings lazy :key="uukey('app', 'console.booking.settings-modal')">
                </div>
                <div x-cloak x-show="_page=='services'">
                  <livewire:components.console.booking.services lazy :key="uukey('app', 'console.booking.services-modal')">
                </div>
                <div x-cloak x-show="_page=='gallery'">
                  <livewire:components.console.booking.gallery lazy :key="uukey('app', 'console.booking.gallery-modal')">
                </div>
               </div>
             </div>
          </div>
       </div>
     </x-modal> --}}
    </div>
    @script
      <script>
          Alpine.data('console__edit_course', () => {
            return {
                description: @entangle('course.description'),
                _page: '-',
                analytics: {
                  booking_count: '0',
                },

                init(){
                  let $this = this;
                //   $this.$wire.getAnalytics().then(r => {
                //     $this.analytics = r;
                //   })
                },
            }
          });
      </script>
    @endscript
 </div>
</div>