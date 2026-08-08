@extends('layouts.app')
@section('content')

<style>
.custom_badge{
    background-color: transparent !important;
    border-radius: 50px;
    margin-right: auto;
    line-height: 1;
    padding: 2px 10px;
    vertical-align: middle;
    font-weight: 400;
    font-size: 11px;
    border: 1px solid #ddd;
}
.custom_badge_warning {
    color: #8a6d3b;
    border-color: #8a6d3b;
}
.custom_badge_info {
    color: #31708f;
    border-color: #31708f;
}
.custom_badge_success {
    color: #3c763d;
    border-color: #3c763d;
}
.loading-overlay {
    position: relative;
    min-height: 200px;
}
.loading-spinner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
}
.printBtn {
    background: none;
    border: none;
    color: #007bff;
    cursor: pointer;
    font-size: 18px;
    padding: 5px 10px;
}
.printBtn:hover {
    color: #0056b3;
}
.report-table {
    width: 100%;
    border-collapse: collapse;
}
.report-table th,
.report-table td {
    padding: 10px 15px;
    border: 1px solid #dee2e6;
    text-align: left;
}
.report-table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
.report-table .total-row {
    background-color: #436fa7;
    color: #fff;
    font-weight: bold;
}
.report-table .total-row td {
    background-color: #436fa7;
    color: #fff;
    font-size: 18px;
}
.report-table .sub-total {
    background-color: #f0f0f0;
}
.alert-info-custom {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}
.alert-warning-custom {
    background-color: #fff3cd;
    border-color: #ffeeba;
    color: #856404;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}
.alert-success-custom {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 15px;
}
.filter-section {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
}
/* Card Styles */
.khazana-card {
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.khazana-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}
.khazana-card .stamp {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    margin-left: 10px;
}
.khazana-card .card-icon {
    font-size: 24px;
}
.khazana-card .card-title {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 5px;
}
.khazana-card .card-value {
    font-size: 15px;
    font-weight: bold;
    margin: 0;
}
.card-income {
    background: linear-gradient(135deg, #ffffff 0%, #d6efff 100%);
    border: 1px solid #afc7f8;
}
.card-outcome {
    background: linear-gradient(135deg, #ffffff 0%, #ffd9d9 100%);
    border: 1px solid #e7c99c;
}
.card-cash {
    background: linear-gradient(135deg, #ffffff 0%, #f0ffd1 100%);
    border: 1px solid #83d31a;
}
.card-treasury {
    background: linear-gradient(135deg, #ffffff 0%, #f9edde 100%);
    border: 1px solid #d39d1a;
}
.card-talab {
    background: linear-gradient(135deg, #ffffff 0%, #d4edda 100%);
    border: 1px solid #28a745;
}
.card-loan {
    background: linear-gradient(135deg, #ffffff 0%, #fff3cd 100%);
    border: 1px solid #ffc107;
}
.card-balance {
    background: linear-gradient(135deg, #436fa7 0%, #2c5a8c 100%);
    border: 1px solid #436fa7;
    color: white;
}
.card-balance .card-title {
    color: rgba(255,255,255,0.8);
}
.card-balance .card-value {
    color: white;
}
@media print {
    .no-print {
        display: none !important;
    }
    .card-header {
        background: white !important;
    }
    .report-table th {
        background-color: #f0f0f0 !important;
    }
    .report-table .total-row {
        background-color: #436fa7 !important;
        color: #fff !important;
    }
    .report-table .total-row td {
        background-color: #436fa7 !important;
        color: #fff !important;
    }
    .khazana-card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .khazana-card .stamp {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12 mt-3">
                    <div class="card">
                        <div class="card-header" style="padding:10px">
                            <div class="pull-left" style="width:80px; margin-left:50px">
                                <a href="{{ route('reports.home') }}">
                                    <button class="btn btn-sm pull-left"><i class="fas fa-arrow-left"></i></button>
                                </a>
                                <button class="printBtn" onclick="print_page_with_image()"><i class="fas fa-print"></i></button>
                            </div>
                            <h4 class="card-title">{{__('reports.profit_and_loss_title')}}</h4>
                        </div>

                        <div class="card-body">
                            
                            <!-- Filter Section -->
                            <div class="col-md-12 filter-section no-print" id="filterArea">
                                <input type="hidden" id="currency_symbol" value="{{ $data['currency_symbol'] }}">
                                <form id="filterForm">
                                    @csrf
                                    <div class="row">
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

                                        <div class="col-md-3 col-sm-6 col-xs-6">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                    <button type="button" id="btn-filter" class="btn btn-info form-control btn-sm">
                                                        <i class="fa fa-search"></i> {{ __('common.filter') }}
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

                            <!-- Loading Indicator -->
                            <div id="loadingIndicator" style="display:none; text-align: center; padding: 30px;">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                <p>{{ __('common.loading') }}...</p>
                            </div>

                            <!-- Report Content -->
                            <div class="col-md-12">
                                <div id="reportContent">
                                    <!-- Content will be loaded via AJAX -->
                                    <div class="text-center text-muted" style="padding: 50px;">
                                        <i class="fa fa-info-circle fa-2x"></i>
                                        <p>{{ __('reports.loading_data') }}...</p>
                                    </div>
                                </div>
                            </div>

                        </div> <!-- End card-body -->
                    </div> <!-- End main card -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Document ready - Profit and Loss Report');
    
    // CSRF Setup for AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize datepickers
    if ($.fn.datepicker) {
        $('.datepicker-input').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    } else {
        console.warn('Datepicker plugin not loaded');
    }

    // Datepicker icon click
    $(document).on('click', '.datepicker-icon', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $input = $(this).closest('.input-group').find('input');
        if ($input.length && $.fn.datepicker) {
            $input.datepicker('show');
        }
    });

    // Initialize Select2
    if ($.fn.select2) {
        $('.select2').select2();
    } else {
        console.warn('Select2 plugin not loaded');
    }

    // Load initial data
    loadReportData();

    // Filter button click
    $('#btn-filter').on('click', function() {
        loadReportData();
    });

    // Reset button click
    $('#btn-reset').on('click', function() {
        var today = new Date();
        var thirtyDaysAgo = new Date(today);
        thirtyDaysAgo.setDate(today.getDate() - 30);
        
        var startDate = thirtyDaysAgo.toISOString().split('T')[0];
        var endDate = today.toISOString().split('T')[0];
        
        $('#currency_id').val('').trigger('change');
        $('#start_date').val('');
        $('#end_date').val('');
        loadReportData();
    });

    // Enter key search
    $('.filter-group input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn-filter').click();
        }
    });

    function formatNumber(num, decimals = 2) {
        num = parseFloat(num) || 0;
        let parts = num.toFixed(decimals).split('.');
        let whole = parts[0];
        let decimal = parts[1] || '';
        whole = whole.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return decimal ? whole + '.' + decimal : whole;
    }

    // =========================================
    // LOAD REPORT DATA VIA AJAX
    // =========================================
    function loadReportData() {
        var currencyId = $('#currency_id').val();
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        // if (!startDate || !endDate) {
        //     var today = new Date();
        //     var thirtyDaysAgo = new Date(today);
        //     thirtyDaysAgo.setDate(today.getDate() - 30);
            
        //     startDate = thirtyDaysAgo.toISOString().split('T')[0];
        //     endDate = today.toISOString().split('T')[0];
            
        //     $('#start_date').val(startDate);
        //     $('#end_date').val(endDate);
        // }

        // if (startDate > endDate) {
        //     showNotification('Start date cannot be after end date', 'warning');
        //     return;
        // }

        $('#reportContent').html(
            '<div class="text-center text-muted" style="padding: 50px;">' +
            '<i class="fa fa-spinner fa-spin fa-2x"></i>' +
            '<p>{{ __("common.loading") }}...</p>' +
            '</div>'
        );

        $.ajax({
            url: '{{ route("profitloss.data") }}',
            type: 'POST',
            data: {
                currency_id: currencyId,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                $('#loadingIndicator').hide();
                
                if (response.status === 'success') {
                    renderReport(response.data);
                } else {
                    showNotification(response.message || '{{ __("common.error_occurred") }}', 'danger');
                    $('#reportContent').html(
                        '<div class="text-center text-danger" style="padding: 50px;">' +
                        '<i class="fa fa-exclamation-circle fa-2x"></i>' +
                        '<p>' + (response.message || '{{ __("common.error_occurred") }}') + '</p>' +
                        '</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr);
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                
                $('#loadingIndicator').hide();
                
                let errorMessage = '{{ __("common.error_occurred") }}';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.status === 419) {
                    errorMessage = 'Session expired. Please refresh the page.';
                } else if (xhr.status === 500) {
                    errorMessage = 'Server error. Please check logs.';
                }
                
                showNotification(errorMessage, 'danger');
                $('#reportContent').html(
                    '<div class="text-center text-danger" style="padding: 50px;">' +
                    '<i class="fa fa-exclamation-circle fa-2x"></i>' +
                    '<p><strong>Error:</strong> ' + errorMessage + '</p>' +
                    '<p class="text-muted" style="font-size: 12px;">Status: ' + status + ' (' + xhr.status + ')</p>' +
                    '<p class="text-muted" style="font-size: 12px;">Please check browser console for details</p>' +
                    '</div>'
                );
            }
        });
    }

    // =========================================
    // RENDER REPORT DATA WITH CARDS
    // =========================================
    function renderReport(data) {
        // console.log('Rendering report with data:', data);
        
        var currencySymbol = '{{ $data["currency_symbol"] ?? $data["currency_name"] }}';
        var html = '';

        // Khazana Report Cards
        html += `
            <div class="col-md-12">
                <div class="row">
                    <!-- Total Income Card -->
                    <div class="col-sm-6 col-lg-3">
                        <div class="khazana-card card-income">
                            <div class="d-flex align-items-center">
                                <span class="stamp" style="background-color: #dbf3ff; color: #3f7cc7;">
                                    <i class="fas fa-arrow-down"></i>
                                </span>
                                <div>
                                    <small class="card-title">آمد نقد در خزانه</small>
                                    <h5 class="card-value">${formatNumber(data.khazanaReport.totalIncome || 0)} </br> ${currencySymbol}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Outcome Card -->
                    <div class="col-sm-6 col-lg-3">
                        <div class="khazana-card card-outcome">
                            <div class="d-flex align-items-center">
                                <span class="stamp" style="background-color: #ffd9d9; color: #dc3545;">
                                    <i class="fas fa-arrow-up"></i>
                                </span>
                                <div>
                                    <small class="card-title">رفت نقد از خزانه</small>
                                    <h5 class="card-value">${formatNumber(data.khazanaReport.totalOutcome || 0)} </br> ${currencySymbol}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Balance Card -->
                    <div class="col-sm-6 col-lg-3">
                        <div class="khazana-card card-cash">
                            <div class="d-flex align-items-center">
                                <span class="stamp" style="background-color: #83d31a; color: #155724;">
                                    <i class="fas fa-wallet"></i>
                                </span>
                                <div>
                                    <small class="card-title">بیلانس نقد</small>
                                    <h5 class="card-value">${formatNumber(data.khazanaReport.cashBalance || 0)} </br> ${currencySymbol}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Talab Card -->
                    <div class="col-sm-6 col-lg-3">
                        <div class="khazana-card card-talab">
                            <div class="d-flex align-items-center">
                                <span class="stamp" style="background-color: #d4edda; color: #28a745;">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </span>
                                <div>
                                    <small class="card-title">{{ __('reports.talab') }}</small>
                                    <h5 class="card-value">${formatNumber(data.khazanaReport.totalTalab || 0)} </br> ${currencySymbol}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Loan Card -->
                    <div class="col-sm-6 col-lg-3">
                        <div class="khazana-card card-loan">
                            <div class="d-flex align-items-center">
                                <span class="stamp" style="background-color: #fff3cd; color: #856404;">
                                    <i class="fas fa-hand-holding-heart"></i>
                                </span>
                                <div>
                                    <small class="card-title">{{ __('reports.loan') }}</small>
                                    <h5 class="card-value">${formatNumber(data.khazanaReport.totalLoan || 0)} </br> ${currencySymbol}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Talab Balance Card -->
                    <div class="col-sm-6 col-lg-3">
                        <div class="khazana-card" style="background: linear-gradient(135deg, #ffffff 0%, #e8d5b7 100%); border: 1px solid #c9a86c;">
                            <div class="d-flex align-items-center">
                                <span class="stamp" style="background-color: #c9a86c; color: #fff;">
                                    <i class="fas fa-balance-scale"></i>
                                </span>
                                <div>
                                    <small class="card-title">بیلانس طلب و قرض</small>
                                    <h5 class="card-value">${formatNumber(data.khazanaReport.loanTalabBalance || 0)} </br> ${currencySymbol}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Final Balance Card (Highlighted) -->
                    <div class="col-sm-6 col-lg-3">
                        <div class="khazana-card card-balance">
                            <div class="d-flex align-items-center">
                                <span class="stamp" style="background-color: rgba(255,255,255,0.2); color: #fff;">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <div>
                                    <small class="card-title">بیلانس عمومی</small>
                                    <h5 class="card-value">${formatNumber(data.khazanaReport.finalBalance || 0)} </br> ${currencySymbol}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Profit and Loss Section
        html += `
            <div class="col-md-12" style="margin-top: 30px;" id="print_area">
                <div class="panel-heading m-t-10 hidden-print" style="background-color:#f0eded; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                    <h4 class="panel-title">
                        <strong> 
                            <span class="custom_badge custom_badge_info">{{ __('common.expense') }}</span>
                            +
                            <span class="custom_badge custom_badge_info">{{ __('reports.emp_salaries') }}</span>
                            -
                            <span class="custom_badge custom_badge_warning">{{ __('reports.sales_net_income') }}</span>
                            =
                            <span class="custom_badge custom_badge_success">{{ __('reports.company_net_profit') }}</span>
                        </strong>   
                    </h4>
                </div>
                <div id="collapseGeneral" class="panel-collapse collapse in" style="height: auto;">
                    <div class="panel-body" id="body">
                        
                        <!-- Header Image for Print -->
                        @if(isset($orgbios) && $orgbios->isNotEmpty() && $orgbios[0]->header)
                        <table class="table table-bordered visible-print" style="width:100%; display: none;">
                            <tr>
                                <td colspan="3">
                                    <img src="{{ asset($orgbios[0]->header ?? '') }}" alt="navbar brand" style="width: 100% !important;">
                                </td>
                            </tr>
                        </table>
                        @endif

                        <!-- Sales Net Income -->
                        <table class="report-table">
                            <tr>
                                <th style="width:200px !important;">{{ __('reports.sales_net_income') }}</th>
                                <td style="width:130px !important;">
                                    <strong>{{ __('reports.afn') }}:</strong>
                                </td>
                                <td>
                                    <strong>${formatNumber(data.sales_profit.total_profit || 0)}</strong>
                                </td>
                            </tr>
                        </table>

                        <!-- Expenses -->
                        <table class="report-table" style="margin-top: 10px;">
                            <tr>
                                <th style="width:200px !important;">{{ __('common.expense') }}</th>
                                <td style="width:130px !important;">
                                    <strong>{{ __('reports.afn') }}:</strong>
                                </td>
                                <td>
                                    <strong>${formatNumber(data.transaction_summary.total_expense || 0)}</strong>
                                </td>
                            </tr>
                        </table>

                        <!-- Employee Salaries -->
                        <table class="report-table" style="margin-top: 10px;">
                            <tr>
                                <th style="width:200px !important;">{{ __('reports.emp_salaries') }}</th>
                                <td style="width:130px !important;">
                                    <strong>{{ __('reports.afn') }}:</strong>
                                </td>
                                <td>
                                    <strong>${formatNumber(data.transaction_summary.total_salary || 0)}</strong>
                                </td>
                            </tr>
                        </table>

                        <!-- Company Net Profit -->
                        <table class="report-table" style="margin-top: 10px;"> 
                            <tr class="total-row">
                                <th style="width:200px !important; color: #000 !important;">
                                    {{ __('reports.company_net_profit') }}
                                </th>
                                <td style="width:130px !important;">
                                    <strong>{{ __('reports.afn') }}:</strong>
                                </td>
                                <td>
                                    <strong style="font-size: 18px;">
                                        ${formatNumber(data.final_net_income || 0)}
                                    </strong>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
        `;

        $('#reportContent').html(html);
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
            var alertClass = 'alert-info';
            if (type === 'danger') alertClass = 'alert-danger';
            else if (type === 'warning') alertClass = 'alert-warning';
            else if (type === 'success') alertClass = 'alert-success';
            
            var notification = $('<div class="alert ' + alertClass + ' alert-dismissible" style="position:fixed;top:20px;right:20px;z-index:9999;max-width:400px;">' +
                '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                message +
                '</div>');
            $('body').append(notification);
            setTimeout(function() {
                notification.alert('close');
            }, 3000);
        }
    }
});
</script>
@endpush

@endsection