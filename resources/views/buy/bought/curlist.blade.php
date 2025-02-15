@php 
    $grandTotal = 0; 
    $grandDiscount = 0;
    $grandTransport =0;
    $payable =0;
    $remained =0;
    $curPay=0;
    $branch_id = 0;
@endphp
<table class="table responsive nowrap table-bordered datatable m-t-10" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>نمبربل</th>
            <th>نام جنس</th>
            <th>تعداد</th>
            <th>واحد</th>
            <th>فی واحد</th>
            <th>تخفیف</th>
            <th>ترانسپورت</th>
            <th>مجموع</th>
            <th>ویرایش</th>
            <th>حذف</th>

        </tr>
    </thead>
    <tbody>
        @foreach($insertedData as $index => $data)
        @php 
            $grandTotal += $data->total;
            $grandDiscount += $data->discount;
            $grandTransport += $data->transport;
        @endphp
        <tr data-id="{{ $data->id }}">
            <td>{{ $loop->iteration }}</td>
            <td>{{ $data->billno }}</td>
            <td>{{ $data->preListRelation->name ?? '' }}</td>
            <td><input type="number" class="form-control edit-field" name="amount" value="{{ $data->amount }}" disabled></td>
            <td>{{ $data->unitRelation->name ?? '' }}</td>
            <td><input type="number" class="form-control edit-field" name="bought_up" value="{{ $data->bought_up }}" disabled></td>
            <td><input type="number" class="form-control edit-field" name="discount" value="{{ $data->discount }}" disabled></td>
            <td><input type="number" class="form-control edit-field" name="transport" value="{{ $data->transport }}" disabled></td>
            <td><input type="number" class="form-control edit-field" name="transport" value="{{ $data->total }}" readonly></td>
            <td>
                <button type="button" class="btn btn-info btn-sm edit-btn">ویرایش</button>
                <button type="button" class="btn btn-success btn-sm save-btn" style="display:none;">ذخیره</button>
            </td>
            <td>
                <button class="btn btn-danger btn-sm delete-btn">حذف</button>
            </td>
        </tr>
        @endforeach
    </tbody> 
</table>

 <input type="hidden" name="branch_id" value="{{ $branch_id  }}">
<table class="table table-bordered new" style="margin-top:10px;">
   <tr>
       <td>مجموع پول &nbsp; </td>
       <td><input type="number" name="total_price" id="total_price" value="{{ $grandTotal }}" readonly step="0.01" class="form-control"></td>
       <td> تخفیف </td>
       <td><input type="number" name="discount" id="discount" value="{{ $grandDiscount }}" readonly step="0.01" onkeyup="updateWhileEnteringDiscount(this.value);" class="form-control"></td>
       <td> مصارف ترانسپورت </td>
       <td><input type="number" name="trans_spend" step="0.01" value="{{ $grandTransport }}" readonly id="trans_spend" class="form-control"></td>
   </tr>
   <tr>
       <td> قابل پرداخت</td>
       <td><input type="number" name="payable" id="payable" value="{{ $grandTotal - $grandDiscount }}" readonly step="0.01" class="form-control"></td>
       <td> پرداخت فعلی</td>
       <td><input type="number" name="cur_pay" id="cur_pay" step="0.01" value="0"   oninput="updateCurPay(this.value);" class="form-control"></td>
       <td> باقی </td>
       <td><input type="number" name="remained" id="remained" step="0.01" value="{{ max($grandTotal - $grandDiscount, 0) }}" readonly class="form-control"></td>
   </tr>
</table>

<script>
$(document).ready(function () {
   // Enable row editing
   $(".edit-btn").on("click", function () {
        let row = $(this).closest("tr");
        row.find(".edit-field").prop("disabled", false); // Enable input fields
        row.find(".edit-btn").hide();
        row.find(".save-btn").show();
    });

    // Save row data via AJAX
    $(".save-btn").on("click", function () {
        let row = $(this).closest("tr");
        let id = row.data("id");

        let data = {
            id: id,
            amount: row.find("[name='amount']").val(),
            bought_up: row.find("[name='bought_up']").val(),
            discount: row.find("[name='discount']").val(),
            transport: row.find("[name='transport']").val(),
            _token: "{{ csrf_token() }}"
        };

        $.ajax({
            url: "/boughtList/update",
            method: "POST",
            data: data,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                showNotification('موفقانه ویرایش گردید', 'success', 'top', 'right', 'withicon');
                row.find(".edit-field").prop("disabled", true); // Disable input fields
                row.find(".edit-btn").show();
                row.find(".save-btn").hide();
            },
            error: function () {
                alert("خطا در به‌روزرسانی!");
            }
        });
    });

     // Delete row
     $(".delete-btn").on("click", function () {
        let row = $(this).closest("tr");
        let id = row.data("id");

        if (confirm("آیا مطمئن هستید که این مورد را حذف می‌کنید؟")) {
            $.ajax({
                url: "/boughtList/destroy/" + id,
                method: "DELETE",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    row.remove(); // Remove row from table
                    showNotification('موفقانه حذف گردید', 'success', 'top', 'right', 'withicon');
                },
                error: function () {
                    showNotification(' حذف نگردید', 'danger', 'top', 'right', 'withicon');
                }
            });
        }
    });
});

</script>