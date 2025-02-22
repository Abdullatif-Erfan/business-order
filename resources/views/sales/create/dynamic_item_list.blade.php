<div class="table-responsive">
    <table class="table table-bordered new" id="itemsTable">
  
            <tr style="background:#e9fffe">
                <th style="width:22%">انتخاب جنس</th>
                <th style="width:10%">تعداد</th>
                <th style="width:10%">واحد</th>
                <th style="width:10%">اوسط خرید</th>
                <th style="width:10%">فیات فروش</th>
                <th style="width:10%">تخفیف</th>
                <th style="width:10%">مفاد</th>
                <th style="width:10%">مجموع</th>
                <th style="width:15%">علاوه</th>
            </tr>
        
            <tr class="item-row">
                <td>
                    <select class="form-control select2 item-select" name="warehouseItemId[]" style="width: 100%;" required >
                        <option value="">انتخاب جنس</option>
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
                                >
                                {{ $item->item_name }} ({{ $item->available_amount }} {{ $item->unit_name }})
                                - {{ $item->warehouse_name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td><input name="amount[]" class="form-control amount" type="number" step="0.01" placeholder="تعداد" required></td>
                <td>
                    <input name="unit_id[]" class="form-control unit-id" type="hidden" readonly required>
                    <input name="branch_id[]" class="form-control branch-id" type="hidden" readonly required>
                    <input name="warehouse_id[]" class="form-control warehouse-id" type="hidden" readonly required>  
                    <input name="pre_list_id[]" class="form-control pre-list-id" type="hidden" readonly required>
                    <input name="unit_name[]" class="form-control unit-name" type="text" readonly required>  

                </td>
                <td><input name="avg_up[]" class="form-control avg-up" type="number" step="0.01" readonly required></td>
                <td><input name="sell_up[]" class="form-control sell-up" type="number" required></td>
                <td><input name="discount[]" class="form-control discount" type="number"  value="0" ></td>
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
$(document).ready(function () 
{
     // Function to toggle required attribute when row is shown or hidden
     function toggleRequiredAttribute(row, isVisible) {
        row.find('.item-select, .amount, .sell-up').each(function () {
            if (isVisible) {
                // Add required attribute
                $(this).attr('required', 'required');
            } else {
                // Remove required attribute
                $(this).removeAttr('required');
            }
        });
    }


    function recalculate(row) 
    {
        var sellUp = parseFloat(row.find('.sell-up').val()) || 0;
        var avgUp = parseFloat(row.find('.avg-up').val()) || 0;
        var enteredAmount = parseFloat(row.find('.amount').val()) || 1;
        var discount = parseFloat(row.find('.discount').val()) || 0;
        var maxAmount = row.find('.amount').data('max') || 1;

        if (enteredAmount < 1) {
            row.find('.amount').val(1);
            enteredAmount = 1;
        } else if (enteredAmount > maxAmount) {
            row.find('.amount').val(maxAmount);
            enteredAmount = maxAmount;
        }

        var total_result = sellUp * enteredAmount;
        row.find('.total').val(total_result.toFixed(2));

        var profit = avgUp * enteredAmount;
        var net_profit = total_result - profit;
        row.find('.profit').val(net_profit.toFixed(2));

        updateTotalPrice(); // Call function to update total price
        updateGeneralDiscount();
    }

    function updateTotalPrice() 
    {
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
        var availableAmount = selectedOption.data('available-amount');

        var row = $(this).closest('tr');
        row.find('.unit-name').val(unitName);
        row.find('.unit-id').val(unitId);
        row.find('.branch-id').val(branchId);
        row.find('.warehouse-id').val(warehouseId);
        row.find('.pre-list-id').val(preListId);
        row.find('.avg-up').val(avgUp);
        row.find('.sell-up').val(sellUp);
        row.find('.amount').data('max', availableAmount);


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

        // Remove old select2 instance and reinitialize
        newRow.find('.select2-container').remove();
        newRow.find('.item-select').removeClass('select2-hidden-accessible').show();

        // Append new row
        $('#itemsTable tbody').append(newRow);

        // Reinitialize select2
        newRow.find('.item-select').select2();

        
         // Add required to new row's inputs
         toggleRequiredAttribute(newRow, true);
    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        if ($('#itemsTable tbody tr').length > 1) {
            // $(this).closest('tr').remove();
            var row = $(this).closest('tr');
            toggleRequiredAttribute(row, false); // Remove required from the removed row
            row.remove();
        } else {
            alert("You must have at least one row.");
        }
    });

    // Initialize select2 on page load
    $('.item-select').select2();
});

</script>
