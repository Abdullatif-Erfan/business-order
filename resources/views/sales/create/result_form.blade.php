<table class="table table-bordered new" style="background-color:#f6f6f6; margin-top:10px">
<tr>
    <td> {{__('buy.total_price')}} </td>
    <td><input type="number" name="total_price" id="total_price" value="0" class="form-control" step="0.01" required></td>
    <td>{{__('buy.cur_pay')}} </td>
    <td><input type="number" name="cur_pay" id="cur_pay" oninput="updateRemainOnCurPay(this.value)"  class="form-control" step="0.01" required></td>
    <td>{{__('buy.remained')}}</td>
    <td><input type="number" name="remained" id="remained" class="form-control" step="0.01" required></td>
</tr>
<tr>
    <td> {{__('journal.receiver_account')}} </td>
    <td>
        <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_account_id" required> 
        @foreach($ownBanks as $acc)
            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
        @endforeach
        </select> 
    </td>
    <td> {{__('common.currency')}}</td>
    <td>
        <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency_id" required> 
        @foreach($currencies as $currency)
            <option value="{{ $currency->id }}">{{ $currency->name }}</option>
        @endforeach
        </select> 
    </td>
    <td>{{__('buy.comment')}}</td>
    <td> <input type="text"  placeholder="{{__('buy.comment')}}" name="note" id="note" class="form-control"> </td>
</tr>
</table>