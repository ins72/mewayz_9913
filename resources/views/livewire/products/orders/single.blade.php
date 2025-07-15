<?php
    use App\Models\Product;
    use App\Models\ProductOrder;
    use App\Models\ProductOrderTimeline;
    use App\Livewire\Actions\ToastUp;

    use function Livewire\Volt\{state, mount, placeholder, usesPagination, rules, with, uses};
    usesPagination();

    uses([ToastUp::class]);
    state([
       'user' => fn() => iam(),
    ]);

    state([
        'timeline' => []
    ]);

    state([
        'order' => fn() => ProductOrder::find(17),
        'order_id' => null,
    ]);

    mount(function(){
        $this->get();
    });

    $setOrder = function($id = null){
      $this->order_id = $id;

      if($id == null){
        $this->order = new ProductOrder;
      }

      $this->get();
    };

    $get = function(){

        if($this->order_id){
            $this->order = ProductOrder::where('payee_user_id', $this->user->id);
            $this->order = $this->order->where('id', $this->order_id);
            $this->order = $this->order->first();
        }

        $this->getTimeline();
    };


    $getTimeline = function(){
        if(!$this->order) return;
        if(!$this->order->id) return;

        $this->timeline = ProductOrderTimeline::where('tid', $this->order->id)->orderBy('id', 'DESC')->get();
    };
    
    $orderStatus = function($status){
        if ($status == 1) {
            return __('Pending');
        }

        if ($status == 2) {
            return __('Completed');
        }

        if ($status == 3) {
            return __('Canceled');
        }
    };
    
    $changeStatus = function($type){
      
        $types = ['resend_order', 'completed', 'canceled'];

        if (!in_array($type, $types)) return;

        if (!$order = $this->order) return;

        $user = $this->user;

        $timeline = function($data, $type) use ($order, $user){
            $ordertimeline = new ProductOrderTimeline;
            $ordertimeline->user_id = $user->id;
            $ordertimeline->tid = $order->id;
            $ordertimeline->type = $type;
            $ordertimeline->data = $data;
            $ordertimeline->save();

            $this->getTimeline();
        };



        if ($type == 'resend_order') {


            // Resend Email
            $data = [
                'email' => $order->email
            ];  

            if (!$payee = $order->payee) {
                return false;
            }

            // Email class
            // $email = new \App\Email;
            // // Get email template
            // $template = $email->template(block_path('shop', 'Email/purchased_product_email.php'), ['order' => $order, 'order_id' => $order->id]);
            // // Email array
            // $mail = [
            //     'to' => $order->email,
            //     'subject' => __('Your purchased product(s)', ['website' => config('app.name')]),
            //     'body' => $template
            // ];

            // $email->send($mail);

            $timeline($data, 'resend_order');

            $this->flashToast('error', __('Order receipt has been sent'));
            return;
        }


        if ($type == 'canceled') {
            if (ProductOrderTimeline::where('type', 'canceled_order')->where('tid', $order->id)->first()) {
                $this->flashToast('error', __('Cannot re-mark a canceled order'));
                return;
            }

            // Resend Email
            $data = [
                'email' => $order->email
            ];  

            $order->status = 3;
            $order->update();

            $timeline($data, 'canceled_order');

            $this->flashToast('error', __('Order has been canceled'));
            return;
        }

        if ($type == 'completed') {
            if (ProductOrderTimeline::where('type', 'completed_order')->where('tid', $order->id)->first()) {
                $this->flashToast('error', __('Cannot re-mark a completed order'));
                return;
            }


            // Resend Email
            $data = [
                'email' => $order->email
            ];  

            $order->status = 2;
            $order->update();

            $timeline($data, 'completed_order');

            $this->flashToast('error', __('Order has been completed'));
            return;
        }
    };
?>


<div>

    <div x-data="app_store_orders_single">

     <div x-data="{ tippy: {
        content: () => $refs.template.innerHTML,
        allowHTML: true,
        appendTo: $root,
        maxWidth: 360,
        interactive: true,
        trigger: 'click',
        animation: 'scale',
     } }">


        <template x-ref="template">
            <div class="yena-menu-list !w-full">
               <div class="px-4">
                  <p class="yena-text">{{ __('Manage Order') }}</p>
      
                  @if ($order)
                  <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">{{ __('Created') }} {{ \Carbon\Carbon::parse($order->created_at)->format('F d\t\h, Y') }}</p>
                  @endif
               </div>
      
               <hr class="--divider">
      
               <a @click="$wire.changeStatus('resend_order')" class="yena-menu-list-item">
                  <div class="--icon">
                     {!! __icon('emails', 'email-mail-letter-fast-send-circle', 'w-5 h-5') !!}
                  </div>
                  <span>{{ __('Re-send Order') }}</span>
               </a>
               <a @click="$wire.changeStatus('completed')" class="yena-menu-list-item !text-green-500">
                  <div class="--icon">
                     {!! __icon('--ie', 'checkmark-done-check', 'w-5 h-5') !!}
                  </div>
                  <span>{{ __('Mark as Completed') }}</span>
               </a>
               
               <hr class="--divider">
               <a class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="$wire.changeStatus('canceled')">
                  <div class="--icon">
                     {!! __icon('interface-essential', 'delete-disabled.2', 'w-5 h-5') !!}
                  </div>
                  <span>{{ __('Mark as Canceled') }}</span>
               </a>
           </div>
        </template>
        <template x-if="loading">
            <div class="p-5 w-full mt-0">
                <div class="--placeholder-skeleton w-full h-[30px] rounded-sm"></div>
                <div class="--placeholder-skeleton w-full h-[50px] rounded-sm mt-2"></div>
                <div class="--placeholder-skeleton w-full h-[50px] rounded-sm mt-1"></div>
                <div class="--placeholder-skeleton w-full h-[50px] rounded-sm mt-1"></div>
            </div>
        </template>
        <div x-cloak x-show="!loading">
            @if ($order && $order->payee)
            <div class="p-3 rounded-2xl mb-4 ![box-shadow:var(--yena-shadows-md)] ![background:var(--yena-colors-gradient-light)]">
                <div class="avatar-upload flex items-center z-40">
                    <div class="flex items-center">
                        <div class="flex relative cursor-pointer">
                            <img src="{{ $order->payee->getAvatar() }}" class="h-20 w-20 [transition:all_.2s_ease-in] object-cover rounded-xl [box-shadow:var(--yena-shadows-md)] [background:var(--yena-colors-gradient-light)] p-0.5" alt="">
                        </div>
                        <div class="content ml-3">
                            <h5>{{ $order->payee->name }}</h5>
                            <p class="text-gray-400 text-xs mt-1">{{ $order->payee->email }}</p>

                            <p class="--sub flex mt-1">
                                <span class="font-bold mr-1 text-sm">{{ __('Status') }}:</span>
                                <span class="text-gray-400 text-sm">{{ $this->orderStatus($order->status) }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <button class="yena-button h-6 w-6 rounded-full transition-all hover:text-[color:#3c3838] hover:bg-[var(--yena-colors-gray-200)] flex items-center justify-center" x-tooltip="{...tippy}">
                            {!! __icon('interface-essential', 'dots-menu', 'w-5 h-5') !!}
                         </button>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="rounded-2xl p-0 mb-5">
                <div class="mt-0 pb-0">
                    @if ($order && is_array(ao($order->extra, 'cart')))
                        @foreach (ao($order->extra, 'cart') as $key => $value)
                        @php
                            $product = Product::find(ao($value, 'attributes.product_id'));
                        @endphp
                        <div class="product-cart !bg-[#f7f3f2] items-center">
                            @if ($product)
                            <div class="banner-o rounded-2xl h-16 w-20">
                                <img src="{!! $product->getFeaturedImage() !!}" alt="">
                            </div>
                            @endif
                            <div class="content flex flex-col">
                                <a class="text-base mb-1">{{ ao($value, 'name') }}</a>
                                <p class="text-gray-400 text-xs mb-2">{!! iam()->price(ao($value, 'price')) !!} x{{ ao($value, 'quantity') }}</p>
                                @if (!empty($op = ao($value, 'attributes.options')))
                                    <div class="flex items-center">
                                        @foreach ($op as $key => $v)
                                        <span class="text-sm font-normal">{{ ao($v, 'name') }}{{ $key === array_key_last($op) ? '' : ','  }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="rounded-2xl mb-5 active">
                @if ($order && is_array(ao($order->details, 'shipping_location')) && !empty(ao($order->details, 'shipping_location')))
                <div class="bg-[#f7f3f2] p-5 rounded-2xl mb-7">
                    <div class="mb-5">
                        <h1 class="text-base font-bold">{{ __('Selected shipping location') }}</h1>
                    </div>
                    <div class="divider mb-5"></div>
                    @foreach (ao($order->details, 'shipping_location') as $key => $value)
                    <span><span class="text-sm">{{ ucwords(str_replace('_', ' ', $key)) }}</span> : {{ $value }}</span><br>
                    @endforeach
                </div>
                @endif
                @if ($order && is_array(ao($order->details, 'shipping')) && !empty(ao($order->details, 'shipping')))
                <div class="bg-[#f7f3f2] p-5 rounded-2xl">
                    
                    <div class="mb-5">
                        <h1 class="text-base font-bold">{{ __('Filled information') }}</h1>
                    </div>
                    <div class="divider mb-5"></div>
                    @foreach (ao($order->details, 'shipping') as $key => $value)
                    <span><span class="text-sm">{{ ucwords(str_replace('_', ' ', $key)) }}</span> : {{ $value }}</span> <br>
                    @endforeach
                </div>
                @endif
            </div>
            @foreach ($timeline as $item)
            <div class="order-timeline">
                <div class="p-5">
                    @includeIf("livewire.components.console.store.orders.timeline.$item->type")
                </div>
                <div class="order-timeline-sep"></div>
            </div>
            @endforeach
        </div>
     </div>
    </div>
    
    
    @script
    <script>
        Alpine.data('app_store_orders_single', () => {
           return {
                loading: false,
                closeOrder(){

                },
                init(){
                    var $this = this;

                    document.addEventListener('alpine:navigated', (e) => {
                        //  $this.$wire._get();
                    });

                    $this.$watch('singleId', (value) => {
                        $this.loading = true;
                        $this.$wire.setOrder(value).then(r => {
                            $this.loading = false;
                        });
                    });
                },
           }
        });
    </script>
    @endscript
</div>