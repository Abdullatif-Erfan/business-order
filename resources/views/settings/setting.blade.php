@extends('layouts.app')
@section('title', 'تنظیمات')

@php
    $packageId = \App\Helpers\ManagementHelper::activePackageId();
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
                            </ul>

                            <div class="tab-content">
                                <!-- branch -->
                                 <div id="branch" class="tab-pane fade in active">
                                      <br> 
                                       @if(auth()->user()->hasAccess('settings','create_records')) 
									       @include('settings.branch.add')
                                       @endif
								       <br>
                                       @include('settings.branch.list')
                                 </div> 
                                <!-- / branch -->

                                <!-- warehouse -->
                                <div id="warehouse" class="tab-pane fade">
                                    <br>
                                    @if(auth()->user()->hasAccess('settings', 'create_records'))
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
                                       @if(auth()->user()->hasAccess('settings','create_records')) 
									       @include('settings.unit.add')
                                        @endif
								       <br>  
                                       @include('settings.unit.list')      
								</div>
						        <!-- / unit -->
                             
                            <!-- currency -->
                              <div id="currency" class="tab-pane fade">
                                    <br> 
                                    @if(auth()->user()->hasAccess('settings','create_records')) 
									    @include('settings.currency.add')
                                    @endif
                                    <br>  
                                    @include('settings.currency.list') 
								</div>
						<!-- /currency -->

                        <!-- account -->
                                <div id="account" class="tab-pane fade">
                                   <br> 
                                    @if(auth()->user()->hasAccess('settings','create_records')) 
									    @include('settings.account.add')
                                    @endif
                                    <br>  
                                    @include('settings.account.list') 
								</div>
						<!-- /account -->

                              
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
    // Initialize default tab's DataTable
    fetchBranchList();

    // Ensure correct tab is initialized on click
    $('#myTab2 li a').on('click', function () {
        const target = $(this).attr('href');
        if (target === '#branch') {
            fetchBranchList();
        } else if (target === '#warehouse') {
            fetchWarehouseList();
        } else if (target === '#unit') {
            fetchUnitList();
        } else if (target === '#currency') {
            fetchCurrencyList();
        } else if (target === '#account') {
            fetchAccountList();
        }
    });
});
</script>
@endsection

