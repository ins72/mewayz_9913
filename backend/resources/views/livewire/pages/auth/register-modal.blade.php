<div>
    <form wire:submit.prevent="register">
        <div class="floating-input">
            <input type="email" wire:model="email" class="form-control modallogin-inpt shadow-none" id="register_modal_email" required wire:change="checkEmail">
            <label for="register_modal_email">Email</label>
            @error('email')
                <span class="text-danger text-xs">{{ $message }}</span>
            @enderror
        </div>

        @if($showForm)
            <div wire:transition>
                <div class="floating-input mt-3">
                    <input type="text" wire:model="name" class="form-control modallogin-inpt shadow-none" id="register_modal_name" required>
                    <label for="register_modal_name">Name</label>
                    @error('name')
                        <span class="text-danger text-xs">{{ $errors->first('name') }}</span>
                    @enderror
                </div>
                <div class="floating-input mt-3">
                    <input type="password" wire:model="password" class="form-control modallogin-inpt shadow-none" id="register_modal_password" required>
                    <label for="register_modal_password">Password</label>
                    @error('password')
                        <span class="text-danger text-xs">{{ $errors->first('password') }}</span>
                    @enderror
                </div>
                <div class="floating-input mt-3">
                    <input type="password" wire:model="password_confirmation" class="form-control modallogin-inpt shadow-none" id="register_modal_password_confirmation" required>
                    <label for="register_modal_password_confirmation">Confirm Password</label>
                </div>

                <div class="sing-in mt-4">
                    <button type="submit" class="sign-btn border-0 w-100">
                        <div wire:loading wire:target="register"> 
                            <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-5 !h-5"></div></div>
                        </div>
                        <div wire:loading.class="!hidden" wire:target="register" style="text-align: center;">
                            Create an account
                        </div>
                    </button>
                </div>
            </div>
        @else
            <div class="sing-in mt-4">
                <button type="button" wire:click="checkEmail" class="sign-btn disable-btn border-0 w-100" @if(!$email) disabled @endif>
                    <div wire:loading wire:target="checkEmail"> 
                        <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-5 !h-5"></div></div>
                    </div>
                    <div wire:loading.class="!hidden" wire:target="checkEmail" style="text-align: center;">
                        Continue with email
                    </div>
                </button>
            </div>
        @endif
    </form>
</div> 