<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>ورود به سیستم</title>
    <!-- Favicon-->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

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
</head>

<body class="login-page">
    <div class="login-box animated fadeInDownBig">
        <div class="logo" style="background-color:#fff;">
            <center>
                <img src="{{ asset('assets/img/logo.png') }}" alt="logo" style="width:200px;">
            </center>
        </div>
        <div class="card">
            <div class="body">
                <form action="{{ route('loginMe') }}" method="POST">
                    @csrf
                    <center><h4>ورود به سیســتم</h4></center>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="user_name" placeholder="نام کاربری" value="erfan" required autofocus>
                        </div>
                          @error('user_name')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="fa fa-lock"></i>
                        </span>
                        <div class="form-line">
                            <input type="password" value="password123" class="form-control" name="password" placeholder="رمز عبور" required>
                        </div>

                        @error('password')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-12 col-sm-12 col-xs-12">
                        @if(session('empty'))
                            <h4 style="text-align:center;color:red;border:1px solid #999;padding:8px;">
                                بدون رمز عبور داخل شده نمیتوانید
                            </h4>
                        @elseif(session('failed'))
                            <h4 style="text-align:center;color:red;border:1px solid #999;padding:8px;">
                                آیدی و یا رمز عبور اشتباه میباشد
                            </h4>
                        @elseif(session('login_first'))
                            <h4 style="text-align:center;color:red;border:1px solid #999;padding:8px;">
                                باید با آیدی داخل شوید
                            </h4>
                        @elseif(session('not_exist'))
                            <h4 style="text-align:center;color:red;border:1px solid #999;padding:8px;">
                                این کاربر بدون رول میباشد ویا غیر فعال میباشد
                            </h4>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-block bg-pink waves-effect" type="submit">ورود به سیستم</button>
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
