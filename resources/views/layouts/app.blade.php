<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'آسان ارسال')</title>

    <meta name="Author" content="Developer" />
    <link rel="icon" href="{{ asset('assets/img/icon.jpeg') }}" type="image/x-icon" />
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            custom: {
                "families": ["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
                urls: ['{{ asset('assets/css/fonts.min.css') }}']
            },
            active: function() {
                sessionStorage.fonts = true;
            }
        });
    </script>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dr.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugin/select2/select2.min.css') }}">
    <script src="{{ asset('assets/plugin/select2/jquery-2.1.4.min.js') }}"></script> 

    <!-- Bootstrap Datepicker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <!-- Additional CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard-step.css') }}">
    <script src="{{ asset('assets/datepicker/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/myHelper.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/plugin/responsive_datatable/css/dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugin/responsive_datatable/css/responsive.bootstrap.css') }}">
       
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Additional Head Content -->
    @stack('head')
    
    @if(session()->has('notification'))
    @php
      $notification = session()->pull('notification');
    @endphp
    <script>
    $(document).ready(function () {
        showNotification(
            {!! json_encode($notification['message']) !!},
            {!! json_encode($notification['type']) !!},
            'top',
            'right',
            'withicon'
        );
    });
   </script>
    @endif

</head>
<body>
    <div class="wrapper">
        <!-- Header -->
        @include('component.header')
        @include('component.sidebar')

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Footer - All scripts moved here -->
    @include('component.footer')

    <!-- Inject script from a view -->
    @stack('scripts')
</body>
</html>