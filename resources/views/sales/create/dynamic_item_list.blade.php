<style>
    /* ===== TABLE WRAPPER ===== */
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
        -webkit-overflow-scrolling: touch; /* Smooth scroll on iOS */
    }
    
    /* ===== TABLE STYLING ===== */
    .table-scroll-wrapper table {
        margin-bottom: 0;
        width: 100%;
        min-width: 700px; /* Ensures horizontal scroll on small screens */
        border-collapse: separate;
        border-spacing: 0;
    }
    
    /* ===== STICKY HEADER ===== */
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
    
    /* ===== TABLE CELLS ===== */
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
    
    /* ===== COLUMN WIDTHS ===== */
    .table-scroll-wrapper .col-item { min-width: 150px; width: 25%; }
    .table-scroll-wrapper .col-amount { min-width: 80px; width: 12%; }
    .table-scroll-wrapper .col-buy-up { min-width: 80px; width: 12%; }
    .table-scroll-wrapper .col-sell-up { min-width: 80px; width: 12%; }
    .table-scroll-wrapper .col-profit { min-width: 80px; width: 12%; }
    .table-scroll-wrapper .col-total { min-width: 80px; width: 12%; }
    .table-scroll-wrapper .col-actions { min-width: 70px; width: 15%; }
    
    /* ===== BUTTONS ===== */
    .table-scroll-wrapper .btn-sm {
        padding: 2px 6px;
        font-size: 12px;
        margin: 0 2px;
    }
    
    .table-scroll-wrapper .btn-sm i {
        font-size: 12px;
    }
    
    /* ===== MOBILE RESPONSIVE ===== */
    @media (max-width: 992px) {
        .table-scroll-wrapper .table-responsive-scroll {
            max-height: 350px;
        }
        .table-scroll-wrapper table {
            min-width: 650px;
        }
        .table-scroll-wrapper thead th {
            font-size: 12px;
            padding: 6px 8px;
        }
        .table-scroll-wrapper tbody td {
            padding: 4px 6px;
        }
        .table-scroll-wrapper tbody td .form-control {
            font-size: 12px;
            padding: 3px 4px;
            height: 28px;
            min-width: 50px;
        }
        .table-scroll-wrapper .btn-sm {
            padding: 2px 4px;
            font-size: 11px;
        }
        .table-scroll-wrapper .btn-sm i {
            font-size: 11px;
        }
        .table-scroll-wrapper .col-item { min-width: 120px; }
        .table-scroll-wrapper .col-amount { min-width: 60px; }
        .table-scroll-wrapper .col-buy-up { min-width: 60px; }
        .table-scroll-wrapper .col-sell-up { min-width: 60px; }
        .table-scroll-wrapper .col-profit { min-width: 60px; }
        .table-scroll-wrapper .col-total { min-width: 60px; }
        .table-scroll-wrapper .col-actions { min-width: 60px; }
    }
    
    @media (max-width: 768px) {
        .table-scroll-wrapper .table-responsive-scroll {
            max-height: 280px;
        }
        .table-scroll-wrapper table {
            min-width: 550px;
            font-size: 12px;
        }
        .table-scroll-wrapper thead th {
            font-size: 11px;
            padding: 4px 5px;
        }
        .table-scroll-wrapper tbody td {
            padding: 3px 4px;
        }
        .table-scroll-wrapper tbody td .form-control {
            font-size: 11px;
            padding: 2px 3px;
            height: 24px;
            min-width: 40px;
        }
        .table-scroll-wrapper .btn-sm {
            padding: 1px 3px;
            font-size: 10px;
        }
        .table-scroll-wrapper .btn-sm i {
            font-size: 10px;
        }
        .table-scroll-wrapper .col-item { min-width: 100px; }
        .table-scroll-wrapper .col-amount { min-width: 50px; }
        .table-scroll-wrapper .col-buy-up { min-width: 50px; }
        .table-scroll-wrapper .col-sell-up { min-width: 50px; }
        .table-scroll-wrapper .col-profit { min-width: 50px; }
        .table-scroll-wrapper .col-total { min-width: 50px; }
        .table-scroll-wrapper .col-actions { min-width: 50px; }
    }
    
    @media (max-width: 576px) {
        .table-scroll-wrapper .table-responsive-scroll {
            max-height: 200px;
        }
        .table-scroll-wrapper table {
            min-width: 550px;
            font-size: 11px;
        }
        .table-scroll-wrapper thead th {
            font-size: 10px;
            padding: 3px 4px;
        }
        .table-scroll-wrapper tbody td {
            padding: 2px 3px;
        }
        .table-scroll-wrapper tbody td .form-control {
            font-size: 10px;
            padding: 1px 2px;
            height: 22px;
            min-width: 35px;
        }
        .table-scroll-wrapper .btn-sm {
            padding: 1px 2px;
            font-size: 9px;
        }
        .table-scroll-wrapper .btn-sm i {
            font-size: 9px;
        }
        .table-scroll-wrapper .col-item { min-width: 80px; }
        .table-scroll-wrapper .col-amount { min-width: 40px; }
        .table-scroll-wrapper .col-buy-up { min-width: 40px; }
        .table-scroll-wrapper .col-sell-up { min-width: 40px; }
        .table-scroll-wrapper .col-profit { min-width: 40px; }
        .table-scroll-wrapper .col-total { min-width: 40px; }
        .table-scroll-wrapper .col-actions { min-width: 40px; }
    }
    
    /* ===== SCROLLBAR STYLING ===== */
    .table-scroll-wrapper .table-responsive-scroll::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    .table-scroll-wrapper .table-responsive-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .table-scroll-wrapper .table-responsive-scroll::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }
    
    .table-scroll-wrapper .table-responsive-scroll::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* ===== TAX PERCENTAGE DISPLAY ===== */
    .tax-display {
        display: inline-block;
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        color: #555;
        margin-top: 2px;
    }
</style>

<div class="table-scroll-wrapper">
    <div class="table-responsive-scroll">
        <table class="table table-bordered new" id="itemsTable">
  
            <tr style="background:#e9fffe">
                <th style="width:25%">{{__('wh.item_selection')}}</th>
                <th style="width:15%">{{__('common.amount')}}</th>
                <th style="width:15%"> {{__('buy.buy_up')}}</th>
                <th style="width:15%">{{__('sales.sold_up')}}</th>
                <th style="width:12%">{{__('sales.profit')}}</th>
                <th style="width:15%">{{__('common.total')}}</th>
                <th style="width:10%">{{__('common.add')}}</th>
            </tr>
        
            <tr class="item-row">
                <td>
                    <select class="form-control select2 item-select" name="warehouseItemId[]" style="width: 100%;" required >
                        <option value="">{{__('wh.item_selection')}}</option>
                        @foreach($warehouseItems as $item)
                            <option value="{{ $item->id }}"
                                data-available-amount="{{ $item->available_amount }}"
                                data-unit-id="{{ $item->unit_id }}"
                                data-unit-name="{{ $item->unit_name }}"
                                data-buy-up="{{ $item->buy_up }}"
                                data-buy-tax-per="{{ $item->buy_tax_per }}"
                                data-sell-tax-per="{{ $item->sell_tax_per }}"
                                data-sell-tax-price="{{ $item->sell_tax_price }}"
                                data-sell-up="{{ $item->sell_up }}"
                                data-warehouse-id="{{ $item->warehouse_id }}"
                                data-pre-list-id="{{ $item->pre_list_id }}"
                                data-category-id="{{ $item->category_id }}"
                                >
                                {{ $item->item_name }} ({{ $item->available_amount }} {{ $item->unit_name }})               
                            </option>
                        @endforeach
                    </select>
                </td>

                <!-- amount -->
                <td>
                    <input name="amount[]" class="form-control amount" type="number" step="0.01" placeholder="{{__('common.amount')}}" required>
                     <input name="unit_name[]" class="form-control unit-name" type="text" readonly>

                    <!-- hidden fields -->
                    <input name="unit_id[]" class="form-control unit-id" type="hidden" readonly required>
                    <input name="warehouse_id[]" class="form-control warehouse-id" type="hidden" readonly required>  
                    <input name="pre_list_id[]" class="form-control pre-list-id" type="hidden" readonly required>
                    <input name="category_id[]" class="form-control category-id" type="hidden" readonly required>
                </td>

                <!-- buy_up -->
                <td>
                    <input name="buy_up[]" class="form-control buy-up" type="number" step="0.01" required>
                    <input class="form-control buy-tax-per" type="text" readonly>
                </td> 
                
                <!-- sell_up -->
                <td>
                    <input name="sell_up[]" class="form-control sell-up" type="number" step="0.01" required>
                    <input class="form-control sell-tax-per-symbol" type="text" readonly>
                    <input class="form-control sell-tax-per" type="hidden" name="sell_tax_per[]" step="0.01">
                    <input class="form-control sell-tax-price" type="hidden"  name="sell_tax_price[]" step="0.01">
                </td>

                <td><input name="profit[]" class="form-control profit" type="number" step="0.01" readonly required></td>
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
        var buyUp = parseFloat(row.find('.buy-up').val()) || 0;
        var enteredAmount = parseFloat(row.find('.amount').val()) || 1;

        // Ensure the amount is valid
        if (enteredAmount <= -1) {
            row.find('.amount').val(0);
            enteredAmount = 0;
        }

        // Calculate total price 
        var total_sell = sellUp * enteredAmount;
        row.find('.total').val(total_sell.toFixed(2));

        // Calculate profit based on average cost
        var total_buy = buyUp * enteredAmount;
        var net_profit = total_sell - total_buy;  
        if(parseFloat(net_profit) > 0)
        {
             row.find('.profit').val(net_profit.toFixed(2));
        }
        else
        {
            row.find('.profit').val(0);
        }
        

        updateTotalPrice();
    }

    function updateTotalPrice() {
        var totalPrice = 0;
        $('.total').each(function () {
            totalPrice += parseFloat($(this).val()) || 0;
        });
        $('#total_price').val(totalPrice.toFixed(2));
        $('#remained').val(totalPrice.toFixed(2));
        // updatePayableAmount();
    }

    // function updateGeneralDiscount() {
    //     var totalDiscount = 0;
    //     $('.discount').each(function () {
    //         totalDiscount += parseFloat($(this).val()) || 0;
    //     });
    //     $('#total_discount').val(totalDiscount.toFixed(2));

    //     var total_price = parseFloat($('#total_price').val()) || 0;
    //     var payable = total_price - totalDiscount;
    //     $('#payable').val(payable.toFixed(2));
    //     updatePayableAmount();
    // }

    // function updatePayableAmount() {
    //     var totalPrice = parseFloat($('#total_price').val()) || 0;
    //     var totalDiscount = parseFloat($('#total_discount').val()) || 0;
    //     var payable = totalPrice - totalDiscount;
    //     $('#payable').val(payable.toFixed(2));
    // }

    // Handle item select change
    $(document).on('change', '.item-select', function () {
        var selectedOption = $(this).find(':selected');
        var unitId = selectedOption.data('unit-id');
        var unitName = selectedOption.data('unit-name');
        var warehouseId = selectedOption.data('warehouse-id');
        var preListId = selectedOption.data('pre-list-id');
        var categoryId = selectedOption.data('category-id');

        var buyTaxPer = selectedOption.data('buy-tax-per');
        var sellTaxPer = selectedOption.data('sell-tax-per');
        var sellTaxPrice = selectedOption.data('sell-tax-price');

        var buyUp = selectedOption.data('buy-up');
        var sellUp = selectedOption.data('sell-up');
        var availableAmount = selectedOption.data('available-amount');
        var row = $(this).closest('tr');
        row.find('.unit-id').val(unitId);
        row.find('.unit-name').val(unitName);
        row.find('.warehouse-id').val(warehouseId);
        // row.find('.buy-tax-per').val(buyTaxPer);
        // row.find('.sell-tax-per').val(sellTaxPer);

        row.find('.buy-tax-per').val(" % " + buyTaxPer);
        row.find('.sell-tax-per-symbol').val(" % " + sellTaxPer);
        row.find('.sell-tax-per').val(sellTaxPer);
        row.find('.sell-tax-price').val(sellTaxPrice);
         
        // row.find('.buy-tax-per-label').text(buyTaxPer + '%');
        // row.find('.sell-tax-per-label').text(sellTaxPer + '%');
        
        row.find('.pre-list-id').val(preListId);
        row.find('.category-id').val(categoryId);
        row.find('.buy-up').val(buyUp);
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
