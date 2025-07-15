    <link rel="icon" href="{{ asset('syn/assets/images/Ellipse 9.png') }}">
    <link href="{{ asset('syn/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('syn/assets/css/style.css') }}">



    <div class="main-nav">
        <div class="main-content d-flex align-items-center justify-content-between">
            <div class="left-content d-flex align-items-center">
                <div class="logo">
                    <button id="toggleMode" class="border-0 p-0 dark-btn "><img src="{{ asset('syn/assets/images/Logo.png') }}" alt=""></button>
                </div>
                <div class="menu-bar">
                    <img src="{{ asset('syn/assets/images/Burger.png') }}" alt="">
                </div>
                <form>
                    <div class="nav-inut ">
                        <input type="search" class="form-control border-0 shadow-none nav-place" placeholder="Search creators..." id="exampleInputEmail1" aria-describedby="emailHelp">
                        <div class="serch-icon">
                            <img src="{{ asset('syn/assets/images/search.png') }}" alt="">
                        </div>
                    </div>
                </form>
            </div>
            <div class="right-content d-flex align-items-center">
                <div class="create d-none d-lg-block">
                    <button type="button" class=" border-0" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Create</button>
                </div>
                <div class="menu-bar-2">
                    <img src="{{ asset('syn/assets/images/bell.png') }}" alt="">
                </div>
                <div class="menu-bar-2">
                    <img src="{{ asset('syn/assets/images/message.png') }}" alt="">
                </div>
                <div class="menu-bar-3">
                    <img class="d-lg-block d-none" src="{{ asset('syn/assets/images/Ellipse 9.png') }}" alt="">
                    <button type="button" class=" border-0 d-lg-none d-block sdsd" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><img src="{{ asset('syn/assets/images/Ellipse 9.png') }}" alt=""></button>
                </div>
            </div>
        </div>
    </div>





    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade crud-modal" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-header border-0 p-0">
            <button type="button" class=" shadow-none border-0 modale-cancel" data-bs-dismiss="modal" aria-label="Close"><img src="{{ asset('syn/assets/images/cancel.svg') }}" alt=""></button>
        </div>
        <div class="modal-dialog modal-dialog-centered modal-login-cont">
            <div class="modal-content modal-erfb">
                <div class="modal-body modal-login">
                    <div class="login-content">
                        <div class="sign">
                            <h2 class="m-0">Sign in to Core 2.0</h2>
                        </div>
                        @if (config('app.GOOGLE_ENABLE'))
                        <div class="google">
                            <a href="{{ route('auth.driver.redirect', 'google') }}">
                                <button type="button" class="google-btn"><img src="{{ asset('syn/assets/images/google.svg') }}" alt="">Sign in with Google</button>
                            </a>
                        </div>
                        @endif
                        <div class="headidfdg">
                            <h5 class="m-0">Or sign in with email</h5>
                        </div>
                        
                        <!-- Include the Livewire login component -->
                        @livewire('pages.auth.login-modal')

                        <div class="login-footer d-flex justify-content-center mt-4">
                            <div class="need">
                                <h5 class="m-0">Need an account?</h5>
                            </div>
                            <div class="sifsknu ms-1">
                                <button type="button" class="border-0 signupbotna p-0" data-bs-toggle="modal" data-bs-target="#staticBackdrop-1"><h2 class="m-0">Sign up</h2></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Create Account Modal signup -->
    <div class="modal fade crud-modal" id="staticBackdrop-1" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-header border-0 p-0">
            <button type="button" class=" shadow-none border-0 modale-cancel" data-bs-dismiss="modal" aria-label="Close"><img src="{{ asset('syn/assets/images/cancel.svg') }}" alt=""></button>
        </div>
        <div class="modal-dialog modal-dialog-centered modal-login-cont">
            <div class="modal-content modal-erfb">
                <div class="modal-body modal-login">
                    <div class="login-content">
                        <div class="sign">
                            <h2 class="m-0">Create an account</h2>
                        </div>
                        @if (config('app.GOOGLE_ENABLE'))
                        <div class="google">
                            <a href="{{ route('auth.driver.redirect', 'google') }}">
                                <button type="button" class="google-btn"><img src="{{ asset('syn/assets/images/google.svg') }}" alt="">Sign up with Google</button>
                            </a>
                        </div>
                        @endif
                        <div class="headidfdg">
                            <h5 class="m-0">Or use your email</h5>
                        </div>
                        
                        <!-- Include the Livewire register component -->
                        @livewire('pages.auth.register-modal')

                        <div class="login-footer d-flex justify-content-center mt-4">
                            <div class="need">
                                <h5 class="m-0">Already have an account?</h5>
                            </div>
                            <div class="sifsknu ms-1">
                                <button type="button" class="border-0 signupbotna p-0" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><h2 class="m-0">Sign in</h2></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>










    <!-- Reset Password Modal -->

    <div class="modal fade crud-modal" id="staticBackdrop-2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-header border-0 p-0">
            <button type="button" class=" shadow-none border-0 modale-cancel" data-bs-dismiss="modal" aria-label="Close"><img src="{{ asset('syn/assets/images/cancel.svg') }}" alt=""></button>
        </div>
        <div class="modal-dialog modal-dialog-centered modal-login-cont">
            <div class="modal-content modal-erfb">
                <div class="modal-body modal-login">
                    <div class="login-content">
                        <div class="sign ressldt">
                            <h2 class="m-0">Reset password</h2>
                        </div>
                        
                        <!-- Include the Livewire password reset component -->
                        @livewire('pages.auth.forgot-password-modal')

                        <div class="login-footer d-flex justify-content-center mt-4">
                            <div class="need">
                                <h5 class="m-0">Have your password?</h5>
                            </div>
                            <div class="sifsknu ms-1">
                                <button type="button" class="border-0 signupbotna p-0" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><h2 class="m-0">Login</h2></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>












    <script src="{{ asset('syn/assets/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        // Set dark mode by default
        document.body.classList.add('dark-mode');
        
        const toggleBtn = document.getElementById('toggleMode');
        toggleBtn.addEventListener('click', () => {
            document.body.classList.toggle('dark-mode');
        });
    </script>
