<div>
    <form wire:submit.prevent="login">
        <div class="floating-input">
            <input type="email" wire:model="email" class="form-control modallogin-inpt shadow-none" id="modal_email" required>
            <label for="modal_email">Email</label>
            @error('email')
                <span class="text-danger text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="floating-input">
            <input type="password" wire:model="password" class="form-control modallogin-inpt shadow-none" id="modal_password" required>
            <label for="modal_password">Password</label>
            @error('password')
                <span class="text-danger text-xs">{{ $message }}</span>
            @enderror
            <div class="mt-4 text-center">
                <button type="button" class="border-0 signupbotna p-0 knkdk" data-bs-toggle="modal" data-bs-target="#staticBackdrop-2">
                    Forgot password?
                </button>
            </div>
        </div>

        <div class="mb-3 mt-2">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" wire:model="remember" id="modal_remember">
                <label class="form-check-label" for="modal_remember">
                    Remember me
                </label>
            </div>
        </div>

        <div class="sing-in">
            <button type="submit" class="sign-btn border-0 w-100">
                <div wire:loading wire:target="login"> 
                    <div class="loader-animation-container flex items-center justify-center"><div class="inner-circles-loader !w-5 !h-5"></div></div>
                </div>
                <div class="flex justify-center items-center" wire:loading.class="!hidden" wire:target="login">
                    Sign in
                </div>
            </button>
        </div>
    </form>
</div> 