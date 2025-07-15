<?php
    use App\Models\Course;
    use App\Models\CoursesPerformanceExam;
    use App\Models\CoursesOrder;
    use App\Models\CoursesEnrollment;
    use function Livewire\Volt\{state, mount, on};


    on([
        'coursesRefresh' => fn() => $this->getCourses(),
    ]);

    state([
        'courses' => [],
        'user' => fn() => iam(), 
    ]);

    mount(function(){
        $this->getCourses();
    });


    $getCourses = function(){
      $this->courses = Course::where('user_id', iam()->id)->orderBy('position', 'ASC')->orderBy('id', 'DESC')->get();
    };

    $sort = function($list) {
       $this->skipRender();

       foreach ($list as $key => $value) {
             $value['value'] = (int) $value['value'];
             $value['order'] = (int) $value['order'];
             $update = Course::find($value['value']);
             $update->position = $value['order'];
             $update->save();
       }
       
       $this->getCourses();
    };

    $getAnalytics = function(){
        $exams = CoursesPerformanceExam::where('user_id', $this->user->id)->count();
        $enrollments = CoursesEnrollment::where('user_id', $this->user->id)->count();
        $courses = Course::where('user_id', $this->user->id)->count();
        $earned = CoursesOrder::where('user_id', $this->user->id)->sum('price');

        return [
            'courses' => $courses,
            'exams' => $exams,
            'earned' => iam()->price($earned),
            'enrollments' => $enrollments,
        ];
    };
?>

<div>
    
    <div x-data="app_course">
         <div class="banner">
            <div class="banner__container !bg-white">
              <div class="banner__preview">
                {!! __icon('Content Edit', 'Book, Open.4') !!}
              </div>
              <div class="banner__wrap">
                <div class="banner__title h3 !text-black">{{ __('Your Courses') }}</div>
                <div class="banner__text !text-black">{{ __('Design and Develop Your Own Courses and Lessons') }}</div>
                
                <div class="mt-7 grid grid-cols-2 gap-1 lg:grid-cols-4">
                    <div>
                     <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                        <div class="detail text-gray-600">{{ __('Earned') }}</div>
                        <template x-if="analyticsLoading">
                            <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                        </template>
                        <template x-if="!analyticsLoading">
                            <div class="number-secondary" x-html="analytics.earned"></div>
                        </template>
                     </div>
                    </div>
                    <div>
                     <div class="rounded-8 p-2 text-center bg-[#f7f3f2] text-black">
                        <div class="detail text-gray-600">{{ __('Courses') }}</div>
                        <template x-if="analyticsLoading">
                            <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                        </template>
                        <template x-if="!analyticsLoading">
                            <div class="number-secondary" x-text="analytics.courses"></div>
                        </template>
                     </div>
                    </div>
                    <div>
                     <div class="rounded-8 p-2 text-center bg-[#f7f3f2] text-black">
                        <div class="detail text-gray-600">{{ __('Enrollments') }}</div>
                        <template x-if="analyticsLoading">
                            <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                        </template>
                        <template x-if="!analyticsLoading">
                            <div class="number-secondary" x-text="analytics.enrollments"></div>
                        </template>
                     </div>
                    </div>
                    <div>
                     <div class="rounded-8 p-2 text-center bg-[#f7f3f2] text-black">
                        <div class="detail text-gray-600">{{ __('Total Exams') }}</div>
                        <template x-if="analyticsLoading">
                            <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                        </template>
                        <template x-if="!analyticsLoading">
                            <div class="number-secondary" x-text="analytics.exams"></div>
                        </template>
                     </div>
                    </div>
                </div>
                <div class="mt-3 flex gap-2">
                    <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-courses-modal')">{{ __('Create Course') }}</button>
                    <a class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'exam-courses-modal');">{{ __('Exam') }}</a>
                </div>
              </div>
            </div>
          </div>
          <div class="cri">
            <div class="catalog">
               <div class="catalog__wrapper !p-0">
                  <div class="catalog__head">
                     <div class="catalog__title h3">{{ __('Courses') }}</div>
                     <!-- tabs-->
                     {{-- <button class="catalog__toggle !ml-auto">
                        <svg class="icon icon-filter-1">
                           <use xlink:href="#icon-filter-1"></use>
                        </svg>
                        <svg class="icon icon-close">
                           <use xlink:href="#icon-close"></use>
                        </svg>
                     </button> --}}
                  </div>
                  <!-- filters-->
                  <div class="catalog__btns !hidden">
                     <button class="button-stroke button-medium catalog__button">load more</button>
                  </div>
               </div>
            </div>
         </div>
            
        @if (!$courses->isEmpty())
        <div>
            {{-- <form class="w-full flex items-centers mb-5 gap-4">
                <div class="search-filter w-full">
                    <input class="-search-input" type="text" name="query" value="{{ request()->get('query') }}" placeholder="{{ __('Search') }}">
                </div>
            </form> --}}


            <div>
                <div>
                    
                    <div class="grid grid-cols-1 sm:!grid-cols-2 md:!grid-cols-3 lg:!grid-cols-4 gap-4 sortable" wire:sortable="sort">
                        @foreach ($courses as $item)
                        <div wire:sortable.item="{{ $item->id }}"
                            wire:key="courses-items-{{ $item->id }}">
                            @php
                                $route = $item->slug ? route('out-courses-page', ['slug' => $item->slug]) : '';
                            @endphp
                            <div class="yena-card-g sortable-item" x-data="{is_delete:false, share: false}">
                                <div class="card-button p-2 flex gap-2" x-cloak @click="$event.stopPropagation();" :class="{
                                    '!hidden': !is_delete
                                }">
                                    <button class="btn btn-medium neutral !h-8 !flex !items-center !justify-center !rounded-full !text-black !bg-white" type="button" @click="is_delete=!is_delete">{{ __('Cancel') }}</button>
                
                                    <button class="btn btn-medium !bg-[var(--c-red)] !text-[var(--c-light)] !h-8 !flex !items-center !justify-center !rounded-full w-full" @click="$wire.deleteCourse('{{ $item->id }}'); is_delete=false;">{{ __('Yes, Delete') }}</button>
                                </div>
                                <div class="-thumbnail p-0 rounded-xl">
                                    @php
                                        $banner = $item->banner;
                                    @endphp
                                    <img src="{{ gs('media/courses/image', $banner) }}" alt="">
                                    <div class="-overlay-cog flex flex-col gap-1">
                                        <button @click="$event.stopPropagation(); is_delete=true;" type="button" class="bg-white w-8 h-8 flex items-center justify-center rounded-md cursor-pointer [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)]">
                                            {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                                        </button>
                                        <a class="bg-white w-8 h-8 flex items-center justify-center rounded-md cursor-pointer [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)]" href="{{ route('console-courses-editor', ['id' => $item->id]) }}" @navigate>
                                            {!! __i('interface-essential', 'pen-edit.5', 'w-4 h-4') !!}
                                        </a>
                                        {{-- <a class="bg-white w-8 h-8 flex items-center justify-center rounded-md cursor-pointer [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)]">
                                            {!! __i('interface-essential', 'arrows-resize', 'w-4 h-4') !!}
                                        </a> --}}
                                    </div>
                                </div>

                                <div class="-content">
                                    
                                    <div class="-card-line flex-col">
                                        <div class="--title mb-1 truncate">{{ $item->name }}</div>
                                        <div class="--price-wrap mb-2">
                                            <span class="--price">
                                                {!! iam()->price($item->price) !!}
                                            </span>

                                            @if (!empty($compare_price = $item->comparePrice))
                                            <div class="--del-price">
                                                {!! iam()->price($compare_price) !!}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="--footer block m-0 md:flex">
                                        <span class="block mb-0 product-type text-black font-bold">
                                            {{ $item->lessons->count() }} {{ __('Lessons') }}
                                        </span>
                                    </div>
                                    <a class="sandy-button !bg-black py-2 flex-grow w-[100%] flex justify-center items-center !text-white rounded-lg mt-1" @click="$event.stopPropagation(); share=!share">
                                        <div class="--sandy-button-container">
                                            <span class="text-xs">{{ __('Share') }}</span>
                                        </div>
                                    </a>
                                    
                                    <div class="card-button mt-2 flex gap-2" x-cloak @click="$event.stopPropagation();" :class="{
                                    '!hidden': !share
                                    }">
                                        <div class="relative flex w-[100%] isolate gap-2">
                                            <input type="text" class="w-[100%] h-[3rem] text-[1rem] pl-4 pr-10 rounded-md min-w-0 outline-[transparent_solid_2px] outline-offset-[2px] relative appearance-none duration-200 border-2 border-solid [border-image:initial] border-transparent bg-[#f7f3f2] !text-black" placeholder="{{ __('link goes here...') }}" readonly value="{{ $route }}">
                            
                                            <div class="right-0 h-[3rem] text-[1rem] flex items-center justify-center absolute top-0 z-[1]">
                                                <button type="button" class="inline-flex appearance-none items-center justify-center select-none relative whitespace-nowrap align-middle outline-[transparent_solid_2px] outline-offset-[2px] leading-[1.2] rounded-md font-semibold [transition-property:background-color,border-color,color,fill,stroke,opacity,box-shadow,transform] duration-200 h-[2rem] [2.5rem] text-[1rem] pl-4 pr-4 text-[#3c3838] [box-shadow:0_4px_6px_-1px_rgba(0,_0,_0,_0.1),_0_2px_4px_-1px_rgba(0,_0,_0,_0.06)] bg-[linear-gradient(180deg,_#FFFFFF_0%,_#FCF9F5_100%)] border border-solid border-[#e5e0df] w-[100%] m-2" @click="$clipboard('{{ $route }}'); $el.innerText = window.builderObject.copiedText;">{{ __('Copy Link') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="">
            <div class="flex flex-col justify-center items-start px-0 py-[60px]">
               {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
               <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                  {!! __t('You have no course. <br> Create a course to get started.') !!}
               </p>
            </div>
        </div>
        @endif
          
    
    
          
          {{-- <x-modal name="store-settings-modal" :show="false" removeoverflow="true" maxWidth="3xl">
            <livewire:components.console.store.settings :key="uukey('app', 'store-settings')">
         </x-modal>

         <x-modal name="store-shipping-modal" :show="false" removeoverflow="true" maxWidth="3xl">
           <livewire:components.console.store.shipping :key="uukey('app', 'store-settings-shipping')">
        </x-modal> --}}
    
        <template x-teleport="body">
           <x-modal name="create-courses-modal" :show="false" removeoverflow="true" maxWidth="xl" >
              <livewire:components.console.courses.create.index :key="uukey('app', 'courses-page-create')">
           </x-modal>
        </template>
    
        <template x-teleport="body">
           <x-modal name="exam-courses-modal" :show="false" removeoverflow="true" maxWidth="3xl" >
              <livewire:components.console.courses.exam.index :key="uukey('app', 'courses-page-exam')">
           </x-modal>
        </template>

        {{-- <template x-teleport="body">
           <div class="[&_.x-modal-body]:m-0 [&_.x-modal-body]:ml-auto [&_.x-modal-body]:mr-0 [&_.x-modal-body]:h-full [&_.fixed]:!p-0 [&_.x-modal-body]:!rounded-none [&_.x-modal-body]:overflow-auto">
              <x-modal name="edit-store-modal" :show="false" removeoverflow="true" maxWidth="xl" >
                 <livewire:components.console.store.edit-modal :key="uukey('app', 'store-page-edit')">
              </x-modal>
           </div>
        </template> --}}
    </div>
   @script
   <script>
       Alpine.data('app_course', () => {
          return {
            analyticsLoading: true,
            analytics: {
              earned: '$0',
              enrollments: '0',
              courses: '0',
              exams: '0',
            },

            init(){
              let $this = this;
              $this.$wire.getAnalytics().then(r => {
                $this.analytics = r;
                $this.analyticsLoading = false;
              });
              document.addEventListener('alpine:navigatedComplete', (e) => {
                // $this.$wire.getCourses();
              });
            },
          }
       });
   </script>
   @endscript
</div>