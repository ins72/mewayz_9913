@extends('mix::layouts.master')
@section('title', __('Pending Payments'))
@section('content')

<div class="h-full pb-16 pt-8 sm:pt-10">
  <div class="flex h-32 items-center border-b border-gray-200">
    <div class="mx-auto w-full max-w-screen-xl px-5">
    
       <div class="mb-5 pb-5 pt-0">
        <div class="font-heading mb-2 px-2 font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-2">
          <span class="whitespace-nowrap">{{ __('Super Admin') }}</span>
          <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
        </div>
        <h1 class="text-zinc-500 text-5xl mb-2 font-bold">{{ __('Pending Payments') }}</h1>
       </div>
    </div>
  </div>
  

  <div class="mx-auto w-full max-w-screen-xl px-2.5 pb-10 pt-8">

    <div class="page-trans">
        @if (!$pending->isEmpty())
        <div class="flex-table mt-4">
            <!--Table header-->
            <div class="flex-table-header">
                <span class="is-grow">{{ __('User') }}</span>
                <span>{{ __('Status') }}</span>
                <span>{{ __('Date') }}</span>
                <span>{{ __('Info') }}</span>
                <span>{{ __('Plan') }}</span>
                <span></span>
            </div>
            @foreach ($pending as $item)
            <div class="flex-table-item shadow-none">
                <div class="flex-table-cell is-media is-grow" data-th="">
                    @if (\App\User::find($item->user))
                    <div class="h-avatar md mr-4">
                        <img class="avatar is-squared" src="{{ avatar($item->user) }}" alt="">
                    </div>
                    @endif
                    <div>
                        <span class="item-name dark-inverted is-font-alt is-weight-600">{{ $item->name }}</span>
                        <span class="item-meta text-xs mt-2">
                            <span>{{ $item->email }}</span>
                        </span>
                        <span class="m-0 c-gray text-xs">
                            <span>#{{$item->ref}}</span>
                        </span>
                    </div>
                </div>
                <div class="flex-table-cell" data-th="{{ __('Status') }}">
                    <span class="light-text">{{ $item->status ? __('Confirmed') : __('Unconfirmed') }}</span>
                </div>
                <div class="flex-table-cell" data-th="{{ __('Date') }}">
                    <span class="light-text">{{ Carbon\Carbon::parse($item->created_at)->toFormattedDateString() }}</span>
                </div>
                <div class="flex-table-cell" data-th="{{ __('Proof') }}">
                    <div class="h-avatar sm mr-4 ml-auto md:ml-0">
                        <div data-background-image="{{ gs('media/site/manual-payment', ao($item->info, 'proof')) }}" class="lozad image"></div>
                    </div>
                    <div>
                        <span class="item-name font-normal text-xs">{{ ao($item->info, 'bank_name') }}</span>
                        <span class="item-meta text-xs mt-2 block">
                            <a href="{{ gs('media/site/manual-payment', ao($item->info, 'proof')) }}" target="_blank" class="link">{{ __('Proof image') }}</a>
                        </span>
                    </div>
                </div>
                <div class="flex-table-cell" data-th="{{ __('Plan') }}">
                    <div class="ml-auto md:ml-0">
                        <span class="item-name font-normal text-sm">{{ GetPlan('name', $item->plan) }}</span>
                        <span class="item-meta text-xs mt-2 block">
                            <span>{{ ucfirst($item->duration) }}</span>
                        </span>
                    </div>
                </div>
                <div class="flex-table-cell" data-th="{{ __('Actions') }}">
                    
                    @if (!$item->status)
                    <div class="dropdown ml-auto md:ml-0">
                        <button class="dropdown__head sorting__action shadow-none rounded-2xl mort-main-bg m-0">
                        <i class="sio construction-icon-021-spanner text-xl"></i>
                        </button>
                        <div class="dropdown__body">
                            
                            <div class="mb-10 p-5 rounded-xl mort-main-bg text-center">
                                {{ __('Actions') }}
                            </div>
                            <div class="justify-between flex">
                                <form action="{{ route('console-admin-payments-post', ['tree' => 'pendingPost', 'type' => 'accept']) }}" method="post">
                                    @csrf
                                    <input type="hidden" value="{{$item->id}}" name="pending">
                                    <button class="button bg-green sm shadow-none is-loader-submit loader-white">{{ __('Accept') }}</button>
                                </form>
                                <form action="{{ route('console-admin-payments-post', ['tree' => 'pendingPost', 'type' => 'decline']) }}" method="post">
                                    @csrf
                                    <input type="hidden" value="{{$item->id}}" name="pending">
                                    <button class="button sm bg-red-500 text-white shadow-none is-loader-submit loader-white">{{ __('Decline') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="is-empty md:p-20 text-center mt-10 block">
            <img src="{{ gs('assets/image/others', 'empty-fld.png') }}" class="w-half m-auto" alt="">
            <p class="mt-10 text-lg font-bold">{{ __('Nothing Here!') }}</p>
        </div>
        @endif
    </div>
  </div>
</div>
@endsection
