<?php
      use App\Traits\LessionInfoTrait;
      use App\Models\CoursesLesson;
      use App\Livewire\Actions\ToastUp;
      use function Livewire\Volt\{state, mount, placeholder, uses, rules, updated, usesFileUploads, on};
      
      on([
         'lessonsRefresh' => fn() => $this->getLessons(),
      ]);

      uses([
         ToastUp::class,
         LessionInfoTrait::class,
      ]);
      
      state([
         'lesson_skeleton' => fn () => $this->lesson_skeleton(),
      ]);

      state([
         'lessons' => [],
         'lessonsArray' => [],
      ]);

      state([
         'course'
      ]);

      mount(function(){
         $this->getLessons();
      });

      $getLessons = function(){
         $this->lessons = CoursesLesson::where('course_id', $this->course->id)->where('user_id', iam()->id)->orderBy('position', 'ASC')->orderBy('id', 'ASC')->get();

         $this->lessonsArray = $this->lessons->toArray();
      };

      $sort = function($list) {
         $this->skipRender();
         foreach ($list as $key => $value) {
               $value['value'] = (int) $value['value'];
               $value['order'] = (int) $value['order'];
               $update = CoursesLesson::find($value['value']);
               $update->position = $value['order'];
               $update->save();
         }
         
         $this->getLessons();
      };

      $deleteLesson = function($id){
         CoursesLesson::where('id', $id)->where('user_id', iam()->id)->delete();
         $this->getLessons();
      };
      $editLesson = function($item){
         if(!$lesson = CoursesLesson::where('id', ao($item, 'id'))->where('user_id', iam()->id)->first()) return;

         $lesson->fill($item);
         $lesson->save();

         $this->getLessons();
      };
?>

<div>
    
    <div x-data="app_course_lession">
      


      <div x-cloak x-show="__page=='-'">
         @if (!$lessons->isEmpty())
         <div class="flex flex-col gap-4" wire:sortable="sort">
            @foreach ($lessons as $item)
            <div class="yena-button-o !h-auto !min-h-[44px] md:!min-h-[4rem] !flex-col !p-3 !bg-[#ffffffa3] !text-left gap-2 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !w-full" x-data="{is_delete:false}" type="button" wire:sortable.item="{{ $item->id }}"
            wire:key="services-mix-lesson-{{ $item->id }}">
            <div class="flex items-center w-full">
               <div class="mr-[8px]">
                  <div class="bg-[#f7f3f2] w-8 h-8 rounded-lg flex items-center justify-center">
                     {!! __i(ao($lesson_skeleton, "$item->lesson_type.svg_folder"), ao($lesson_skeleton, "$item->lesson_type.svg_file"), 'w-5 h-5 text-black') !!}
                  </div>
               </div>
               <div class="flex flex-col truncate">
                  <p class="text-xs font-bold text-[var(--yena-colors-gray-800)] truncate">{{ strtoupper($item->lesson_type) }}</p>
                  <p class="text-[12px] text-[var(--yena-colors-gray-800)] font-[var(--yena-fontWeights-medium)] truncate">{{ $item->name }}</p>
               </div>
               <div class="flex items-center ml-auto gap-2">
                     <p class="drag handle pointer-events-auto cursor-pointer w-6 h-6 flex items-center justify-center rounded-md bg-[var(--yena-colors-gray-100)]" wire:sortable.handle>
                        <i class="ph ph-arrows-out-cardinal text-black"></i>
                     </p>
                     <a class="pointer-events-auto cursor-pointer w-6 h-6 flex items-center justify-center rounded-md bg-[var(--yena-colors-gray-100)]" x-on:click="__page = 'edit::{{ $item->id }}'">
                        <i class="ph ph-pencil-line text-black"></i>
                     </a>
                     <a class="pointer-events-auto cursor-pointer w-6 h-6 flex items-center justify-center rounded-md bg-[var(--yena-colors-gray-100)]" @click="$event.stopPropagation(); is_delete=true;">
                        <i class="ph ph-trash text-black"></i>
                     </a>
               </div>
            </div>
            
            <div class="card-button p-3 mt-4 flex gap-2 bg-[var(--yena-colors-gray-100)] w-full rounded-lg !hidden" x-cloak @click="$event.stopPropagation();" :class="{
               '!hidden': !is_delete
               }">
               <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>

               <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-full" @click="$wire.deleteLesson('{{ $item->id }}'); is_delete=false;">{{ __('Yes, Delete') }}</button>
            </div>
         </div>
            @endforeach
         </div>
         @else
         <div class="flex flex-col items-center my-auto gap-2 h-[calc(100vh_-_200px)] justify-center">
            {!! __i('--ie', 'eye-hidden', 'w-10 h-10') !!}
            <div class="flex flex-col">
            <p class="text-color-descriptive text-center w-full">{{ __('Looks like you don\'t have any lessons...') }}</p>
            </div>
            <a class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'course-lesson-create-modal')">{{ __('Create lesson') }}</a>
         </div>
         @endif
      </div>

      <div wire:ignore>
         <template x-for="(item, index) in lessons" :key="index">
            <template x-cloak x-if="__page == 'edit::' + item.id">
               <div x-bit="'lesson-section-' + item.lesson_type"></div>
            </template>
         </template>
      </div>

      @php 
         $components = \Storage::disk('components')->files('console/courses/lessons/sections');
      @endphp

      <div wire:ignore>
         @foreach ($components as $key => $item)
            @php
               $file = Str::before($item, '.blade.php');
               $name = basename($file);
               $component = "livewire::components.console.courses.lessons.sections.$name";
            @endphp
            <template bit-component="lesson-section-{{ $name }}">
               <form class="flex flex-col" @submit.prevent="editLesson(item)">
                   <div class="flex justify-between items-center">
                       <div class="bg-white mb-2 w-10 h-10 rounded-lg flex items-center justify-center ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] cursor-pointer" @click="__page='-'">
                           <i class="ph ph-caret-left text-lg"></i>
                       </div>
                       <div>
                           <p class="font-bold">{{ __('Edit Lesson') }}</p>
                       </div>
                   </div>
                   <div class="form-input !bg-transparent">
                      <label>{{ __('Name of Lesson') }}</label>
                      <input type="text" name="name" x-model="item.name">
                   </div>
                   <div class="form-input mt-4 !bg-transparent">
                      <label>{{ __('Description') }}</label>
                      <textarea name="description" x-model="item.description"></textarea>
                   </div>
                   <div class="form-input my-4 !bg-transparent">
                      <label>{{ __('Lesson duration') }}</label>
                      <input type="text" name="duration" x-model="item.lesson_duration">
                   </div>
   
                   <x-dynamic-component :component="$component"/>
   
                   <button class="yena-button-stack !w-full mt-4" type="submit">{{ __('Save') }}</button>
               </form>
            </template>
         @endforeach
      </div>


      
      {{-- @foreach (Storage:: as $item)
          
      @endforeach --}}
      {{-- @foreach ($lessons as $item)
        <div x-cloak x-show="__page == 'edit::{{ $item->id }}'">
            <form class="flex flex-col" wire:submit.prevent="edit({{ $item->id }})">
                <div class="flex justify-between items-center">
                    <div class="bg-white mb-2 w-10 h-10 rounded-lg flex items-center justify-center ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] cursor-pointer" @click="__page='-'">
                        <i class="ph ph-caret-left text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold">{{ __('Edit Service') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div class="form-input !bg-transparent">
                        <label>{{ __('Service Name') }}</label>
                        <input type="text" wire:model="services.{{ $index }}.name">
                    </div>
                    <div class="form-input !bg-transparent">
                        <label>{{ __('Duration (minutes)') }}</label>
                        <input type="text" wire:model="services.{{ $index }}.duration">
                    </div>
                    <div class="form-input !bg-transparent">
                        <label>{{ __('Service Price') }}</label>
                        <input type="text" wire:model="services.{{ $index }}.price">
                    </div>
                </div>

                <button class="yena-button-stack !w-full mt-4" type="submit">{{ __('Save') }}</button>
            </form>
        </div>
      @foreach --}}




    
      <template x-teleport="body">
         <x-modal name="course-lesson-create-modal" :show="false" removeoverflow="true" maxWidth="xl" >
            <livewire:components.console.courses.lessons.create :$course :key="uukey('app', 'console.courses.lessons.create')">
         </x-modal>
      </template>
    </div>
   @script
   <script>
       Alpine.data('app_course_lession', () => {
          return {
            lessons: @entangle('lessonsArray'),
            __page: '-',
            editLesson(data){
               let $this = this;

               // console.log(this.lessons)
               // console.log(data, data.lesson_data, JSON.parse(JSON.stringify(data)))
               $this.$wire.editLesson(data).then(r => {
                  $this.__page = '-';
               });
            },
            init(){
               var $this = this;
            }
          }
       });
   </script>
   @endscript
</div>