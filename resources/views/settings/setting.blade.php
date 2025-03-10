@extends('layouts.app')
@section('title', 'تنظیمات')

@php
    $packageId = \App\Helpers\ManagementHelper::activePackageId();
@endphp

@php
    $user = auth()->user();
    $isAdmin = $user->isAdmin == 1;
    $permissions = [
        'settings' => $user->hasAccess('settings', 'create_records'),
    ];
@endphp

@section('content')
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
                                <li class="active"><a data-toggle="tab"  href="#branch">شعبه</a></li>
                                <li><a data-toggle="tab"  href="#warehouse">گدام</a></li>
                                <li><a data-toggle="tab"  href="#unit">واحد اجناس</a></li>
                                <li><a data-toggle="tab"  href="#currency">واحد پولی</a></li>
                                <li><a data-toggle="tab"  href="#account"> حساب  </a></li>
                                <li><a data-toggle="tab"  href="#income_type"> کتگوری عواید  </a></li>
                                <li><a data-toggle="tab"  href="#expense_type"> کتگوری مصارف  </a></li>
                                <li><a data-toggle="tab"  href="#company_profile">  پروفایل شرکت  </a></li>

                            </ul>

                            <div class="tab-content">
                                <!-- branch -->
                                 <div id="branch" class="tab-pane fade in active">
                                      <br> 
                                      @if($isAdmin) 
									       @include('settings.branch.add')
                                       @endif
								       <br>
                                       @include('settings.branch.list')
                                 </div> 
                                <!-- / branch -->

                                <!-- warehouse -->
                                <div id="warehouse" class="tab-pane fade">
                                    <br>
                                    @if($permissions['settings'] || $isAdmin)
                                        @if($packageId >= 2)
                                            @include('settings.warehouse.add')
                                        @endif
                                    @endif
                                    <br>
                                    @include('settings.warehouse.list')
                                </div>
                                <!-- / warehouse -->


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
									    @include('settings.currency.add')
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

                                <!-- income_type -->
                                <div id="income_type" class="tab-pane fade"> 
                                       <br> 
                                       @if($permissions['settings'] || $isAdmin)
									       @include('settings.income_type.add')
                                        @endif
								       <br>  
                                       @include('settings.income_type.list')      
								</div>
						        <!-- / income_type -->


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
    content.title = '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;"> پیام </span>';
    
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
        activeTab = '#branch'; // Default to the first tab if none is stored
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
        if (tab === '#branch') {
            fetchBranchList();
        } else if (tab === '#warehouse') {
            fetchWarehouseList();
        } else if (tab === '#unit') {
            fetchUnitList();
        } else if (tab === '#currency') {
            fetchCurrencyList();
        } else if (tab === '#account') {
            fetchAccountList();
        } else if (tab === '#income_type') {
            fetchIncomeTypeList();
        } else if (tab === '#expense_type') {
            fetchExpenseTypeList();
        } else if (tab === '#company_profile') {
            fetchProfileList();
        }
    }
});

// $(document).ready(function () {
//     // Initialize default tab's DataTable
//     fetchBranchList();

//     // Ensure correct tab is initialized on click
//     $('#myTab2 li a').on('click', function () {
//         const target = $(this).attr('href');
//         if (target === '#branch') {
//             fetchBranchList();
//         } else if (target === '#warehouse') {
//             fetchWarehouseList();
//         } else if (target === '#unit') {
//             fetchUnitList();
//         } else if (target === '#currency') {
//             fetchCurrencyList();
//         } else if (target === '#account') {
//             fetchAccountList();
//         } else if (target === '#income_type') {
//             fetchIncomeTypeList();
//         }
//         else if (target === '#expense_type') {
//             fetchExpenseTypeList();
//         } else if (target === '#company_profile') {
//             fetchProfileList();
//         } 
//     });
// });
</script>
@endsection

