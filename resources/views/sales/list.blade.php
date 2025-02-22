@extends('layouts.app')

@section('content')

@if(Session::has('notification'))
    @php
        $notification = Session::get('notification');
    @endphp
    <script>
    // Show the notification using the data from the session
    $(document).ready(function(){
        showNotification('{{ $notification['message'] }}', '{{ $notification['type'] }}');
    });
</script>
@endif


<!-- main content -->
<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="card-header" style="padding: 10px;">
                            <span class="card-title">   لست فروشات  {{ $warehouse->name ?? ''}} </span>

                            <button class="printBtn" onclick="print_page()"><i class="fas fa-print"></i></button>
                        </div>


                        <div class="filterForm" id="searchWrapper1">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="row">
                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="customer_name" placeholder="مشتری" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6 m-b-4">
                                        <select class="form-control select2" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id">
                                            <!-- <option value="">  واحد پولی </option> -->
                                            @foreach($currencies as $currency)
                                                <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <input type="text" id="bill_number" placeholder="بل نمبر" class="form-control">
                                    </div>

                                    <div class="col-md-2 col-sm-6 col-xs-6">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#start_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="start_date" id="start_date"
                                            data-targetselector="#start_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ شروع"  data-placement="right" data-englishnumber="true"  >
                                        </div>
							     	</div>
                                

                                     <div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#end_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control" name="end_date" id="end_date"
                                            data-targetselector="#end_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ ختم / الی امروز"  data-placement="right" data-englishnumber="true" >
                                        </div>
							     	</div>


                                    <div class="col-md-1 col-sm-6 col-xs-6">
                                        <button class="btn mybtn search_btn form-control" id="btn-filter">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- /filter_form -->


                        <div class="card-body">
                            <div class="table-responsive" id="print_area" style="padding:5px;">
                                <input type="hidden" id="warehouse_id" value="14" >
                                <span class="pull-left visible-print">تاریخ چاپ : {{ $todaysDate }}</span>
                                <table id="salesTable" class="display responsive nowrap table table-bordered my_table datatable" width="100%">
                                <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="13">
                                            <img src="{{ $orgbios[0]->header }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="13">
                                            <center> لست فروشات   </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th> شماره &nbsp; </th>
                                            <th> بل نمبر </th>
                                            <th> فاکتور </th>
                                            <th> مشتری </th>
                                            <th> مبلغ مجموعی </th>
                                            <th> تخفیف  </th>
                                            <th> قابل دریافت </th>
                                            <th> دریافت فعلی </th>
                                            <th> باقی </th>
                                            <th> واحد پولی </th>
                                            <th> تاریخ </th>
                                            <th> جزییات </th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr style="background:#eefcff">
                                            <td colspan="4">مجموع</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>  
                                </table>
                            </div> <!-- /table responsive -->
                        </div> <!-- /card-body -->
                    </div> <!-- /card -->
                </div> <!-- /col-md-12 -->
            </div> <!-- /row -->
        </div> <!-- /page-inner -->
    </div> <!-- /content -->
</div> <!-- /main content -->

@include('sales.scripts')
@endsection

