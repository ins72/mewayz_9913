<?php
  use Illuminate\View\View;
  use App\Models\Invoice;

  use function Laravel\Folio\render;
  use function Laravel\Folio\name;
    
  name('out-invoice-screenshot');
  
  render(function ($slug, View $view) {
    if(!$invoice = Invoice::where('slug', $slug)->first()) abort(404);
    
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
       <livewire:invoice.screen :$invoice :key="uukey('app', 'components.invoice.screen')"/>
  </div>
</x-layouts.base>
