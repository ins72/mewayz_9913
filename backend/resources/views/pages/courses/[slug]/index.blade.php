<?php
  use Illuminate\View\View;
  use App\Models\Course;

  use function Laravel\Folio\render;
  use function Laravel\Folio\name;
    
  name('out-courses-page');
  
  render(function ($slug, View $view) {

    if(!$course = Course::where('slug', $slug)->first()) abort(404);
    
    // dd($course);
    return $view->with('course', $course);
  });
?>
<x-layouts.site>

  <x-slot:title>{{ __('Course') }}</x-slot>

  <div>
       <livewire:courses.single :$course :key="uukey('livewire', 'courses.single')"/>
  </div>
</x-layouts.site>
