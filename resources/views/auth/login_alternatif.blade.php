<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/Ionicons/css/ionicons.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('/assets/dist/css/AdminLTE.min.css') }}">
  <style>
    .bg {
      opacity: 0.92;
    }
    .bg:hover {
      opacity: 1;
    }
  </style>

 </head>
 <body class="hold-transition login-page" style="background-image: url('{{ asset('/assets/images/background/sai2.jpg') }}');background-repeat: no-repeat;background-size: cover;">
    <div class="col-md-12">
        <div class="login-box" style="padding-top:125px;">
            <!-- /.login-logo -->
            <div class="login-box-body bg" style="background:#555;color:#FFF;border:2px solid #333;border-radius:10px;">
                <p class="login-box-msg" style="font-size:24px;">EMS LOGIN</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                <div class="form-group has-feedback">
                    <input id="email" placeholder="NIK / Email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input id="password" placeholder="Password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row" style="padding:10px 0px 10px 0px;">
                    <div class="col-xs-8" style="padding-bottom:0px;">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="padding-left:0px;color:#FFF;">
                                {{ __('Reset Password') }}
                            </a>
                        @endif
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat" style="padding:8px;background:#ffbb00;color:#000;border:0px;border-radius:3px;">
                            {{ __('Login') }}
                        </button>
                    </div>
                    <!-- /.col -->
                </div>
                </form>

            </div>
            <!-- /.login-box-body -->
        </div>
    </div>    
    <!-- /.login-box -->

    <!-- jQuery 3 -->
    <script src="{{ asset('/assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('/assets/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

</body>
</html>