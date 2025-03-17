<div class="panel-heading" style="background-color:#f0eded">
    <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse" class="">
            حسابات نقده 
        </a>
        <div class="col-md-6 pull-left hidden-print">
            <select name="" id="" class="form-control" onchange="updateURLWithCurrencyId(this.value)">
                @foreach($currencies as $currency)
                <option value="{{ $currency->id }}" {{  $currency_id == $currency->id ? 'selected': '' }} >{{ $currency->name }}</option>
                @endforeach
            </select>
        </div>
    </h4>
</div>
<div id="collapse" class="panel-collapse collapse in" style="height: auto;">
    <div class="panel-body" id="body">       
    @php
        $total_cache_recieved = 0;
        $total_cache_paid = 0;
        $total_loan_recieved = 0;
        $total_loan_paid = 0;
        $cache_balance = 0;
        $total_cache_balance = 0;
        $loan_balance = 0;
        $total_loan_balance = 0;
        $general_balance = 0;
        $general_total_balance = 0;
    @endphp
    <table class="table table-bordered"  style="width:100%">
        <tr style="background-color:#edf7ff">
            <th>شماره</th>
            <th>حساب</th>
            <th>آمدنقد</th>
            <th>رفت نقد</th>
            <th>بیلانس نقد</th>
            <th class="bg-dark">طلبات</th>
            <th class="bg-dark">قرضه</th>
            <th class="bg-dark">بیلانس طلبات و قرضه</th>
            <th>بیلانس عمومی</th>
        </tr>
        @foreach($company_accounts as $index => $row)
        @php
            // آمد نقد
            $total_cache_recieved += $row->cache_recieved;

            // رفت نقد
            $total_cache_paid += $row->cache_paid;

            // قرضه
            $total_loan_recieved += $row->loan_recieved;

            // طلبات
            $total_loan_paid += $row->loan_paid;

            // بیلانس نقد
            $cache_balance = $row->cache_recieved - $row->cache_paid;
            $total_cache_balance += $cache_balance;

            // بیلانس طلب و قرض
            $loan_balance = $row->loan_paid - $row->loan_recieved;
            $total_loan_balance += $loan_balance;

            // بیلانس عمومی
            $general_balance = $cache_balance + $row->loan_paid - $row->loan_recieved;

            // مجموع بیلانس عمومی
            $general_total_balance += $general_balance;
         @endphp
            <tr >
                <td class="priceStyle">{{ $loop->iteration }}</td>
                <td class="priceStyle">{{ $row->name }}</td>
                <td class="priceStyle">{{ number_format($row->cache_recieved) }}</td>  <!-- آورد نقد -->
                <td class="priceStyle">{{ number_format($row->cache_paid) }}</td>      <!-- برد نقد -->
                <td class="priceStyle"> {{ number_format($cache_balance)  }} </td>
                <td class="bg-dark">{{ number_format($row->loan_paid) }}</td>       <!--  طلبات -->
                <td class="bg-dark">{{ number_format($row->loan_recieved) }}</td>   <!--  قرضه -->
                <td class="bg-dark">{{ number_format($loan_balance) }}</td>
                <td class="priceStyle">{{ number_format($general_balance) }}</td>
            </tr>
            @endforeach
        <tfoot>
            <tr style="background-color:#edf7ff">
                <td class="priceStyle" colspan="2">مجموع</td>
                <td class="priceStyle">{{ number_format($total_cache_recieved) }}</td>  <!-- آورد نقد -->
                <td class="priceStyle">{{ number_format($total_cache_paid) }}</td>      <!-- برد نقد -->
                <td class="priceStyle" >{{ number_format($total_cache_balance) }}</td>
                <td class="bg-dark" style="color:green">{{ number_format($total_loan_paid) }}</td>        <!--  طلبات -->
                <td class="bg-dark" style="color:red">{{ number_format($total_loan_recieved) }}</td>    <!--  قرضه -->
                <td class="bg-dark" style="color:blue">{{ number_format($total_loan_balance) }}</td>
                <td class="priceStyle">{{ number_format($general_total_balance) }}</td>
            </tr>
        </tfoot>
    </table>
   </div>
</div>
