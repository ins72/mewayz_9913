<?php
    use function Livewire\Volt\{state, mount, on};

    state([
        'invoice',
        'invoiceArray' => [],
    ]);

    state([
        'owner' => fn() => $this->invoice->user()->first(),
        'currency' => fn() => $this->owner->currency(),
        'screenshotUrl' => fn() => route('out-invoice-screenshot', ['slug' => $this->invoice->slug]),
    ]);

    mount(function(){
        $this->_get();
    });

    $_get = function(){
        $this->invoiceArray = $this->invoice->toArray();
    };

    $checkout = function(){
        if($this->invoice->paid){
            session()->flash('error._error', __('Invoice has already been paid.'));
            return;
        }

        $item = [
            'name' => __('Invoice payment'),
            'description' => ao($this->invoice->data, 'item_description'),
        ];
        
        $meta = [
            'user_id' => $this->owner->id,
            'invoice_id' => $this->invoice->id,
            'item' => $item
        ];

        $data = [
            'uref'  => md5(microtime()),
            'email' => ao($this->invoice->payer, 'email'),
            'price' => $this->invoice->price,
            'callback' => route('general-success', [
                'redirect' => route('out-invoice-single', ['slug' => $this->invoice->slug])
            ]),
            'frequency' => 'monthly',
            'currency' => ao($this->owner->currency(), 'currency'),
            'payment_type' => 'onetime',
            'meta' => $meta,
        ];

        //
        
        $call_function = \App\Yena\InvoiceCheckout::checkout($this->invoice, $this->owner);
        $call = \App\Yena\SandyCheckout::cr($this->owner->paymentMethod(), $data, $call_function);
        
        return $this->js("window.location.replace('$call');");
    };
?>
<div>
    <style>
        html, body{
            background: #f7f3f2 !important;
        }
    </style>
    <div x-data="pay_invoicing">
        <div class="w-[100%] max-w-4xl mx-auto px-5 md:!px-[80px] py-10 md:!py-[80px]">
            <div x-cloak>
                <x-livewire::components.console.invoicing.sections.payment />
            </div>
        </div>
    </div>
    @script
    <script>
        Alpine.data('pay_invoicing', () => {
           return {
            invoice: @entangle('invoiceArray'),
            currency: @entangle('currency'),
            gs: '{{ gs('media/invoices') }}',
            screenshotUrl: @entangle('screenshotUrl'),
            payInvoice(){
                let $this = this;
                $this.$wire.checkout();
            },
            downloadInvoice() {
                let $this = this;
                let url = this.screenshotUrl;
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
                        link.download = `screenshot-${$this.invoice.slug.split('/').pop()}-${$this.$store.app.getRandomString(4)}.png`;
                        link.click();
                        
                        // Remove the iframe after capturing
                        document.body.removeChild(iframe);
                    });
                };
            },

            getMedia(media){
                return this.gs +'/'+ media;
            },
            
            init(){
               let $this = this;

            },
           }
        });
    </script>
    @endscript
</div>