<div>
    
    @php
        $error = false;

        if(!$errors->isEmpty()){
            $error = $errors->first();
        }

        if(Session::get('error._error')){
            $error = Session::get('error._error');
        }
    @endphp
    @if ($error)
        <div {{ $attributes->merge(['class' => 'bg-red-200 font--11 p-1 px-2 rounded-md']) }}>
            <div class="flex items-center">
                <div>
                    <i class="fi fi-rr-cross-circle flex text-xs"></i>
                </div>
                <div class="flex-grow ml-1 text-xs">{{ $error }}</div>
            </div>
        </div>
    @endif
</div>