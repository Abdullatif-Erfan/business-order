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
            <td>{{ $data->amount }}</td>
            <td>{{ $data->unitRelation->name ?? '' }}</td>
            <td>{{ $data->bought_up }}</td>
            <td>{{ $data->discount }}</td>
            <td>{{ $data->transport }}</td>
            <td>{{ $data->total }}</td>
        </tr>
        @endforeach
    </tbody> 
</table>

<input type="hidden" name="branch_id" value="{{ $insertedData->first()->preListRelation->first()->branch_id  }}">
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
