<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'سیستم مدیریت تجارت')</title>

    <meta name="Author" content="Developer" />
    <link rel="icon" href="{{ asset('assets/img/icon.png') }}" type="image/x-icon" />
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

    <!-- Additional CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.css') }}">
    <script src="{{ asset('assets/datepicker/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/myHelper.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/plugin/responsive_datatable/css/dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugin/responsive_datatable/css/responsive.bootstrap.css') }}">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <script>
        window.onload = function() {
            $.ajax({
                type: 'POST',
                url: '{{ route("home.warehouse_item_notify_amount") }}',
                data: { _token: '{{ csrf_token() }}' },
                success: function(result) {
                    $('#warehouse_item_notifyable_amount').html(result);
                }
            });

            $.ajax({
                type: 'POST',
                url: '{{ route("home.expired_date_notify_amount") }}',
                data: { _token: '{{ csrf_token() }}' },
                success: function(result) {
                    $('#expire_date_notifyable_amount').html(result);
                }
            });
        }

        function getWarehouseItemList() {
            $('#warehouse_item_list').html('<center><img src="{{ asset("assets/img/small_loader.gif") }}" style="width:12%;margin-top:30px;margin-bottom:20px" alt="Loading"/></center>');
            $.ajax({
                type: 'POST',
                url: '{{ route("home.warehouse_item_list") }}',
                data: { _token: '{{ csrf_token() }}' },
                success: function(result) {
                    $("#warehouse_item_list").html(result);
                },
                error: function(xhr, status) {
                    $('#warehouse_item_list').html('Error, مشکل رخ داد');
                }
            });
        }

        function getExpiredMedicineList() {
            $('#expire_date_list').html('<center><img src="{{ asset("assets/img/small_loader.gif") }}" style="width:12%;margin-top:30px;margin-bottom:20px" alt="Loading"/></center>');
            $.ajax({
                type: 'POST',
                url: '{{ route("home.get_expire_date_list") }}',
                data: { _token: '{{ csrf_token() }}' },
                success: function(result) {
                    $("#expire_date_list").html(result);
                },
                error: function(xhr, status) {
                    $('#expire_date_list').html('Error, مشکل رخ داد');
                }
            });
        }
    </script>


    <!-- Additional Head Content -->
    @stack('head')
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

    <!-- Footer -->
    <!-- @include('component.footer3') -->

     <!-- Core JS Files -->
    <script src="{{ asset('assets/js/core/custom.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/atlantis.min.js') }}"></script>
    <script src="{{ asset('assets/plugin/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>


    <script src="{{ asset('assets/plugin/responsive_datatable/js/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugin/responsive_datatable/js/dataTables.responsive.js') }}"></script>
    
    <script type="text/javascript">
        $(function () {
            $(".select2").select2();
        });
    </script>
        
    <!-- Inject script from a view -->
    @stack('scripts')

</body>
</html>
