<style>
   .navbar-header {
       /* background: linear-gradient(90deg,#03228f 10%,#0e73e4 100%); */
       background: linear-gradient(90deg, #008fff 10%, #0068dd 50%);
   }
   .logo-header{
       background: linear-gradient(90deg, #008cfa 10%, #0068dd 50%);
   }
   </style>

<script>
    function changeLanguage(locale) {
        window.location.href = '/set-locale/' + locale;
    }
</script>


<div class="main-header">
    <!-- Logo Header -->
    <div class="logo-header" >
        <a href="#" class="logo">
            <img src="{{ asset(\App\Helpers\FunctionHelper::showWhere('logos', 'org_bios', ['is_active' => 1])) }}" alt="navbar brand" class="navbar-brand" style="width: 40px !important; border-radius: 8px;">
        </a>
        <button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
        </button>
        <button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
        <div class="nav-toggle">
            <button class="btn btn-toggle toggle-sidebar"><i class="fas fa-bars"></i></button>
        </div>
    </div>
    <!-- End Logo Header -->

    <!-- Navbar Header -->
    <nav class="navbar navbar-header navbar-expand-lg" data-background-color="gradiant">
        <div class="container-fluid">
            <div class="collapse" id="search-nav">
                 <a href="{{ route('boughtList.create') }}">
                    <button class="btn btn-sm btn-info"><i class="fas fa-cart-arrow-down">&nbsp; {{__('validate.buy')}}</i></button>
                </a>
                <a href="{{ route('sales.create') }}">
                    <button class="btn btn-sm btn-info"><i class="fas fa-luggage-cart">&nbsp; {{__('validate.sale')}}</i></button>
                </a>
                <a href="{{ route('journal.index') }}">
                    <button class="btn btn-sm btn-info"><i class="fas fa-exchange-alt ">&nbsp; {{__('validate.journal')}}</i></button>
                </a> 
                <a href="{{ route('laws.index') }}">
                    <button class="btn btn-sm btn-info"><i class="fas fa-file-invoice-dollar">&nbsp;  {{__('validate.lows')}}</i></button>
                </a>
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
                            <div class="dropdown-title bg-info col-white font-normal">{{__('wh.expired_items')}}</div>
                        </li>
                        <li>
                            <div class="notif-scroll scrollbar-outer">
                                <div class="notif-center" id="expire_date_list" style="padding:8px;max-height: 370px;overflow-y: auto;"></div>
                            </div>
                        </li>
                        <li class="bg-light">
                            <a class="see-all" href="javascript:void(0);">{{__('common.close')}}<i class="fa fa-angle-right"></i></a>
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
                            <div class="dropdown-title bg-info col-white font-normal">{{__('wh.wh_notification')}}</div>
                        </li>
                        <li>
                            <div class="notif-scroll scrollbar-outer">
                                <div class="notif-center" id="warehouse_item_list" style="padding:8px;max-height: 370px;overflow-y: auto;"></div>
                            </div>
                        </li>
                        <li class="bg-light">
                            <a class="see-all" href="javascript:void(0);">{{__('common.close')}}<i class="fa fa-angle-right"></i></a>
                        </li>
                    </ul>
                </li>
                <!-- / warehouse_item -->

                <li class="nav-item">
                    <select onchange="changeLanguage(this.value)" class="form-control" style="border-radius:5px !important;">
                        <option value="fa" {{ app()->getLocale() === 'fa' ? 'selected' : '' }}>دری</option>
                        <option value="pa" {{ app()->getLocale() === 'pa' ? 'selected' : '' }}>پشتو</option>
                        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </li>

                <li class="nav-item hidden-caret">
                    <a href="{{ route('login.logout') }}">
                        <button class="btn btn-info" style="padding:5px 10px;background:#1e5bab !important; border-color:#1269db !important;">{{__('wh.exit')}}</button>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <!-- End Navbar -->
</div>
