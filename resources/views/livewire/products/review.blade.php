<?php
    use App\Models\ProductReview;
    use Darryldecode\Cart\Facades\CartFacade as Cart;
    use function Livewire\Volt\{state, mount};

    state([
        'owner'
    ]);

    state([
        'product'
    ]);
    
    state([
        'rating' => 4,
        'review' => '',
        'reviews' => [],
        'hasPurchased' => function(){
            if(!$user = auth()->user()) return false;
            return $this->product->hasPurchased($user->id);
        },
    ]);

    mount(function(){
        $this->_get();
    });

    $_get = function(){
        $this->reviews = ProductReview::where('product_id', $this->product->id)->where('user_id', $this->product->user_id)->orderBy('id', 'DESC')->get();
    };

    $leaveReview = function(){
        if (!$user = auth()->user()) {
            session()->flash('error._error', __('Login to continue'));
            return;
        }

        if (!$this->hasPurchased) {
            session()->flash('error._error', __('Please unlock this course to leave review'));
            return;
        }

        $this->validate([
            'review' => 'required|max:500'
        ]);

        if (ProductReview::where('user_id', $this->product->user_id)->where('reviewer_id', $user->id)->where('product_id', $this->product->id)->first()) {
            session()->flash('error._error', __('Cannot repost another review'));
            return;
        }

        $review = new ProductReview;
        $review->user_id = $this->product->user_id;
        $review->reviewer_id = $user->id;
        $review->product_id = $this->product->id;
        $review->rating = $this->rating;
        $review->review = $this->review;
        $review->save();
        

        $this->review = '';
        $this->_get();
    };
?>
<div>
    <div x-data="store_review">
        <div class="modal-outer" @click="$event.stopPropagation()">
            <div>
               <div class="cart-container">
                <div class="cart-overlay" @click="page='-'" x-cloak x-show="page=='review'">
                    <div class="bg-white w-[100%]" @click="$event.stopPropagation()">
                        <form wire:submit='leaveReview' class="p-[24px] flex flex-col items-start gap-[24px]">
                            <div class=" flex items-center justify-between w-[100%]">
                                <div class="text-base tracking-[1.2px] font-medium leading-[20px]">{{ __('Leave a review') }}</div>
                                <button class="close-button" @click="page='-'">
                                    <i class="ph ph-x text-xl"></i>
                                </button>
                            </div>

                            <div class="text-field w-[100%]">
                                <textarea wire:model="review" cols="30" rows="5"></textarea>
                            </div>
                            
                            <div class="">
                                <input type="hidden" x-model="rating">
                                <div x-rating:input="rating"></div>
                            </div>

                            <button class="button checkout-button !h-[56px] !bg-black !rounded-full">{{ __('Submit') }}</button>
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
                    </div>
                </div>
                
                  <div class="cart-head">
                     <div class="text-[40px] leading-[52px] tracking-[-.54px] font-bold cart-title">{{__('Reviews')}}</div>
                     <button class="close-button" @click="openReview = false;">
                         <i class="ph ph-x text-xl"></i>
                     </button>
                  </div>
                  <div class="cart-items">
                    
                    @if ($reviews->isEmpty())
                    <div class="">
                        <div class="flex flex-col justify-center items-start px-0 py-[60px]">
                        {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                        <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                            {!! __t('No review yet on this product. <br> Purchase to leave a review.') !!}
                        </p>
                        </div>
                    </div>
                    @endif

                    <div class="comment">
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
                                    <div class="comment-content mt-2">{{ $item->review }}</div>
                                    <div class="comment-foot">
                                        <div class="comment-time">{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                  </div>
                  <div class="cart-checkout">
                     <div class="checkout-container">
                        <div class="checkout-buttons">
                            <button class="button checkout-button !bg-white !text-black" @click="openReview = false;" type="button">{{ __('Close') }}</button>
                            <button class="button checkout-button" @click="page='review'" type="button">{{ __('Leave review') }}</button>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
    </div>
    @script
    <script>
        Alpine.data('store_review', () => {
           return {
            page: '-',
            rating: @entangle('rating'),
            
            init(){
               let $this = this;
            },
           }
        });
    </script>
    @endscript
</div>