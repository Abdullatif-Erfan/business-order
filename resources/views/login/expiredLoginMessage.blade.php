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
                    <div class="alert alert-danger" style="margin-top:50px; margin-bottom:50px">
                    <p>
                        مشتری محترم <br/>
                        هاست و دامین شما نیاز به تمدید دارد. هرچه عاجلتر اقدام نمایید
                        <br/>

                        @if(session('expired_days') !== null)
                            از هاست شما <strong>( {{ session('expired_days') }} ) </strong> روز گذشته است.
                        @elseif(session('expired_text'))
                            {{ session('expired_text') }}
                        @endif

                    </p>
                    </div>

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
