@php
    use Carbon\Carbon;
@endphp
@extends('layouts.app')

@section('content')

<style>
    .table-row-highlight {
        background-color: #d4edda !important;
    }
    .table-row-highlight td {
        font-weight: bold;
    }
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 mt-2">
                    <div class="card">
                    
                        <div class="card-header" style="padding:10px">
                           <a href="{{ route('reports.home') }}">
                               <button class="btn btn-sm pull-left"><i class="fas fa-arrow-left"></i></button>
                           </a>
                           <button class="printBtn m-l-40" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                           <h4 class="card-title"> گزارش به اساس تاریخ </h4>
                        </div>

                        <div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->					
                            
                            <!-- filter area -->
                            <div class="col-md-12 col-sm-12 col-xs-12 filter_cover m-t-10 m-b-5" id="filterArea">
                                <form id="filterForm">
                                    @csrf
                                    <div class="row">

                                        <!-- Currency Selection -->
                                        <div class="col-md-3 col-sm-6 col-xs-6">
                                            <select class="form-control select2" 
                                                style="width: 100%; border:none !important; background-color:#ddd;" 
                                                name="currency_id" id="currency_id">
                                                <option value=""> --- {{__('common.currency')}} --- </option>
                                                @foreach($currencies as $currency)
                                                    <option value="{{ $currency->id }}" 
                                                        {{ $data['currency_id'] == $currency->id ? 'selected' : '' }}>
                                                        {{ $currency->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Start Date -->
                                        <div class="col-md-3 col-sm-6 col-xs-6">
                                            <div class="filter-group" style="min-width: 120px;">
                                                <div class="input-group">
                                                    <input type="text" class="form-control datepicker-input" 
                                                        name="start_date" id="start_date" 
                                                        value="{{ $data['start_date'] }}" 
                                                        placeholder="{{__('common.start_date')}}">
                                                    <span class="input-group-text datepicker-icon">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- End Date -->
                                        <div class="col-md-3 col-sm-6 col-xs-6">
                                            <div class="filter-group" style="min-width: 120px;">
                                                <div class="input-group">
                                                    <input type="text" class="form-control datepicker-input" 
                                                        name="end_date" id="end_date" 
                                                        value="{{ $data['end_date'] }}" 
                                                        placeholder="{{__('common.end_date')}}">
                                                    <span class="input-group-text datepicker-icon">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="col-md-3 col-sm-6 col-xs-6">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                    <button type="button" id="btn-filter" class="btn btn-info form-control btn-sm">
                                                        <i class="fa fa-search"></i>
                                                        {{ __('common.filter') }}
                                                    </button>
                                                </div>
                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                    <button type="button" id="btn-reset" class="btn btn-secondary form-control btn-sm">
                                                        <i class="fa fa-undo"></i> {{ __('common.reset') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                            <!-- / filter area -->

                            <!-- Loading Indicator -->
                            <!-- <div id="loadingIndicator" style="display:none; text-align: center; padding: 20px;">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                <p>{{ __('common.loading') }}...</p>
                            </div> -->

                            <!-- table -->
                            <div class="table-responsive" style="padding:5px; overflow-x: auto; -webkit-overflow-scrolling: touch;" id="print_area">
                                <table class="table table-bordered table-striped"  style="width:100%; min-width: 700px;">
                                    <thead>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="9">
                                                <img src="{{ asset($orgbios[0]->header ?? '') }}" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
                                            </td>
                                        </tr>
                                        <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                            <td colspan="9">
                                                <center> گزارش خرید و فروش </center>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th rowspan="2" style="border-bottom:1px solid #fff !important;width:15%"><center>{{__('common.date')}}</center></th>
                                            <th colspan="3" style="border-bottom:1px solid #fff !important;"><center>{{__('reports.buy')}}</center></th>
                                            <th colspan="4" style="border-bottom:1px solid #fff !important;"><center>{{__('reports.sales')}}</center></th>
                                        </tr>
                                        <tr>
                                            <th style="width:10%"><center>{{__('reports.buy')}}</center></th>
                                            <th style="width:10%"><center>{{__('reports.bought_paid')}}</center></th>
                                            <th style="width:10%"><center>{{__('reports.buy_low')}}</center></th>
                                            <th style="width:10%"><center>{{__('reports.sales')}}</center></th>
                                            <th style="width:10%"><center>{{__('reports.sales_income')}}</center></th>
                                            <th style="width:10%"><center>{{__('reports.sales_talab')}}</center></th>
                                            <th style="width:15%"><center>{{__('reports.sales_profit')}}</center></th>
                                        </tr>
                                    </thead>

                                    <tbody id="reportBody">
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <i class="fa fa-spinner fa-spin"></i> 
                                                {{ __('common.loading') }}...
                                            </td>
                                        </tr>
                                    </tbody>

                                    <tfoot id="reportFooter" style="display:none;">
                                        <tr style="background-color:#fff8d9">
                                            <td><strong>{{__('reports.total')}}</strong></td>
                                            <td id="totalBoughtPayable"><strong>0.00</strong></td>
                                            <td id="totalBoughtCurPay"><strong>0.00</strong></td>
                                            <td id="totalBoughtRemained"><strong>0.00</strong></td>
                                            <td id="totalSalesPayable"><strong>0.00</strong></td>
                                            <td id="totalSalesCurPay"><strong>0.00</strong></td>
                                            <td id="totalSalesRemained"><strong>0.00</strong></td>
                                            <td id="totalSalesProfit"><strong>0.00</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /table -->
                        </div> <!-- / card-body -->
                      
                    </div> 
                </div>
            </div>  
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize datepickers
    $('.datepicker-input').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // Datepicker icon click
    $(document).on('click', '.datepicker-icon', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $input = $(this).closest('.input-group').find('input');
        if ($input.length) {
            $input.datepicker('show');
        }
    });

    // Initialize Select2
    $('.select2').select2();

    // =========================================
    // LOAD DATA ON PAGE LOAD
    // =========================================
    loadReportData();

    // =========================================
    // FILTER BUTTON
    // =========================================
    $('#btn-filter').on('click', function() {
        loadReportData();
    });

    // =========================================
    // RESET BUTTON - Set to last 7 days
    // =========================================
    $('#btn-reset').on('click', function() {
        var today = '{{ Carbon::now()->format("Y-m-d") }}';
        var sevenDaysAgo = '{{ Carbon::now()->subDays(6)->format("Y-m-d") }}';
        
        $('#currency_id').val('').trigger('change');
        $('#start_date').val(sevenDaysAgo);
        $('#end_date').val(today);
        loadReportData();
    });

    // =========================================
    // ENTER KEY SEARCH
    // =========================================
    $('.filter-group input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn-filter').click();
        }
    });

    // =========================================
    // LOAD REPORT DATA VIA AJAX
    // =========================================
    function loadReportData() {
        var currencyId = $('#currency_id').val();
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        // ✅ If no date range, use default (last 7 days)
        if (!startDate || !endDate) {
            var today = new Date();
            var sevenDaysAgo = new Date(today);
            sevenDaysAgo.setDate(today.getDate() - 6);
            
            startDate = sevenDaysAgo.toISOString().split('T')[0];
            endDate = today.toISOString().split('T')[0];
            
            $('#start_date').val(startDate);
            $('#end_date').val(endDate);
        }

        // Validate dates
        if (startDate > endDate) {
            showNotification('{{ __("reports.start_date_cannot_be_after_end_date") }}', 'warning');
            return;
        }

        // Show loading
        $('#loadingIndicator').show();
        $('#reportBody').html('<tr><td colspan="8" class="text-center"><i class="fa fa-spinner fa-spin"></i> {{ __("common.loading") }}...</td></tr>');
        $('#reportFooter').hide();
        $.ajax({
            url: '{{ route("reports.daily.data") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                currency_id: currencyId,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                $('#loadingIndicator').hide();
                
                if (response.status === 'success') {
                    if (response.data.length > 0) {
                        renderTableData(response);
                    } else {
                        $('#reportBody').html(
                            '<tr><td colspan="8" class="text-center text-muted">' +
                            '<i class="fa fa-info-circle"></i> {{ __("reports.no_data_found_for_date_range") }}' +
                            '</td></tr>'
                        );
                        $('#reportFooter').hide();
                    }
                } else {
                    showNotification(response.message || '{{ __("common.error_occurred") }}', 'danger');
                    $('#reportBody').html(
                        '<tr><td colspan="8" class="text-center text-danger">' +
                        '<i class="fa fa-exclamation-circle"></i> {{ __("common.error_occurred") }}' +
                        '</td></tr>'
                    );
                }
            },
            error: function(xhr) {
                $('#loadingIndicator').hide();
                showNotification('{{ __("common.error_occurred") }}', 'danger');
                $('#reportBody').html(
                    '<tr><td colspan="8" class="text-center text-danger">' +
                    '<i class="fa fa-exclamation-circle"></i> {{ __("common.error_occurred") }}' +
                    '</td></tr>'
                );
            }
        });
    }

    // =========================================
    // RENDER TABLE DATA
    // =========================================
    function renderTableData(response) {
        var html = '';
        var today = '{{ Carbon::now()->format("Y-m-d") }}';

        $.each(response.data, function(index, row) {
            var isToday = (row.report_date === today) ? 'table-row-highlight' : '';
            
            html += `
                <tr class="${isToday}">
                    <td>${row.day_name}</td>
                    <td>${row.total_bought_payable}</td>
                    <td>${row.total_bought_curpay}</td>
                    <td>${row.total_bought_remained}</td>
                    <td>${row.total_sales_payable}</td>
                    <td>${row.total_sales_curpay}</td>
                    <td>${row.total_sales_remained}</td>
                    <td>${row.total_sales_profit}</td>
                </tr>
            `;
        });

        $('#reportBody').html(html);

        // Update totals
        $('#totalBoughtPayable').text(parseFloat(response.totals.total_bought_payable).toFixed(2));
        $('#totalBoughtCurPay').text(parseFloat(response.totals.total_bought_curpay).toFixed(2));
        $('#totalBoughtRemained').text(parseFloat(response.totals.total_bought_remained).toFixed(2));
        $('#totalSalesPayable').text(parseFloat(response.totals.total_sales_payable).toFixed(2));
        $('#totalSalesCurPay').text(parseFloat(response.totals.total_sales_curpay).toFixed(2));
        $('#totalSalesRemained').text(parseFloat(response.totals.total_sales_remained).toFixed(2));
        $('#totalSalesProfit').text(parseFloat(response.totals.total_sales_profit).toFixed(2));

        $('#reportFooter').show();

        // Update summary
        var currencyName = $('#currency_id option:selected').text() || '{{ $data["currency_name"] }}';
        var startDate = response.start_date || $('#start_date').val();
        var endDate = response.end_date || $('#end_date').val();
        
    }

    // =========================================
    // NOTIFICATION FUNCTION
    // =========================================
    function showNotification(message, type = 'info', from = 'top', align = 'center') {
        if (typeof $.notify === 'function') {
            $.notify({
                message: '<span style="font-size:14px;">' + message + '</span>',
                title: '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">{{ __("settings.message") }}</span>',
                icon: 'fa fa-bell'
            }, {
                type: type,
                placement: {
                    from: from,
                    align: align
                },
                time: 3000
            });
        } else {
            alert(message);
        }
    }
});
</script>
@endsection