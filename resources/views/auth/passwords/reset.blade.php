@extends('layouts.loginapp')

@section('content')

<style>
    #toggle-password {
    padding: 0.375rem 0.75rem; /* Adjust the padding to fit the input height */
    font-size: 1.25rem; /* Set font size for the icon */
    display: flex;
    align-items: center;
    justify-content: center;
}

#eye-icon {
    font-size: 1.25rem; /* Adjust the icon size */
}

</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                            <input id="email" type="email" class="form-control" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                <script>
                                    document.getElementById('email').addEventListener('keydown', function(event) {
                                        event.preventDefault();
                                    });
                                </script>

                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="my-password" type="password" class="form-control  @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary " id="toggle-password" onclick="myFunction()">
                                        <i class="fa fa-eye" id="eye-icon"></i>
                                    </button>
                                </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control"  name="password_confirmation" required autocomplete="new-password">
                            </div>
                            <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary " id="toggle-password" onclick="confirmFunction()">
                                        <i class="fa fa-eye" id="eye-ConfirmIcon"></i>
                                    </button>
                                </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function myFunction() {
    var x = document.getElementById("my-password");
    var eyeIcon = document.getElementById("eye-icon");

    if (x.type === "password") {
        x.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        x.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
}

function confirmFunction() {
    var x = document.getElementById("password-confirm");
    var eyeIcon = document.getElementById("eye-ConfirmIcon");

    if (x.type === "password") {
        x.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        x.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
}
</script>
@endsection
