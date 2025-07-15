@extends('mix::layouts.master')
@section('title', __('User report'))
@section('content')

<div>
   
  <div class="flex h-48 items-center">
   <div class="mx-auto w-full max-w-screen-xl px-5">
   
      <div class="mb-5 pb-5 border-b border-solid border-gray-300 pt-4">
       <div class="font-heading mb-2 px-2 font--12 font-extrabold uppercase tracking-wider text-zinc-400 flex items-center mb-2">
         <span class="whitespace-nowrap">{{ __('Super Admin') }}</span>
         <div class="border-b border-solid border-gray-300 w-full ml-2 flex"></div>
       </div>
       <h1 class="text-zinc-500 text-5xl mb-2 font-bold">{{ __('Page reports') }}</h1>
      </div>
   </div>
 </div>
</div>
<div class="sandy-page-row">
  <div class="sandy-page-col pl-0">
    <div class="page__head">
      <div class="step-banner remove-shadow">
        <div class="section-header">
          
          <div class="flex items-center gap-2">
            <div class="h-avatar md remove-before remove-after bg-transparent border-0 border-solid border-gray-300 bg-white rounded-full w-12 h-12">
              <img src="{{ $page->getLogo() }}" alt="">
            </div>
            <div>
                
              <div class="heading font-bold mb-0">{{ $page->name }}</div>
              <div class="subheading theme-text-color">{{ '@' . $page->address }}</div>
            </div>
            @if ($page->banned)
            <form action="{{ route('console-admin-pages-post', 'unban') }}" method="post">
              @csrf
              <input type="hidden" name="_page" value="{{ $page->id }}">
              <button class="sandy-button bg-black py-2 flex-grow rounded-none block ml-4">
                <div class="--sandy-button-container">
                   <span class="text-xs">{{ __('Click to Unban page') }}</span>
                </div>
              </button>
            </form>
            @else
            <form action="{{ route('console-admin-pages-post', 'ban') }}" method="post">
              @csrf
              <input type="hidden" name="_page" value="{{ $page->id }}">
              <button class="sandy-button bg-black py-2 flex-grow rounded-none block ml-4">
                <div class="--sandy-button-container">
                   <span class="text-xs">{{ __('Click to ban page') }}</span>
                </div>
              </button>
            </form>
            @endif
          </div>
          <div class="section-header-info">
            <h2 class="section-title">{{ __('Reports') }}</h2>
          </div>
        </div>
      </div>
    </div>


    <div class="overflow-x-auto">
    
      <table class="custom-table mb-10">
        <tbody>
          <tr>
            <th class="text-left">{{ __('Message') }}</th>
            <th class="text-left">{{ __('Total Report') }}</th>
          </tr>
          @foreach ($reports as $item)
          <tr>
            <td>{{ $item->desc }}</td>
            <td>{{ $item->total }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    

  </div>
  <div class="sandy-page-col sandy-page-col_pt100">
    
  </div>
</div>
@endsection