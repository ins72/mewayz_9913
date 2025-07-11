<?php
  use Illuminate\View\View;
  use App\Models\BookingService;

  use function Laravel\Folio\render;
  use function Laravel\Folio\name;
    
  name('out-booking-service-page');
  
  render(function ($slug, View $view) {

    if(!$service = BookingService::where('id', $slug)->first()) abort(404);
    
    return $view->with('service', $service);
  });
?>
<x-layouts.site>

  <x-slot:title>{{ __('Product') }}</x-slot>

  <div>
       <livewire:booking.service :$service :key="uukey('app', 'components.product.service')"/>
  </div>
</x-layouts.site>
