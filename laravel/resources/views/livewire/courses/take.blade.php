<?php
    use App\Traits\LessionInfoTrait;
    use App\Livewire\Actions\ToastUp;
    use App\Models\CoursesLesson;
    use App\Models\CoursesPerformanceExam;
    use function Livewire\Volt\{state, mount, uses};

    state([
        'course'
    ]);

    state([
        'lesson_skeleton' => fn () => $this->lesson_skeleton(),
        'page' => '-',
    ]);

    uses([
        ToastUp::class,
        LessionInfoTrait::class,
    ]);

    state([
        'lessons' => [],
        'exam' => [],
        'hasEnroll' => function(){
            if(!$user = auth()->user()) return false;
            return $this->course->has_enroll($user->id);
        },
    ]);

    mount(function(){
        if(!auth()->check()){
            return redirect(route('login'))->with('error', __('Please login to proceed.'));
        }

        if(!$this->hasEnroll){
            return redirect(route('out-courses-page', ['slug' => $this->course->slug]))->with('error', __('Please purchase this course to proceed.'));
        }


        $this->_get();

        if($firstLesson = $this->lessons->first()){
            $this->page = 'lesson::' . $firstLesson->id;
        }
    });

    $_get = function(){
        $this->lessons = CoursesLesson::where('course_id', $this->course->id)->where('user_id', $this->course->user_id)->orderBy('position', 'ASC')->orderBy('id', 'ASC')->get();
        $this->exam = CoursesPerformanceExam::where('id', ao($this->course->settings, 'exam_id'))->where('user_id', $this->course->user_id)->first();
    };
?>
<div>
    <div x-data="take_course">
        @php
            $has_exam = ao($course->settings, 'enable_exam') && $exam;
        @endphp
        <div class="rbt-lesson-content-wrapper sandy-tabs">
            <div class="rbt-lesson-leftsidebar !bg-white" :class="{'hidden': !show_sidebar}">
            <div class="rbt-course-feature-inner rbt-search-activation p-5">
                <div class="flex-col sm:!flex-row mb-2 !bg-[#f7f3f2] p-2 !rounded-lg">
                    <div class="flex items-center">
                        <div class="-icon p-2 h-10 w-10 flex-none ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !rounded-lg">
                            {!! __i('Content Edit', 'Book, Open.4') !!}
                        </div>
            
                        <div class="-content ml-2">
                            <p class="--title !my-auto whitespace-nowrap text-sm font-bold">{{ __('Take Course') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="search-filter w-[100%]">
                        <input class="-search-input" type="text" name="query" x-model="searchQuery" x-on:input="filterSocials()" placeholder="{{ __('Search') }}">

                        <a class="-filter-btn">
                            {{-- {!! __ion('search-1', 'w-5 h-5') !!} --}}
                        </a>
                    </div>
                </div>
                <hr class="mt--10">
                <div class="px-5">
                    <div class="border-l-2 page-section pl-8 pt-5 flex flex-col gap-4">
                        @foreach ($lessons as $item)
                        <div class="flex items-center page-num-container -ml-12 w-[100%] filter-item {{ $loop->first ? 'active' : '' }}" data-filter-name="{{ strtolower($item->name) }}" @click="page='lesson::{{ $item->id }}'">
                            <div>
                                <div class="page-num ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)] !rounded-md">
                                    {!! __i(ao($lesson_skeleton, "$item->lesson_type.svg_folder"), ao($lesson_skeleton, "$item->lesson_type.svg_file"), 'w-5 h-5 text-black') !!}
                                </div>
                            </div>
                            <div class="ml-3 w-[100%]">
                                
                                <li class="flex w-[100%] items-center text-gray-500 p-2 drop-shadow-md filter bg-white text-xs ml-300 zmb-3 cursor-pointer">
                                    <div class="mr-2 hidden">
                                        {!! __i(ao($lesson_skeleton, "$item->lesson_type.svg_folder"), ao($lesson_skeleton, "$item->lesson_type.svg_file"), 'w-5 h-5') !!}
                                    </div>

                                    <a class="flex">{{ $item->name }}</a>
                                    <span class="text-muted ml-auto text-xs">{{ $item->lesson_duration }}</span>
                                </li>
                                <p class="text-xs mb-24pt hidden" style="">{{ $item->description }}</p>
                            </div>
                        </div>
                        @endforeach

                        @if ($has_exam)
                            <div class="hidden sandy-tabs-link exam-button-tabs"></div>
                        @endif

                    </div>
                </div>
                
                @if ($has_exam)
                <div class="pt-5 mt-5 border-t-2 border-gray-200 border-solid">
                    <div class="flex gap-2 mb-3 items-center">
                    <p class="text-lg font-semibold">{{ __('Exam') }}</p>
                    <div>
                        <a>
                            <div type="button" class="inline-flex items-center px-2 py-1 !bg-[#f7f3f2] text-xs font-medium rounded-full shadow-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {!! __i('Design Tools', 'Pencil, Edit, Create.1', 'w-4 h-4 mr-1') !!} {{ __(':count questions available', ['count' => $exam->questions()->count()]) }}
                            </div>
                        </a>
                    </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                    <div class="">
                        <p class="text-gray-500 text-xs lg:text-sm mb-0">{{ __('Take our performance exam to check how well you understand this course. ✨') }}</p>
                    </div>
                    <div class="col-span-2">
                        <a class="sandy-button !bg-black py-2 flex-grow w-[100%] flex justify-center items-center !text-white" @click="page='exam'">
                            <div class="--sandy-button-container">
                                <span class="text-xs">{{ __('Take Exam') }}</span>
                            </div>
                        </a>
                    </div>
                    </div>
                </div>
                @endif
            </div>
            </div>
            <div class="rbt-lesson-rightsidebar overflow-hidden">
                @foreach ($lessons as $index => $item)
                    @php
                        $previous = $index > 0 ? $lessons[$index - 1] : null;
                        $next = $index < count($lessons) - 1 ? $lessons[$index + 1] : null;
                    @endphp
                    <div class="sandy-tabs-item" x-cloak x-show="page == 'lesson::{{ $item->id }}'">
                        <div class="lesson-top-bar">
                        <div class="lesson-top-left">
                            <div class="rbt-lesson-toggle">
                                <a class="lesson-toggle-active btn-round-white-opacity flex items-center justify-center cursor-pointer" @click="show_sidebar=!show_sidebar">
                                    <i class="fi fi-rr-angle-left"></i>
                                </a>
                            </div>
                            <h5>{{ $item->name }}</h5>
                        </div>
                        <div class="lesson-top-right">
                            <div class="rbt-btn-close">
                                <a href="{{ route('out-courses-page', ['slug' => $course->slug]) }}" class="rbt-round-btn !flex !items-center !justify-center">
                                    <i class="fi fi-rr-cross"></i>
                                </a>
                            </div>
                        </div>
                        </div>
                        
                    <hr class="mb-5">
                    <div class="inner pt-0">
                        <div class="content">
                            <div class="icon mb-2">
                                {!! __i(ao($lesson_skeleton, "$item->lesson_type.svg_folder"), ao($lesson_skeleton, "$item->lesson_type.svg_file"), 'w-8 h-8') !!}
                            </div>
                            <p class="duration text-gray-400 text-sm mb-8">{{ $item->lesson_duration }}</p>
                            <div class="text-base mb-10" style="
                            ">
                                <div class="details__text">
                                    {{ $item->description }}
                                </div>
                            </div>
                            @includeIf("Blocks-course::page.lesson.$item->lesson_type", ['course' => $course, 'lesson' => $item])
                        </div>
                    </div>
                    <div class="exam-fixed-screen">
                        
                        <div class="w-full mt-5 px-2 flex items-center justify-between !bg-[#f7f3f2] py-2 rounded-lg">
                            <div class="text-sm pl-2">
                                {{__('Lesson')}} <span>{{ $index + 1 }}</span>/<span>{{ $lessons->count() }}</span>
                            </div>
                            <div>
    
                                @if ($next)
                                    <a class="yena-button-stack" @click="page='lesson::{{ $next->id }}'">
                                        <span class="text-xs">{{ __('Next') }}</span>
                                    </a>
                                    @else
                                    <div class="text-sm pl-2">
                                        {{__('End')}}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if ($has_exam)
                <div class="sandy-tabs-item" x-cloak x-show="page == 'exam'">
                    <div class="lesson-top-bar">
                    <div class="lesson-top-left">
                        <div class="rbt-lesson-toggle">
                            <a class="lesson-toggle-active btn-round-white-opacity flex items-center justify-center cursor-pointer" @click="show_sidebar=!show_sidebar">
                                <i class="fi fi-rr-angle-left"></i>
                            </a>
                        </div>
                        <h5>{{ __('Take exam') }}</h5>
                    </div>
                    <div class="lesson-top-right">
                        <div class="rbt-btn-close">
                            <a href="{{ route('out-courses-page', ['slug' => $course->slug]) }}" class="rbt-round-btn !flex !items-center !justify-center">
                                <i class="fi fi-rr-cross"></i>
                            </a>
                        </div>
                    </div>
                    </div>
                    
                    <div class="inner pt-0">
                        <div class="content">
                            <livewire:courses.exam zzlazy :$course :exam_id="$exam->id" :key="uukey('app', 'components.courses.exam')"/>
                            {{-- @livewire('take-exam-livewire', ['page' => $_user, 'exam_id' => $exam->id, 'course' => $course]) --}}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @script
    <script>
        Alpine.data('take_course', () => {
           return {
                page: @entangle('page'),
                show_sidebar: true,
                searchQuery: '',
                filterSocials(){
                    var __ = this;
                    var items = this.$root.querySelectorAll('.filter-item');
                    var searchQuery = this.searchQuery.toLowerCase();
                    items.forEach(item => {
                        var _name = item.getAttribute('data-filter-name');
                        
                        if (_name.indexOf(searchQuery) == -1) {
                            item.classList.add('hidden');
                        }else { item.classList.remove('hidden') }
                    });
                },
            }
        });
    </script>
    @endscript
</div>