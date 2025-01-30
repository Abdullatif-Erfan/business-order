<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>سیستم مدیریتی تجارت</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <meta name="Author" content="Developer" />
    <link rel="icon" href="{{ asset('assets/img/icon.ico') }}" type="image/x-icon" />
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
    <!-- <link rel="stylesheet" href="{{ asset('assets/css/rtl.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('assets/css/dr.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugin/select2/select2.min.css') }}">
    <script src="{{ asset('assets/plugin/select2/jquery-2.1.4.min.js') }}"></script> 

    <!-- Additional CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('assets/datepicker/jquery.Bootstrap-PersianDateTimePicker.css') }}"> -->
    <script src="{{ asset('assets/datepicker/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/myHelper.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugin/responsive_datatable/css/dataTables.min.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('assets/plugin/responsive_datatable/css/responsive.bootstrap.css') }}"> -->

    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.css" />

    <script>
        window.onload = function() {
            $.ajax({
                type: 'POST',
                url: '{{ route("home.warehouse_item_notify_amount") }}',
                data: {},
                success: function(result) {
                    $('#warehouse_item_notifyable_amount').html(result);
                }
            });

            $.ajax({
                type: 'POST',
                url: '{{ route("home.expired_date_notify_amount") }}',
                data: {},
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
                data: {},
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
                data: {},
                success: function(result) {
                    $("#expire_date_list").html(result);
                },
                error: function(xhr, status) {
                    $('#expire_date_list').html('Error, مشکل رخ داد');
                }
            });
        }
    </script>

    <style>
        span.panel-title a {
            font-size: 14px !important;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="main-header">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="blue">
                <a href="#" class="logo">
                    <img src="{{ asset(\App\Helpers\FunctionHelper::showWhere('logos', 'org_bios', ['is_active' => 1])) }}" alt="navbar brand" class="navbar-brand" style="width: 40px !important; border-radius: 8px;">
                </a>
                <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"><i class="icon-menu"></i></span>
                </button>
                <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
                <div class="nav-toggle">
                    <button class="btn btn-toggle toggle-sidebar"><i class="icon-menu"></i></button>
                </div>
            </div>
            <!-- End Logo Header -->

            <!-- Navbar Header -->
            <nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2">
                <div class="container-fluid">
                    <div class="collapse" id="search-nav">
                        {{-- <a href="{{ route('showBuyingForm') }}">
                            <button class="btn btn-sm btn-info"><i class="fas fa-cart-arrow-down">&nbsp; خرید</i></button>
                        </a>
                        <a href="{{ route('showSalesForm') }}">
                            <button class="btn btn-sm btn-info"><i class="fas fa-luggage-cart">&nbsp; فروش</i></button>
                        </a>
                        <a href="{{ route('movements') }}">
                            <button class="btn btn-sm btn-info"><i class="fas fa-exchange-alt">&nbsp; انتقالات</i></button>
                        </a>
                        <a href="{{ route('journals') }}">
                            <button class="btn btn-sm btn-info"><i class="fas fa-file-invoice-dollar">&nbsp; روزنامچه</i></button>
                        </a> --}}
                    </div>
                    <ul class="navbar-nav topbar-nav ml-md-auto align-items-center">
                        <li class="nav-item toggle-nav-search hidden-caret">
                            <a class="nav-link" data-toggle="collapse" href="#search-nav" role="button" aria-expanded="false" aria-controls="search-nav">
                                <i class="fa fa-search"></i>
                            </a>
                        </li>

                        <!-- expire_date -->
                        <li class="nav-item dropdown hidden-caret">
                            <a class="nav-link dropdown-toggle" href="#" onclick="getExpiredMedicineList()" id="notifDropdown2" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i> <b id="expire_date_notifyable_amount"></b>
                            </a>
                            <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown2">
                                <li>
                                    <div class="dropdown-title bg-info col-white font-normal">آگهی اجناس با تاریخ انقضا</div>
                                </li>
                                <li>
                                    <div class="notif-scroll scrollbar-outer">
                                        <div class="notif-center" id="expire_date_list" style="padding:8px;max-height: 370px;overflow-y: auto;"></div>
                                    </div>
                                </li>
                                <li class="bg-light">
                                    <a class="see-all" href="javascript:void(0);">بستن<i class="fa fa-angle-right"></i></a>
                                </li>
                            </ul>
                        </li>
                        <!-- / expire_date -->

                        <!-- warehouse_item -->
                        <li class="nav-item dropdown hidden-caret">
                            <a class="nav-link dropdown-toggle" href="#" onclick="getWarehouseItemList()" id="notifDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-shopping-cart "></i> <b id="warehouse_item_notifyable_amount"></b>
                            </a>
                            <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                                <li>
                                    <div class="dropdown-title bg-info col-white font-normal">آگهی گدام</div>
                                </li>
                                <li>
                                    <div class="notif-scroll scrollbar-outer">
                                        <div class="notif-center" id="warehouse_item_list" style="padding:8px;max-height: 370px;overflow-y: auto;"></div>
                                    </div>
                                </li>
                                <li class="bg-light">
                                    <a class="see-all" href="javascript:void(0);">بستن<i class="fa fa-angle-right"></i></a>
                                </li>
                            </ul>
                        </li>
                        <!-- / warehouse_item -->

                        <li class="nav-item hidden-caret">
                            <a href="{{ route('login.logout') }}">
                                <button class="btn btn-info" style="padding:5px 10px;background:#1e5bab !important; border-color:#1269db !important;">خروج</button>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End Navbar -->
        </div>
