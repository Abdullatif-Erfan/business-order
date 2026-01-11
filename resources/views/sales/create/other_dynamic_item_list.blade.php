<input type="hidden" name="default_currency" value="{{ $default_currency->id }}" />
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
                    <input name="main_currency_id[]" class="form-control main-currency-id" type="hidden" readonly required>
                    <input name="branch_id[]" class="form-control branch-id" type="hidden" readonly required>
                    <input name="warehouse_id[]" class="form-control warehouse-id" type="hidden" readonly required>  
                    <input name="pre_list_id[]" class="form-control pre-list-id" type="hidden" readonly required>
                    <input name="unit_name[]" class="form-control unit-name" type="text" readonly required>  

                    <!-- first values -->
                    <input name="def_avg_up[]" class="form-control def-avg-up" type="hidden" >
                    <input name="def_sell_up[]" class="form-control def-sell-up" type="hidden" >
                    <input name="def_profit[]" class="form-control def-profit" type="hidden"  >
                    <input name="def_total[]" class="form-control def-total" value="0" type="hidden">
                    <input name="def_total_price[]" class="form-control def-total-price" value="0" type="hidden">
                    <input name="def_payable[]" class="form-control def-payable" value="0" type="hidden">

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
                <td><input name="profit[]" class="form-control profit" type="number" step="0.01"  required></td>
                <td><input name="total[]" class="form-control total" value="0" type="number" step="0.01"  required></td>
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
        

        // -------- set defult currency value for re-select default currency on currencyConverter function ---------
        row.find('.def-avg-up').val(avgUp);
        row.find('.def-sell-up').val(sellUp);
        row.find('.def-profit').val(profit);
        row.find('.def-total').val(total_result);
        // row.find('.def-total-price').val(total_result);
        // row.find('.def-payable').val(total_result);


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

        let currency_id = selectedOption.data('currency-id');

        var availableAmount = selectedOption.data('available-amount');
        var mainCurrencyId = selectedOption.data('main-currency-id');
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
            row.find('.main-currency-id').val(currency_id);
        }
        else
        {
            row.find('.currency-select')
            .val('')
            .trigger('change'); 
            row.find('.main-currency-id').val('');
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

        newRow.find('.item-select, .currency-select').select2();

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

<script>
 // Handle foreign currency 
 $(document).on('change', '.currency-select', function () {

let row = $(this).closest('tr');
let selectedCurrencyId = $(this).val();
let mainCurrencyId = row.find('.main-currency-id').val();
let amount = parseFloat(row.find('.amount').val()) || 0;

    // console.log('Selected:', selectedCurrencyId);
    // console.log('Main:', mainCurrencyId);

    if (amount > 0 && selectedCurrencyId && mainCurrencyId) 
    {

        // 🔒 prevent double conversion
        if (row.data('converting')) return;
        row.data('converting', true);

        if (selectedCurrencyId !== mainCurrencyId) {
            // alert('convert should be done');
            let avgUp = parseFloat(row.find('.def-avg-up').val()) || 0;
            let sellUp = parseFloat(row.find('.def-sell-up').val()) || 0;
            let profit = parseFloat(row.find('.def-profit').val()) || 0;
            let total = parseFloat(row.find('.def-total').val()) || 0;
        

            // console.log('avg_up', avgUp);
            // console.log('sellUp', sellUp);
            // console.log('profit', profit);
            // console.log('total', total);

            convertCurrency(selectedCurrencyId, mainCurrencyId, row.find('.def-avg-up').val(), function (v) {
            row.find('.avg-up').val(v);
            });

            convertCurrency(selectedCurrencyId, mainCurrencyId, row.find('.def-sell-up').val(), function (v) {
                row.find('.sell-up').val(v);
            });

            convertCurrency(selectedCurrencyId, mainCurrencyId, row.find('.def-profit').val(), function (v) {
                row.find('.profit').val(v);
            });

            convertCurrency(selectedCurrencyId, mainCurrencyId, row.find('.def-total').val(), function (v) {
                row.find('.total').val(v);
                let yet_total_price = $('#total_price').val();
                let total_price_result = parseFloat(v) + parseFloat(yet_total_price);
                $('#total_price').val(total_price_result);
                // $('#payable').val(v);

                // 🔓 release lock AFTER last conversion
                row.data('converting', false);
            });
        } 
        else 
        {
            // 🔄 restore base values
            row.find('.avg-up').val(row.find('.def-avg-up').val());
            row.find('.sell-up').val(row.find('.def-sell-up').val());
            row.find('.profit').val(row.find('.def-profit').val());
            row.find('.total').val(row.find('.def-total').val());
            // $('#total_price').val(row.find('.def-total-price').val());
            // $('#payable').val(row.find('.def-payable').val());

            row.data('converting', false);
        }
    }
});

function convertCurrency(to_currency, from_currency, amount, callback)
    {
        let newRate = parseFloat($('#newRate').val()) || 0;

        if (!from_currency || !to_currency || !amount) return;

        if (from_currency === to_currency) {
            callback(amount); // no conversion needed
            return;
        }

        $('#conversion_flag').val(1);
        $('#newRate').fadeIn(1);

        $.ajax({
            url: '/home/currencyConverter',
            type: 'POST',
            data: {
                from_currency: from_currency,
                to_currency: to_currency,
                fromAmount: amount,
                newRate: newRate,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function (result) {

                if (result.convertedAmount !== undefined) {
                    let converted = parseFloat(result.convertedAmount).toFixed(2);

                    // ✅ RETURN RESULT TO CALLER
                    if (typeof callback === 'function') {
                        callback(converted);
                    }

                } else {
                    alert('Invalid conversion response');
                }
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error:", error);
                alert('Currency conversion failed');
            }
        });
    }

    function number_format(num, decimals = 2) {
       return num.toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,'); 
    }

</script>
