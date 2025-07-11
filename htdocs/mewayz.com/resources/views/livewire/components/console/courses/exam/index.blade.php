<?php
    use App\Models\CoursesPerformanceExam;
    use App\Models\CoursesPerformanceExamAnswer;
    use App\Models\CoursesPerformanceExamQuestion;
    use function Livewire\Volt\{state, mount, rules};

    state([
        'user' => fn() => iam(), 
        'blocks' => null,
        'thumbnail' => null,
        'user_id' => null,
        'exam' => null,
        'examodel' => [],
        'question_name' => '',
    ]);

    mount(function(){
      $this->refresh_exam();
    });

    rules(fn() => [
        'exam.*.name' => 'string',
        'exam.*.level' => 'string',

        'exam.*.questions.*.name' => 'string',
        'exam.*.questions.*.answers.*.name' => 'string',
    ]);


    $refresh_exam = function() {
        $this->exam = CoursesPerformanceExam::where('user_id', $this->user->id)
                    ->orderBy('id', 'DESC')
                    ->get();
    };

    $add_question = function($exam_id) {
        if (CoursesPerformanceExamQuestion::where('user_id', $this->user->id)->count() > 14) return false;

        $new = new CoursesPerformanceExamQuestion;
        $new->user_id = $this->user->id;
        $new->exam_id = $exam_id;
        $new->name = $this->question_name;
        $new->save();

        for ($i = 1; $i < 5; $i++) {
            $name = "Answer Option 0$i";
            $n = new CoursesPerformanceExamAnswer;
            $n->user_id = $this->user->id;
            $n->exam_id = $exam_id;
            $n->question_id = $new->id;
            $n->name = $name;
            $n->save();
        }

        $this->question_name = '';
        $this->refresh_exam();
    };

    $remove_question = function($question_id) {
        if (!$question = CoursesPerformanceExamQuestion::where('user_id', $this->user->id)->where('id', $question_id)->first()) return false;

        CoursesPerformanceExamAnswer::where('question_id', $question->id)->delete();

        $question->delete();
        $this->refresh_exam();
    };

    $set_active = function($answer_id) {
        if (!$answer = CoursesPerformanceExamAnswer::where('user_id', $this->user->id)->where('id', $answer_id)->first()) return false;

        CoursesPerformanceExamAnswer::where('question_id', $answer->question_id)->update(['is_correct' => 0]);

        $answer->is_correct = 1;
        $answer->update();

        $this->refresh_exam();
    };

    $remove_exam = function($exam_id) {
        if (!$exam = CoursesPerformanceExam::where('user_id', $this->user->id)->where('id', $exam_id)->first()) return false;

        CoursesPerformanceExamAnswer::where('exam_id', $exam->id)->delete();
        CoursesPerformanceExamQuestion::where('exam_id', $exam->id)->delete();

        $exam->delete();
        $this->refresh_exam();
    };

    $add_exam = function() {
        $new = new CoursesPerformanceExam;
        $new->user_id = $this->user->id;
        $new->name = "New Exam";
        $new->save();

        $this->refresh_exam();
    };

    $_post = function() {
        foreach ($this->exam as $item) {
            $item->save();

            foreach ($item->questions as $question) {
                $question->save();

                foreach ($question->answers as $answer) {
                    $answer->save();
                }
            }
        }
        $this->refresh_exam();
    };
?>
<div>
    <div class="w-full" x-data="_exam_data">
        <div class="flex flex-col">
           <a x-on:click="$dispatch('close')" class="absolute appearance-none select-none top-3 right-3 p-2 text-foreground-500 rounded-full hover:bg-default-100 active:bg-default-200 tap-highlight-transparent outline-none data-[focus-visible=true]:z-10 data-[focus-visible=true]:outline-2 data-[focus-visible=true]:outline-focus data-[focus-visible=true]:outline-offset-2 cursor-pointer">
              <i class="fi fi-rr-cross text-sm"></i>
           </a>
     
           <header class="flex-[0_1_0%] py-4 text-3xl px-6 font-extrabold tracking-[-1px]">{{ __('Exam') }}</header>
     
           <hr class="yena-divider">
     
           <form wire:submit="_post" class="px-8 pt-2 pb-6">
                <a wire:click="add_exam" class="yena-button-stack w-full flex items-center gap-2">
                    <i class="fi fi-rr-plus text-gray-400 text-xs font-bold"></i> {{ __('Add New Exam') }}
                </a>
     

                <div class="max-h-[calc(100vh_-_400px)] overflow-y-auto koon-white-card {{ $exam->isEmpty() ? 'hidden' : '' }}" >
                    <div class="flex hidden">
                        <div class="grid grid-cols-3 gap-2 mr-2">
                            <div class="form-input w-full">
                                <input type="text" class="border-dashed" wire:model="name" placeholder="{{ __('Exam Name') }}">
                            </div>
                            <div class="form-input w-full">
                                <input type="text" class="border-dashed" wire:model="price" placeholder="{{ __('Exam Level') }}">
                            </div>
                        </div>
            
                        <div class="flex items-center">
                            <a class="ml-auto my-auto bg-g-gray w-8 h-8 flex items-center justify-center rounded-full cursor-pointer">
                                {!! __icon('--ie', 'eye-show-visible', 'w-5 h-5') !!}
                            </a>
                        </div>
                    </div>
            
                    <div class="mt-3 flex flex-col gap-3">
                        @foreach ($exam as $exami => $item)
                        <div class="flex">
                            <div class="grid grid-cols-2 w-full gap-2 mr-2">
                                <div class="form-input">
                                    <input type="text" class="" wire:model="exam.{{ $exami }}.name" placeholder="{{ __('Exam Name') }}">
                                </div>
                                <div class="form-input">
        
                                    <select name="level" wire:model="exam.{{ $exami }}.level" id="">
                                        <option value="">{{ __('Exam Level') }}</option>
                                        <option value="beginner">{{ __('Beginner') }}</option>
                                        <option value="advanced">{{ __('Advanced') }}</option>
                                        <option value="intermediate">{{ __('Intermediate') }}</option>
                                    </select>
                                </div>
                            </div>
                
                            <div class="flex items-center">
                                <a class="ml-auto my-auto bg-white w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" @click="!shown_exam[{{ $item->id }}] ? shown_exam[{{ $item->id }}] = true : shown_exam[{{ $item->id }}] = false">
                                    {!! __icon('--ie', 'eye-show-visible', 'w-5 h-5') !!}
                                </a>
                            </div>
                        </div>
        
        
                        <div class="exam-questions" :class="shown_exam[{{ $item->id }}] ? 'hidden' : ''">
                            <div class="flex gap-2">
                                <div class="form-input w-full has-fancy-r">
                                    <input type="text" class="border-dashed" wire:model="question_name" placeholder="{{ __('Add New Question') }}">
        
                                    <div class="fancy-r text-xs pr-2">
                                        15/{{ $item->questions->count() }}
                                    </div>
                                </div>
                    
                                <div class="flex items-center">
                                    <a class="ml-auto my-auto bg-g-gray w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" wire:click="add_question({{ $item->id }})">
                                        <i class="fi fi-rr-plus text-gray-400 text-xs font-bold"></i>
                                    </a>
                                </div>
                            </div>
        
                            @foreach ($item->questions as $qi => $question)
                                
                                <div class="flex gap-2 mt-3">
                                    <div class="form-input w-full">
                                        <input type="text" wire:model="exam.{{ $exami }}.questions.{{ $qi }}.name" placeholder="{{ __('Add New Question') }}">
                                    </div>
                        
                                    <div class="flex items-center">
                                        <a class="ml-auto my-auto bg-g-gray w-8 h-8 flex items-center justify-center rounded-full cursor-pointer" wire:click="remove_question({{ $question->id }})">
                                            {!! __i('interface-essential', 'trash-delete-remove', 'w-4 h-4 text-red-400') !!}
                                        </a>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 mr-2 mt-3">
                                    @foreach ($question->answers as $ai => $answer)
                                    <div class="form-input w-full has-fancy-r">
                                        <input type="text" class="{{ $answer->is_correct ? 'border-black' : '' }}" wire:model="exam.{{ $exami }}.questions.{{ $qi }}.answers.{{ $ai }}.name" placeholder="{{ __('Answer Option') }}">
        
                                        <div class="fancy-r mr-1">
                                            @if ($answer->is_correct)
                                            <div class="-fancy-rw bg-green-400 flex items-center justify-center rounded-full">
                                                {!! __i('Smileys', 'Smileys.5', 'w-6 h-6 text-white') !!}
                                            </div>
                                            @else
                                            
                                            <div class="-fancy-rw bg-white flex items-center justify-center rounded-full cursor-pointer" wire:click="set_active({{ $answer->id }})">
                                                {!! __i('interface-essential', 'checkmark-done-check-circle', 'w-5 h-5 text-black') !!}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @endforeach
        
                            <a wire:click="remove_exam({{ $item->id }})" class="koon-gray-title mt-3 py-3 flex w-full justify-center items-center bg-red-500 cursor-pointer rounded-lg" wire:loading.class="disabled"><div class="-title text-white">{{ __('Delete Exam') }}</div></a>
                        </div>
                        
                        @endforeach
                    </div>
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
     @script
     <script>
         Alpine.data('_exam_data', () => {
            return {
                shown_exam: {},
                init(){
                    var $this = this;
                }
            }
         });
     </script>
     @endscript
</div>