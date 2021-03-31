@extends('layouts.app')

@section('content')

<style>
    html,
    body {
        height: 100%;
    }

    .global-container {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    form {
        padding-top: 10px;
        font-size: 14px;
        margin-top: 30px;
    }

    .card-title {
        font-weight: 300;
    }

    .btn {
        font-size: 14px;
        margin-top: 20px;
    }


    .login-form {
        width: 330px;
        margin: 20px;
    }

    .sign-up {
        text-align: center;
        padding: 20px 0 0;
    }

    .alert {
        margin-bottom: -30px;
        font-size: 13px;
        margin-top: 20px;
    }

    .google-button {
        height: 40px;
        border-width: 0;
        background: white;
        color: #737373;
        border-radius: 5px;
        white-space: nowrap;
        box-shadow: 1px 1px 0px 1px rgba(0, 0, 0, 0.05);
        transition-property: background-color, box-shadow;
        transition-duration: 150ms;
        transition-timing-function: ease-in-out;
        padding: 0;
        width: 100%;

        &:focus,
        &:hover {
            box-shadow: 1px 4px 5px 1px rgba(0, 0, 0, 0.1);
        }

        &:active {
            background-color: #e5e5e5;
            box-shadow: none;
            transition-duration: 10ms;
        }
    }

    .google-button__icon {
        display: inline-block;
        vertical-align: middle;
        margin: 8px 0 8px 8px;
        width: 18px;
        height: 18px;
        box-sizing: border-box;
    }

    .google-button__icon--plus {
        width: 27px;
    }

    .google-button__text {
        display: inline-block;
        vertical-align: middle;
        padding: 0 24px;
        font-size: 14px;
        font-weight: bold;
        font-family: 'Roboto', arial, sans-serif;
    }
</style>


<script type="text/javascript">
    jQuery(document).ready(function() {
        $("title").text(`Login / {{ config('app.name', 'Crowdoubt') }}`);

        $("#login-google-btn").click(function (e) {
            window.location.href = "{{ route('login.provider', ['google']) }}";
        })
    })
</script>


<div class="global-container">
    <div class="card login-form">
        <div class="card-body">
            <h3 class="card-title text-center">Log in to {{ config('app.name', 'Crowdoubt') }}</h3>
            <div class="card-text">

                <!--<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Incorrect username or password.
                </div>-->

                <form method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}
                    <!-- to error: add class "has-danger" -->
                    <div class="form-group">
                        <label for="InputEmail1">Email address</label>
                        <input name="email" value="{{ old('email') }}" type="email" class="form-control form-control-sm" id="InputEmail1" aria-describedby="emailHelp" required>
                        @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <label for="InputPassword1">Password</label>
                        <a href="{{ route('password.request') }}" style="float:right;font-size:12px;">Forgot password?</a>
                        <input name="password" type="password" class="form-control form-control-sm" id="InputPassword1">
                        @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Sign in</button>

                    <div class="container-fluid mt-2">
                        <div class="row">
                            <div class="col-12">
                                <button id="login-google-btn" type="button" class="google-button shadow">
                                    <span class="google-button__icon">
                                        <svg viewBox="0 0 366 372" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M125.9 10.2c40.2-13.9 85.3-13.6 125.3 1.1 22.2 8.2 42.5 21 59.9 37.1-5.8 6.3-12.1 12.2-18.1 18.3l-34.2 34.2c-11.3-10.8-25.1-19-40.1-23.6-17.6-5.3-36.6-6.1-54.6-2.2-21 4.5-40.5 15.5-55.6 30.9-12.2 12.3-21.4 27.5-27 43.9-20.3-15.8-40.6-31.5-61-47.3 21.5-43 60.1-76.9 105.4-92.4z" id="Shape" fill="#EA4335" />
                                            <path d="M20.6 102.4c20.3 15.8 40.6 31.5 61 47.3-8 23.3-8 49.2 0 72.4-20.3 15.8-40.6 31.6-60.9 47.3C1.9 232.7-3.8 189.6 4.4 149.2c3.3-16.2 8.7-32 16.2-46.8z" id="Shape" fill="#FBBC05" />
                                            <path d="M361.7 151.1c5.8 32.7 4.5 66.8-4.7 98.8-8.5 29.3-24.6 56.5-47.1 77.2l-59.1-45.9c19.5-13.1 33.3-34.3 37.2-57.5H186.6c.1-24.2.1-48.4.1-72.6h175z" id="Shape" fill="#4285F4" />
                                            <path d="M81.4 222.2c7.8 22.9 22.8 43.2 42.6 57.1 12.4 8.7 26.6 14.9 41.4 17.9 14.6 3 29.7 2.6 44.4.1 14.6-2.6 28.7-7.9 41-16.2l59.1 45.9c-21.3 19.7-48 33.1-76.2 39.6-31.2 7.1-64.2 7.3-95.2-1-24.6-6.5-47.7-18.2-67.6-34.1-20.9-16.6-38.3-38-50.4-62 20.3-15.7 40.6-31.5 60.9-47.3z" fill="#34A853" /></svg>
                                    </span>
                                    <span class="google-button__text">Sign in with Google</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="sign-up">
                        Don't have an account? <a href="{{ route('register') }}">Create One</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection