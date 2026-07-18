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
    .table-scroll-wrapper .col-amount { min-width: 80px; width: 12%; }
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
</style>

<div class="main-panel">
    <div class="content">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card" style="min-height: 400px">
                        <div class="card-header" style="padding: 10px;">
                            <h4 class="card-title"> {{__('buy.create_title')}}
                                <span class="pull-left">
                                    <a href="{{ route('boughtList.index') }}">
                                        <button class="btn mybtn bg-default">{{__('common.back')}}</button>
                                    </a>
                                </span>
                            </h4>
                        </div>

                        <form id="buyingForm" action="{{ route('boughtList.submit') }}" method="POST">
                            @csrf
                            <input type="hidden" name="times" value="{{ $times }}">
                            <input type="hidden" name="journal_code" value="{{ $newJournalCode }}">
                            <input type="hidden" name="tax_activation" value="{{ $tax->tax_activation ?? 0 }}">
                            <input type="hidden" name="currency_id" value="{{ $currencies->first()->id ?? 1 }}">
                            
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
                                            <label for="supplier_account_id">{{__('order.supplier_selection')}} <span class="danger">*</span></label>
                                            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="supplier_account_id" id="supplier_account_id" required>
                                                <option value="">{{__('order.supplier_name')}}</option>
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('supplier_account_id')
                                                <span style='color:red'>{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-2 col-sm-4 col-xs-6">
                                            <label for="from_account_id">{{__('buy.company_account')}} <span class="danger">*</span></label>
                                            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="from_account_id" required>
                                                @foreach($ownBanks as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-3 col-sm-4 col-xs-6">
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
                                            <span id="successMsg" style="display:none">
                                                <div style="color:green">{{__('buy.confirmed')}}</div>
                                            </span>
                                            <span id="failurMsg" style="display:none">
                                                <div style="color:red">{{__('buy.repeated_billno')}}</div>
                                            </span>
                                        </div>

                                        <div class="col-md-2 col-sm-4 col-xs-6">
                                            <label for="factor">{{__('buy.factor')}}</label>
                                            <input type="text" class="form-control" name="factor" id="factor" placeholder="{{__('buy.factor')}}">
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
                                                                    <th style="width:10%">{{__('common.unit')}}</th>
                                                                    <th style="width:15%">{{__('buy.buy_up')}}</th>
                                                                    <th style="width:15%">{{__('sales.profit')}} </th>
                                                                    <th style="width:15%">{{__('sales.sold_up')}}</th>
                                                                    <th style="width:10%">{{__('common.total')}}</th>
                                                                    <th style="width:5%">{{__('common.action')}}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="itemsBody">
                                                                <!-- Items will be loaded dynamically via JavaScript -->
                                                            </tbody>
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
                                                            <input type="number" name="total_price" id="total_price" value="0" class="form-control" step="0.01" readonly>
                                                        </td>
                                                        <td style="width:10%"><strong>{{__('buy.cur_pay')}}</strong></td>
                                                        <td style="width:15%">
                                                            <input type="number" name="cur_pay" id="cur_pay" oninput="updateRemainOnCurPay(this.value)" class="form-control" step="0.01" required>
                                                        </td>
                                                        <td style="width:10%"><strong>{{__('buy.remained')}}</strong></td>
                                                        <td style="width:15%">
                                                            <input type="number" name="remained" id="remained" class="form-control" step="0.01" readonly>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>{{__('journal.payer_account')}}</strong></td>
                                                        <td>
                                                            <select class="form-control select2" style="width:100%; background-color:#ddd;" name="from_account_id" required>
                                                                @foreach($ownBanks as $acc)
                                                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><strong>{{__('common.currency')}}</strong></td>
                                                        <td>
                                                            <select class="form-control select2" style="width:100%; background-color:#ddd;" name="currency_id" required>
                                                                @foreach($currencies as $currency)
                                                                    <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td><strong>{{__('buy.comment')}}</strong></td>
                                                        <td>
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
                                                    <a href="{{ route('boughtList.index') }}">
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
$(document).ready(function () {
    // Store orders data
    var ordersData = {!! json_encode($orders) !!};
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
    // SUPPLIER SELECT CHANGE - Load Items
    // =========================================
    $('#supplier_account_id').on('change', function() {
        var supplierId = $(this).val();
        loadItemsForSupplier(supplierId);
    });

    // =========================================
    // LOAD ITEMS FOR SELECTED SUPPLIER
    // =========================================
    function loadItemsForSupplier(supplierId) {
        if (!supplierId) {
            $('#itemsBody').html('<tr><td colspan="9" class="text-center text-muted">{{__("buy.select_supplier_first")}}</td></tr>');
            return;
        }

        // Find orders for this supplier
        var supplierOrders = ordersData.filter(function(order) {
            return order.supplier_id == supplierId;
        });

        if (supplierOrders.length === 0) {
            $('#itemsBody').html('<tr><td colspan="9" class="text-center text-muted">{{__("buy.no_items_for_supplier")}}</td></tr>');
            return;
        }

        // Extract all items from orders
        var allItems = [];
        supplierOrders.forEach(function(order) {
            order.items.forEach(function(item) {
                allItems.push({
                    order_id: order.id,
                    ord_num: order.ord_num,
                    pre_list_id: item.pre_list_id,
                    pre_list_name: item.pre_list.name || 'Unknown',
                    unit_id: item.unit_id,
                    unit_name: item.unit.name || 'Unknown',
                    amount: parseFloat(item.amount) || 0,
                    category_id: item.category_id,
                    category_name: order.category_relation ? order.category_relation.name : 'Unknown'
                });
            });
        });

        currentItems = allItems;
        renderItems(allItems);
    }

    // =========================================
    // RENDER ITEMS IN TABLE
    // =========================================
    function renderItems(items) {
        if (!items || items.length === 0) {
            $('#itemsBody').html('<tr><td colspan="9" class="text-center text-muted">{{__("buy.no_items_found")}}</td></tr>');
            return;
        }

        var html = '';
        items.forEach(function(item, index) {
            var amount = parseFloat(item.amount) || 0;
            html += `
                <tr class="item-row" data-index="${index}" data-pre-list-id="${item.pre_list_id}" data-unit-id="${item.unit_id}">
                    <td>${index + 1}</td>
                    <td>
                        <strong>${item.pre_list_name}</strong>
                        <input type="hidden" name="items[${index}][pre_list_id]" value="${item.pre_list_id}">
                        <input type="hidden" name="items[${index}][order_id]" value="${item.order_id || ''}">
                        <input type="hidden" name="items[${index}][category_id]" value="${item.category_id || ''}">
                        <input type="hidden" name="items[${index}][unit_id]" value="${item.unit_id}">
                    </td>
                    <td>
                        <input name="items[${index}][amount]" class="form-control amount" type="number" step="0.01" 
                            value="${amount}" min="0" required>
                    </td>
                    <td>
                        <span class="unit-name-display">${item.unit_name}</span>
                    </td>
                    <td>
                        <input name="items[${index}][buy_up]" class="form-control buy-up" type="number" step="0.01" 
                            value="0" min="0" required>
                    </td>
                    <td>
                        <input name="items[${index}][profit_amount]" class="form-control profit-amount" type="number" step="0.01" 
                            value="0" placeholder="0.00">
                    </td>
                    <td>
                        <input name="items[${index}][sell_up]" class="form-control sell-up" type="number" step="0.01" 
                            value="0" min="0" readonly>
                    </td>
                    <td>
                        <input name="items[${index}][total]" class="form-control total" type="number" step="0.01" 
                            value="0" min="0" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-item" style="padding: 2px 8px !important;" title="{{__('common.remove')}}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#itemsBody').html(html);
        updateTotalPrice();
    }

    // =========================================
    // RECALCULATE ROW
    // =========================================
    function recalculateRow(row) {
        var amount = parseFloat(row.find('.amount').val()) || 0;
        var buyUp = parseFloat(row.find('.buy-up').val()) || 0;
        var profit = parseFloat(row.find('.profit-amount').val()) || 0;

        // Calculate sell_up = buy_up + profit
        var sellUp = buyUp + profit;
        row.find('.sell-up').val(sellUp.toFixed(2));

        // Calculate total = amount * sell_up
        var total = amount * sellUp;
        row.find('.total').val(total.toFixed(2));

        updateTotalPrice();
    }

    // =========================================
    // UPDATE TOTAL PRICE
    // =========================================
    function updateTotalPrice() {
        var totalPrice = 0;
        $('.total').each(function() {
            var val = parseFloat($(this).val()) || 0;
            totalPrice += val;
        });
        $('#total_price').val(totalPrice.toFixed(2));
        
        // Update remained
        var curPay = parseFloat($('#cur_pay').val()) || 0;
        var remained = totalPrice - curPay;
        $('#remained').val(remained.toFixed(2));
    }

    // =========================================
    // UPDATE REMAIN ON CUR PAY
    // =========================================
    window.updateRemainOnCurPay = function(value) {
        var totalPrice = parseFloat($('#total_price').val()) || 0;
        var curPay = parseFloat(value) || 0;
        var remained = totalPrice - curPay;
        $('#remained').val(remained.toFixed(2));
    };

    // =========================================
    // EVENT HANDLERS
    // =========================================

    // Recalculate on amount, buy_up, or profit_percent change
    $(document).on('input', '.amount, .buy-up, .profit-amount', function() {
        var row = $(this).closest('tr');
        recalculateRow(row);
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        var row = $(this).closest('tr');
        var index = row.data('index');
        
        if (confirm('{{__("common.delete_confirm")}}')) {
            currentItems.splice(index, 1);
            renderItems(currentItems);
        }
    });

    // =========================================
    // FORM SUBMISSION
    // =========================================
    $('#buyingForm').on('submit', function(e) {
        e.preventDefault();

        var isValid = true;
        var errorMessages = [];

        $('.item-row').each(function() {
            var row = $(this);
            var amount = row.find('.amount').val();
            var buyUp = row.find('.buy-up').val();
            var profitPercent = row.find('.profit-percent').val();

            if (!amount || parseFloat(amount) <= 0) {
                isValid = false;
                row.find('.amount').css('border-color', 'red');
                errorMessages.push('{{__("wh.enter_valid_amount")}}');
            } else {
                row.find('.amount').css('border-color', '');
            }

            if (!buyUp || parseFloat(buyUp) <= 0) {
                isValid = false;
                row.find('.buy-up').css('border-color', 'red');
                errorMessages.push('{{__("buy.enter_valid_buy_up")}}');
            } else {
                row.find('.buy-up').css('border-color', '');
            }

            if (!profitPercent || parseFloat(profitPercent) < 0) {
                isValid = false;
                row.find('.profit-percent').css('border-color', 'red');
                errorMessages.push('{{__("buy.enter_valid_profit")}}');
            } else {
                row.find('.profit-percent').css('border-color', '');
            }
        });

        if (!isValid) {
            alert(errorMessages.join('\n'));
            return;
        }

        var $submitBtn = $('#submit_button');
        var originalText = $submitBtn.val();
        $submitBtn.prop('disabled', true).val('{{__("common.saving")}}...');

        // Prepare form data
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
                    setTimeout(function() {
                        window.location.href = '{{ route("boughtList.index") }}';
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
});
</script>
@endsection