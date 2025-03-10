@php
    $base_url = url('/');
    $packageId = \App\Helpers\ManagementHelper::activePackageId();
@endphp

<div class="sidebar sidebar-style-2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-left float_dr float_en mr-2 avatar avatar-online">
                    @if(auth()->check())
                        @php
                            $userId = auth()->id();
                            $userImage = auth()->user()->photo;
                            $imagePath = !empty($userImage) && file_exists(storage_path('app/public/' . $userImage))
                                ? asset('storage/' . $userImage)
                                : asset('storage/user_photos/no_image.png');
                        @endphp
                        <img src="{{ $imagePath }}" alt="User ID: {{ $userId }}" class="avatar-img rounded-circle">
                        <p class="mt-2 text-center">User ID: {{ $userId }}</p>
                    @endif
                </div>

                <div class="info">
                    <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                        <span>
                            {{ auth()->user()->full_name ?? '' }}
                            <span class="user-level">{{ auth()->user()->user_name ?? '' }}</span>
                            <!-- <span class="caret"></span> -->
                        </span>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>
            <ul class="nav nav-primary">
                <li class="nav-item">
                    <a href="{{ $base_url }}/home">
                        <i class="fas fa-home"></i>
                        <p>صفحه اصلی</p>
                    </a>
                </li>

                @if(auth()->user()->hasAccess('settings', 'list'))
                    <li class="nav-item">
                        <a href="{{ route('setting') }}">
                            <i class="fas fa-cog"></i>
                            <p> تنظیمات اولیه </p>
                        </a>
                    </li>
                @endif

                    <li class="nav-item">
                        <a href="{{ route('rate.index') }}">
                            <i class="fas fa-percent"></i>
                            <p> نرخ ارزها</p>
                        </a>
                    </li>


                    <li class="nav-item">
                        <a data-toggle="collapse" href="#journal">
                          <i class="fas fa-file-invoice-dollar"></i>
                            <p> معاملات </p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="journal">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('journal.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> روزنامچه  </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('income.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> عواید </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('expense.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> مصارف</span>
                                    </a>
                                </li>
                                <!-- <li>
                                    <a href="{{ route('boughtList.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> سهم سهامداران</span>
                                    </a>
                                </li> -->
                            </ul>
                        </div>
                    </li>


                    <li class="nav-item">
                        <a data-toggle="collapse" href="#hr">
                          <i class="fas fa-users"></i>
                            <p> منابع بشری </p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="hr">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('employee.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> لیست کارمندان  </span>
                                    </a>
                                </li>
                                <!-- <li>
                                    <a href="{{ route('income.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> پیش پرداخت </span>
                                    </a>
                                </li> -->
                                <li>
                                    <a href="{{ route('salary.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  پرداخت معاشات </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('salary.report.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> گزارش معاشات</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                @if(auth()->user()->hasAccess('gen_buy', 'list') && $packageId >= 1)
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#buy-chicken">
                            <i class="fas fa-cart-arrow-down"></i>
                            <p>خرید عمومی</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="buy-chicken">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('buyprelist.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> ثبت اجناس برای خرید</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('boughtList.create') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> خرید جدید</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('boughtList.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> لیست خرید</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif


                @if(auth()->user()->hasAccess('gudam', 'list') && $packageId >= 1)
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#items">
                            <i class="fas fa-luggage-cart"></i>
                            <p> گدام</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="items">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('warehousesList.create') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> ثبت موجودی گدام</span>
                                    </a>
                                </li>
                                @foreach(\App\Models\Setting\Warehouse::all() as $warehouse)
                                    <li>
                                        <a href="{{ route('warehousesList.index') }}?id={{ $warehouse->id }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                            <span class="sub-item">{{ $warehouse->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endif

                @if(auth()->user()->hasAccess('sales', 'list') && $packageId >= 1)
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#selling">
                            <i class="fas fa-file-upload"></i>
                            <p> فروشات</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="selling">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('sales.create') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> فروشات جدید</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('sales.index')  }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> لیست فروشات</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif


                <li class="nav-item">
                        <a data-toggle="collapse" href="#clearance">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <p> تصفیه حسابات </p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="clearance">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('clearance.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> تصفیه حساب خرید</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('clearance.sales.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> تصفیه حساب فروشات</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- <li class="nav-item">
                        <a data-toggle="collapse" href="#reports">
                            <i class="fas fa-list-ol"></i>
                            <p>  گزارشات </p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="reports">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="cashflow"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> کهاته مشتریان </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="chartOfAccount"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> چارت حسابات </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li> -->

                    <li class="nav-item">
                        <a href="{{ route('reports.home') }}">
                            <i class="fas fa-list-ol"></i>
                            <p> گزارشات</p>
                        </a>
                    </li>


                @if(auth()->user()->hasAccess('users', 'list') || auth()->user()->isAdmin)
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#user">
                            <i class="fas fa-users"></i>
                            <p> مدیریت کاربران</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="user">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('roles.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> رول</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('user.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> کاربران</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if(!empty($packageId))
                    <li class="nav-item">
                        <a href="{{ route('backups.index') }}">
                            <i class="fas fa-database"></i>
                            <p> نسخه پشتبان</p>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
