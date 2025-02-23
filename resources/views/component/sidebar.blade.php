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
                            $userImage = \App\Models\User::find($userId)->photo; // Assuming the User model has a `photo` attribute
                        @endphp
                        @if(!empty($userImage))
                            <img src="{{ asset($userImage) }}" alt="..." class="avatar-img rounded-circle">
                        @else
                            <img src="{{ asset('assets/img/no_image.png') }}" alt="..." class="avatar-img rounded-circle">
                        @endif
                    @endif
                </div>
                <div class="info">
                    <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                        <span>
                            {{ auth()->user()->name ?? '' }}
                            <span class="user-level">{{ auth()->user()->roleText ?? '' }}</span>
                            <span class="caret"></span>
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
                @else
                    <p>No Access</p>
                @endif

                @if(auth()->user()->hasAccess('journal', 'list') && $packageId >= 1)
                    <li class="nav-item">
                        <a href="{{ route('journal.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <p> روزنامچه / ژورنال </p>
                        </a>
                    </li>
                @endif

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

                @if(auth()->user()->hasAccess('users', 'list') || auth()->user()->isAdmin())
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
                        <a href="{{ $base_url }}/settings/backup">
                            <i class="fas fa-database"></i>
                            <p> نسخه پشتبان</p>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
