@extends('layouts.app')

@section('content')

<style>
    table.new thead tr th{color:#000 !important;text-align:center;}
    table.my_table thead tr th{background-color:#3f7cc7  !important; color:#fff !important;text-align:center;}
    .new tbody tr td{padding: 5px 5px;}
    select.select2{text-align:right !important;direction:rtl !important;}
    .form-control {
        padding-right: 3px !important;
    }
    
    .table-scroll-wrapper {
        position: relative;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #fff;
        overflow: hidden;
        width: 100%;
    }
    
    .table-scroll-wrapper .table-responsive-scroll {
        max-height: 400px;
        overflow-y: auto;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table-scroll-wrapper table {
        margin-bottom: 0;
        width: 100%;
        min-width: 800px;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-scroll-wrapper thead {
        position: sticky;
        top: 0;
        z-index: 100;
    }
    
    .table-scroll-wrapper thead th {
        background: #e9fffe !important;
        position: sticky;
        top: 0;
        z-index: 100;
        border-bottom: 2px solid #ddd;
        white-space: nowrap;
        padding: 10px 12px;
        font-size: 14px;
        text-align: center;
        vertical-align: middle;
        box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);
    }
    
    .table-scroll-wrapper tbody td {
        padding: 8px 10px;
        vertical-align: middle;
        text-align: center;
    }
    
    .table-scroll-wrapper tbody td .form-control {
        width: 100%;
        min-width: 60px;
        padding: 4px 6px;
        font-size: 13px;
        height: 32px;
    }
    
    .table-scroll-wrapper tbody td .select2-container {
        width: 100% !important;
        min-width: 120px;
    }
    
    .table-scroll-wrapper .col-item { min-width: 150px; width: 20%; }
    .table-scroll-wrapper .col-amount { min-width: 80px; width: 10%; }
    .table-scroll-wrapper .col-buy-up { min-width: 80px; width: 12%; }
    .table-scroll-wrapper .col-profit { min-width: 80px; width: 12%; }
    .table-scroll-wrapper .col-sell-up { min-width: 80px; width: 12%; }
    .table-scroll-wrapper .col-total { min-width: 80px; width: 12%; }
    .table-scroll-wrapper .col-actions { min-width: 70px; width: 10%; }
    
    .summary-table td {
        padding: 8px 15px;
        vertical-align: middle;
    }
    .summary-table .form-control {
        height: 34px;
    }
    
    .add-row-btn {
        width: 100%;
        padding: 8px;
        border: 2px dashed #ddd;
        background: #fafafa;
        color: #666;
        font-weight: 500;
        cursor: pointer;
        border-radius: 4px;
        transition: all 0.3s ease;
        text-align: center;
    }
    .add-row-btn:hover {
        background: #f0f4ff;
        border-color: #4a6cf7;
        color: #4a6cf7;
    }
    .add-row-btn i {
        margin-right: 8px;
    }
    /* Tax display styles */
.tax-label {
    font-size: 11px;
    color: #666;
    display: block;
    margin-top: 2px;
    font-weight: normal;
}

.tax-value {
    color: #3f7cc7;
    font-weight: bold;
}

.tax-row {
    background-color: #f8f9fa;
}

.tax-row td {
    border-top: 2px solid #3f7cc7 !important;
}

/* Tax summary styles */
.tax-summary {
    background: #f0f7ff;
    border-radius: 4px;
    padding: 8px 15px;
    margin-top: 5px;
}

.tax-summary strong {
    color: #3f7cc7;
}

/* Make buy_up column relative for tax label positioning */
/* td:has(.buy-up) {
    position: relative;
    vertical-align: top;
    padding-bottom: 30px !important;
} */

.buy-up {
    margin-bottom: 2px;
}

.tax-label {
    font-size: 10px;
    color: #555;
    display: block;
    margin-top: 4px;
    padding: 2px 4px;
    background: #f4f4d0;
    border-radius: 3px;
    border: 1px dashed #a7adb5;
}

.availability-badge {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 12px;
    display: inline-block;
    white-space: nowrap;
}
.availability-high { background: #d4edda; color: #155724; }
.availability-medium { background: #fff3cd; color: #856404; }
.availability-low { background: #f8d7da; color: #721c24; }
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title"> {{__('sales.pos_list_title')}}
                                <span class="pull-left">
                                    <a href="{{  route('sales.index')  }}">
                                        <button class="btn mybtn bg-default">{{__('common.back')}}</button>
                                    </a>
                                </span>
                            </h4>
                        </div>

                        <form id="salesForm" action="{{  route('sales.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tax_activation" id="tax_activation" value="{{ $tax->tax_activation ?? 0 }}">
                            <input type="hidden" name="tax_per" id="tax_per" value="{{ $tax->tax_per ?? 0 }}">
                            <input type="hidden" name="currency_id" value="{{ $currencies->first()->id ?? 1 }}">
                            <input type="hidden" name="alloweLimitValue" id="alloweLimitValue">
                            <input type="hidden2" name="shouldCheck" id="shouldCheck">

                            
                            <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                                <div class="form-body" style="padding: 0px 0px 15px !important;">
                                    <div class="row" style="padding: 10px 20px;">

                                        @if ($errors->any())
                                            <div class="col-md-12">
                                                <div class="alert alert-danger col-12" role="alert">
                                                    <button type="button" class="close pull-left" data-dismiss="alert" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- First Row -->
                                        <div class="col-md-3 col-sm-4 col-xs-6">
                                            <label for="customer_account_id">{{__('order.customer_selection')}} <span class="danger">*</span></label>
                                            <select class="form-control select2" onchange="getBalance(this.value)" style="width: 100%; background-color:#ddd;" name="customer_account_id" id="customer_account_id" required>
                                                <option value="">{{__('buy.customer')}}</option>
                                                @foreach($customersWithStatus as $customer)
                                                    <option value="{{ $customer->id }}" 
                                                        data-has-order="{{ $customer->has_order ? 1 : 0 }}"
                                                        data-has-items="{{ $customer->has_available_items ? 1 : 0 }}"
                                                        data-name="{{ $customer->name }}">
                                                        {{ $customer->name }}
                                                        @if($customer->has_order)
                                                            ✅
                                                        @endif
                                                        @if(!$customer->has_available_items && $customer->has_order)
                                                            ({{__('sales.no_stock')}})
                                                        @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('customer_account_id')
                                                <span style='color:red'>{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 col-sm-4 col-xs-6">
                                            <label for="car_id">{{__('buy.car')}} <span class="danger">*</span></label>
                                            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="car_id" id="car_id" required>
                                             <!-- <option value="">{{__('sales.select_car')}}</option>    -->
                                            @foreach($cars as $car)
                                                    <option value="{{ $car->id }}">{{ $car->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('car_id')
                                                <span style='color:red'>{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 col-sm-4 col-xs-6">
                                            <label for="date">{{__('order.date')}} <span class="text-danger">*</span></label>
                                            <div class="input-group date" id="datepicker">
                                                <input type="text" class="form-control" name="todays_date" required
                                                    value="{{ date('Y-m-d') }}" placeholder="{{__('order.date')}}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2 col-sm-4 col-xs-6">
                                            <label for="billno">{{__('common.bill')}} <span class="danger">*</span></label>
                                            <input type="number" class="form-control" value="{{ $billno }}" name="billno" id="billno"
                                                placeholder="{{__('common.bill')}}" required readonly>
                                        </div>

                                        <div class="col-md-2 col-sm-4 col-xs-6">
                                            <label for="factor">{{__('common.factor')}}</label>
                                            <input type="text" class="form-control" name="factor" id="factor" placeholder="{{__('common.factor')}}">
                                        </div>
                                        <!-- / First Row -->

                                        <!-- Second Row - Items Table -->
                                        <div class="col-md-12 m-t-20">
                                            <div class="row">
                                                <div class="table-scroll-wrapper">
                                                    <div class="table-responsive-scroll">
                                                        <table class="table table-bordered new" id="itemsTable">
                                                            <thead>
                                                                <tr style="background:#e9fffe">
                                                                    <th style="width:5%">#</th>
                                                                    <th style="width:20%">{{__('wh.item_selection')}}</th>
                                                                    <th style="width:10%">{{__('common.amount')}}</th>
                                                                    <th style="width:8%">{{__('common.unit')}}</th>
                                                                    <th style="width:12%">{{__('buy.buy_up')}}</th>
                                                                    <th style="width:12%">{{__('buy.profit')}}</th>
                                                                    <th style="width:15%">{{__('sales.sold_up')}}</th>
                                                                    <th style="width:15%">{{__('common.total')}}</th>
                                                                    <th style="width:10%">{{__('common.availability')}}</th>
                                                                    <th style="width:5%">{{__('common.delete')}}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="itemsBody">
                                                                <!-- Items will be loaded dynamically via JavaScript -->
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="10">
                                                                        <button type="button" id="addNewItemBtn" class="add-row-btn">
                                                                            <i class="fa fa-plus-circle"></i> {{__('sales.add_new_item')}}
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- / Second Row -->

                                        <hr />

                                        <!-- Summary Row -->
                                        <div class="col-md-12 m-t-20">
                                            <div class="row">
                                                <table class="table table-bordered summary-table" style="background-color:#f6f6f6;">
                                                    <tr>
                                                        <td style="width:10%"><strong>{{__('buy.total_price')}}</strong></td>
                                                        <td style="width:15%">
                                                            <input type="number" name="total_vat_summary" id="total_price" value="0" class="form-control" step="0.01" readonly>
                                                              <small style="font-size:10px; border:1px solid #ddd; border-radius:10px;padding: 2px 5px;">
                                                                 سقف قرض  : 
                                                                <label style="font-size:10px !important;" id="loanLimitLabel"></label>
                                                            </small>
                                                        </td>
                                                        <td style="width:10%"  colspan="2"><strong>{{__('buy.total_buy_with_tax')}}</strong></td>
                                                        <td style="width:15%" colspan="2">
                                                            <input type="number" name="total_price" id="total_vat_summary" value="0" class="form-control" step="0.01" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width:10%"><strong>{{__('buy.cur_pay')}}</strong></td>
                                                        <td style="width:15%">
                                                            <input type="number" name="cur_pay" id="cur_pay" value="0" oninput="updateRemainOnCurPay(this.value)" class="form-control" step="any" min="0" required>
                                                            <small style="font-size:10px; border:1px solid #ddd; border-radius:10px;padding: 2px 5px;">
                                                                  بیلانس فعلی  : 
                                                                <label style="font-size:10px !important;" id="curBalanceLabel"></label>
                                                            </small>
                                                        </td>
                                                        <td style="width:10%"><strong>{{__('buy.remained')}}</strong></td>
                                                        <td style="width:15%">
                                                            <input type="number" name="remained" id="remained" class="form-control" step="0.01" readonly>
                                                             <small style="font-size:10px; border:1px solid #ddd; border-radius:10px;padding: 2px 5px;">
                                                                 حد اکثر قرض  : 
                                                                <label style="font-size:10px !important;" id="alloweLimitlabel"></label>
                                                            </small>
                                                        </td>
                                                        <td><strong>{{__('journal.payer_account')}}</strong></td>
                                                        <td>
                                                            <select class="form-control select2" style="width:100%; background-color:#ddd;" name="account_id" required>
                                                                <!-- <option value="">انتخاب حساب</option>    -->
                                                                @foreach($ownBanks as $acc)
                                                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{__('common.currency')}}</strong></td>
                                                        <td>
                                                            <select class="form-control select2" style="width:100%; background-color:#ddd;" name="currency_id" required>
                                                                @foreach($currencies as $currency)
                                                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><strong>{{__('buy.comment')}}</strong></td>
                                                        <td colspan="3">
                                                            <input type="text" placeholder="{{__('buy.comment')}}" name="note" id="note" class="form-control">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- / Summary Row -->

                                        <!-- Submit and Cancel Buttons -->
                                        <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                            <div class="row">
                                                <div class="col-3 col-xs-6">
                                                    <input type="submit" id="submit_button" name="submit" value="{{__('common.save')}}" class="form-control btn bg-blue">
                                                </div>
                                                <div class="col-3 col-xs-6">
                                                    <a href="{{ route('sales.index') }}">
                                                        <button type="button" class="form-control btn bg-danger">{{__('common.cancel')}}</button>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
     // check balance
  function getBalance(account_id)
  {
    //  $('#'+balanceLabel).text(value);
    if (account_id > 0) 
    {
            let formData = {
                account_id: account_id,
                _token: $('meta[name="csrf-token"]').attr('content') // Get CSRF token dynamically
            };

            $.ajax({
            url: '/home/getBalanceWithLoanLimit',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (result) {
                if (result.cur_balance !== undefined) {
                    // $('#to_amount').val(number_format(parseFloat(result.convertedAmount), 2));
                    // $('#rate').text(' نرخ  ' + result.exchangeRate.toFixed(2));
                    // $('#newRate').val(result.exchangeRate.toFixed(2));
                    $('#shouldCheck').val(result.shouldCheck ? 1 : 0);
                    $('#alloweLimitValue').val(result.allowed_Limit ?? 0);
                    $('#curBalanceLabel').text(parseFloat(result.cur_balance));
                    $('#loanLimitLabel').text(parseFloat(result.loan_limit ?? 0));
                    $('#alloweLimitlabel').text(parseFloat(result.allowed_Limit ?? 0));
                    console.log('alloweLimitlabel');

                } else {
                    alert('Getting Balance failed. Invalid response.');
                }
            },
            error: function (xhr, status, error) {
                $('#'+balanceLabel).text('Not found');
            },
        });
    }
  }
</script>
<script>
$(document).ready(function () {

    // =========================================
    // DEBUG - Check tax values
    // =========================================
    // console.log('=== TAX VALUES ===');
    // console.log('tax_activation:', $('#tax_activation').val());
    // console.log('tax_per:', $('#tax_per').val());
    // console.log('tax_activation type:', typeof $('#tax_activation').val());
    // console.log('tax_per type:', typeof $('#tax_per').val());
    // console.log('tax_activation parsed:', parseInt($('#tax_activation').val()));
    // console.log('tax_per parsed:', parseFloat($('#tax_per').val()));
    // console.log('showTax:', parseInt($('#tax_activation').val()) === 1);

    // =========================================
    // DATA STORAGE
    // =========================================
    var combinedItemsData = {!! json_encode($combinedItems ?? []) !!};
    var customersWithStatusData = {!! json_encode($customersWithStatus ?? []) !!};
    var warehouseItemsData = {!! json_encode($warehouseItems ?? []) !!};
    var unitsData = {!! json_encode($units ?? []) !!};
    var currentItems = [];

    // =========================================
    // INITIALIZE SELECT2
    // =========================================
    $('.select2').select2();

    // =========================================
    // DATE PICKER
    // =========================================
    $('#datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // =========================================
    // GENERATE ROW HTML
    // =========================================
    function generateRowHtml(item, index) {
        var amount = parseFloat(item.amount) || 1;
        var buyUp = (item.buy_up !== '' && item.buy_up !== undefined) ? parseFloat(item.buy_up) : 0;
        var profitAmount = (item.profit_amount !== '' && item.profit_amount !== undefined) ? parseFloat(item.profit_amount) : 0;
        var sellUp = (item.sell_up !== '' && item.sell_up !== undefined) ? parseFloat(item.sell_up) : 0;
        var total = (item.total !== '' && item.total !== undefined) ? parseFloat(item.total) : 0;
        var availableAmount = item.available_amount || 0;
        var isFromOrder = item.from_order !== undefined && item.from_order === true;
        
        var sellUpVat = (item.sell_up_vat !== '' && item.sell_up_vat !== undefined) ? parseFloat(item.sell_up_vat) : 0;
        var sellTaxPrice = (item.sell_tax_price !== '' && item.sell_tax_price !== undefined) ? parseFloat(item.sell_tax_price) : 0;
        var totalVat = (item.total_vat !== '' && item.total_vat !== undefined) ? parseFloat(item.total_vat) : 0;

        var unitId = item.unit_id || '';
        var preListId = item.pre_list_id || '';
        var warehouseItemId = item.warehouse_item_id || '';
        var itemName = item.item_name || item.pre_list_name || '';

        // Get tax activation and percentage from hidden fields
        var taxActivation = parseInt($('#tax_activation').val()) || 0;
        var taxPercent = parseFloat($('#tax_per').val()) || 0;
        var showTax = taxActivation === 1;

        // Tax display (sell_up_vat as label below sell_up)
        var sellUpVatDisplay = showTax ? `
            <span class="tax-label">${taxPercent}% | <span class="tax-value" id="sell_up_vat_display_${index}">${sellUpVat ? sellUpVat.toFixed(2) : '0.00'}</span></span>
            <input type="hidden" name="items[${index}][sell_up_vat]" class="sell-up-vat" value="${sellUpVat || 0}">
            <input type="hidden" name="items[${index}][sell_tax_price]" class="sell-tax-price" value="${sellTaxPrice || 0}">
            <input type="hidden" name="items[${index}][sell_tax_per]" class="sell-tax-per" value="${taxPercent}">
        ` : `
            <input type="hidden" name="items[${index}][sell_up_vat]" class="sell-up-vat" value="0">
            <input type="hidden" name="items[${index}][sell_tax_price]" class="sell-tax-price" value="0">
            <input type="hidden" name="items[${index}][sell_tax_per]" class="sell-tax-per" value="${taxPercent}">
        `;

        // Total VAT display
        var totalVatDisplay = showTax ? `
            <span class="tax-label" id="total_vat_display_${index}">${totalVat ? totalVat.toFixed(2) : '0.00'}</span>
            <input type="hidden" name="items[${index}][total_vat]" class="total-vat" value="${totalVat || 0}">
        ` : `
            <input type="hidden" name="items[${index}][total_vat]" class="total-vat" value="0">
        `;

        // Availability badge
        var badgeClass = availableAmount > 10 ? 'availability-high' : (availableAmount > 5 ? 'availability-medium' : 'availability-low');
        var availabilityBadge = availableAmount > 0 
            ? `<span class="availability-badge ${badgeClass}"> ${availableAmount}</span>`
            : `<span class="availability-badge availability-low">{{__('common.out_of_stock')}}</span>`;

        return `
            <tr class="item-row" data-index="${index}">
                <td class="row-number">${index + 1}</td>
                <td>
                    <input type="text" class="form-control" value="${itemName}" readonly style="background:#f5f5f5;">
                    <input type="hidden" name="items[${index}][pre_list_id]" class="pre-list-id-hidden" value="${item.pre_list_id || ''}">
                    <input type="hidden" name="items[${index}][warehouse_item_id]" class="warehouse-item-id-hidden" value="${warehouseItemId}">
                    <input type="hidden" name="items[${index}][order_id]" value="${item.dord_num || ''}">
                </td>
                <td>
                    <input name="items[${index}][amount]" class="form-control amount" type="number" step="any" min="0.1" 
                        value="${amount}" max="${availableAmount}" required>
                    <small class="text-muted" style="display:block;font-size:9px;">{{__('sales.max')}}: ${availableAmount}</small>
                </td>
                <td>
                    <input type="text" class="form-control unit-name-display" value="${item.unit_name || ''}" readonly style="background:#f5f5f5;">
                    <input type="hidden" name="items[${index}][unit_id]" class="unit-id-hidden" value="${unitId}">
                </td>
                <td>
                    <input name="items[${index}][buy_up]" class="form-control buy-up" type="number" step="any" min="0" 
                        value="${buyUp}" readonly style="background:#f5f5f5;">
                </td>
                <td>
                    <input name="items[${index}][profit_amount]" class="form-control profit-amount" type="number" step="any" 
                        value="${profitAmount}" placeholder="0.00">
                </td>
                <td>
                    <input name="items[${index}][sell_up]" class="form-control sell-up" type="number" step="any" min="0" 
                        value="${sellUp}" readonly>
                    ${sellUpVatDisplay}
                </td>
                <td>
                    <input name="items[${index}][total]" class="form-control total" type="number" step="any" min="0" 
                        value="${total}" readonly>
                        ${totalVatDisplay}
                </td>
                <td>${availabilityBadge}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item" style="padding: 2px 8px !important;" title="{{__('common.remove')}}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    // =========================================
    // APPEND ROW
    // =========================================
    function appendRow(item, index) {
        var html = generateRowHtml(item, index);
        var $newRow = $(html);
        
        $('#itemsBody').append($newRow);
        $newRow.find('.select2').select2();
        
        var index = $newRow.data('index');
        
        updateRowNumbers();
        recalculateRow($newRow);
        
        var scrollWrapper = $('.table-responsive-scroll');
        if (scrollWrapper.length) {
            setTimeout(function() {
                scrollWrapper.scrollTop(scrollWrapper[0].scrollHeight);
            }, 50);
        }
    }

    // =========================================
    // REMOVE ROW
    // =========================================
    function removeRow(row) {
        var index = row.data('index');
        
        if (confirm('{{__("common.delete_confirm")}}')) {
            currentItems.splice(index, 1);
            row.remove();
            updateRowNumbers();
            updateTotalPrice();
            
            if (currentItems.length === 0) {
                showEmptyState();
            }
        }
    }

    // =========================================
    // UPDATE ROW NUMBERS
    // =========================================
    function updateRowNumbers() {
        $('#itemsBody .item-row').each(function(index) {
            $(this).find('.row-number').text(index + 1);
            $(this).data('index', index);
            
            $(this).find('[name]').each(function() {
                var name = $(this).attr('name');
                if (name && name.includes('[')) {
                    var newName = name.replace(/items\[\d+\]/, 'items[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }

    // =========================================
    // SHOW EMPTY STATE
    // =========================================
    function showEmptyState(message) {
        var msg = message || '{{__("sales.no_items_found")}}';
        $('#itemsBody').html(`
            <tr>
                <td colspan="10" class="text-center text-muted">
                    <i class="fa fa-info-circle"></i> ${msg}
                </td>
            </tr>
        `);
        updateTotalPrice();
    }

    // =========================================
    // LOAD ITEMS FOR SELECTED CUSTOMER
    // =========================================
    function loadItemsForCustomer(customerId, hasItems, hasOrder) {
        if (!customerId) {
            currentItems = [];
            showEmptyState('{{__("sales.select_customer")}}');
            return;
        }

        if (!hasItems) {
            currentItems = [];
            if (hasOrder) {
                showEmptyState('{{__("sales.no_available_stock_for_customer")}}');
            } else {
                showEmptyState('{{__("sales.no_orders_for_customer_add_new")}}');
            }
            return;
        }

        var customerIdInt = parseInt(customerId);
        var customerItems = combinedItemsData.filter(function(item) {
            var itemCustomerId = parseInt(item.customer_id);
            return itemCustomerId === customerIdInt;
        });

        if (customerItems.length === 0) {
            currentItems = [];
            showEmptyState('{{__("sales.no_available_items")}}');
            return;
        }

        var allItems = [];
        customerItems.forEach(function(item) {
            allItems.push({
                dord_num: item.dord_num,
                customer_id: item.customer_id,
                customer_name: item.customer_name || 'Unknown',
                pre_list_id: item.pre_list_id,
                pre_list_name: item.pre_list_name || 'Unknown',
                unit_id: item.unit_id,
                unit_name: item.unit_name || 'Unknown',
                amount: parseFloat(item.amount) || 0,
                category_id: item.category_id || null,
                warehouse_item_id: item.warehouse_item_id,
                buy_up: parseFloat(item.buy_up) || 0,
                sell_up: parseFloat(item.sell_up) || 0,
                available_amount: parseFloat(item.available_amount) || 0,
                item_name: item.item_name || 'Unknown',
                from_order: true,
                is_new: false,
                profit_amount: 0,
                total: 0,
                sell_up_vat: 0,
                sell_tax_price: 0,
                total_vat: 0
            });
        });

        currentItems = allItems;

        $('#itemsBody').empty();
        
        if (currentItems.length === 0) {
            showEmptyState('{{__("sales.no_available_items")}}');
        } else {
            currentItems.forEach(function(item, index) {
                appendRow(item, index);
            });
        }
        
        updateTotalPrice();
    }

    // =========================================
    // RECALCULATE ROW
    // =========================================
    function recalculateRow(row) {
        var amount = parseFloat(row.find('.amount').val()) || 0;
        var buyUp = parseFloat(row.find('.buy-up').val()) || 0;
        var profit = Math.max(0, parseFloat(row.find('.profit-amount').val()) || 0);
        var taxActivation = parseInt($('#tax_activation').val()) || 0;
        var taxPercent = parseFloat($('#tax_per').val()) || 0;
        var index = row.data('index');

        // prevent negative value
         row.find('.profit-amount').val(profit);

        // Calculate sell price
        var sellUp = buyUp + profit;
        row.find('.sell-up').val(sellUp.toFixed(2));

        // Calculate total without tax
        var total = amount * sellUp;
        row.find('.total').val(total.toFixed(2));

        // === TAX CALCULATION ===
        var sellTaxPrice = 0;
        var sellUpVat = 0;
        var totalVat = 0;

        if (taxActivation === 1 && taxPercent > 0 && amount > 0 && sellUp > 0) {
            var curTotal = amount * sellUp;
            sellTaxPrice = (curTotal * taxPercent) / 100;  // Total VAT amount
            sellUpVat = sellTaxPrice + sellUp;  // Unit price + total tax
            totalVat = sellUpVat * amount;  // Total with VAT
        }

        // Update tax display
        var sellUpVatDisplay = row.find('#sell_up_vat_display_' + index);
        if (sellUpVatDisplay.length) {
            sellUpVatDisplay.text(sellUpVat.toFixed(2));
        }

        var totalVatDisplay = row.find('#total_vat_display_' + index);
        if (totalVatDisplay.length) {
            totalVatDisplay.text(totalVat.toFixed(2));
        }

        // Update hidden fields
        row.find('.sell-up-vat').val(sellUpVat.toFixed(2));
        row.find('.sell-tax-price').val(sellTaxPrice.toFixed(2));
        row.find('.total-vat').val(totalVat.toFixed(2));

        // Update currentItems
        if (currentItems[index]) {
            currentItems[index].amount = amount;
            currentItems[index].buy_up = buyUp;
            currentItems[index].profit_amount = profit;
            currentItems[index].sell_up = sellUp;
            currentItems[index].sell_tax_price = sellTaxPrice;
            currentItems[index].sell_up_vat = sellUpVat;
            currentItems[index].total = total;
            currentItems[index].total_vat = totalVat;
        }

        updateTotalPrice();
    }

    // =========================================
    // UPDATE TOTAL PRICE
    // =========================================
    function updateTotalPrice() {
        var totalPrice = 0;
        var totalVat = 0;
        
        $('.total').each(function() {
            var val = parseFloat($(this).val()) || 0;
            totalPrice += val;
        });
        
        $('.total-vat').each(function() {
            var val = parseFloat($(this).val()) || 0;
            totalVat += val;
        });
        
        $('#total_price').val(totalPrice.toFixed(2));
        $('#total_vat_summary').val(totalVat.toFixed(2));
        
        var curPay = parseFloat($('#cur_pay').val()) || 0;
        var remained = totalVat - curPay;
        $('#remained').val(remained.toFixed(2));
    }

    // =========================================
    // UPDATE REMAIN ON CUR PAY
    // =========================================
    window.updateRemainOnCurPay = function(value) {
        var totalPrice = parseFloat($('#total_price').val()) || 0;
        var totalVat = parseFloat($('#total_vat_summary').val()) || 0;
        var curPay = parseFloat(value) || 0;
        var remained = totalVat - curPay;
        $('#remained').val(remained.toFixed(2));
    };

    // =========================================
    // AMOUNT ARROW KEY BEHAVIOR
    // =========================================
    $(document).on('keydown', '.amount', function(e) {
        var key = e.key || e.keyCode;
        
        if (key === 'ArrowUp' || key === 38) {
            e.preventDefault();
            var currentVal = parseFloat($(this).val()) || 0;
            var maxVal = parseFloat($(this).attr('max')) || Infinity;
            var newVal = currentVal + 1;
            if (newVal > maxVal && maxVal !== Infinity) {
                newVal = maxVal;
            }
            $(this).val(newVal).trigger('input');
        } else if (key === 'ArrowDown' || key === 40) {
            e.preventDefault();
            var currentVal = parseFloat($(this).val()) || 0;
            var minVal = parseFloat($(this).attr('min')) || 0;
            var newVal = currentVal - 1;
            if (newVal < minVal) {
                newVal = minVal;
            }
            $(this).val(newVal).trigger('input');
        }
    });

    // =========================================
    // AMOUNT VALIDATION
    // =========================================
    $(document).on('input', '.amount', function() {
        var maxVal = parseFloat($(this).attr('max')) || 0;
        var currentVal = parseFloat($(this).val()) || 0;
        
        if (currentVal > maxVal && maxVal > 0) {
            $(this).val(maxVal);
            showNotification('{{__("sales.cannot_exceed_availability")}}', 'warning');
        }
        
        var row = $(this).closest('tr');
        recalculateRow(row);
    });

    // =========================================
    // EVENT HANDLERS
    // =========================================

    // Customer select change
    $('#customer_account_id').on('change', function() {
        var customerId = $(this).val();
        var selectedOption = $(this).find(':selected');
        var hasItems = parseInt(selectedOption.data('has-items')) === 1;
        var hasOrder = parseInt(selectedOption.data('has-order')) === 1;
        loadItemsForCustomer(customerId, hasItems, hasOrder);
    });

    // Add new item button
    $('#addNewItemBtn').on('click', function() {
        addNewItem();
    });

    function addNewItem() {
        var newItem = {
            pre_list_id: '',
            pre_list_name: '',
            unit_id: '',
            unit_name: '',
            amount: 1,
            buy_up: 0,
            profit_amount: 0,
            sell_up: 0,
            total: 0,
            category_id: '',
            category_name: '',
            available_amount: 0,
            warehouse_item_id: '',
            item_name: '',
            from_order: false,
            is_new: true,
            sell_up_vat: 0,
            sell_tax_price: 0,
            total_vat: 0
        };

        currentItems.push(newItem);
        var index = currentItems.length - 1;
        appendNewRow(index);
    }

    // =========================================
    // GENERATE NEW ROW HTML
    // =========================================
    function generateNewRowHtml(index) 
    {
        if (!warehouseItemsData || warehouseItemsData.length === 0) {
            return `
                <tr class="item-row" data-index="${index}">
                    <td colspan="10" class="text-center text-danger">
                        {{__('sales.no_warehouse_items_available')}}
                    </td>
                </tr>
            `;
        }

        // Get tax activation and percentage from hidden fields
        var taxActivation = parseInt($('#tax_activation').val()) || 0;
        var taxPercent = parseFloat($('#tax_per').val()) || 0;
        var showTax = taxActivation === 1;

        var optionsHtml = warehouseItemsData.map(function(item) {
            return `<option value="${item.warehouse_item_id}" 
                data-pre-list-id="${item.pre_list_id}"
                data-unit-id="${item.warehouse_unit_id}"
                data-unit-name="${item.warehouse_unit_name}"
                data-available-amount="${item.available_amount}"
                data-sell-up="${item.sell_up}"
                data-buy-up="${item.buy_up}"
                data-item-name="${item.item_name}"
                data-category-id="${item.category_id || ''}">
                ${item.item_name} (${item.available_amount} ${item.warehouse_unit_name})
            </option>`;
        }).join('');

        // Tax display for new row (initially 0)
        var sellUpVatDisplay = showTax ? `
            <span class="tax-label">${taxPercent}% | <span class="tax-value" id="sell_up_vat_display_${index}">0.00</span></span>
            <input type="hidden" name="items[${index}][sell_up_vat]" class="sell-up-vat" value="0">
            <input type="hidden" name="items[${index}][sell_tax_price]" class="sell-tax-price" value="0">
            <input type="hidden" name="items[${index}][sell_tax_per]" class="sell-tax-per" value="${taxPercent}">
        ` : `
            <input type="hidden" name="items[${index}][sell_up_vat]" class="sell-up-vat" value="0">
            <input type="hidden" name="items[${index}][sell_tax_price]" class="sell-tax-price" value="0">
            <input type="hidden" name="items[${index}][sell_tax_per]" class="sell-tax-per" value="${taxPercent}">
        `;

        var totalVatDisplay = showTax ? `
            <span class="tax-label" id="total_vat_display_${index}">0.00</span>
            <input type="hidden" name="items[${index}][total_vat]" class="total-vat" value="0">
        ` : `
            <input type="hidden" name="items[${index}][total_vat]" class="total-vat" value="0">
        `;

        return `
            <tr class="item-row" data-index="${index}">
                <td class="row-number">${index + 1}</td>
                <td>
                    <select class="form-control select2 warehouse-item-select" name="items[${index}][warehouse_item_id]" style="width: 100%;" required>
                        <option value="">{{__('wh.select_available_item')}}</option>
                        ${optionsHtml}
                    </select>
                    <input type="hidden" name="items[${index}][pre_list_id]" class="pre-list-id-hidden" value="">
                    <input type="hidden" name="items[${index}][order_id]" value="">
                </td>
                <td>
                    <input name="items[${index}][amount]" class="form-control amount" type="number" step="any" min="0.1" 
                        value="1" required>
                    <small class="text-muted max-label" style="display:block;font-size:9px;">{{__('sales.max')}}: 0</small>
                </td>
                <td>
                    <input type="text" class="form-control unit-name-display" value="" readonly style="background:#f5f5f5;">
                    <input type="hidden" name="items[${index}][unit_id]" class="unit-id-hidden" value="">
                </td>
                <td>
                    <input name="items[${index}][buy_up]" class="form-control buy-up" type="number" step="any" 
                        value="0" readonly style="background:#f5f5f5;">
                </td>
                <td>
                    <input name="items[${index}][profit_amount]" class="form-control profit-amount" type="number" step="any" 
                        value="0" placeholder="0.00">
                </td>
                <td>
                    <input name="items[${index}][sell_up]" class="form-control sell-up" type="number" step="any" 
                        value="0" readonly>
                    ${sellUpVatDisplay}
                </td>
                <td>
                    <input name="items[${index}][total]" class="form-control total" type="number" step="any" 
                        value="0" readonly>
                    ${totalVatDisplay}
                </td>
                <td>
                    <span class="availability-badge" style="background:#e9ecef; color:#6c757d;"> ? </span>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item" style="padding: 2px 8px !important;" title="{{__('common.remove')}}">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    // =========================================
    // APPEND NEW ROW
    // =========================================
    function appendNewRow(index) 
    {
        var html = generateNewRowHtml(index);
        var $newRow = $(html);
        $('#itemsBody').append($newRow);
        
        if (!warehouseItemsData || warehouseItemsData.length === 0) {
            return;
        }
        
        $newRow.find('.warehouse-item-select').select2();
        
        $newRow.find('.warehouse-item-select').on('change', function() {
            var selectedOption = $(this).find(':selected');
            var warehouseItemId = $(this).val();
            
            if (!warehouseItemId) {
                resetNewRow($(this).closest('tr'));
                return;
            }
            
            var preListId = selectedOption.data('pre-list-id') || '';
            var unitId = selectedOption.data('unit-id') || '';
            var unitName = selectedOption.data('unit-name') || '';
            var availableAmount = selectedOption.data('available-amount') || 0;
            var sellUp = selectedOption.data('sell-up') || 0;
            var buyUp = selectedOption.data('buy-up') || 0;
            var itemName = selectedOption.data('item-name') || '';
            var categoryId = selectedOption.data('category-id') || '';
            
            var row = $(this).closest('tr');
            var index = row.data('index');
            
            // Set fields
            row.find('.pre-list-id-hidden').val(preListId);
            row.find('.unit-id-hidden').val(unitId);
            row.find('.unit-name-display').val(unitName);
            row.find('.warehouse-item-id-hidden').val(warehouseItemId);
            row.find('.buy-up').val(buyUp);
            row.find('.sell-up').val(sellUp);
            row.find('.amount').attr('max', availableAmount);
            
            var maxLabel = row.find('.max-label');
            if (maxLabel.length) {
                maxLabel.text('{{__("sales.max")}}: ' + availableAmount);
            }
            
            var badge = row.find('.availability-badge');
            if (badge.length) {
                var badgeClass = availableAmount > 10 ? 'availability-high' : (availableAmount > 5 ? 'availability-medium' : 'availability-low');
                badge.attr('class', 'availability-badge ' + badgeClass);
                badge.text(availableAmount);
            }
            
            // Update currentItems
            if (currentItems[index]) {
                currentItems[index].pre_list_id = preListId;
                currentItems[index].unit_id = unitId;
                currentItems[index].available_amount = availableAmount;
                currentItems[index].warehouse_item_id = warehouseItemId;
                currentItems[index].sell_up = sellUp;
                currentItems[index].buy_up = buyUp;
                currentItems[index].item_name = itemName;
                currentItems[index].unit_name = unitName;
                currentItems[index].category_id = categoryId;
            }
            
            // Trigger recalculation to update tax
            recalculateRow(row);
        });
        
        updateRowNumbers();
        updateTotalPrice();
    }

    // =========================================
    // RESET NEW ROW
    // =========================================
    function resetNewRow(row) {
        row.find('.pre-list-id-hidden').val('');
        row.find('.unit-id-hidden').val('');
        row.find('.unit-name-display').val('');
        row.find('.buy-up').val(0);
        row.find('.profit-amount').val(0);
        row.find('.sell-up').val(0);
        row.find('.total').val(0);
        row.find('.amount').val(0).attr('max', 0);
        row.find('.max-label').text('{{__("sales.max")}}: 0');
        row.find('.availability-badge').attr('class', 'availability-badge').text('{{__("common.select_item")}}');
        
        var index = row.data('index');
        if (currentItems[index]) {
            currentItems[index].pre_list_id = '';
            currentItems[index].unit_id = '';
            currentItems[index].available_amount = 0;
            currentItems[index].warehouse_item_id = '';
            currentItems[index].sell_up = 0;
            currentItems[index].buy_up = 0;
            currentItems[index].item_name = '';
            currentItems[index].unit_name = '';
        }
        
        recalculateRow(row);
    }

    // =========================================
    // RECALCULATE ON INPUT CHANGE
    // =========================================
    $(document).on('input change', '.amount, .profit-amount', function() {
        var row = $(this).closest('tr');
        recalculateRow(row);
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        var row = $(this).closest('tr');
        removeRow(row);
    });

    // =========================================
    // FORM SUBMISSION
    // =========================================
    $('#salesForm').on('submit', function(e) {
        e.preventDefault();

        var isValid = true;
        var errorMessages = [];

        $('.item-row').each(function() {
            var row = $(this);
            var preListId = row.find('.pre-list-id-hidden').val();
            var amount = row.find('.amount').val();
            var buyUp = row.find('.buy-up').val();
            var sellUp = row.find('.sell-up').val();
            var unitId = row.find('.unit-id-hidden').val();
            var warehouseItemId = row.find('.warehouse-item-id-hidden').val();
            var availableAmount = parseFloat(row.find('.amount').attr('max')) || 0;
            var enteredAmount = parseFloat(amount) || 0;

            if (!preListId && !warehouseItemId) {
                return;
            }

            if (!preListId) {
                isValid = false;
                errorMessages.push('{{__("wh.select_item")}}');
            }

            if (!amount || enteredAmount <= 0) {
                isValid = false;
                row.find('.amount').css('border-color', 'red');
                errorMessages.push('{{__("wh.enter_valid_amount")}}');
            } else if (enteredAmount > availableAmount && availableAmount > 0) {
                isValid = false;
                row.find('.amount').css('border-color', 'red');
                errorMessages.push('{{__("sales.insufficient_stock")}}');
            } else {
                row.find('.amount').css('border-color', '');
            }

            if (!sellUp || parseFloat(sellUp) <= 0) {
                isValid = false;
                row.find('.sell-up').css('border-color', 'red');
                errorMessages.push('{{__("sales.enter_valid_sell_up")}}');
            } else {
                row.find('.sell-up').css('border-color', '');
            }

            if (!unitId) {
                isValid = false;
                errorMessages.push('{{__("wh.select_unit")}}');
            }

            // if (!warehouseItemId) {
            //     isValid = false;
            //     errorMessages.push('{{__("sales.select_valid_warehouse_item")}}');
            // }
        });

        if (!isValid) {
            alert(errorMessages.join('\n'));
            return;
        }


           // check remained and loandLimit
        var loanLimit = parseFloat($('#alloweLimitValue').val()) || 0;
        var shouldCheckFlag = parseFloat($('#shouldCheck').val()) || 0; 
        var remained =  parseFloat($('#remained').val()) || 0;
        if(parseInt(shouldCheckFlag) === 1)  { // if option is active, check loan limit
            if(remained > loanLimit) {
                // $('#submit_button').fadeOut(1);
                showNotification('بالاتر از سقف قرض مجاز نیست', 'warning');
                return;
            } 
        }
        var $submitBtn = $('#submit_button');
        var originalText = $submitBtn.val();
        $submitBtn.prop('disabled', true).val('{{__("common.saving")}}...');

        var formData = $(this).serialize();
    

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $submitBtn.prop('disabled', false).val(originalText);
                if (response.status === 'success') {
                    showNotification(response.message || '{{__("common.added_successfully")}}', 'success');
                    var billno = response.data.billno || response.billno || $('#billno').val();
                    setTimeout(function() {
                        if (billno) {
                            window.location.href = '/sales/bill/' + billno;
                        } else {
                            window.location.href = '{{ route("sales.index") }}';
                        }
                    }, 1500);
                } else {
                    showNotification(response.message || '{{__("common.error_occurred")}}', 'danger');
                }
            },
            error: function(xhr) {
                $submitBtn.prop('disabled', false).val(originalText);
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorMessages = [];
                    $.each(errors, function(key, messages) {
                        errorMessages.push(messages[0]);
                    });
                    showNotification(errorMessages.join('<br>'), 'danger');
                } else {
                    showNotification('{{__("common.error_occurred")}}', 'danger');
                }
            }
        });
    });

    // =========================================
    // NOTIFICATION FUNCTION
    // =========================================
    function showNotification(message, type = 'info', from = 'top', align = 'center', style = 'withicon') {
        var content = {
            message: '<span style="font-size:16px;">' + message + '</span>',
            title: '&nbsp;&nbsp;&nbsp;<span style="font-size:16px;">{{__("settings.message")}}</span>',
            icon: style === 'withicon' ? 'fa fa-bell' : 'none',
            url: '#',
            target: '_blank'
        };

        $.notify(content, {
            type: type,
            placement: {
                from: from,
                align: align
            },
            time: 500
        });
    }

    // =========================================
    // INITIAL SETUP
    // =========================================
    showEmptyState('{{__("sales.select_customer")}}');
});
</script>
@endsection