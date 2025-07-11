<?php
    use App\Models\Course;
    use App\Models\CoursesLesson;
    use App\Models\CoursesReview;
    use function Livewire\Volt\{state, mount};

    state([
        'course',
        'owner' => fn() => $this->course->user()->first(),
    ]);

    state([
        'lessons' => [],
        'reviews' => [],
        'hasEnroll' => function(){
            if(!$user = auth()->user()) return false;
            return $this->course->has_enroll($user->id);
        },

        'totalCourses' => function(){
            return Course::where('user_id', $this->owner->id)->count(); 
        },
        'totalAvgRating' => function(){
            return CoursesReview::where('user_id', $this->course->user_id)->avg('rating'); 
        },
        'totalReviews' => function(){
            return CoursesReview::where('user_id', $this->course->user_id)->count(); 
        },
    ]);
    
    state([
        'ratings' => 0,
        'rating' => 4,
        'review' => '',
    ]);

    mount(function(){
        $this->_get();
    });

    $_get = function(){
        $this->lessons = CoursesLesson::where('course_id', $this->course->id)->where('user_id', $this->course->user_id)->orderBy('position', 'ASC')->orderBy('id', 'ASC')->get();
        $this->reviews = CoursesReview::where('course_id', $this->course->id)->where('user_id', $this->course->user_id)->orderBy('id', 'DESC')->get();
        $this->ratings = CoursesReview::where('course_id', $this->course->id)->where('user_id', $this->course->user_id)->avg('rating') ?: 0;
    };

    $ratingsSummary = function(){
        $totalReviews = $this->reviews->count();
        
        return $this->reviews->groupBy('rating')->map(function ($group) use ($totalReviews) {
            return [
                'count' => $group->count(),
                'percentage' => ($totalReviews > 0) ? ($group->count() / $totalReviews) * 100 : 0
            ];
        })->sortKeysDesc();  
    };

    $leaveReview = function(){
        if (!$user = auth()->user()) {
            session()->flash('error._error', __('Login to continue'));
            return;
        }

        if (!$this->hasEnroll) {
            session()->flash('error._error', __('Please unlock this course to leave review'));
            return;
        }

        $this->validate([
            'review' => 'required|max:500'
        ]);

        if (CoursesReview::where('user_id', $this->course->user_id)->where('reviewer_id', $user->id)->where('course_id', $this->course->id)->first()) {
            session()->flash('error._error', __('Cannot repost another review'));
            return;
        }

        $review = new CoursesReview;
        $review->user_id = $this->course->user_id;
        $review->reviewer_id = $user->id;
        $review->course_id = $this->course->id;
        $review->rating = $this->rating;
        $review->review = $this->review;
        $review->save();
        

        $this->review = '';
        $this->_get();
    };

    $checkout = function(){
        if (!$auth = auth()->user()) {
            return redirect(route('login'))->with('error', __('Please login to proceed.'));
        }

        $item = [
            'name' => $this->course->name,
            'description' => __('Purchased a course on :page', ['page' => $this->owner->name]),
            'processing_description' => __('Purchasing ":course" on :page', ['page' => $this->owner->name, 'course' => $this->course->name]),
            'processed_description' => __('Just purchased <strong>:course</strong> for <strong>:amount</strong>', [
                'page' => $this->owner->name, 'course' => $this->course->name, 'amount' => $this->course->price
            ])
        ];
        
        $meta = [
            'user_id' => $this->owner->id,
            'course' => $this->course->id,
            'payee_id' => $auth->id,
            'item' => $item
        ];

        $data = [
            'uref'  => md5(microtime()),
            'email' => $auth->email,
            'price' => $this->course->price,
            'callback' => route('general-success', [
                'redirect' => route('out-courses-page', ['slug' => $this->course->slug])
            ]),
            'frequency' => 'monthly',
            'currency' => config('app.wallet.currency'),
            'payment_type' => 'onetime',
            'meta' => $meta,
        ];

        //
        
        $call_function = \App\Yena\SandyCheckout::course_function($this->owner);
        $call = \App\Yena\SandyCheckout::cr(config('app.wallet.defaultMethod'), $data, $call_function);
        
        return $this->js("window.location.replace('$call');");
    };
?>
<div>
    <div x-data="course_single">
        <div class="courses-wrapper-main">
            <section class="relative bg-black px-[0] py-[80px]">
            <div class="w-[100%] pr-[15px] pl-[15px] max-w-[1300px] mx-auto">
                <div class="mb-4 flex flex-col lg:!flex-row gap-4">
                    <div class="lg:!w-2/3 mb-4 lg:!mb-0">
                        <div class="wrapper__video relative mb-4 lg:!mb-0">
                            {{-- <img src="./../images/Button.png" class="play cursor-pointer" alt=""> --}}
                            <img src="{{ $course->_get_featured_image() }}" alt="" class="path__video-bg !object-cover rounded-2xl">

                            {{-- <video poster="https://sloosh-host.surge.sh/images/Rectangle%2036.png" class="path__video-bg" id="video__play" playsinline loop>
                                <source src="https://www.w3schools.com/tags/movie.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video> --}}
                        </div>
                        <div class="flex lg:!hidden flex-wrap justify-between">
                            <div class="mb-3 xl:mb-0">
                                <h4 class="bosl text-white text-[28px] lg:!text-[28px]">{{ $course->name }}</h4>
                                <div class="wrapper__list-info flex-wrap">
                                    <div class="items mb-3 sm:mb-0">
                                        <div class="rate [&_svg]:!h-[19px]">
                                            <div x-rating="ratings"></div>

                                            <span class="bosl text-[18px] lg:!text-[18px] text-white">({{ $ratings }})</span>
                                        </div>
                                    </div>
                                    @if($course->course_level)
                                    <div class="items mb-3 sm:mb-0">
                                        <span class="font-bold text-[16px] lg:!text-[16px] text-white">{{ ucfirst($course->course_level) }}</span>
                                    </div>
                                    @endif

                                    {{-- <div class="items mb-3 sm:mb-0"><span class="font-bold text-[16px] lg:!text-[16px] text-white tag">Design</span></div> --}}
                                    <div class="items mb-3 sm:mb-0"><span class="font-bold text-[16px] lg:!text-[16px] text-white">{{ __(':count Lessons', ['count' => $lessons->count()]) }}</span></div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <a href="#!" class="btn__action font-bold text-[16px] lg:!text-[16px] text-white ml-3">
                                    <img src="./../images/Frame (6).png" alt="">Share
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="lg:!w-1/3">
                        <div class="wrapper__playlist mb-3">
                            <div class="head">
                                <h4 class="font-bold text-[20px] lg:!text-[20px] text-white mb-0">{{ __('Lessons') }}</h4>
                            </div>
                            <hr class="my-0">
                            <div class="content">
                                <div class="scroll">
                                    @foreach ($lessons as $item)
                                    <div class="item">
                                        <h5 class="font-bold text-[16px] lg:!text-[16px] text-white">{{ $item->name }}</h5>
                                        <p class="mb-0 medium text-[14px] lg:!text-[14px] text-gray-300">{{ $item->lesson_duration }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @if (!$hasEnroll)
                        <a class="btn btn__white font-bold w-[100%] text-[16px] lg:!text-[16px] !text-black shadow" wire:click='checkout'>{!! __('Buy this course (:price)', ['price' => $owner->price($course->price)]) !!}</a>
                        @else
                        <a class="btn btn__white font-bold w-[100%] text-[16px] lg:!text-[16px] !text-black shadow" href="{{ route('out-courses-take', ['slug' => $course->slug]) }}">{!! __('Take Course') !!}</a>
                        @endif
                    </div>
                </div>
            

                <div class="hidden lg:!block">
                    <div class="md:!w-2/3">
                        <div class="flex flex-wrap justify-between">
                            <div class="mb-3 xl:mb-0">
                                <h4 class="bosl text-white text-[28px] lg:!text-[28px]">{{ $course->name }}</h4>
                                <div class="wrapper__list-info">
                                    <div class="items">
                                        <div class="rate [&_svg]:!h-[19px]">
                                            <div x-rating="ratings"></div>
                                            <span class="bosl text-[18px] lg:!text-[18px] text-white">({{ $ratings }})</span>
                                        </div>
                                    </div>
                                    @if (!empty($course->course_includes) && is_array($course->course_includes))
                                    @foreach ($course->course_includes as $item)
                                    <div class="items">
                                        <span class="font-bold text-[16px] lg:!text-[16px] text-white tag">{{ $item }}</span>
                                    </div>
                                    @endforeach
                                    @endif
                                    {{-- <div class="items"><span class="font-bold text-[16px] lg:!text-[16px] text-white tag">Design</span></div> --}}
                                    <div class="items"><span class="font-bold text-[16px] lg:!text-[16px] text-white">{{ __(':count Lessons', ['count' => $lessons->count()]) }}</span></div>
                                </div>
                            </div>
                            <div class="flex items-center">
                                @if($course->course_level)
                                <a class="btn__action font-bold text-[16px] lg:!text-[16px] text-white ml-3">
                                    {{ ucfirst($course->course_level) }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
            </section>
            
            <section class="relative py-[80px]">
            <div class="w-[100%] pr-[15px] pl-[15px] max-w-[1300px] mx-auto">
                <ul class="flex flex-wrap [list-style:none] mb-3 wrapper__navtab-wrap">
                <li class="mr-[30px]">
                    <a class="!bg-transparent relative rounded-[.25rem] font-bold text-[20px] md:!text-[28px] capitalize text-black" @click="_tab='-'" :class="{
                        'active' : _tab == '-',
                    }">{{ __('About') }}</a>
                </li>
                <li>
                    <a class="!bg-transparent relative rounded-[.25rem] font-bold text-[20px] md:!text-[28px] capitalize text-black" @click="_tab='review'" :class="{
                        'active' : _tab == 'review',
                    }">{{ __('Reviews') }}</a>
                </li>
                </ul>
                <div class="flex flex-col lg:!flex-row gap-4">
                    <div class="lg:!w-2/3 mb-4 lg:!mb-0">
                        <hr class="hr__line mt-0 mb-3">
                        <div class="">
                            <div x-cloak x-show="_tab=='-'">
                                <div class="textarea-content">
                                    <p class="font-medium text-sm md:!text-base text-gray-600 mb-4">{!! $course->description !!}</p>
                                </div>
                            </div>
                            <div x-cloak x-show="_tab=='review'">
                                <div>
                                    <form class="comment-form m-0 !mb-3 bg-[#f7f3f2] rounded-2xl p-5" wire:submit='leaveReview'>
                                       @csrf
                                       <div class="flex items-center mb-2">
                                        <div class="comment-title">{{ __('Leave a Review') }}</div>
                                        <div class="ml-auto">
                                            <input type="hidden" x-model="rating">
                                            <div x-rating:input="rating"></div>
                                        </div>
                                       </div>
                                       <div class="form-input mt-2 !bg-transparent">
                                          <span class="text-count-field"></span>
                                          <textarea wire:model="review" cols="30" rows="4"></textarea>
                                       </div>
                                       <button class="yena-button-stack mt-1 w-[100%]">{{ __('Submit') }}</button>
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
                                           <div class="mt-1 bg-red-200 font--11 p-1 px-2 rounded-md">
                                               <div class="flex items-center">
                                                   <div>
                                                       <i class="fi fi-rr-cross-circle flex text-xs"></i>
                                                   </div>
                                                   <div class="flex-grow ml-1 text-xs">{{ str_replace('create.', '', $error) }}</div>
                                               </div>
                                           </div>
                                       @endif
                                    </form>
                                    
                                                    
                                    <div class="comment">
                                        {{-- <div class="comment-head">
                                            <div class="comment-title">{{ number_format($reviews->count()) }} {{ __('Review(s)') }}</div>
                                        </div> --}}
                                        <div class="comment-list">
                                        @foreach ($reviews as $item)
                                            @php
                                                $reviewer = $item->reviewer;
                                            @endphp
                                            <div class="comment-item !bg-[#f7f3f2] p-5 rounded-2xl">
                                                <div class="comment-details">
                                                    <div class="comment-top">
                                                        <div class="comment-author">
                                                            @if ($reviewer)
                                                                <div class="tiny-avatar ml-0 mr-2 !w-10 !h-10">
                                                                    <img src="{{ $reviewer->getAvatar() }}" alt=" ">
                                                                </div>
                                                            @endif
                                                            @if ($reviewer)
                                                                <h5 class="font-bold text-base md:!text-lg mb-0">{{ $reviewer->name }}</h5>
                                                            @endif
                                                        </div>
                                                        <div class="rating" x-rating="'{{ !empty($item->rating) ? $item->rating : '0' }}'"></div>
                                                    </div>
                                                    <div class="comment-content">{{ $item->review }}</div>
                                                    <div class="comment-foot">
                                                        <div class="comment-time">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="lg:!w-1/3">
                        <div class="wrapper__profile-mentor mb-4">
                            <div class="head">
                                <h4 class="font-bold text-[20px] lg:!text-[20px] mb-4">{{ __('About Creator') }}</h4>
                                <div class="flex flex-wrap items-center justify-between">
                                    <div class="flex items-center mb-3 sm:mb-0 lg:!mb-3 xl:mb-0">
                                        <img src="{{ $owner->getAvatar() }}" class="images__profile" alt="">
                                        <div class="ml-3">
                                            <h5 class="font-bold text-[14px] lg:!text-[14px] mb-0">{{ $owner->name }}</h5>
                                            <p class="mb-0 medium text-[12px] lg:!text-[12px]">{{ $owner->title }}</p>
                                        </div>
                                    </div>
                                    {{-- <a class="font-bold text-[12px] lg:!text-[12px] btn btn__purple text-white shadow btn__profile" href="/mentor">See Full Profile</a> --}}
                                </div>
                            </div>
                            <hr class="my-0">
                            <div class="footer">
                                <div class="flex flex-wrap items-center justify-between w-[100%]">
                                    <div class="items">
                                        <h5 class="medium text-[12px] lg:!text-[12px] text-gray-400 mb-0">{{__('Total Course')}}</h5>
                                        <p class="mb-0 font-bold text-[14px] lg:!text-[14px]">{{ __(':count Course', ['count' => number_format($totalCourses)]) }}</p>
                                    </div>
                                    <div class="items">
                                        <h5 class="medium text-[12px] lg:!text-[12px] text-gray-400 mb-0">{{ __('Rating') }}</h5>
                                        <p class="mb-0 font-bold text-[14px] lg:!text-[14px]">{{ __(':avg (:count Reviews)', ['avg' => number_format($totalAvgRating, 1), 'count' => number_format($totalReviews)]) }}</p>
                                    </div>
                                    <div class="items mt-3 sm:mt-0 lg:!mt-3 xl:mt-0 flex items-center gap-3">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ route('out-courses-page', ['slug' => $course->slug]) }}" target="_blank" rel="noopener noreferrer" class="opacity-50 hover:opacity-100 items-center justify-center flex">
                                            <i class="ph ph-facebook-logo text-2xl"></i>
                                        </a>
                                        <a href="https://twitter.com/intent/tweet?url={{ route('out-courses-page', ['slug' => $course->slug]) }}" target="_blank" rel="noopener noreferrer" class="opacity-50 hover:opacity-100 items-center justify-center flex">
                                            <i class="ph ph-x-logo text-2xl"></i>
                                        </a>
                                        <a href="https://api.whatsapp.com/send?text={{ route('out-courses-page', ['slug' => $course->slug]) }}" target="_blank" rel="noopener noreferrer" class="opacity-50 hover:opacity-100 items-center justify-center flex">
                                            <i class="ph ph-whatsapp-logo text-2xl"></i>
                                        </a>
                                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ route('out-courses-page', ['slug' => $course->slug]) }}" target="_blank" rel="noopener noreferrer" class="opacity-50 hover:opacity-100 items-center justify-center flex">
                                            <i class="ph ph-linkedin-logo text-2xl"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="wrapper__rating-list">
                            <h5 class="font-bold text-[20px] lg:!text-[20px] mb-2">{{ __('Rating') }}</h5>
                            <div class="flex items-end">
                                <div class="rating flex-shrink-0">
                                    <h5 class="font-bold text-[42px]">{{ $ratings }}</h5>
                                    <div class="star mb-1">
                                        <div x-rating="ratings"></div>
                                    </div>
                                    <p class="medium mb-0 text-[12px] lg:!text-[12px] text-gray-400">
                                        {{ __(':count Reviews', ['count' => $reviews->count()]) }}
                                    </p>
                                </div>
                                <div class="progress__wrap w-[100%] ml-3 flex flex-col gap-2">
                                    @for ($i = 5; $i >= 1; $i--)
                                        @php
                                            $rating = $this->ratingsSummary()->get($i, ['count' => 0, 'percentage' => 0]);
                                        @endphp
                                        <div class="items">
                                            <div class="flex items-center">
                                                <i class="ph ph-star text-[#ffd05b]"></i>
                                                <span class="font-bold text-[12px] lg:!text-[12px] mr-2">{{ $i }}</span>
                                                <div class="progress w-[100%] mr-2 !rounded-full">
                                                    <div class="progress-bar !bg-blue-500" role="progressbar" aria-valuenow="{{ $rating['percentage'] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $rating['percentage'] }}%;"></div>
                                                </div>
                                                <span class="font-bold text-[12px] lg:!text-[12px]">{{ $rating['count'] }}</span>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </section>
        </div>
    </div>

    
    @script
    <script>
        Alpine.data('course_single', () => {
           return {
                _tab: '-',
                rating: @entangle('rating'),
                ratings: @entangle('ratings'),
            }
        });
    </script>
    @endscript
</div>