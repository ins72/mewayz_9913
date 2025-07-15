<?php
    use App\Models\Invoice;
    use App\Models\InvoicesTimeline;
    use App\Livewire\Actions\ToastUp;
    use function Livewire\Volt\{state, mount, updated, on, uses, placeholder};
    uses([ToastUp::class]);
    state([
        'user' => fn() => iam(),
    ]);
    placeholder('
    <div class="w-[100%] p-5 mt-1">
        <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)]"></div>
        <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
        <div class="--placeholder-skeleton w-[100%] h-[30px] rounded-[var(--yena-radii-sm)] mt-1"></div>
    </div>');

    state([
        'has_more_pages' => false,
        'per_page' => 10,
    ]);
    state([
        'invoices' => [],
    ]);

    mount(function(){
        $this->loadData();
    });

    on([
        'updateInvoice' => fn() => $this->loadData(),
    ]);

    $loadData = function(){
        $data = Invoice::where('user_id', $this->user->id)->orderBy('id', 'desc');

            // if (!empty($query = $this->search)) {
            //     $searchBy = filter_var($query, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
            //     $audiences = $audiences->where("contact->$searchBy", 'LIKE', '%' . $query . '%');
            // }

        $data = $data->paginate($this->per_page, ['*'], 'page');
        $this->per_page = $this->per_page + 5;
        $this->has_more_pages = $data->hasMorePages();

        $this->invoices = $data->items();
    };
    $deleteInvoice = function($id){
        if (!$invoice = Invoice::where('user_id', iam()->id)->where('id', $id)->first()) return;

        InvoicesTimeline::where('invoice_id', $invoice->id)->get();

        $invoice->delete();
        $this->loadData();
    };
    $emailInvoice = function($id){
        if (!$invoice = Invoice::where('user_id', iam()->id)->where('id', $id)->first()) return;

        $mail = new \App\Yena\YenaMail;
        $mail->send([
            'to' => ao($invoice->payer, 'email'),
            'subject' => __('Invoice from :name', ['name' => ao($invoice->data, 'name')]),
        ], 'invoice.email', [
            'invoice' => $invoice
        ]);
        $invoice->addTimeline('email_sent');
        $this->loadData();
        $this->flashToast('success', __('Invoce has been sent successfully'));
    };
    $markPaid = function($id){
        if (!$invoice = Invoice::where('user_id', iam()->id)->where('id', $id)->first()) return;

        $invoice->addTimeline('paid');
        $invoice->paid = 1;
        $invoice->save();
        $this->loadData();
        $this->flashToast('success', __('Invoce mark as paid'));
    };
    $toggleReminders = function($id){
        if (!$invoice = Invoice::where('user_id', iam()->id)->where('id', $id)->first()) return;

        $invoice->enable_reminder = $invoice->enable_reminder ? 0 : 1;
        $invoice->save();
        
        $this->loadData();
    };
    $getAnalytics = function(){
        $userId = iam()->id;
        $total_views = 0;
        $paid_invoices = Invoice::where('user_id', $this->user->id)
        ->where('paid', true)
        ->sum('price');
        $unpaid_invoices = Invoice::where('user_id', $this->user->id)
        ->where('paid', false)
        ->sum('price');

        $total_invoices = Invoice::where('user_id', $this->user->id)->count();

        $total_invoices = nr($total_invoices);

        return [
            'total_invoices' => $total_invoices,
            'paid_invoices' => $this->user->price($paid_invoices),
            'unpaid_invoices' => $this->user->price($unpaid_invoices),
        ];
    };

    $queryEarningsMetric = function(){
                
        $start_of_this_year = \Carbon\Carbon::now()->startOfYear();
        $start_of_last_year = \Carbon\Carbon::now()->subYear()->startOfYear();
        $end_of_last_year = $start_of_this_year->copy()->subSecond();

        // Get Paid Invoices
        $paid_invoices = Invoice::where('user_id', $this->user->id)
        ->where('paid', true)
        ->get();

        // Get Unpaid Invoices
        $unpaid_invoices = Invoice::where('user_id', $this->user->id)
        ->where('paid', false)
        ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $paid_all = array_fill_keys($months, ['amount' => 0]);
        $unpaid_all = array_fill_keys($months, ['amount' => 0]);

        // Process Paid Invoices
        foreach ($paid_invoices as $item) {
            $date = (string) \Carbon\Carbon::parse($item->created_at)->format('M');
            if (!empty($date) && array_key_exists($date, $paid_all)) {
                $paid_all[$date]['amount'] += $item->price;
            }
        }

        // Process Unpaid Invoices
        foreach ($unpaid_invoices as $item) {
            $date = (string) \Carbon\Carbon::parse($item->created_at)->format('M');
            if (!empty($date) && array_key_exists($date, $unpaid_all)) {
                $unpaid_all[$date]['amount'] += $item->price;
            }
        }

        // Ensure all months are present with zero if not already filled
        foreach ($months as $month) {
            if (!array_key_exists($month, $paid_all)) {
                $paid_all[$month] = ['amount' => 0];
            }
            if (!array_key_exists($month, $unpaid_all)) {
                $unpaid_all[$month] = ['amount' => 0];
            }
        }

        return [
            'labels' => $months,
            'paid' => _get_chart_data($paid_all),
            'unpaid' => _get_chart_data($unpaid_all),
        ];
    };
?>
<div>

    <div x-data="console__app_invoicing">
        <div class="banner">
           <div class="banner__container !bg-white">
              <div class="banner__preview !right-0 !w-[300px] !top-[10rem]">
                 {!! __icon('Payments Finance', 'Invoice, Accounting.1') !!}
              </div>
              <div class="banner__wrap z-[50]">
                 <div class="banner__title h3 !text-black">{{ __('Invoicing') }}</div>
                 <div class="banner__text !text-black">{{ __('A simple and fast way to shorten links.') }}</div>
                 
                 <div class="mt-7 grid grid-cols-2 gap-1">
                    <div>
                       <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                          <div class="detail text-gray-600">{{ __('Total Invoices') }}</div>
                          <template x-if="analyticsLoading">
                              <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                          </template>
                          <template x-if="!analyticsLoading">
                           <div class="number-secondary" x-html="analytics.total_invoices"></div>
                          </template>
                       </div>
                    </div>
                    <div>
                       <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                          <div class="detail text-gray-600">{{ __('Total Paid') }}</div>
                          <template x-if="analyticsLoading">
                              <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                          </template>
                          <template x-if="!analyticsLoading">
                           <div class="number-secondary" x-html="analytics.paid_invoices"></div>
                          </template>
                       </div>
                    </div>
                    <div class="col-span-2">
                       <div class="rounded-8 p-2 text-center col-span-2 lg:col-span-1 bg-[#f7f3f2] text-black">
                          <div class="detail text-gray-600">{{ __('Total Unpaid') }}</div>
                          <template x-if="analyticsLoading">
                              <div class="--placeholder-skeleton w-full h-[20px] rounded-sm mt-1"></div>
                          </template>
                          <template x-if="!analyticsLoading">
                           <div class="number-secondary" x-html="analytics.unpaid_invoices"></div>
                          </template>
                       </div>
                    </div>
                 </div>
                 <div class="mt-3 flex gap-2">
                    <button class="yena-button-stack !rounded-full" @click="$dispatch('open-modal', 'create-invoicing-modal')">{{ __('Create Invoice') }}</button>
                 </div>
              </div>
           </div>
        </div>
        <div wire:ignore>
           <div class="grid grid-cols-1 gap-4">
              <div class="widget widget_chart">
                 <div class="p-4 bg-[#f7f3f2] !bg-white mb-0 rounded-8 cursor-pointer lg:flex lg:justify-between lg:items-center hover:opacity-70">
                    <div class="flex flex-col lg:flex-row items-center gap-3 ">
                       
                       {!! __i('Business, Products', 'document-cash-chart', 'w-10 h-10 self-start') !!}


                       <div class="flex flex-col">
                          <div class="text-gray-800 font-bold text-2xl">{{ __('Earnings') }}</div>
                          {{-- <div class="text-gray-600"></div> --}}
                       </div>
                    </div>
                 </div>
                 {{-- <div class="widget__title">{{ __('Store Earnings') }}</div> --}}
                 <div class="widget__wrap">
                   <div class="widget__chart widget__chart_earning relative" wire:ignore>
                    <template x-if="chartLoading">
                        <div class="flex flex-col gap-2 p-5 w-full mt-auto">
                          <div class="--placeholder-skeleton w-full h-[40px] rounded-sm"></div>
                          <div class="--placeholder-skeleton w-full h-[40px] rounded-sm"></div>
                          <div class="--placeholder-skeleton w-full h-[40px] rounded-sm"></div>
                          <div class="--placeholder-skeleton w-full h-[40px] rounded-sm"></div>
                        </div>
                    </template>
                    
                    <div class="store-line-chart" x-ref="earningsChart"></div>
                   </div>
            
                   {{-- <div class="widget__btns"><button class="widget__btn btn btn_purple btn_wide">Analytics</button></div> --}}
                 </div>
               </div>
           </div>
        </div>
        <div class="mt-4 p-3 bg-white rounded-[24px]">
            
            @if (empty($invoices))
            <div class="">
                <div class="flex flex-col justify-center items-start px-4 py-[60px]">
                   {!! __i('--ie', 'eye-cross-circle 2', 'w-14 h-14') !!}
                   <p class="mt-3 text-base text-left md:!text-2xl font-bold">
                      {!! __t('You have no invoice. <br> Create an invoice to get started.') !!}
                   </p>
                </div>
            </div>
            @endif
            <div class="grid grid-cols-1 md:!grid-cols-2 gap-4 gap-2 items-start" x-data x-masonry.poll.50>
                @foreach ($invoices as $item)
                    <div class="relative">
                        <div x-data="{share: false, showOptions: false }">
                       
                            <div class="mb-2 rounded-8 bg-[#f7f3f2] relative" x-data="timelineComponent('{{ $item->id }}')">
    
                                <template x-if="showOptions">
                                    <div class="absolute z-[3] top-[10px] right-[10px] opacity-0 w-auto h-auto flex flex-col items-end" :class="{
                                        '!opacity-100': showOptions,
                                    }" x-on:click.outside="showOptions=false">
                                            <div class="yena-menu-list !w-full">
                                            <div class="px-4">
                                                {{-- <p class="yena-text">{{ $item->name }}</p> --}}
                                    
                                                <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">{{ __('Created') }} {{ \Carbon\Carbon::parse($item->created_at)->format('F d\t\h, Y') }}</p>
                                                {{-- <p class="text-[color:var(--yena-colors-gray-500)] truncate text-sm">{{ __('by :who', [
                                                    'who' => $item->createdBy()->name
                                                ]) }}</p> --}}
                                            </div>
                                    
                                            <hr class="--divider">
                                            <a href="{{ route('dashboard-invoicing-edit', ['slug' => $item->slug]) }}" class="yena-menu-list-item">
                                                <div class="--icon">
                                                    {!! __icon('Design Tools', 'Pencil.1', 'w-5 h-5') !!}
                                                </div>
                                                <span>{{ __('Edit invoice') }}</span>
                                            </a>
                                            <hr class="--divider">
                                    
                                            <a @click="$wire.markPaid('{{ $item->id }}')" class="yena-menu-list-item">
                                                <div class="--icon">
                                                    {!! __icon('--ie', 'checkmark-done-check', 'w-5 h-5') !!}
                                                </div>
                                                <span>{{ __('Mark as paid') }}</span>
                                            </a>
                                            <a href="{{ route('out-invoice-single', ['slug' => $item->slug]) }}" target="_blank" class="yena-menu-list-item">
                                                <div class="--icon">
                                                    {!! __icon('--ie', 'eye.5', 'w-5 h-5') !!}
                                                </div>
                                                <span>{{ __('View invoice') }}</span>
                                            </a>
                                            <a @click="$wire.emailInvoice('{{ $item->id }}')" class="yena-menu-list-item">
                                                <div class="--icon">
                                                    {!! __icon('emails', 'email-mail-letter', 'w-5 h-5') !!}
                                                </div>
                                                <span>{{ __('Email invoice') }}</span>
                                            </a>
                                            <a @click="downloadInvoice('{{ $item->slug }}')"  class="yena-menu-list-item">
                                                <div class="--icon">
                                                    {!! __icon('--ie', 'download-arrow', 'w-5 h-5') !!}
                                                </div>
                                                <span>{{ __('Download Invoice') }}</span>
                                            </a>
                                            <div x-data="{
                                                __text:'{{ __('Copy URL') }}',
                                                link: '{{ route('out-invoice-single', ['slug' => $item->slug]) }}'
                                            }" @click="$clipboard(link); __text = window.builderObject.copiedText;" class="yena-menu-list-item cursor-pointer">
                                                <div class="--icon">
                                                    {!! __icon('interface-essential', 'share-arrow.1', 'w-5 h-5') !!}
                                                </div>
                                                <span x-text="__text">{{ __('Copy URL') }}</span>
                                            </div>
                                            <div @click="$wire.toggleReminders('{{ $item->id }}')" class="yena-menu-list-item cursor-pointer">
                                                <div class="--icon">
                                                    {!! __icon('Users', 'user-profile-time-clock', 'w-5 h-5') !!}
                                                </div>
                                                <span>{{ $item->enable_reminder ? __('Turn off reminders') : __('Turn on reminders') }}</span>
                                            </div>
                                            {{-- <a @click="$dispatch('open-modal', 'site-share-modal'); $dispatch('registerSite', {site_id: '{{ $item->id }}'})" class="yena-menu-list-item">
                                                <div class="--icon">
                                                    {!! __icon('interface-essential', 'copy-duplicate-object-add-plus', 'w-5 h-5') !!}
                                                </div>
                                                <span>{{ __('Duplicate invoice') }}</span>
                                            </a> --}}
                                            <hr class="--divider">
                                            <div x-data="{confirm:false}">
                                                <div class="yena-menu-list-item !text-[color:red] cursor-pointer" @click="!confirm ? confirm = true : $wire.deleteInvoice('{{ $item->id }}');" x-init="$watch('confirm', value => {
                                                    if (value) {
                                                        setTimeout(function(){
                                                            confirm = false;
                                                        }, 5000)
                                                    }
                                                })">
                                                    <div class="--icon">
                                                    {!! __icon('interface-essential', 'delete-disabled.2', 'w-5 h-5') !!}
                                                    </div>
                                                    <span x-text="!confirm ? '{{ __('Permanently delete') }}' : '{{ __('Confirm Delete?') }}'"></span>
                                                    <template x-if="confirm">
                                                        <div x-data="confirmDotsHandler">
                                                            <template x-for="(dots, index) in dotsArray" :key="index">
                                                                <span x-text="dots"></span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div class="py-4 px-4">
                                    <div class="flex justify-between">
                                        <div class="text-[10px] uppercase leading-[1.1] tracking-[.7px] flex h-8 items-center rounded-md px-2 {{ $item->paid ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100' }}">
                                            {{ $item->paid ? __('Paid') : __('Outstanding') }}
                                        </div>
                                        
                                        <div class="flex items-center gap-2">
                                            <a class="yena-button-o !bg-white" href="{{ route('dashboard-invoicing-edit', ['slug' => $item->slug]) }}">
                                                <i class="ph ph-pencil text-sm"></i>
                                            </a>
                                            <button class="yena-button-o !bg-white" type="button" @click.stop="showOptions=true">
                                                {!! __icon('interface-essential', 'dots-menu', 'w-5 h-5') !!}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="flex items-end justify-between">
                                            <span class="font-bold leading-[1.1] tracking-[-.42px] text-[28px]">{!! iam()->price($item->price) !!}</span>
                                            <span class="font-semibold">{{ \Carbon\Carbon::parse($item->due)->toFormattedDateString() }}</span>
                                        </div>
                                        <div class="mb-2 flex items-center justify-between">
                                            <span>{{ ao($item->data, 'name') }} {{ __('to') }} {{ ao($item->payer, 'name') }}</span>
                                            <a href="mailto:{{ ao($item->payer, 'email') }}" class="font-normal text-gray-600">{{ ao($item->payer, 'email') }}</a>
                                        </div>
                                    </div>
    
                                    <div :class="{
                                        '!hidden': !showTimeline,
                                    }" x-cloak class=" px-[16px] py-[6px]">
                                        <x-livewire::components.console.invoicing.sections.timeline :invoice="$item" />
                                    </div>
                                    
                                </div>
                                <div class="text-[10px] uppercase leading-[1.1] tracking-[.7px]">
                                    <div class="w-full h-[1px] bg-[#e0e0e0] my-2"></div>
                                    <div class="text-center">
                                        <button class="yena-button-o !mb-2" type="button" @click="toggleTimeline" x-text="showTimeline ? '{{ __('Hide timeline') }}' : '{{ __('Show timeline') }}'">
                                            {{ __('Show timeline') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        
        <x-modal name="create-invoicing-modal" :show="false" removeoverflow="true" maxWidth="2xl">
            <livewire:components.console.invoicing.create zzlazy :key="uukey('app', 'console.invoicing.create')">
        </x-modal>
    </div>
    @script
      <script>
          Alpine.data('console__app_invoicing', () => {
            return {
                _page: '-',
                analyticsLoading: true,
                chartLoading: true,
                analytics: {
                    paid_invoices: '$0',
                    unpaid_invoices: '$0',
                    total_invoices: '0',
                },
                has_more_pages: @entangle('has_more_pages'),
                downloadInvoice(slug) {
                    let $this = this;
                    let url = window.builderObject.baseUrl + '/invoice/' + slug + '/screen';
                    // Create a new iframe element
                    const iframe = document.createElement('iframe');
                    iframe.src = url;
                    iframe.classList.add('w-[1280px]', 'h-[800px]', 'absolute', '-top-[9999px]', '-left-[9999px]');
                    document.body.appendChild(iframe);
                    // Wait for the iframe to load
                    iframe.onload = () => {
                        // Use html2canvas on the iframe's content
                        html2canvas(iframe.contentWindow.document.body).then(canvas => {
                            // Convert canvas to an image and download it
                            const link = document.createElement('a');
                            link.href = canvas.toDataURL('image/png');
                            link.download = `screenshot-${slug.split('/').pop()}-${$this.$store.app.getRandomString(4)}.png`;
                            link.click();
                            
                            // Remove the iframe after capturing
                            document.body.removeChild(iframe);
                        });
                    };
                },

                queryChart(){
                    let $this = this;
                    $this.$wire.queryEarningsMetric().then(r => {
                        let options = {
                            ...window.apexOptions,
                            labels: r.labels,
                            series: [
                                {
                                    name: '{{ __('Paid') }}',
                                    data: r.paid.amount,
                                },
                                {
                                    name: '{{ __('Unpaid') }}',
                                    data: r.unpaid.amount,
                                },
                            ],
                            colors: ['#6C5DD3', '#A0D7E7'],
                            grid: {
                                borderColor: '#E4E4E4',
                                strokeDashArray: 0,
                                xaxis: {
                                    lines: {
                                        show: false
                                    }
                                },
                                yaxis: {
                                    lines: {
                                        show: false
                                    }
                                },
                                padding: {
                                    top: 0,
                                    left: 10,
                                    right: 0,
                                    bottom: 0
                                }
                            },
                            yaxis: {
                                show: true,
                            },
                            xaxis: {
                                ...window.apexOptions.xaxis,
                                show: true,
                                labels: {
                                    show: true
                                },
                            },
                            stroke: {
                                curve: 'smooth'
                            },
                            dataLabels: {
                                enabled: false
                            },
                            legend: {
                                show: false
                            },
                            chart: {
                                ...window.apexOptions.chart,
                                type: 'bar',
                                height: '100%',
                            },
                        };

                        options['chart']['height'] = 200;

                        $this.chartLoading = false;
                        new ApexCharts($this.$refs.earningsChart, options).render();
                    });
                },

                init(){
                    let $this = this;

                    $this.queryChart();

                    let _throttleTimer = null;
                    let _throttleDelay = 100;
                    let $windowContactWrapper = $this.$root;

                    let handler = function(e) {
                        clearTimeout(_throttleTimer);
                        _throttleTimer = setTimeout(function() {
                            if ($windowContactWrapper.scrollTop + $windowContactWrapper.clientHeight > $windowContactWrapper.scrollHeight - 100) {
                                if($this.has_more_pages){
                                    $this.$wire.loadData();
                                }
                            }
                        }, _throttleDelay);
                    };

                    $windowContactWrapper.removeEventListener('scroll', handler);
                    $windowContactWrapper.addEventListener('scroll', handler);
                    $this.$wire.getAnalytics().then(r => {
                        $this.analytics = r;
                        $this.analyticsLoading = false;
                    });
                },
            }
          });
      </script>
    @endscript
</div>