@extends('layouts.app')
@section('title', 'داشبورد')

@section('content')
<script>
    function submit12MonthForm() {
        $('#myForm').submit();
    }
    function submitCircleGraphForm() {
        $('#myForm2').submit();
    }
</script>

<script>
    $(document).ready(function () {
        // Restore the active tab from local storage, if available
        var activeTab = localStorage.getItem('activeTab');
        if (activeTab) {
            $('#myTab2 li').removeClass('active');
            $(activeTab).addClass('active');
            $('.tab-content .tab-pane').removeClass('active in');
            $(activeTab + '.tab-pane').addClass('active in');
        }

        // Handle tab click event
        $('#myTab2 a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // Store the ID of the active tab in local storage
            var targetTab = $(e.target).attr('href');
            localStorage.setItem('activeTab', targetTab);
        });
    });
</script>

<style>
    a {
        color: #555 !important;
    }

    a:hover {
        text-decoration: none;
    }
</style>

<div class="main-panel">
    <input type="hidden" id="todays_date" value="{{ date('Y-m-d') }}">
    <div class="content">
        <div class="panel-header bg-primary-gradient">
            <div class="page-inner">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                    <div>
                        <h1 class="text-white pb-2 fw-bold main_title">
                            Sample Organization Name
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- tab -->
        @if(1 == 1) {{-- Replace condition with package check --}}
        <div class="col-12 tab-wrapper">
            @include('dashboard.first-tab.todays_search')
            @include('dashboard.first-tab.todays_card')
            @if(1 === 1) {{-- Replace with currency check --}}
                @include('dashboard.first-tab.graph')
            @endif
        </div>
        @elseif(2 >= 2) {{-- Replace condition with active package check --}}
        <div class="col-12 tab-wrapper">
            <ul class="nav my_nave nav-tabs" id="myTab2">
                <li class="active"><a data-toggle="tab" href="#todaysTransaction">معاملات امروز</a></li>
                <li><a data-toggle="tab" href="#importantTrans">معاملات مهم تجارت</a></li>
                <li><a data-toggle="tab" href="#cache">خزانه</a></li>
            </ul>

            <div class="tab-content">
                <!-- todaysTransaction -->
                <div id="todaysTransaction" class="tab-pane fade in active">
                    @include('dashboard.first-tab.todays_search')
                    @include('dashboard.first-tab.todays_card')
                    @if(1 === 1) {{-- Replace with currency check --}}
                        @include('dashboard.first-tab.graph')
                    @endif
                </div>
                <!-- / todaysTransaction -->

                <!-- importantTrans -->
                <div id="importantTrans" class="tab-pane fade">
                    @include('dashboard.second-tab.overall_business_search')
                    @include('dashboard.second-tab.cards')
                </div>
                <!-- / importantTrans -->

                <!-- cache -->
                <div id="cache" class="tab-pane fade">
                    @include('dashboard.third-tab.cash_search')
                    @include('dashboard.third-tab.cash_cards')
                </div>
                <!-- / cache -->
            </div>
        </div>
        @else
        <h1>لطفا یکی از پکیج هارا انتخاب نمایید</h1>
        @endif
        <!-- / tab -->

    </div>
    @include('component.footer-text')
</div>

<!-- modal -->
<div id="edit_modal" class="modal fade in" role="dialog" aria-labelledby="edit_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-blue3">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 class="modal-title">ویرایش</h5>
            </div>
            <div class="modal-body" id="EditData"></div>
        </div>
    </div>
</div>
<!-- /modal -->
@endsection
