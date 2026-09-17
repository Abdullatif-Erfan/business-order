<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>آسان ارسال</title>
    <!-- Favicon-->
     <link rel="icon" href="{{ asset('assets/img/icon.jpeg') }}" type="image/x-icon" />

    <!-- Fonts -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ['{{ asset('assets/css/fonts.min.css') }}']},
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>

    <!-- Styles -->
    <link href="{{ asset('assets/css/waves.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/style_login.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/plugin/css/bootstrap.css') }}" rel="stylesheet">
    <style>
        .login-box .form-group label {
                font-size: 14px;
                color: #555;
            }

            .login-box input[type="checkbox"] {
                width: 16px;
                height: 16px;
                accent-color: #e91e63; /* matches bg-pink button */
                cursor: pointer;
            }
    </style>
</head>

<body class="login-page">
     <div class="overlay"></div>
    <div class="login-box animated fadeInDownBig">
        <div class="logo" style="background-color:#fff;">
            <center>
                <img src="{{ asset('assets/img/logo.jpeg') }}" alt="logo" style="width:200px;">
            </center>
        </div>
        <div class="card">
            <div class="body">
                <form action="{{ route('loginMe') }}" method="POST">
                    @csrf
                    <center><h4>ورود به سیســتم</h4></center>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-user"></i></span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="user_name"
                                placeholder="نام کاربری" value="{{ old('user_name') }}"
                                required autofocus>
                        </div>
                        @error('user_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password"
                                placeholder="رمز عبور" required>
                        </div>
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="form-group" style="margin: 10px 0 15px 0;">
                        <label style="display:flex; align-items:center; gap:6px; cursor:pointer; user-select:none;">
                            <input type="checkbox" name="remember" id="remember" value="1"
                                {{ old('remember') ? 'checked' : '' }}>
                            <span>مرا به خاطر بسپار</span>
                        </label>
                    </div>


                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-block bg-pink waves-effect" type="submit">
                                ورود به سیستم
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/plugin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/plugin/js/waves.js') }}"></script>
    <script src="{{ asset('assets/plugin/js/jquery.validate.js') }}"></script>
    <script src="{{ asset('assets/plugin/js/admin.js') }}"></script>
    <script src="{{ asset('assets/plugin/js/sign-in.js') }}"></script>
</body>
</html>
