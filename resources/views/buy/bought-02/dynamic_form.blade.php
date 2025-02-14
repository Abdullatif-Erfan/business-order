<div class="table-responsive">
    <table class="table table-bordered new">
        <thead>
            <tr>
                <th style="width:5%"> شماره </th>                                    
                <th style="width:10%"> نوع خریداری </th>
                <th style="width:10%">تعداد خرید </th>
                <th style="width:10%">واحد</th>
                <th style="width:10%"> قیمت فی واحد </th>
                <th style="width:10%"> قیمت مجموعی</th>
                <th style="width:10%">تاریخ انقضاه</th>
                <th style="width:10%">علاوه / حذف</th>
            </tr>
        </thead>
        <tbody id="rowsContainer">
            <tr id="row1">
                <td>1</td>
                <td>
                    <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="pre_list_id[]" required>
                        <option value="0">انتخاب جنس</option>
                        @foreach($preLists as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input class="form-control" name="amount[]" id="amount" type="number" oninput="recalculateEachTotal(this)" step="0.01" required>
                </td>
                <td>
                    <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="unit_id[]" required>
                        <option value="">واحد</option>
                        @foreach($units as $unitItem)
                            <option value="{{ $unitItem->id }}">{{ $unitItem->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input class="form-control" name="bought_up[]" type="number" step="0.01"  oninput="recalculateEachTotal(this)" required>
                </td>
                <td>
                    <input class="form-control" name="total[]" id="total" type="number" step="0.01" required>
                </td>
                <td>
                    <input type="date" class="form-control" name="expire_date[]" placeholder="تاریخ ختم" >
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-info addMoreBtn" onclick="addRow()">
                        <i class="fa fa-plus"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- --------------------- Dynamic Form ----------------------------- -->

<script>
    let rowCount = 1; // Keep track of row numbers for ID purposes.

    // Function to add a new row
    function addRow() {
    rowCount++;
    const newRow = `
        <tr id="row${rowCount}">
            <td>${rowCount}</td>
            <td>
                <select class="form-control select2" name="pre_list_id[]" required>
                    <option value="">انتخاب مواد خریداری</option>
                    @foreach($preLists as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input class="form-control" name="amount[]" type="number" step="0.01" required></td>
            <td>
                <select class="form-control select2" name="unit_id[]" required>
                    <option value="">واحد</option>
                    @foreach($units as $unitItem)
                        <option value="{{ $unitItem->id }}">{{ $unitItem->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input class="form-control" name="bought_up[]" type="number" step="0.01" oninput="findTotal(this)" required></td>
            <td><input class="form-control" name="total[]" type="number" step="0.01" required></td>
            <td>
               <input type="date" class="form-control" name="expire_date[]" required>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-info addMoreBtn" onclick="addRow()">
                    <i class="fa fa-plus"></i>
                </button>
                <button type="button" class="btn btn-sm btn-warning removeBtn" onclick="removeRow(this)">
                    <i class="fa fa-minus"></i>
                </button>
            </td>
        </tr>
    `;

    $('#rowsContainer').append(newRow);

    // Reinitialize select2 for new elements
    $('#rowsContainer .select2').select2();

    // Ensure required fields are enforced
    updateRequiredAttributes();
}





    // Function to remove a row
    function removeRow(button) {
        const row = button.closest('tr');
        row.remove(); // Remove the clicked row
        updateRequiredAttributes(); // Update required attributes after removing a row
    }


// Function to update required attributes
function updateRequiredAttributes() {
    const rows = $('#rowsContainer tr');

    rows.each(function (index) {
        // Apply required only to select and input fields except input[type="date"]
        $(this).find('select, input').attr('required', 'required');
        $(this).find('input[type="date"]').removeAttr('required');

        // If only one row remains, keep required on the first row
        if (rows.length === 1) {
            $(this).find('select, input').attr('required', 'required');
            $(this).find('input[type="date"]').removeAttr('required'); // Keep date optional
        }
    });
}

// Call the function once at the beginning to ensure the first row doesn't require fields initially
$(document).ready(function () {
    updateRequiredAttributes();
});


// Function to calculate the total
function findTotal(up) {
    const row = $(up).closest('tr');
    var amountInput = row.find('input[name="amount[]"]');
    var boughtUpInput = row.find('input[name="bought_up[]"]'); // Ensure you have a separate name for the field
    
    var amount = parseFloat(amountInput.val());
    var bought_up = parseFloat(boughtUpInput.val());

    // console.log('Amount:', amount);
    // console.log('Bought Up:', bought_up);

    // Only calculate if both fields have valid numbers
    if (!isNaN(amount) && !isNaN(bought_up) && bought_up > 0 && amount > 0) {
        var result = amount * bought_up;
        row.find('input[name="total[]"]').val(result.toFixed(2));

        // Update total sum
        recalculateTotal();
    } else {
        // If any field is empty or invalid, clear the total field
        row.find('input[name="total[]"]').val('');
    }
}



function recalculateEachTotal(up) {
    // Find the closest row to the 'bought_up' input field
    const row = $(up).closest('tr');

    // Get values from 'amount[]' and 'bought_up[]'
    var amount = row.find('input[name="amount[]"]').val();
    var bought_up = row.find('input[name="bought_up[]"]').val();

    // Ensure that amount and bought_up are numbers and greater than 0
    var amountFloat = parseFloat(amount) || 0;  // If empty or invalid, set to 0
    var boughtUpFloat = parseFloat(bought_up) || 0;  // If empty or invalid, set to 0

    // Perform calculation only if both fields have valid values (greater than 0)
    if (amountFloat > 0 && boughtUpFloat > 0) {
        // Calculate total (multiply amount and bought_up)
        var result = amountFloat * boughtUpFloat;
        row.find('input[name="total[]"]').val(result.toFixed(2));  // Set the total value
    } else {
        // If any field is empty or invalid, clear the total field
        row.find('input[name="total[]"]').val('');
    }
}

// Automatically recalculate when amount or price changes
$(document).on('input', 'input[name="amount[]"], input[name="bought_up[]"]', function () {
    recalculateEachTotal(this);
});



    function recalculateTotal() {
        let totalSum = 0;

        $('input[name="total[]"]').each(function () {
            totalSum += parseFloat($(this).val()) || 0;
        });

        $('#total_price').val(totalSum.toFixed(2));

        // Also update payable after discount
        var discount = parseFloat($('#discount').val()) || 0;
        $('#payable').val((totalSum - discount).toFixed(2));
    }

    // Update total while entering discount
    function updateWhileEnteringDiscount(discount) {
        var total_price = parseFloat($('#total_price').val()) || 0;
        $('#payable').val((total_price - discount).toFixed(2));
    }

    // Update remaining amount based on current payment
    function updateWhileEnteringCurPay(curpay) {
        var payable = parseFloat($('#payable').val()) || 0;
        if (parseFloat(curpay) > payable) {
            alert('مقدار پرداخت نادرست میباشد');
        } else {
            $('#remained').val((payable - curpay).toFixed(2));
        }
    }

    // Automatically recalculate when amount or price changes
    $(document).on('input', 'input[name="amount[]"], input[name="bought_up[]"]', function () {
        findTotal(this);
    });

</script>
<!-- -------------------- / Dynamic Form ---------------------------- -->

<hr />


<div class="col-12">
    <button type="button" class="btn btn-info btn-sm mb-20" onclick="recalculateTotal()">محاسبه قیمت مجموعی</button>
</div>

<hr />

<table class="table table-bordered new" style="background-color:#f6f6f6; margin-top:10px;">
    <tr>
        <td>مجموع پول &nbsp; </td>
        <td><input type="number" name="total_price" id="total_price" value="0" step="0.01" class="form-control"></td>
        <td> تخفیف </td>
        <td><input type="number" name="discount" id="discount" step="0.01" class="form-control" onkeyup="updateWhileEnteringDiscount(this.value)"></td>
        <td> قابل پرداخت</td>
        <td><input type="number" name="payable" id="payable" step="0.01" class="form-control"></td>
    </tr>
    <tr>
        <td> پرداخت فعلی</td>
        <td><input type="number" name="cur_pay" id="cur_pay" step="0.01" onkeyup="updateWhileEnteringCurPay(this.value)" class="form-control"></td>
        <td> باقی </td>
        <td><input type="number" name="remained" id="remained" step="0.01" class="form-control"></td>
        <td> پرداخت کننده</td>
        <td>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="from_account_id">
                <!-- <option value="">حساب پرداخت کننده</option> -->
                @foreach($ownBanks as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                @endforeach
            </select>
        </td>
    </tr>
    <tr>
        <td> واحد پولی</td>
        <td>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="currency_id" required>
                @foreach($currencies as $curr)
                    <option value="{{ $curr->id }}">{{ $curr->name }}</option>
                @endforeach
            </select>
        </td>
        <td> مصارف ترانسپورت </td>
        <td><input type="number" name="trans_spend" step="0.01" id="trans_spend" class="form-control"></td>
        <td> پرداخت کننده</td>
        <td>
            <select class="form-control select2" style="width: 100%; background-color:#ddd;" name="trans_account_id">
                <option value="">حساب پرداخت کننده ترانسپورت</option>
                @foreach($ownBanks as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                @endforeach
            </select>
        </td>
    </tr>
    <tr>
        <td> کمنت </td>
        <td colspan="5"><input readonly type="text" name="note" id="note" class="form-control" placeholder="توسط سیستم خلاصه معامله ثبت میگردد"></td>
    </tr>
</table>


@push('scripts')
<script>
 $(document).ready(function () {});
</script>
@endpush
