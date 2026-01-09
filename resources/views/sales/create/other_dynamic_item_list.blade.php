<div class="table-responsive">
    <table class="table table-bordered new" id="itemsTable">
  
            <tr style="background:#e9fffe">
                <th style="width:20%">{{__('wh.item_selection')}}</th>
                <th style="width:10%">{{__('common.amount')}}</th>
                <th style="width:10%">{{__('common.unit')}}</th>
                <th style="width:10%">{{__('common.currency')}}</th>
                <th style="width:10%"> {{__('wh.average')}}</th>
                <th style="width:10%">{{__('sales.sold_up')}}</th>
                <th style="width:12%">{{__('sales.profit')}}</th>
                <th style="width:15%">{{__('common.total')}}</th>
                <th style="width:15%">{{__('common.add')}}</th>
            </tr>
        
            <tr class="item-row">
                <td>
                    <select class="form-control select2 item-select" name="warehouseItemId[]" style="width: 100%;" required >
                        <option value="">{{__('wh.item_selection')}}</option>
                        @foreach($warehouseItems as $item)
                            <option value="{{ $item->id }}"
                                data-available-amount="{{ $item->available_amount }}"
                                data-unit-name="{{ $item->unit_name }}"
                                data-unit-id="{{ $item->unit_id }}"
                                data-avg-up="{{ $item->avg_up }}"
                                data-sell-up="{{ $item->sell_up }}"
                                data-branch-id="{{ $item->branch_id }}"
                                data-warehouse-id="{{ $item->warehouse_id }}"
                                data-pre-list-id="{{ $item->pre_list_id }}"
                                data-currency-id="{{ $item->currency_id }}"
                                >
                                {{ $item->item_name }} ({{ $item->available_amount }} {{ $item->unit_name }})
                                - {{ $item->warehouse_name }}
                                @if(session('package_type') == 4)
                                  / ( کد = {{ $item->code }}  ) 
                                @endif
                                                          
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input name="amount[]" class="form-control amount" type="number" step="0.01" placeholder="{{__('common.amount')}}" required></td>
                <td>
                    <input name="unit_id[]" class="form-control unit-id" type="hidden" readonly required>
                    <input name="main_currency_id[]" class="form-control main_currency_id" type="hidden2" readonly required>
                    <input name="branch_id[]" class="form-control branch-id" type="hidden" readonly required>
                    <input name="warehouse_id[]" class="form-control warehouse-id" type="hidden" readonly required>  
                    <input name="pre_list_id[]" class="form-control pre-list-id" type="hidden" readonly required>
                    <input name="unit_name[]" class="form-control unit-name" type="text" readonly required>  
                </td>


                <td>
                  <!-- <input name="discount[]" class="form-control discount" type="number" step="0.01"  value="0" > -->
                  <select class="form-control select2 currency-select" name="warehouseItemCurrencyId[]" style="width: 100%;" required >
                        <option value="">{{__('common.currency')}}</option>
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->id }}"  data-selected-currency="{{ $currency->id }}" >
                                {{ $currency->name }}                                                           
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input name="avg_up[]" class="form-control avg-up" type="number" step="0.01" required></td>
                <td><input name="sell_up[]" class="form-control sell-up" type="number" step="0.01" required></td>
                <td><input name="profit[]" class="form-control profit" type="number" step="0.01" readonly required></td>
                <td><input name="total[]" class="form-control total" value="0" type="number" step="0.01" readonly required></td>
                <td>
                    <button type="button" class="btn btn-info btn-sm addRow" style="padding: 2px 8px !important;">
                        <i class="fa fa-plus"></i>
                    </button>
                    <button type="button" class="btn btn-warning btn-sm removeRow" style="padding: 2px 8px !important;">
                       <i class="fa fa-minus"></i>
                    </button>
                </td>
            </tr>
    </table>
</div>




<script>
$(document).ready(function () {
    // Function to toggle required attribute when row is shown or hidden
    function toggleRequiredAttribute(row, isVisible) {
        row.find('.item-select, .amount, .sell-up').each(function () {
            if (isVisible) {
                $(this).attr('required', 'required');
            } else {
                $(this).removeAttr('required');
            }
        });
    }

    function recalculate(row) {
        var sellUp = parseFloat(row.find('.sell-up').val()) || 0;
        var avgUp = parseFloat(row.find('.avg-up').val()) || 0;
        var enteredAmount = parseFloat(row.find('.amount').val()) || 1;
        var discount = parseFloat(row.find('.discount').val()) || 0;

        // Ensure the amount is valid
        if (enteredAmount <= -1) {
            row.find('.amount').val(0);
            enteredAmount = 0;
        }

        // Calculate total price before discount
        var total_result = sellUp * enteredAmount;
        row.find('.total').val(total_result.toFixed(2));

        // Calculate profit based on average cost
        var profit = avgUp * enteredAmount;
        var net_profit = total_result - profit - discount;  // Subtract the discount from the net profit
        if(parseFloat(net_profit) > 0)
        {
             row.find('.profit').val(net_profit.toFixed(2));
        }
        else
        {
            row.find('.profit').val(0);
        }
        

        updateTotalPrice();
        updateGeneralDiscount();
    }

    function updateTotalPrice() {
        var totalPrice = 0;
        $('.total').each(function () {
            totalPrice += parseFloat($(this).val()) || 0;
        });
        $('#total_price').val(totalPrice.toFixed(2));
        updatePayableAmount();
    }

    function updateGeneralDiscount() {
        var totalDiscount = 0;
        $('.discount').each(function () {
            totalDiscount += parseFloat($(this).val()) || 0;
        });
        $('#total_discount').val(totalDiscount.toFixed(2));

        var total_price = parseFloat($('#total_price').val()) || 0;
        var payable = total_price - totalDiscount;
        $('#payable').val(payable.toFixed(2));
        updatePayableAmount();
    }

    function updatePayableAmount() {
        var totalPrice = parseFloat($('#total_price').val()) || 0;
        var totalDiscount = parseFloat($('#total_discount').val()) || 0;
        var payable = totalPrice - totalDiscount;
        $('#payable').val(payable.toFixed(2));
    }

    // Handle item select change
    $(document).on('change', '.item-select', function () {
        var selectedOption = $(this).find(':selected');
        var unitName = selectedOption.data('unit-name');
        var unitId = selectedOption.data('unit-id');
        var branchId = selectedOption.data('branch-id');
        var warehouseId = selectedOption.data('warehouse-id');
        var preListId = selectedOption.data('pre-list-id');
        var avgUp = selectedOption.data('avg-up');
        var sellUp = selectedOption.data('sell-up');
        var currency_id = selectedOption.data('currency-id');
        var availableAmount = selectedOption.data('available-amount');
        var row = $(this).closest('tr');

        // put currency_id at the top of select 

        row.find('.unit-name').val(unitName);
        row.find('.unit-id').val(unitId);
        row.find('.branch-id').val(branchId);
        row.find('.warehouse-id').val(warehouseId);
        row.find('.pre-list-id').val(preListId);
        row.find('.avg-up').val(avgUp);
        row.find('.sell-up').val(sellUp);
        row.find('.amount').data('max', availableAmount);

         // ✅ FIX: select correct currency automatically
        if (currency_id) {
            row.find('.currency-select')
            .val(currency_id)
            .trigger('change');
            $('#main_currency_id').val(currency_id);
        }
        else
        {
            row.find('.currency-select')
            .val('')
            .trigger('change'); 
            $('.main_currency_id').val('');
        }

    });

    // Handle input changes for recalculations
    $(document).on('input', '.amount, .sell-up, .discount', function () {
        var row = $(this).closest('tr');
        recalculate(row);
    });

    // Add new row
    $(document).on('click', '.addRow', function () {
        var newRow = $('#itemsTable tbody .item-row:first').clone();

        // Reset input values
        newRow.find('input').val('');
        newRow.find('.item-select').val('').trigger('change');
        newRow.find('.select2-container').remove();
        newRow.find('.item-select').removeClass('select2-hidden-accessible').show();
        $('#itemsTable tbody').append(newRow);

        newRow.find('.item-select').select2();

        // Add required to new row's inputs
        toggleRequiredAttribute(newRow, true);
    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        var rows = $('#itemsTable tbody tr.item-row');
        
        // Ensure there is more than one row and prevent deleting the first row
        if (rows.length > 1) {
            var row = $(this).closest('tr');
            // Prevent deletion of the first row
            if (row.index() !== 0) {
                toggleRequiredAttribute(row, false); // Remove required from the removed row
                row.remove();
                updateTotalPrice();
                updateGeneralDiscount();
            } else {
                alert("You must have at least one row.");
            }
        } else {
            alert("You must have at least one row.");
        }
    });

    // Initialize select2 on page load
    $('.item-select').select2();
});


</script>
