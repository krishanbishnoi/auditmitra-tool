@extends('layouts.loginapp')



@section('content')

<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card">

                <div class="card-header">{{ __('Reset Password') }}</div>



                <div class="card-body">

                    @if (session('status'))

                        <div class="alert alert-success" role="alert">

                            {{ session('status') }}

                        </div>

                    @endif



                    <form method="POST" action="{{ route('password.email') }}">

                        @csrf



                        <div class="form-group">
                            <!-- Email Label -->
                            

                            <!-- Centered column with col-6 -->
                            <div class="col-6 mx-auto">
                                <label for="email" class="">EMAIL ADDRESS</label>
                                <input id="email" type="email" placeholder="Enter Email Address" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group row mb-0">

                            <div class="col-md-12 mt-2 text-center">

                                <button type="submit" class="btn btn-primary text-center">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                            

                            <div class="col-md-12 text-center mt-4">
                                <a href="{{ route('login') }}" class="text-center">
                                    <i class="fa fa-arrow-left"></i> Back To Login Page
                                </a>
                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection

