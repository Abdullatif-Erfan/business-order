<table class="table table-bordered new" style="background-color:#f6f6f6; margin-top:10px">
<tr>
    <td> مبلغ مجموعی </td>
    <td><input type="number" name="total_price" id="total_price" value="0" class="form-control" step="0.01" required></td>
    <td> مجموع تخفیف </td>
    <td><input type="number" name="total_discount" id="total_discount"  
    step="0.01" class="form-control" readonly required></td>
    <td> قابل پرداخت</td>
    <td><input type="number" name="payable" id="payable" class="form-control" step="0.01" required></td>
</tr>
<tr>
    <td> پرداخت فعلی</td>
    <td><input type="number" name="cur_pay" id="cur_pay" oninput="updateRemainOnCurPay(this.value)"  class="form-control" step="0.01" required></td>
    <td> باقی </td>
    <td><input type="number" name="remained" id="remained" class="form-control" step="0.01" required></td>
    <td> حساب دریافت کننده</td>
    <td>
        <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_account_id" required> 
        @foreach($ownBanks as $acc)
            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
        @endforeach
        </select> 
    </td>
</tr>
<tr>
    <td> واحد پولی</td>
    <td>
        <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency_id" required> 
        @foreach($currencies as $currency)
            <option value="{{ $currency->id }}">{{ $currency->name }}</option>
        @endforeach
        </select> 
    </td>
    <td>کمنت</td>
    <td colspan="3"> <input type="text" name="note" id="note" class="form-control"> </td>
</tr>
</table>