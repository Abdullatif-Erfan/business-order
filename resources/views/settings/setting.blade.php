@extends('layouts.app')
@section('title', __('settings.title'))

@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin == 1;
    $permissions = [
        'settings' => $user->hasAccess('settings', 'create_records'),
    ];
@endphp

@section('content')
<style>
    @media (max-width: 780px) {
        .my_nave li a{
            font-size: 12px !important;
        }
        table th, table td {
            font-size: 12px !important;
        }
    }
    
</style>
<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-body" style="padding: 15px 15px 33px 15px;">
                            <!-- card-body -->

                            <ul class="nav my_nave nav-tabs" id="myTab2">
                                <li class="active"><a data-toggle="tab"  href="#car">{{__('settings.car')}}</a></li>
                                <li><a data-toggle="tab"  href="#category">{{__('settings.category')}}</a></li>
                                <li><a data-toggle="tab"  href="#unit">{{__('settings.unit')}}</a></li>
                                <li><a data-toggle="tab"  href="#currency"> {{__('settings.currency')}}</a></li>
                                <li><a data-toggle="tab"  href="#account"> {{__('settings.account')}}  </a></li>
                                <li><a data-toggle="tab"  href="#expense_type">  {{__('settings.expense_type')}}  </a></li>
                                <li><a data-toggle="tab"  href="#company_profile">  {{__('settings.company_profile')}}   </a></li>
                            </ul>

                            <div class="tab-content">

                                <!-- car -->
                                 <div id="car" class="tab-pane fade in active"> 
                                       <br> 
                                       @if($permissions['settings'] || $isAdmin)
									       @include('settings.car.add')
                                        @endif
								       <br>  
                                       @include('settings.car.list')      
								</div>
						        <!-- / car -->

                                <!-- category -->
                                 <div id="category" class="tab-pane fade"> 
                                       <br> 
                                       @if($permissions['settings'] || $isAdmin)
									       @include('settings.category.add')
                                        @endif
								       <br>  
                                       @include('settings.category.list')      
								</div>
						        <!-- / category -->

                                <!-- unit -->
                                <div id="unit" class="tab-pane fade"> 
                                       <br> 
                                       @if($permissions['settings'] || $isAdmin)
									       @include('settings.unit.add')
                                        @endif
								       <br>  
                                       @include('settings.unit.list')      
								</div>
						        <!-- / unit -->
                             
                              <!-- currency -->
                              <div id="currency" class="tab-pane fade">
                                    <br> 
                                    @if($permissions['settings'] || $isAdmin)
									    <!-- @include('settings.currency.add') -->
                                    @endif
                                    <br>  
                                    @include('settings.currency.list') 
								</div>
						       <!-- /currency -->

                               <!-- account -->
                                <div id="account" class="tab-pane fade">
                                   <br> 
                                   @if($permissions['settings'] || $isAdmin)
									    @include('settings.account.add')
                                    @endif
                                    <br>  
                                    @include('settings.account.list') 
								</div>
					        	<!-- /account -->

                                <!-- expense_type -->
                                <div id="expense_type" class="tab-pane fade"> 
                                       <br> 
                                       @if($permissions['settings'] || $isAdmin) 
									       @include('settings.expense_type.add')
                                        @endif
								       <br>  
                                       @include('settings.expense_type.list')      
								</div>
						        <!-- / expense_type -->

                                <!-- company_profile -->
                                 <div id="company_profile" class="tab-pane fade"> 
                                       <br> 
                                       @if($isAdmin)
                                          @include('settings.organization.list')      
                                        @endif
								</div>
                                <!-- / company_profile -->

                                
                            </div>
                        </div>
                        <!-- / card-body -->
                    </div>
                </div>
                <!-- / row -->
            </div>
        </div>
    </div>

    <!-- footer -->
    @include('component.footer-text')
    <!-- / footer -->
</div>
<!-- / main content -->

<script>

function showNotification(message, type = 'info', from = 'top', align = 'center', style = 'withicon') {
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


$(document).ready(function () {
    // Check if there's a stored active tab and set it
    let activeTab = localStorage.getItem('activeTab');
    console.log('Active tab is:', activeTab);

    if (activeTab) {
        $('#myTab2 a[href="' + activeTab + '"]').tab('show');
    } else {
        activeTab = '#car'; // Default to the first tab if none is stored
    }

    // Call the respective function on page load
    callFetchFunction(activeTab);

    // Ensure correct tab is initialized on click and store the selected tab
    $('#myTab2 li a').on('click', function () {
        const target = $(this).attr('href');
        localStorage.setItem('activeTab', target); // Store the selected tab in local storage

        callFetchFunction(target);
    });

    function callFetchFunction(tab) {
        console.log('Calling fetch function for:', tab);
         if (tab === '#car') {
            fetchCarList();
        }
        else if (tab === '#category') {
            fetchCategoryList();
        }  else if (tab === '#unit') {
            fetchUnitList();
        } else if (tab === '#currency') {
            fetchCurrencyList();
        } else if (tab === '#account') {
            fetchAccountList();
        }  else if (tab === '#expense_type') {
            fetchExpenseTypeList();
        } else if (tab === '#company_profile') {
            fetchProfileList();
        }
    }
});
</script>
@endsection

