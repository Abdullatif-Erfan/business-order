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
        $total_talabat = 0;
        $total_loans = 0;
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
            $total_loans = $talabat_and_loans->cache_paid + $talabat_and_loans->loan_paid;

            // طلبات
            $total_talabat = $talabat_and_loans->cache_recieved + $talabat_and_loans->loan_recieved;

            // بیلانس نقد
            $cache_balance = $row->cache_recieved - $row->cache_paid;
            $total_cache_balance += $cache_balance; // مجموع بیلانس نقد

            // بیلانس طلب و قرض
            $loan_balance = $total_talabat - $total_loans;
            $total_loan_balance = $loan_balance; 

            // بیلانس عمومی
            $general_balance = $cache_balance + $total_talabat - $total_loans;

            // مجموع بیلانس عمومی
            $general_total_balance = $total_cache_balance + $total_loan_balance;
         @endphp
            <tr >
                <td class="priceStyle">{{ $loop->iteration }}</td>
                <td class="priceStyle">{{ $row->name }}</td>
                <td class="priceStyle">{{ number_format($row->cache_recieved,2) }}</td>  <!-- آورد نقد -->
                <td class="priceStyle">{{ number_format($row->cache_paid,2) }}</td>      <!-- برد نقد -->
                <td class="priceStyle"> {{ number_format($cache_balance,2)  }} </td>

                <td class="bg-dark"></td>       <!--  طلبات -->
                <td class="bg-dark"></td>       <!--  قرضه -->
                <td class="bg-dark"></td>
                <td class="priceStyle"></td>
            </tr>
            @endforeach
        <tfoot>
            <tr style="background-color:#edf7ff">
                <td class="priceStyle" colspan="2">مجموع</td>
                <td class="priceStyle">{{ number_format($total_cache_recieved,2) }}</td>  <!-- آورد نقد -->
                <td class="priceStyle">{{ number_format($total_cache_paid,2) }}</td>      <!-- برد نقد -->
                <td class="priceStyle" >{{ number_format($total_cache_balance,2) }}</td>

                <td class="bg-dark" style="color:green;font-weight:bolder;">{{ number_format($total_talabat,2) }}</td>  <!--  طلبات -->
                <td class="bg-dark" style="color:red;font-weight:bolder;">{{ number_format($total_loans,2) }}</td>       <!--  قرضه -->
                <td class="bg-dark" style="color:blue;font-weight:bolder;">{{ number_format($total_loan_balance,2) }}</td>
                <td class="priceStyle">{{ number_format($general_total_balance,2) }}</td>
            </tr>
        </tfoot>
    </table>
   </div>
</div>
