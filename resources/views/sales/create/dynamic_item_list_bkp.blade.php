<div class="table-responsive">
    <table class="table table-bordered">
        <tr style="background:#e9fffe">
            <th style="width:25%">انتخاب جنس
                <span id="loader" style="display:none;">
                    <img src="{{ asset('assets/img/loader.gif') }} " style="width:35px;margin:10px;" alt="Loading"/>
                </span>
            </th>
            <th style="width:10%">تعداد</th>
            <th style="width:10%">واحد</th>
            <th style="width:10%;">اوسط خرید</th>
            <th style="width:10%">فیات فروش</th>
            <th style="width:10%">تخفیف</th>
            <th style="width:10%">مفاد</th>
            <th style="width:10%">مجموع</th>
            <th style="width:10%">علاوه</th>
        </tr>
        <tr>
            <td>
                <select class="form-control select2 item-select" style="width: 100%; border:none !important; background-color:#ddd;">
                    <option value="">انتخاب جنس</option>
                    @foreach($warehouseItems as $item)
                        <option value="{{ $item->id }}"
                            data-available-amount="{{ $item->available_amount }}"
                            data-unit-id="{{ $item->unit_id }}"
                            data-unit-name="{{ $item->unit_name }}"
                            data-avg-up="{{ $item->avg_up }}"
                            data-sell-up="{{ $item->sell_up }}">
                            {{$item->item_name}} ({{ $item->available_amount }} {{ $item->unit_name }}) - {{ $item->warehouse_name }}
                        </option>
                    @endforeach
                </select>
            </td>
            
            <td style="width:10%">
                <input class="form-control amount" type="number"  step="0.01" placeholder="تعداد">
            </td>

            <td>
                <input class="form-control unit-name" type="text" readonly>
            </td>

            <td>
                <input class="form-control avg-up" type="number" step="0.01" readonly>
            </td>

            <td>
                <input class="form-control sell-up" type="number" step="0.01">
            </td>

            <td>
                <input class="form-control discount" type="number" step="0.01" value="0">
            </td>

            <td>
                <input class="form-control profit"  type="number" step="0.01" readonly>
            </td>
            
            <td>
                <input class="form-control total" value="0" type="number" step="0.01" readonly>
            </td>

            <td>
                <div style="display:inline">
                    <button type="button" class="btn btn-info btn-sm addBtn" style="padding: 2px 8px !important;">
                        <i class="fa fa-plus"></i>
                    </button>

                    <button type="button" class="btn btn-warning btn-sm removeBtn" style="padding: 2px 8px !important;">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </td>
        </tr>
    </table>
</div>


<script>
$(document).ready(function () {
    $('.item-select').on('change', function () {
        var selectedOption = $(this).find(':selected');

        var unitName = selectedOption.data('unit-name');
        var avgUp = selectedOption.data('avg-up');
        var sellUp = selectedOption.data('sell-up');
        var availableAmount = selectedOption.data('available-amount'); 
        
        var row = $(this).closest('tr');

        row.find('.unit-name').val(unitName);
        row.find('.avg-up').val(avgUp);
        row.find('.sell-up').val(sellUp);

        row.find('.amount').data('max', availableAmount);
    });

    function recalculate(row) {
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
        // row.find('.total_price').val(total_result.toFixed(2));

        // ---- calculate total_price ---------------
        $('#total_price').val(total_result.toFixed(2));

        // --------- calculate discount ----------------
        // var old_general_discount = parseFloat($('#general_discount').val());
        // $('#general_discount').val(old_general_discount + discount);
        updateGeneralDiscount();

    }

    function updateGeneralDiscount() {
        var totalDiscount = 0;
        $('.discount').each(function () {
            totalDiscount += parseFloat($(this).val()) || 0;
        });
        $('#general_discount').val(totalDiscount.toFixed(2));

       var total_price =  $('#total_price').val();
       var payable = total_price - totalDiscount;
       $('#payable').val(payable.toFixed(2));

    }


    $('.amount, .sell-up, .discount').on('input', function () {
        var row = $(this).closest('tr');
        recalculate(row);
    });
});

</script>
