<div class="account-content">
    <div class="login-wrapper login-new">
        <div class="container">
            <div class="login-content user-login mx-auto">
                <div class="login-logo text-center mb-4">
                    <a href="javascript:void(0)" class="login-logo">
                        <img src="{{ asset('backend/img/qsoft2.png') }}" alt="logo">
                    </a>
                </div>

                <form wire:submit.prevent="login" autocomplete="off" novalidate>
                    <div class="login-userset">
                        <div class="login-userheading mb-4 text-center">
                            <h3 class="mb-1">Sign In</h3>
                            <h4 class="fw-normal">Access the panel using your email and passcode.</h4>
                        </div>

                        <!-- Error message at the top -->
                        @if($errors->has('invalid_credentials'))
                            <div class="alert alert-danger mb-3 text-center">
                                Invalid Email or Password.
                            </div>
                        @endif

                        <div class="form-login mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="form-addons right-addon">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model.defer="email">
                                <img src="backend/img/icons/mail.svg" alt="img">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message == 'Invalid Email & Password.' ? '' : $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-login mb-3">
                            <label class="form-label">Password</label>
                            <div class="pass-group">
                                <input type="password"
                                    class="pass-input form-control @error('password') is-invalid @enderror"
                                    wire:model.defer="password">
                                <span class="fas toggle-password fa-eye-slash"></span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
<div class="form_login">
    <button class="btn btn-primary w-100" type="submit" wire:loading.attr="disabled" wire:target="login">
        <span wire:loading.remove wire:target="login">Sign In</span>
        <span wire:loading wire:target="login">
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            Processing...
        </span>
    </button>
</div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .login-wrapper .login-content .login-logo img {
            margin-bottom: 10px !important;
        }

        /* Disable hover effect for login button */
.btn-login:hover,
.btn-login:focus,
.btn-login:active {
    background-color: inherit !important;
    color: inherit !important;
    box-shadow: none !important;
    opacity: 1 !important;
}

    </style>
</div>