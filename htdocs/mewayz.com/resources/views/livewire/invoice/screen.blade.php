<?php
    use App\Models\Invoice;
    use function Livewire\Volt\{state, mount, on};

    state([
        'invoice',
        'invoiceArray' => []
    ]);

    state([
        'owner' => fn() => $this->invoice->user()->first(),
        'currency' => fn() => $this->owner->currency(),
    ]);
    state([
        'currentInvoiceNumber' => 0
    ]);

    mount(function(){
        $this->_get();
    });

    $_get = function(){
        $user_id = $this->owner->id;
        $this->invoiceArray = $this->invoice->toArray();

        $records = \Cache::remember("invoiceRecordModel:{$this->invoice->id}", 900, function () use($user_id) {
            return Invoice::orderBy('id', 'asc')->where('user_id', $user_id)->get();
        });

        $this->currentInvoiceNumber = null;
        foreach ($records as $index => $record) {
            // Check if this is the current invoice
            if ($record->id == $this->invoice->id) {
                // Format the number to have leading zeros
                $this->currentInvoiceNumber = str_pad($index + 1, 4, '0', STR_PAD_LEFT);
                break;
            }
        }
    };

    $checkout = function(){
        if($this->invoice->paid){
            session()->flash('error.error', __('Invoice has already been paid.'));
            return;
        }
    };
?>
<div>
    <style>
        html, body{
            background: #f7f3f2 !important;
        }
    </style>
    <div x-data="pay_invoicing">
        <div class="w-[100%] max-w-2xl mx-auto px-5 md:!px-[80px] py-10 md:!py-[80px]">
            <x-livewire::components.console.invoicing.sections.pdf />
        </div>
    </div>
    @script
    <script>
        Alpine.data('pay_invoicing', () => {
           return {
            currentInvoiceNumber: @entangle('currentInvoiceNumber'),
            invoice: @entangle('invoiceArray'),
            currency: @entangle('currency'),
            gs: '{{ gs('media/invoices') }}',

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