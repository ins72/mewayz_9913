<?php
    use App\Models\YenaTeamsInvite;
    use App\Yena\Teams;
    use function Livewire\Volt\{state, mount, placeholder};

    state([
        'team'
    ]);

    state([
        'customError' => null,
        'joined' => false,
    ]);

    mount(function(){
        $user = auth()->user();

        if(!auth()->check()){
            $this->customError = __('Login to continue.');
        }

        if(auth()->check()){
            if(!$this->customError && $user->id == $this->team->owner_id){
                $this->customError = __('Cannot join your own team.');
            }

            if(!$this->customError && Teams::has_team($user->id, $this->team->id)){
                $this->customError = __('You are in this team.');
            }
        }
    });

    $join = function(){
        $user = auth()->user();

        $join = Teams::add_to_team($user->id, $this->team->id, 0, [
            'can_create' => 0,
            'can_edit' => 0,
            'can_delete' => 0,
        ]);

        
        YenaTeamsInvite::where('team_id', $this->team->id)->where('email', $user->email)->delete();
        $this->redirect(route('dashboard-index'));
    };

    placeholder(function(){

        $loader = '<div class="p-12"><div class="container"><div class="loading"><i></i><i></i><i></i><i></i></div></div></div>';

        $loader = '<div class="p-12"><div class="loader-animation-container"><div class="inner-circles-loader"></div></div></div>';
        return $loader;
    });
?>

<div class="h-screen">

    <div class="h-full !max-w-full p-12 md:p-12 lg:p-24">

        <div class="flex flex-col gap-[var(--yena-space-6)] flex-1 h-full">
            <img src="{{ logo() }}" class="h-16 w-16 object-contain" alt=" " width="36" class="block">

            <div class="flex-1 place-self-stretch"></div>

            <div>{{ __('🎉 Welcome to the party') }}</div>
            <h2 class="text-4xl relative font-medium leading-[1.2em] tracking-[-0.03em] md:text-4xl">{{ __('Join :workspace on :site.', [
                'workspace' => $team->name,
                'site' => config('app.name')
               ]) }}</h2>

            <p class="yena-text">{{ __('After joining, you\'ll be able to view, edit, and share sites in the :workspace workspace.', [
                'workspace' => $team->name
            ]) }}</p>

            <div>
                @if (auth()->check() && !$customError)
                    <div class="flex flex-col">
                        <button type="submit" wire:click="join" class="yena-button-stack --black">{{ __('Join workspace') }}</button>
                    </div>
                @endif
                
                @if (!auth()->check())
                    <div class="flex flex-col">
                        <a href="{{ route('login') }}" wire:navigate class="yena-button-stack --black">{{ __('Login') }}</a>
                    </div>
                @endif

                @php
                    $error = false;
            
                    if(!$errors->isEmpty()){
                        $error = $errors->first();
                    }
                    
                    if($customError) $error = $customError;
                    if(Session::get('error')) $error = Session::get('error');
                @endphp
                
                @if ($error)
                    <div class="mb-5 mt-2 bg-red-200 text-[11px] p-1 px-2 rounded-md">
                        <div class="flex items-center">
                            <div>
                                <i class="fi fi-rr-cross-circle flex"></i>
                            </div>
                            <div class="flex-grow ml-1">{{ $error }}</div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex-1 place-self-stretch"></div>

            <div class="flex items-center justify-center">
                <div>
                    <img src="{{ logo_icon() }}" class="h-10 w-10 object-contain" alt=" " width="36" class="block">
                </div>
            </div>
            <div class="text-[11px] text-center color-gray mt-5">
                @php
                    $terms_link = settings('others.terms');
                    $privacy_link = settings('others.privacy');
                @endphp
                {!! __t("By joining, I agree to the <a href=\"$terms_link\">Terms of service</a> and <a href=\"$privacy_link\">Privacy policy</a>.") !!}
            </div>
        </div>
    </div>
</div>