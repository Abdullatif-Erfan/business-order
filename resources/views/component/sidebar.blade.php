@php
    $base_url = url('/');
    $packageId = session('package_type');
    $user = auth()->user();
    $isAdmin = $user->isAdmin == 1;
    $branch_id = $user->branch_id ?? 0;
    $permissions = [
        'settings' => $user->hasAccess('settings', 'list'),
        'rates' => $user->hasAccess('rates', 'list'),
        'journal' => $user->hasAccess('journal', 'list'),
        'income' => $user->hasAccess('income', 'list'),
        'expense' => $user->hasAccess('expense', 'list'),
        'hr' => $user->hasAccess('hr', 'list'),
        'buy' => $user->hasAccess('buy', 'list'),
        'gudam' => $user->hasAccess('gudam', 'list'),
        'production' => $user->hasAccess('production', 'list'),
        'sales' => $user->hasAccess('sales', 'list'),
        'clearance' => $user->hasAccess('clearance', 'list'),
        'reports' => $user->hasAccess('reports', 'list'),
    ];
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
                        <img src="{{ $imagePath }}"  class="avatar-img rounded-circle">
                    @endif
                </div>

                <div class="info">
                    <a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
                        <span>
                            {{ $user->full_name ?? '' }}
                            <span class="user-level">{{ $user->user_name ?? '' }}</span>
                            <!-- <span class="caret"></span> -->
                        </span>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>
            <ul class="nav nav-primary">
                <li class="nav-item">
                    <a href="{{ route('home') }}">
                        <i class="fas fa-home"></i>
                        <p> {{ __('menu.dashboard') }}  </p> 
                    </a>
                </li>

                @if($permissions['settings'] || $isAdmin)
                    <li class="nav-item">
                        <a href="{{ route('setting') }}">
                            <i class="fas fa-cog"></i>
                            <p> {{ __('menu.settings') }} </p>
                        </a>
                    </li>
                @endif

                @if(($permissions['rates'] || $isAdmin) && $packageId >= 2)
                    <li class="nav-item">
                        <a href="{{ route('rate.index') }}">
                            <i class="fas fa-percent"></i>
                            <p> {{ __('menu.rate') }}</p>
                        </a>
                    </li>
                 @endif

                    <li class="nav-item">
                        <a data-toggle="collapse" href="#journal">
                          <i class="fas fa-file-invoice-dollar"></i>
                            <p> {{ __('menu.transaction') }} </p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="journal">
                            <ul class="nav nav-collapse">
                                @if($permissions['journal'] || $isAdmin)
                                <li>
                                    <a href="{{ route('journal.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> {{ __('menu.rooznamcha') }}  </span>
                                    </a>
                                </li>
                                @endif
                                @if(($permissions['income'] || $isAdmin))
                                <li>
                                    <a href="{{ route('income.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> {{ __('menu.income') }} </span>
                                    </a>
                                </li>
                                @endif
                                @if(($permissions['expense'] || $isAdmin))
                                <li>
                                    <a href="{{ route('expense.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> {{ __('menu.expense') }}</span>
                                    </a>
                                </li>
                                @endif
                                <!-- <li>
                                    <a href="{{ route('boughtList.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> سهم سهامداران</span>
                                    </a>
                                </li> -->
                            </ul>
                        </div>
                    </li>


                    @if(($permissions['hr'] || $isAdmin) && $packageId >= 3)
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#hr">
                          <i class="fas fa-users"></i>
                            <p>  {{ __('menu.hr') }} </p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="hr">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('employee.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.employee_lists') }}  </span>
                                    </a>
                                </li>
                                <!-- <li>
                                    <a href="{{ route('income.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> پیش پرداخت </span>
                                    </a>
                                </li> -->
                                <li>
                                    <a href="{{ route('salary.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.salary') }} </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('salary.report.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.report') }} </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endif

                    @if($permissions['buy'] || $isAdmin)
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#buy-chicken">
                            <i class="fas fa-cart-arrow-down"></i>
                            <p> {{ __('menu.buy') }}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="buy-chicken">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('buyprelist.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> {{ __('menu.buy_pre_list')}} </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('boughtList.create') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.new_buy')}} </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('boughtList.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.bought_list')}} </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('boughtListBasedItem.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.bought_list_item')}} </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if($permissions['gudam'] || $isAdmin)
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#items">
                            <i class="fas fa-luggage-cart"></i>
                            <p> {{ __('menu.warehouse')}}</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="items">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('warehousesList.create') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.warehouse_add')}} </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('warehousesList.wastage') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.wastage')}}   </span>
                                    </a>
                                </li>
                                @foreach(\App\Models\Setting\Warehouse::where('branch_id', $branch_id)->get() as $warehouse)
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


                @if(($permissions['production'] || $isAdmin) && $packageId == 5)
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#production">
                            <i class="fas fa-plus-circle"></i>
                            <p> {{ __('menu.production') }} </p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="production">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{ route('model.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> {{__('production.model')}}  </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('qalam.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> {{__('production.qalam')}}  </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if($permissions['sales'] || $isAdmin)
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#selling">
                            <i class="fas fa-file-upload"></i>
                            <p> {{ __('menu.sales') }} </p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="selling">
                            <ul class="nav nav-collapse">
                                 <li>
                                    <a href="{{ route('sales.create') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> {{ __('menu.new_sales')}} </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('sales.createWithOtherCurrency') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> {{ __('menu.new_sales_with_other_currency')}} </span>
                                    </a>
                                </li>
                                <!-- businessplus + POS -->
                                @if($packageId == 4)
                                <li>
                                    <a href="{{ route('sales.pos_create') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.pos_sales')}} </span>
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a href="{{ route('sales.index')  }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.sold_list')}}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('soldItemList.index')  }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.sold_list_by_item')}}</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endif

                
                @if(($permissions['clearance'] || $isAdmin) && $packageId >= 2)
                <li class="nav-item">
                    <a data-toggle="collapse" href="#clearance">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <p>  {{ __('menu.clearance')}} </p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="clearance">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('clearance.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                    <span class="sub-item"> {{ __('menu.bought_clearance')}}</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('clearance.sales.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                    <span class="sub-item"> {{ __('menu.sold_clearance')}}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @if($packageId ==1)
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#reports">
                            <i class="fas fa-list-ol"></i>
                            <p>  {{ __('menu.report')}} </p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="reports">
                            <ul class="nav nav-collapse">
                                <li>
                                    <a href="{{route('cacheflow.index')}}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> {{ __('menu.cacheflow') }} </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('chartOfAccount.index')}}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item"> {{ __('menu.chartOfAccount') }} </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endif

                    @if(($permissions['reports'] || $isAdmin) && $packageId >= 2)
                    <li class="nav-item">
                        <a href="{{ route('reports.home') }}">
                            <i class="fas fa-list-ol"></i>
                            <p>  {{ __('menu.reports')}}</p>
                        </a>
                    </li>
                    @endif


                   
                    <li class="nav-item">
                        <a data-toggle="collapse" href="#user">
                            <i class="fas fa-users"></i>
                            <p>  {{ __('menu.user_management')}} </p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse" id="user">
                            <ul class="nav nav-collapse">
                                @if($isAdmin)
                                <li>
                                    <a href="{{ route('roles.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.role')}}</span>
                                    </a>
                                </li>
                                @endif
                                
                                <li>
                                    <a href="{{ route('user.index') }}"><i class="fa fa-arrow-left sidebar_arrow_size"></i>
                                        <span class="sub-item">  {{ __('menu.users')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                

                @if($isAdmin)
                    <li class="nav-item">
                        <a href="{{ route('backups.index') }}">
                            <i class="fas fa-database"></i>
                            <p>   {{ __('menu.backup')}}</p>
                        </a>
                    </li>
                 @endif 

    
            </ul>
        </div>
    </div>
</div>
