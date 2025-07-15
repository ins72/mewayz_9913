<?php
  use Illuminate\View\View;
  use App\Models\Invoice;

  use function Laravel\Folio\render;
  use function Laravel\Folio\name;
    
  name('out-invoice-single');
  
  render(function ($slug, View $view) {
    if(!$invoice = Invoice::where('slug', $slug)->first()) abort(404);

    $invoice->last_viewed = \Carbon\Carbon::now();
    $invoice->save();
    
    // dd($slug);
    return $view->with('invoice', $invoice);
  });
?>
<x-layouts.base>

  <x-slot:title>{{ __('Invoice') }}</x-slot>

  <x-slot:meta>
    @vite([
      'resources/sass/create.scss',
    ])
  </x-slot>

  <div>
       <livewire:invoice.single :$invoice :key="uukey('app', 'components.invoice.single')"/>
  </div>
</x-layouts.base>
