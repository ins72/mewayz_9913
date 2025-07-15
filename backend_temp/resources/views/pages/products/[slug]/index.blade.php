<?php
  use Illuminate\View\View;
  use App\Models\Product;

  use function Laravel\Folio\render;
  use function Laravel\Folio\name;
    
  name('out-products-single-page');
  
  render(function ($slug, View $view) {

    if(!$product = Product::where('slug', $slug)->first()) abort(404);
    
    // dd($slug);
    return $view->with('product', $product);
  });
?>
<x-layouts.site>

  <x-slot:title>{{ __('Product') }}</x-slot>

  <div>
       <livewire:products.single :$product :key="uukey('app', 'components.product.single')"/>
  </div>
</x-layouts.site>
