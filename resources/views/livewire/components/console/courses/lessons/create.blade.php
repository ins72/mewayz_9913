
<?php

    use App\Traits\LessionInfoTrait;
    use App\Models\CoursesLesson;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated, usesFileUploads};

    usesFileUploads();

    state([
        'course'
    ]);

    state([
        'lesson_skeleton' => fn () => $this->lesson_skeleton(),
    ]);


    uses([
        ToastUp::class,
        LessionInfoTrait::class,
    ]);
    
    mount(fn() => '');

    placeholder('
    <div class="p-5 w-full mt-1">
        <div class="flex mb-2 gap-4">
            <div>
                <div class="--placeholder-skeleton w-[200px] h-[200px] rounded-3xl"></div>
            </div>
            <div class="flex flex-col gap-2 w-full">
                <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
                <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
                <div class="--placeholder-skeleton w-full h-[20px] rounded-[var(--yena-radii-sm)] mt-1"></div>
                <div class="--placeholder-skeleton w-[150px] h-[40px] rounded-full mt-5"></div>
            </div>
        </div>
        
        <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)]"></div>
        <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
        <div class="--placeholder-skeleton w-full h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
    </div>');

    $createLesson = function($type){
        $types = $this->lesson_skeleton();

        if (!array_key_exists($type, $types)) {
            abort(404);
        }

        $data = [
            'silence' => 'golden'
        ];


        $lesson = new CoursesLesson;
        $lesson->user_id = iam()->id;
        $lesson->course_id = $this->course->id;
        $lesson->name = __('New :type', ['type' => ucfirst($types[$type]['name'])]);
        $lesson->description = __('A short description of your lesson.');
        $lesson->lesson_type = $type;
        $lesson->status = 1;
        $lesson->lesson_duration = '15min';
        $lesson->lesson_data = $data;
        $lesson->save();

        $this->flashToast('success', __('Lesson created'));
        $this->dispatch('close');

        $this->dispatch('lessonsRefresh');
    };
?>


<div class="w-full">
   <div class="flex flex-col">
      <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
         <i class="fi fi-rr-cross text-sm"></i>
      </a>

      <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Create Lesson') }}</header>

      <hr class="yena-divider">

      <div class="px-8 pt-2 pb-6">
        <div class="grid grid-cols-1 gap-0">
            @foreach ($lesson_skeleton as $key => $value)
            <a class="contact-list block border-b border-gray-200 border-solid px-0 remove-before w-full outline-none no-disabled-btn py-4" wire:click="createLesson('{{ $key }}')">
                <div class="flex items-center justify-center">
                    <div class="preview mr-">
                        {!! __i(ao($value, 'svg_folder'), ao($value, 'svg_file'), 'w-10 h-6') !!}
                    </div>
                    <div class="ml-0 mr-auto">
                        <h2 class="flex items-center text-sm font-semibold">
                            <div class="truncate">{{ ao($value, 'name') }}</div>
                        </h2>
                    </div>
    
                    <div class="flex gap-4 mr-0">
                        <div>
                            <i class="ph ph-caret-right text-lg"></i>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
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
         {{-- <button class="yena-button-stack mt-5 w-full">{{ __('Save') }}</button> --}}
        </div>
   </div>
</div>