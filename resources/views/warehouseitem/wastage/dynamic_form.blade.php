<div class="table-responsive">
    <table class="table table-bordered new" id="itemsTable">
  
            <tr style="background:#e9fffe">
                <th style="width:20%">انتخاب جنس</th>
                <th style="width:10%">مقدار ضایعات</th>
                <th style="width:10%">واحد</th>
                <th style="width:10%">اوسط خرید</th>
                <th style="width:15%">مجموع</th>
                <th style="width:15%">تاریخ انقضا</th>
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
                                data-expire-date="{{ $item->expire_date }}"
                                data-currency-id="{{ $item->currency_id }}"

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
                    <input name="currency_id[]" class="form-control currency-id" type="hidden" readonly required>  
                    <input name="unit_name[]" class="form-control unit-name" type="text" readonly required>  

                </td>
                <td><input name="avg_up[]" class="form-control avg-up" type="number" step="0.01" readonly required></td>
                <td><input name="total[]" class="form-control total" value="0" type="number" step="0.01" readonly required></td>
                <td><input name="expire_date[]" class="form-control expire_date" type="text" readonly required></td>
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

    function recalculate(row) {
        let amount = parseFloat(row.find('.amount').val()) || 0;
        let avgUp = parseFloat(row.find('.avg-up').val()) || 0;
        let total = amount * avgUp;
        row.find('.total').val(total.toFixed(2));
    }

    // When an item is selected
    $(document).on('change', '.item-select', function () {
        const selected = $(this).find(':selected');
        const row = $(this).closest('tr');

        row.find('.unit-name').val(selected.data('unit-name'));
        row.find('.unit-id').val(selected.data('unit-id'));
        row.find('.branch-id').val(selected.data('branch-id'));
        row.find('.warehouse-id').val(selected.data('warehouse-id'));
        row.find('.pre-list-id').val(selected.data('pre-list-id'));
        row.find('.avg-up').val(selected.data('avg-up'));
        row.find('.expire_date').val(selected.data('expire-date'));
        row.find('.currency-id').val(selected.data('currency-id'));
        

        recalculate(row);
    });

    // When amount changes
    $(document).on('input', '.amount', function () 
    {
        const row = $(this).closest('tr');
        const enteredAmount = parseFloat($(this).val()) || 0;
        const maxAvailable = parseFloat(row.find('.item-select option:selected').data('available-amount')) || 0;

        if (enteredAmount > maxAvailable) {
            alert("مقدار ضایعات نمی‌تواند بیشتر از موجودی انبار باشد (" + maxAvailable + ")");
            $(this).val(maxAvailable);
        } else if(enteredAmount < 0) 
        {
            alert('مقدار ضایعات بالاتراز صفر انتخاب گردد');
        }

        recalculate(row);
    });

    // Add new row
    $(document).on('click', '.addRow', function () {
        let newRow = $('#itemsTable .item-row:first').clone();

        newRow.find('input').val('');
        newRow.find('.item-select').val('').trigger('change');
        newRow.find('.select2-container').remove();
        newRow.find('.item-select').removeClass('select2-hidden-accessible').show();

        $('#itemsTable').append(newRow);
        newRow.find('.item-select').select2();
    });

    // Remove row
    $(document).on('click', '.removeRow', function () {
        let rows = $('#itemsTable .item-row');
        if (rows.length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert("باید حداقل یک سطر باقی بماند.");
        }
    });

    // Initialize Select2
    $('.item-select').select2();
});

</script>
