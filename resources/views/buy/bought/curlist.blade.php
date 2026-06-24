@php 
    $grandTotal = 0; 
    $payable =0;
    $remained =0;
    $curPay=0;
@endphp
<table class="table responsive nowrap table-bordered datatable m-t-10" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>{{__('common.bill')}}</th>
            <th>{{__('common.item_name')}}</th>
            <th>{{__('buy.amount')}}</th>
            <th>{{__('common.unit')}}</th>
            <th>{{__('common.unit_price')}}</th>
            <th>{{__('common.total')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($insertedData as $index => $data)
        @php 
            $grandTotal += $data->total;
        @endphp
        <tr data-id="{{ $data->id }}">
            <td>{{ $loop->iteration }}</td>
            <td>{{ $data->billno }}</td>
            <td>{{ $data->preListRelation->name ?? '' }}</td>
            <td>{{ $data->amount }}</td>
            <td>{{ $data->unitRelation->name ?? '' }}</td>
            <td>{{ $data->bought_up }}</td>
            <td>{{ $data->total }}</td>
        </tr>
        @endforeach
    </tbody> 
</table>

<input type="hidden" name="branch_id" value="{{ $insertedData->first()->preListRelation->first()->branch_id  }}">
<table class="table table-bordered new" style="margin-top:10px;">
   <tr>
       <td>{{__('buy.total_price')}} &nbsp; </td>
       <td>
            <input type="number" name="total_price" id="fina_total_price" value="{{ $grandTotal }}" readonly step="0.01" class="form-control">
            <input type="hidden" name="payable" id="payable" value="{{ $grandTotal }}" readonly step="0.01" class="form-control">
        </td>
       <td>{{__('buy.cur_pay')}}</td>
       <td><input type="number" name="cur_pay" id="cur_pay" step="0.01" value="0"   oninput="updateCurPay(this.value);" class="form-control"></td>
       <td> {{__('buy.remained')}} </td>
       <td><input type="number" name="remained" id="remained" step="0.01" value="{{ $grandTotal }}" readonly class="form-control"></td>
   </tr>
</table>
