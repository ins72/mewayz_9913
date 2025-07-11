<?php
    use App\Models\CoursesPerformanceExam;
    use App\Models\CoursesPerformanceTakeDb;
    use App\Models\CoursesPerformanceExamAnswer;
    use App\Models\CoursesPerformanceExamQuestion;
    use App\Models\CoursesPerformanceTakenCompleted;
    use function Livewire\Volt\{state, mount};

    state([
        'course'
    ]);

    state([
        'exam_id',
        'page',
        'course',
        'is_completed' => false,
        'selected_answer',
        'exam',
        'total_questions_count',
        'current_question_count' => 1,
        'questions',
        'selected_answer_array' => [],
        'showPage' => 0,
        'error_page' => []
    ]);

    mount(function(){
        $this->total_questions_count = CoursesPerformanceExamQuestion::where('exam_id', $this->exam_id)->count();


        $this->refresh_exam();
    });

    $refresh_exam = function() {
        $this->exam = CoursesPerformanceExam::where('id', $this->exam_id)
            ->where('user_id', $this->course->user_id)->first();

        $this->questions = CoursesPerformanceExamQuestion::where('exam_id', $this->exam->id)
            ->orderBy('id', 'DESC')->get();
    };
    
    $next_question = function() {
        $this->questions = CoursesPerformanceExamQuestion::where('exam_id', $this->exam->id)
            ->orderBy('id', 'DESC')->get();
    };

    
    $_has_passed = function() {
        $user = auth()->user();
        if (CoursesPerformanceTakenCompleted::where('user_id', $user->id)
            ->where('page_id', $this->course->user_id)->where('exam_id', $this->exam->id)
            ->where('course_id', $this->course->id)->where('is_passed', 1)->first()) {
            return true;
        }
        return false;
    };
    
    $_has_taken = function() {
        $user = auth()->user();
        if (CoursesPerformanceTakenCompleted::where('user_id', $user->id)
            ->where('page_id', $this->course->user_id)->where('exam_id', $this->exam->id)
            ->where('course_id', $this->course->id)->where('is_passed', 1)->first()) {
            return true;
        }

        if (CoursesPerformanceTakenCompleted::where('user_id', $user->id)
            ->where('page_id', $this->course->user_id)->where('exam_id', $this->exam->id)
            ->where('course_id', $this->course->id)->where('is_passed', 0)->count() > 1) {
            return true;
        }

        return false;
    };

        
    $_store_answers = function($question, $selected_answer) {
        $user = auth()->user();
        if($this->_has_taken()) return false;

        if(CoursesPerformanceTakeDb::where('user_id', $user->id)
        ->where('question_id', $question->id)
        ->where('course_id', $this->course->id)
        ->first()) return false;

        // Store Selected answer

        if(!$answer = CoursesPerformanceExamAnswer::where('exam_id', $this->exam->id)->where('id', $selected_answer)->first()) return false;

        $new = new CoursesPerformanceTakeDb;
        $new->page_id = $this->course->user_id;
        $new->user_id = $user->id;
        $new->course_id = $this->course->id;
        $new->exam_id = $this->exam->id;
        $new->question_id = $question->id;
        $new->selected_answer = $answer->id;
        $new->selected_answer_name = $answer->name;
        $new->is_passed = $answer->is_correct;
        $new->save();
    };

    $_last_question = function() {
        $user = auth()->user();
        

        // Check condition
        if($this->_has_taken()) return false;

       // Check if total answers is ready for passed exam
       $count_take = CoursesPerformanceTakeDb::where('user_id', $user->id)
       ->where('page_id',$this->course->user_id)
       ->where('exam_id', $this->exam->id)
       ->where('course_id', $this->course->id)
       ->where('is_passed', 1)->count();
       $_pass = (int) ao($this->course->settings, 'questions_to_pass');

       // is Passed?
       $is_passed = 0;
       if($count_take >= $_pass) $is_passed = 1;

       $n = new CoursesPerformanceTakenCompleted;
       $n->page_id = $this->course->user_id;
       $n->user_id = $user->id;
       $n->exam_id = $this->exam->id;
       $n->course_id = $this->course->id;
       $n->is_passed = $is_passed;
       $n->save();
    };

    $_post = function() {
        $user = auth()->user();
        
        // Validate

        foreach($this->questions as $index => $question){
            if(!array_key_exists($question->id, $this->selected_answer_array)){
                $this->showPage = $index;

                $this->error_page[$index] = __('Please select an answer.');
                continue;
            }
        }

        if(!empty($this->error_page)){
            $this->showPage = key($this->error_page); return;
        }

        foreach($this->questions as $index => $question){
            $selected_answer = $this->selected_answer_array[$question->id];
            $this->_store_answers($question, $selected_answer);
        }

        $this->_last_question();
    };
?>
<div>
    <div x-data="_exam">
        @if ($this->_has_taken())
            <x-empty-state  :title="__('Exam completed')" :desc="__('Thank you for completing this performance exam.')" :svg="__i('Romance Wedding', 'smiley-heart', 'w-20 h-20')" />
        @endif

        <div class="{{ $this->_has_taken() ? 'hidden' : '' }}">
            <form wire:submit="_post" class="flex justify-center"> 
    
                @foreach ($questions as $index => $question)
                <div x-cloak x-show="showPage == {{ $index }}" class="max-w-full w-[720px]">
            
                    <div class="banner mb-2">
                        <div class="banner__container !bg-[#f7f3f2]">
                           <div class="banner__preview !right-0 !w-[300px] !top-[4rem]">
                              {!! __icon('School, Learning', 'laptop-book', '!text-black') !!}
                           </div>
                           <div class="banner__wrap z-[50]">
                              <div class="banner__title h3 !text-black text-center lg:!text-left">{{ $exam->name }}</div>
                              <div class="banner__text !text-black text-center lg:!text-left">{{ $question->name }}</div>
                           </div>
                        </div>
                     </div>
                    
                    
                    <div class="p-5 flex flex-col gap-4 !bg-[#f7f3f2] rounded-lg">
                        @foreach ($question->answers as $item)
                        <label class="sandy-big-checkbox">
                            <input type="radio" wire:model="selected_answer_array.{{ $question->id }}" class="sandy-input-inner" name="selected_answer" value="{{ $item->id }}">
                            <div class="checkbox-inner p-5 h-16 rounded-xl">
                                <div class="checkbox-wrap">
                                    <div class="icon">
                                        <div class="active-dot">
    
                                        </div>
                                        </div>
                                        <div class="content ml-2">
                                            <h1>{{ $item->name }}</h1>
                                        </div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
    
                    @if (array_key_exists($index, $error_page))
                    <div class="bg-red-200 font--11 mt-4 p-1 px-2 rounded-md">
                        <div class="flex items-center">
                            <div>
                                <i class="fi fi-rr-cross-circle flex"></i>
                            </div>
                            <div class="flex-grow ml-1">{{ $error_page[$index] }}</div>
                        </div>
                    </div>
                    @endif
    
                    <div class="exam-fixed-screen">
                        
                        <div class="w-full mt-5 px-2 flex items-center justify-between !bg-[#f7f3f2] py-2 rounded-lg">
                            <div class="text-sm pl-2">
                                Q <span x-text="showPage+1"></span>/<span>{{ $total_questions_count }}</span>
                            </div>
                            <div>
    
                                @if ($loop->last)
                                    <button class="yena-button-stack">
                                        <span class="text-xs">{{ __('Submit') }}</span>
                                    </button>
                                    @else
                                    <a class="yena-button-stack" @click="showPage++">
                                        <span class="text-xs">{{ __('Next') }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </form>
    
        </div>
    </div>
    @script
    <script>
        Alpine.data('_exam', () => {
           return {
                showPage: @entangle('showPage'),
            }
        });
    </script>
    @endscript
</div>