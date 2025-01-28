@include('component.header')
@include('component.sidebar')

@php
    $packageId = \App\Helpers\ManagementHelper::activePackageId();
@endphp
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
                                <li class="active"><a data-toggle="tab" href="#branch">شعبه</a></li>
                                <li><a data-toggle="tab" data-id="0" href="#warehouse">گدام</a></li>
                            </ul>

                            <div class="tab-content">
                                <!-- branch -->
                                 <div id="branch" class="tab-pane fade in active">
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


$(document).ready(function() {
    // When the submit button is clicked
   
});
</script>


@include('component.footer')