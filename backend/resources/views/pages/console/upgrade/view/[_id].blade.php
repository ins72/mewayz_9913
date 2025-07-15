<?php

    use App\Models\Plan;
    use function Laravel\Folio\name;
    use function Laravel\Folio\render;
    use Illuminate\View\View;
    
    render(function (View $view, $_id) {
        if(!$plan = Plan::where('id', $_id)->where('status', 1)->first()) abort(404);

        return $view->with('plan', $plan);
    });
    name('console-upgrade-view');
?>
<x-layouts.app>
    <x-slot:title>{{ __('Upgrade') }}</x-slot>


    <div class="pt-0">
        <livewire:components.upgrade.view.index :$plan :key="uukey('app', 'upgrade-view' . $plan->id)" lazyzz/>
    </div>
</x-layouts.app>