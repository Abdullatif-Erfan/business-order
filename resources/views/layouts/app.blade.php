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

     <!-- Bootstrap Datepicker CSS - Make sure this is loaded -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <!-- Additional CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard-step.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.css') }}"> -->
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

            // Automatically hide after 2 seconds (if you're using a custom alert)
            setTimeout(function () {
                $('.alert').fadeOut(); // adjust if your alert uses a different class
            }, 2000);
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

    <!-- Footer -->
    <!-- @include('component.footer3') -->

     <!-- Core JS Files -->
    <script src="{{ asset('assets/js/core/custom.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/atlantis.min.js') }}"></script>
    <script src="{{ asset('assets/plugin/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>


  
    
    <script type="text/javascript">
        $(function () {
            $(".select2").select2();
        });
    </script>

 <script>
$(document).ready(function() {
    // Initialize all datepickers
    function initDatepicker(selector) {
        $(selector).datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom auto',
            clearBtn: true,
            todayBtn: 'linked'
        });
    }
    
    // Initialize specific datepickers
    $('#datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom'
    });
    
    $('#start_date, #end_date').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto',
        clearBtn: true,
        todayBtn: 'linked'
    });
    
    // Single click handler for all datepicker icons - use a flag to prevent double trigger
    // $(document).on('click', '.datepicker-icon', function(e) {
    //     e.preventDefault();
    //     e.stopPropagation();
    //     var $input = $(this).closest('.input-group').find('input');
    //     if ($input.length) {
    //         $input.datepicker('show');
    //     }
    // });
});
</script>
<script>
    function toggleFilterForm() {
    var elem = document.getElementById('searchWrapper');
    var icon = document.querySelector('.responsive_button i');
    
    if (!elem) return;
    
    // Toggle the 'filter-visible' class
    elem.classList.toggle('filter-visible');
    
    // Update icon
    if (elem.classList.contains('filter-visible')) {
        if (icon) icon.className = 'fas fa-times';
    } else {
        if (icon) icon.className = 'fas fa-filter';
    }
}
</script>



<script>
    function showNotification(message, type = 'info', from = 'top', align = 'left', style = 'withicon') {
        var content = {};
        content.message = '<span style="font-size:16px;">' + message + '</span>';
        content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> {{ __('settings.message') }} </span>';
        
        if (style === "withicon") {
            content.icon = 'fa fa-bell';
        } else {
            content.icon = 'none';
        }
        content.url = '#';
        content.target = '_blank';

        $.notify(content, {
            type: type, // Default, Primary, Secondary, Info, Success, Warning, Danger
            placement: {
                from: from, // top, bottom
                align: align // right, center, left
            },
            time: 500
        });
    }
</script>
        
    <!-- Inject script from a view -->
    @stack('scripts')

</body>
</html>
