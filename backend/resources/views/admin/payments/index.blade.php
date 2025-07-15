<x-layouts.app>
    <x-slot:title>{{ __('Payments') }}</x-slot>
  
    <div class="h-full pb-16">
      <div class="mb-6 ">
        <div class="flex flex-col mb-4">
          <div class="font-heading font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center">
            <span class="whitespace-nowrap">{{ __('Admin') }}</span>
          </div>
          <div class="border-b border-solid border-gray-300 w-[100%] my-4 flex"></div>
           
           <div class="flex items-center h-6">
              <h2 class="text-lg font-semibold ">
                  {!! __icon('Payments Finance', 'Credit cards', 'w-6 h-6 inline-block') !!}
                  <span class="ml-2">{{ __('Payments') }}</span>
              </h2>
           </div>
        </div>
     </div>
     
      <div class="mx-auto w-[100%] max-w-screen-xl pb-10 pt-0">
        @if ($payments->isEmpty())
          
        <div>
          <p class="mt-2 text-xs text-gray-600 flex items-center gap-1"><i class="fi fi-rr-triangle-warning"></i> {{ __('No Payments Found') }}</p>
        </div>
      
        @endif

        <div class="mx-auto w-full pb-10">
          @if (!$payments->isEmpty())
          <div class="page-trans">
              
              <div class="flex-table mt-4">
                  <!--Table header-->
                  <div class="flex-table-header">
                      <span class="is-grow">{{ __('User') }}</span>
                      <span>{{ __('Date') }}</span>
                      <span>{{ __('Amount') }}</span>
                      <span>{{ __('Method') }}</span>
                      <span>{{ __('Plan') }}</span>
                  </div>
                  @foreach ($payments as $item)
                  <div class="flex-table-item shadow-none">
                      @if ($user = \App\Models\User::find($item->user))
                      <div class="flex-table-cell is-media is-grow" data-th="">
                          <div class="h-avatar h-12 w-12 rounded-full overflow-hidden mr-4">
                              <img class="w-full h-full object-cover" src="{{ $user->getAvatar() }}" alt="">
                          </div>
                          <div>
                              <span class="item-name dark-inverted is-font-alt is-weight-600">{{ $user->name }}</span>
                              <span class="item-meta text-xs mt-2">
                                  <span>{{ $item->email }}</span>
                              </span>
                          </div>
                      </div>
                      @endif
                      <div class="flex-table-cell" data-th="{{ __('Date') }}">
                          <span class="light-text">{{ Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}</span>
                      </div>
                      <div class="flex-table-cell" data-th="{{ __('Amount') }}">
                          <span class="dark-inverted is-weight-600">{!! Currency::symbol($item->currency) . $item->price !!}</span>
                      </div>
                      <div class="flex-table-cell" data-th="{{ __('Method') }}">
                          <span class="tag is-green is-rounded">{{ $item->gateway }}</span>
                      </div>
                      <div class="flex-table-cell" data-th="{{ __('Plan') }}">
                          <div class="ml-auto md:ml-0">
                              <span class="item-name font-normal text-base">{{ $item->plan_name }}</span>
                              <span class="item-meta text-xs mt-2 block">
                                  <span>{{ ucfirst($item->duration) }}</span>
                              </span>
                          </div>
                      </div>
                  </div>
                  @endforeach
              </div>
          </div>
          @endif
        </div>
      </div>
    </div>
</x-layouts.app>