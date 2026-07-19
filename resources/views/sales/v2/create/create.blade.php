@extends('layouts.app')

@section('content')


<style>
    table.new thead tr th{color:#000 !important;text-align:center;}
    table.my_table thead tr th{background-color:#3f7cc7 !important; color:#fff !important;text-align:center;}
    .new tbody tr td{padding: 5px 5px;}
    select.select2{text-align:right !important;direction:rtl !important;}
    .form-control {
        padding-right: 3px !important;
    }
    
    table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    thead th {
        background: #e9fffe !important;
        border-bottom: 2px solid #ddd;
        white-space: nowrap;
        padding: 10px 12px;
        font-size: 14px;
        text-align: center;
        vertical-align: middle;
    }
    
    tbody td {
        padding: 8px 10px;
        vertical-align: middle;
        text-align: center;
    }
    
    tbody td .form-control {
        width: 100%;
        min-width: 60px;
        padding: 4px 6px;
        font-size: 13px;
        height: 32px;
    }
    
    tbody td .select2-container {
        width: 100% !important;
        min-width: 120px;
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

    .warehouse-item-select {
        width: 100% !important;
    }

    .text-muted small {
        font-size: 9px;
    }

    .order-badge {
        font-size: 9px;
        padding: 2px 6px;
        border-radius: 10px;
        background: #e9ecef;
        color: #495057;
        margin-left: 4px;
        display: inline-block;
    }
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
                                    <a href="{{ route('sales.index') }}">
                                        <button class="btn mybtn bg-default">{{__('common.back')}}</button>
                                    </a>
                                </span>
                            </h4>
                        </div>

                        <form id="salesForm" action="{{ route('sales.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="times" value="{{ $times }}">
                            <input type="hidden" name="code" value="{{ $journal_code }}">
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
                                        <label for="customer_account_id">{{__('order.customer_selection')}} <span class="danger">*</span></label>
                                        <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="customer_account_id" id="customer_account_id" required>
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
                                                <!-- <option value="">{{__('sales.select_car')}}</option> -->
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
                                                <div class="table-responsive">
                                                    <table class="table table-bordered new" id="itemsTable">
                                                        <thead>
                                                            <tr style="background:#e9fffe">
                                                                <th style="width:5%">#</th>
                                                                <th style="width:20%">{{__('wh.item_selection')}}</th>
                                                                <th style="width:10%">{{__('common.amount')}}</th>
                                                                <th style="width:12%">{{__('common.unit')}}</th>
                                                                <th style="width:15%">{{__('sales.sold_up')}}</th>
                                                                <th style="width:15%">{{__('common.total')}}</th>
                                                                <th style="width:10%">{{__('common.availability')}}</th>
                                                                <th style="width:5%">{{__('common.action')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="itemsBody">
                                                            <!-- Items will be loaded dynamically via JavaScript -->
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="8">
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
                                                        <td><strong>{{__('journal.receiver_account')}}</strong></td>
                                                        <td>
                                                            <select class="form-control select2" style="width:100%; background-color:#ddd;" name="account_id" required>
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
$(document).ready(function () {
    // =========================================
    // DATA STORAGE - Use correct variable names
    // =========================================
    var combinedItemsData = {!! json_encode($combinedItems ?? []) !!};
    var customersWithStatusData = {!! json_encode($customersWithStatus ?? []) !!};
    var warehouseItemsData = {!! json_encode($warehouseItems ?? []) !!};
    var unitsData = {!! json_encode($units ?? []) !!};
    var currentItems = [];

    // =========================================
    // DEBUG - Log data
    // =========================================
    console.log('=== SALES FORM DEBUG ===');
    console.log('combinedItemsData:', combinedItemsData);
    console.log('combinedItemsData length:', combinedItemsData.length);
    console.log('customersWithStatusData:', customersWithStatusData);

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
    // LOAD ITEMS FOR SELECTED CUSTOMER
    // =========================================
    $('#customer_account_id').on('change', function() {
        var customerId = $(this).val();
        var selectedOption = $(this).find(':selected');
        
        // Use integer comparison (1 = true, 0 = false)
        var hasItems = parseInt(selectedOption.data('has-items')) === 1;
        var hasOrder = parseInt(selectedOption.data('has-order')) === 1;
        
        console.log('=== CUSTOMER SELECTED ===');
        console.log('Customer ID:', customerId);
        console.log('Has items:', hasItems);
        console.log('Has order:', hasOrder);
        console.log('Selected option data:', selectedOption.data());
        
        loadItemsForCustomer(customerId, hasItems, hasOrder);
    });

    function loadItemsForCustomer(customerId, hasItems, hasOrder) 
    {
        console.log('Loading items for customer:', customerId);
        console.log('combinedItemsData:', combinedItemsData);
        
        if (!customerId) {
            currentItems = [];
            $('#itemsBody').empty();
            showEmptyState('{{__("sales.select_customer")}}');
            return;
        }

        // If customer has no available items, show empty state
        if (!hasItems) {
            currentItems = [];
            $('#itemsBody').empty();
            if (hasOrder) {
                showEmptyState('{{__("sales.no_available_stock_for_customer")}}');
            } else {
                showEmptyState('{{__("sales.no_orders_for_customer_add_new")}}');
            }
            return;
        }

        // Find items for this customer from combined data
        var customerIdInt = parseInt(customerId);
        
        var customerItems = combinedItemsData.filter(function(item) {
            var itemCustomerId = parseInt(item.customer_id);
            return itemCustomerId === customerIdInt;
        });

        console.log('Filtered items for customer:', customerItems);
        console.log('Number of items found:', customerItems.length);

        if (customerItems.length === 0) {
            currentItems = [];
            $('#itemsBody').empty();
            showEmptyState('{{__("sales.no_available_items")}}');
            return;
        }

        var allItems = [];

        customerItems.forEach(function(item, index) {
            console.log('Processing item ' + index + ':', item);
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
                sell_up: parseFloat(item.sell_up) || 0,
                available_amount: parseFloat(item.available_amount) || 0,
                item_name: item.item_name || 'Unknown',
                from_order: true,
                is_new: false
            });
        });

        currentItems = allItems;
        $('#itemsBody').empty();
        
        if (currentItems.length === 0) {
            showEmptyState('{{__("sales.no_available_items")}}');
        } else {
            console.log('Rendering ' + currentItems.length + ' items');
            currentItems.forEach(function(item, index) {
                appendRow(item, index);
            });
        }
        
        updateTotalPrice();
    }

    // =========================================
    // GENERATE ROW HTML
    // =========================================
    function generateRowHtml(item, index) {
        var amount = parseFloat(item.amount) || 0;
        var sellUp = (item.sell_up !== '' && item.sell_up !== undefined) ? parseFloat(item.sell_up) : '';
        var total = (item.total !== '' && item.total !== undefined) ? parseFloat(item.total) : 0;
        var availableAmount = item.available_amount || 0;
        var unitId = item.unit_id || '';
        var warehouseItemId = item.warehouse_item_id || '';
        var itemName = item.item_name || item.pre_list_name || '';

        // Availability badge
        var badgeClass = availableAmount > 10 ? 'availability-high' : (availableAmount > 5 ? 'availability-medium' : 'availability-low');
        var availabilityBadge = availableAmount > 0 
            ? `<span class="availability-badge ${badgeClass}"> ${availableAmount} {{__('common.available')}}</span>`
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
                    <input name="items[${index}][amount]" class="form-control amount" type="number" step="0.5" 
                        value="${amount}" min="0" max="${availableAmount}" required>
                    <small class="text-muted" style="display:block;font-size:9px;">{{__('sales.max')}}: ${availableAmount}</small>
                </td>
                <td>
                    <input type="text" class="form-control unit-name-display" value="${item.unit_name || ''}" readonly style="background:#f5f5f5;">
                    <input type="hidden" name="items[${index}][unit_id]" class="unit-id-hidden" value="${unitId}">
                </td>
                <td>
                    <input name="items[${index}][sell_up]" class="form-control sell-up" type="number" step="0.01" 
                        value="${sellUp !== '' ? sellUp : ''}" min="0" readonly>
                </td>
                <td>
                    <input name="items[${index}][total]" class="form-control total" type="number" step="0.01" 
                        value="${total}" min="0" readonly>
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
        
        updateRowNumbers();
        recalculateRow($newRow);
    }

    // =========================================
    // RECALCULATE ROW
    // =========================================
    function recalculateRow(row) {
        var amount = parseFloat(row.find('.amount').val()) || 0;
        var sellUp = parseFloat(row.find('.sell-up').val()) || 0;

        var total = amount * sellUp;
        row.find('.total').val(total.toFixed(2));

        var index = row.data('index');
        if (currentItems[index]) {
            currentItems[index].amount = amount;
            currentItems[index].sell_up = sellUp;
            currentItems[index].total = total;
        }

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
    // SHOW EMPTY STATE
    // =========================================
    function showEmptyState(message) {
        var msg = message || '{{__("sales.no_items_found")}}';
        $('#itemsBody').html(`
            <tr>
                <td colspan="8" class="text-center text-muted">
                    <i class="fa fa-info-circle"></i> ${msg}
                </td>
            </tr>
        `);
        updateTotalPrice();
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
    $('#addNewItemBtn').on('click', function() {
        addNewItem();
    });

    function addNewItem() {
        var newItem = {
            pre_list_id: '',
            item_name: '',
            unit_id: '',
            unit_name: '',
            amount: 0,
            sell_up: '',
            total: 0,
            available_amount: 0,
            warehouse_item_id: '',
            from_order: false,
            is_new: true
        };

        currentItems.push(newItem);
        var index = currentItems.length - 1;
        appendNewRow(index);
    }

    // =========================================
    // GENERATE NEW ROW HTML
    // =========================================
    function generateNewRowHtml(index) {
          // Check if warehouseItemsData has items
        if (!warehouseItemsData || warehouseItemsData.length === 0) {
            return `
                <tr class="item-row" data-index="${index}">
                    <td colspan="8" class="text-center text-danger">
                        {{__('sales.no_warehouse_items_available')}}
                    </td>
                </tr>
            `;
        }
        var optionsHtml = warehouseItemsData.map(function(item) {
            return `<option value="${item.warehouse_item_id}" 
                data-pre-list-id="${item.pre_list_id}"
                data-unit-id="${item.warehouse_unit_id}"
                data-unit-name="${item.warehouse_unit_name}"
                data-available-amount="${item.available_amount}"
                data-sell-up="${item.sell_up}"
                data-item-name="${item.item_name}"
                data-category-id="${item.category_id || ''}">
                ${item.item_name} (${item.available_amount} ${item.warehouse_unit_name})
            </option>`;
        }).join('');

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
                    <input name="items[${index}][amount]" class="form-control amount" type="number" step="0.5" 
                        value="0" min="0" required>
                    <small class="text-muted max-label" style="display:block;font-size:9px;">{{__('sales.max')}}: 0</small>
                </td>
                <td>
                    <input type="text" class="form-control unit-name-display" value="" readonly style="background:#f5f5f5;">
                    <input type="hidden" name="items[${index}][unit_id]" class="unit-id-hidden" value="">
                </td>
                <td>
                    <input name="items[${index}][sell_up]" class="form-control sell-up" type="number" step="0.01" 
                        value="" min="0" readonly>
                </td>
                <td>
                    <input name="items[${index}][total]" class="form-control total" type="number" step="0.01" 
                        value="0" min="0" readonly>
                </td>
                <td>
                    <span class="availability-badge" style="background:#e9ecef; color:#6c757d;">{{__('common.select_item')}}</span>
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
    // APPEND NEW ROW (With dropdown)
    // =========================================
    function appendNewRow(index) 
    {
        var html = generateNewRowHtml(index);
        var $newRow = $(html);
        $('#itemsBody').append($newRow);
        
        // Check if there are warehouse items
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
            var sellUp = selectedOption.data('sell-up') || '';
            var itemName = selectedOption.data('item-name') || '';
            var categoryId = selectedOption.data('category-id') || '';
            
            var row = $(this).closest('tr');
            var index = row.data('index');
            
            // Set ALL hidden fields
            row.find('.pre-list-id-hidden').val(preListId);
            row.find('.unit-id-hidden').val(unitId);
            row.find('.unit-name-display').val(unitName);
            row.find('.warehouse-item-id-hidden').val(warehouseItemId);
            
            // Also set category if needed
            if (categoryId) {
                row.find('.category-id-hidden').val(categoryId);
            }
            
            // Update display fields
            row.find('.amount').attr('max', availableAmount);
            row.find('.sell-up').val(sellUp);
            
            var maxLabel = row.find('.max-label');
            if (maxLabel.length) {
                maxLabel.text('{{__("sales.max")}}: ' + availableAmount);
            }
            
            var badge = row.find('.availability-badge');
            if (badge.length) {
                var badgeClass = availableAmount > 10 ? 'availability-high' : (availableAmount > 5 ? 'availability-medium' : 'availability-low');
                badge.attr('class', 'availability-badge ' + badgeClass);
                badge.text(availableAmount + ' {{__("common.available")}}');
            }
            
            // Update currentItems
            if (currentItems[index]) {
                currentItems[index].pre_list_id = preListId;
                currentItems[index].unit_id = unitId;
                currentItems[index].available_amount = availableAmount;
                currentItems[index].warehouse_item_id = warehouseItemId;
                currentItems[index].sell_up = sellUp;
                currentItems[index].item_name = itemName;
                currentItems[index].unit_name = unitName;
                currentItems[index].category_id = categoryId;
            }
            
            // Trigger recalculation
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
        row.find('.amount').val(0).attr('max', 0);
        row.find('.sell-up').val('');
        row.find('.total').val(0);
        row.find('.max-label').text('{{__("sales.max")}}: 0');
        row.find('.availability-badge').attr('class', 'availability-badge').text('{{__("common.select_item")}}');
        
        var index = row.data('index');
        if (currentItems[index]) {
            currentItems[index].pre_list_id = '';
            currentItems[index].unit_id = '';
            currentItems[index].available_amount = 0;
            currentItems[index].warehouse_item_id = '';
            currentItems[index].sell_up = '';
            currentItems[index].item_name = '';
            currentItems[index].unit_name = '';
        }
        
        recalculateRow(row);
    }

    $(document).on('input change', '.sell-up', function() {
        var row = $(this).closest('tr');
        recalculateRow(row);
    });

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

         $('.item-row').each(function() 
         {
            var row = $(this);
            var preListId = row.find('.pre-list-id-hidden').val();
            var amount = row.find('.amount').val();
            var sellUp = row.find('.sell-up').val();
            var unitId = row.find('.unit-id-hidden').val();
            var warehouseItemId = row.find('.warehouse-item-id-hidden').val();
            var availableAmount = parseFloat(row.find('.amount').attr('max')) || 0;
            var enteredAmount = parseFloat(amount) || 0;

            // Skip validation for empty rows (new item not selected yet)
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
                    setTimeout(function() {
                        window.location.href = '{{ route("sales.index") }}';
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