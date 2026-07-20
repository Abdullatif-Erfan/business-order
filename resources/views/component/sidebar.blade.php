@php
    $base_url = url('/');
    $user = auth()->user();
    $isAdmin = $user->isAdmin == 1;
    $permissions = [
        'settings' => $user->hasAccess('settings', 'list'),
        'journal' => $user->hasAccess('journal', 'list'),
        'hr' => $user->hasAccess('hr', 'list'),
        'order' => $user->hasAccess('order', 'list'),
        'buy' => $user->hasAccess('buy', 'list'),
        'gudam' => $user->hasAccess('gudam', 'list'),
        'sales' => $user->hasAccess('sales', 'list'),
        'expense' => $user->hasAccess('expense', 'list'),
        'reports' => $user->hasAccess('reports', 'list'),
        'users' => $user->hasAccess('users', 'list'),
        'backup' => $user->hasAccess('backup', 'list'),
    ];

    // Helper function to check if a section is active
    function isSectionActive($routes) {
        $currentRoute = Route::currentRouteName();
        foreach ((array)$routes as $route) {
            if (str_contains($currentRoute, $route)) {
                return true;
            }
        }
        return false;
    }
@endphp

<div class="sidebar sidebar-style-2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <div class="user">
                <div class="avatar-sm float-left float_dr float_en mr-2 avatar avatar-online">
                    @if(auth()->check())
                        @php
                            $userImage = $user->photo;
                            $imagePath = !empty($userImage) && file_exists(storage_path('app/public/' . $userImage))
                                ? asset('storage/' . $userImage)
                                : asset('storage/user_photos/no_image.png');
                        @endphp
                        <img src="{{ $imagePath }}" class="avatar-img rounded-circle">
                    @endif
                </div>

                <div class="info">
                    <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                        <span>
                            {{ $user->full_name ?? '' }}
                            <span class="user-level">{{ $user->user_name ?? '' }}</span>
                        </span>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>

            <ul class="nav nav-primary">
                <!-- ============================================ -->
                <!-- SECTION 1: DASHBOARD -->
                <!-- ============================================ -->
                <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                        <p>{{ __('menu.dashboard') }}</p>
                    </a>
                </li>

                <!-- ============================================ -->
                <!-- SECTION 2: SETTINGS & SYSTEM -->
                <!-- ============================================ -->
                @if($permissions['settings'] || $isAdmin)
                    <li class="nav-item {{ request()->routeIs('setting') ? 'active' : '' }}">
                        <a href="{{ route('setting') }}">
                            <i class="fas fa-cog"></i>
                            <p>{{ __('menu.settings') }}</p>
                        </a>
                    </li>
                @endif

                <!-- ============================================ -->
                <!-- SECTION 3: FINANCE & JOURNAL -->
                <!-- ============================================ -->
                @if($permissions['journal'] || $isAdmin)
                    <li class="nav-item {{ request()->routeIs('journal.*') ? 'active' : '' }}">
                        <a href="{{ route('journal.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <p>{{ __('menu.rooznamcha') }}</p>
                        </a>
                    </li>
                @endif

                <!-- ============================================ -->
                <!-- SECTION 4: HUMAN RESOURCES -->
                <!-- ============================================ -->
                @if($permissions['hr'] || $isAdmin)
                    @php
                        $hrActive = isSectionActive(['employee.', 'salary.', 'salary.report.']);
                    @endphp
                    <li class="nav-item {{ $hrActive ? 'active' : '' }}">
                        <a data-toggle="collapse" href="#hr-section" class="{{ $hrActive ? 'collapsed' : '' }}" aria-expanded="{{ $hrActive ? 'true' : 'false' }}">
                            <i class="fas fa-users"></i>
                            <p>{{ __('menu.hr') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ $hrActive ? 'in' : '' }}" id="hr-section">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('employee.*') ? 'active' : '' }}">
                                    <a href="{{ route('employee.index') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.employee_lists') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('salary.*') ? 'active' : '' }}">
                                    <a href="{{ route('salary.index') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.salary') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('salary.report.*') ? 'active' : '' }}">
                                    <a href="{{ route('salary.report.index') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.report') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- ============================================ -->
                <!-- SECTION 5: ORDERS  -->
                <!-- ============================================ -->
                @if($permissions['order'] || $isAdmin)
                    @php
                        $orderActive = isSectionActive(['draftOrders.', 'order.']);
                    @endphp
                    <li class="nav-item {{ $orderActive ? 'active' : '' }}">
                        <a data-toggle="collapse" href="#orders-section" class="{{ $orderActive ? 'collapsed' : '' }}" aria-expanded="{{ $orderActive ? 'true' : 'false' }}">
                            <i class="fas fa-shopping-cart"></i>
                            <p>{{ __('menu.orders') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ $orderActive ? 'in' : '' }}" id="orders-section">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('draftOrders.index') ? 'active' : '' }}">
                                    <a href="{{ route('draftOrders.index') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.orders_title') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('draftOrders.create') ? 'active' : '' }}">
                                    <a href="{{ route('draftOrders.create') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.create_order') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- ============================================ -->
                <!-- SECTION 6: PURCHASE (BUY) -->
                <!-- ============================================ -->
                @if($permissions['buy'] || $isAdmin)
                    @php
                        $buyActive = isSectionActive(['boughtList.', 'return.', 'buyprelist.']);
                    @endphp
                    <li class="nav-item {{ $buyActive ? 'active' : '' }}">
                        <a data-toggle="collapse" href="#buy-section" class="{{ $buyActive ? 'collapsed' : '' }}" aria-expanded="{{ $buyActive ? 'true' : 'false' }}">
                            <i class="fas fa-cart-arrow-down"></i>
                            <p>{{ __('menu.buy') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ $buyActive ? 'in' : '' }}" id="buy-section">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('boughtList.create') ? 'active' : '' }}">
                                    <a href="{{ route('boughtList.create') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.new_buy') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('boughtList.index') ? 'active' : '' }}">
                                    <a href="{{ route('boughtList.index') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.bought_list') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('boughtListBasedItem.index') ? 'active' : '' }}">
                                    <a href="{{ route('boughtListBasedItem.index') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.bought_list_item') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('return.list') ? 'active' : '' }}">
                                    <a href="{{ route('return.list') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.return') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('boughtList.invoices') ? 'active' : '' }}">
                                    <a href="{{ route('boughtList.invoices') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.invoices') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- ============================================ -->
                <!-- SECTION 7: WAREHOUSE & INVENTORY -->
                <!-- ============================================ -->
                @if($permissions['gudam'] || $isAdmin)
                    <li class="nav-item {{ request()->routeIs('warehousesList.*') ? 'active' : '' }}">
                        <a href="{{ route('warehousesList.index') }}?id=1">
                            <i class="fas fa-luggage-cart"></i>
                            <p>{{ __('menu.warehouse') }}</p>
                        </a>
                    </li>
                @endif

                <!-- ============================================ -->
                <!-- SECTION 8: SALES -->
                <!-- ============================================ -->
                @if($permissions['sales'] || $isAdmin)
                    @php
                        $salesActive = isSectionActive(['sales.', 'soldItemList.']);
                    @endphp
                    <li class="nav-item {{ $salesActive ? 'active' : '' }}">
                        <a data-toggle="collapse" href="#selling-section" class="{{ $salesActive ? 'collapsed' : '' }}" aria-expanded="{{ $salesActive ? 'true' : 'false' }}">
                            <i class="fas fa-file-upload"></i>
                            <p>{{ __('menu.sales') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ $salesActive ? 'in' : '' }}" id="selling-section">
                            <ul class="nav nav-collapse">
                                <li class="{{ request()->routeIs('sales.create') ? 'active' : '' }}">
                                    <a href="{{ route('sales.create') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.new_sales') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">
                                    <a href="{{ route('sales.index') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.sold_list') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('soldItemList.index') ? 'active' : '' }}">
                                    <a href="{{ route('soldItemList.index') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.sold_list_by_item') }}</span>
                                    </a>
                                </li>
                                <li class="{{ request()->routeIs('sales.invoices') ? 'active' : '' }}">
                                    <a href="{{ route('sales.invoices') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.invoices') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- ============================================ -->
                <!-- SECTION 9: EXPENSE -->
                <!-- ============================================ -->
                @if($permissions['expense'] || $isAdmin)
                    <li class="nav-item {{ request()->routeIs('expense.*') ? 'active' : '' }}">
                        <a href="{{ route('expense.index') }}">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <p>{{ __('menu.expense') }}</p>
                        </a>
                    </li>
                @endif

                <!-- ============================================ -->
                <!-- SECTION 10: REPORTS -->
                <!-- ============================================ -->
                @if($permissions['reports'] || $isAdmin)
                    <li class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a href="{{ route('reports.home') }}">
                            <i class="fas fa-list-ol"></i>
                            <p>{{ __('menu.reports') }}</p>
                        </a>
                    </li>
                @endif

                <!-- ============================================ -->
                <!-- SECTION 11: USER MANAGEMENT -->
                <!-- ============================================ -->
                @if($permissions['users'] || $isAdmin)
                    @php
                        $userActive = isSectionActive(['roles.', 'user.']);
                    @endphp
                    <li class="nav-item {{ $userActive ? 'active' : '' }}">
                        <a data-toggle="collapse" href="#user-section" class="{{ $userActive ? 'collapsed' : '' }}" aria-expanded="{{ $userActive ? 'true' : 'false' }}">
                            <i class="fas fa-user-cog"></i>
                            <p>{{ __('menu.user_management') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse {{ $userActive ? 'in' : '' }}" id="user-section">
                            <ul class="nav nav-collapse">
                                @if($isAdmin)
                                    <li class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                        <a href="{{ route('roles.index') }}">
                                            <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                            <span class="sub-item">{{ __('menu.role') }}</span>
                                        </a>
                                    </li>
                                @endif
                                <li class="{{ request()->routeIs('user.*') ? 'active' : '' }}">
                                    <a href="{{ route('user.index') }}">
                                        <i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">{{ __('menu.users') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- ============================================ -->
                <!-- SECTION 12: BACKUP (Admin Only) -->
                <!-- ============================================ -->
                @if($permissions['backup'] || $isAdmin)
                    <li class="nav-item {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                        <a href="{{ route('backups.index') }}">
                            <i class="fas fa-database"></i>
                            <p>{{ __('menu.backup') }}</p>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>