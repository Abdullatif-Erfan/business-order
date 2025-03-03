<div class="panel-heading m-t-10" style="background-color:#f0eded">
    <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapseSuppliers" class="">
            فروشنده گان  
        </a>
    </h4>
</div>
<div id="collapseSuppliers" class="panel-collapse collapse in" style="height: auto;">
    <div class="panel-body" id="body">       
    @php
        $total_cache_recieved = 0;
        $total_cache_paid = 0;
        $total_loan_recieved = 0;
        $total_loan_paid = 0;
        $cache_balance = 0;
        $general_balance = 0;
        $general_total_balance = 0;
    @endphp
    <table class="table table-bordered"  style="width:100%">
        <tr style="background-color:#edf7ff">
            <th>شماره</th>
            <th>حساب</th>
            <th>آورد نقد</th>
            <th>برد نقد</th>
            <th>قرضه</th>
            <th>طلبات</th>
            <th>بیلانس </th>
            <th>تشخیص</th>
        </tr>
        @foreach($supplier_accounts as $index => $row)
        @php
            // آورد نقد
            $total_cache_recieved += $row->cache_recieved;

            // برد نقد
            $total_cache_paid += $row->cache_paid;

            // قرضه
            $total_loan_recieved += $row->loan_recieved;

            // طلبات
            $total_loan_paid += $row->loan_paid;

            // بیلانس نقد
            $cache_balance = $row->cache_recieved - $row->cache_paid;

            // بیلانس عمومی
            $general_balance = $cache_balance + $row->loan_paid - $row->loan_recieved;

            // مجموع بیلانس عمومی
            $general_total_balance += $general_balance;
        @endphp
        <tr >
                <td class="priceStyle">{{ $loop->iteration }}</td>
                <td class="priceStyle">{{ $row->name }}</td>
                <td class="priceStyle">{{ number_format($row->cache_recieved) }}</td> <!-- آورد نقد -->
                <td class="priceStyle">{{ number_format($row->cache_paid) }}</td>     <!-- برد نقد -->
                <td class="priceStyle">{{ number_format($row->loan_recieved) }}</td>  <!--  قرضه -->
                <td class="priceStyle">{{ number_format($row->loan_paid) }}</td>      <!--  طلبات -->
                <td class="priceStyle">{{ number_format($general_balance) }}</td>
                <td class="priceStyle"> {{ $general_balance == 0 ? 'تصفیه' : ($general_balance < 0 ? 'باقی' : 'طلب') }} </td>
            </tr>
            @endforeach
        <tfoot>
            <tr style="background-color:#edf7ff">
                <td class="priceStyle" colspan="2">مجموع</td>
                <td class="priceStyle">{{ number_format($total_cache_recieved) }}</td>  <!-- آورد نقد -->
                <td class="priceStyle">{{ number_format($total_cache_paid) }}</td>      <!-- برد نقد -->
                <td class="priceStyle">{{ number_format($total_loan_recieved) }}</td>   <!--  قرضه -->
                <td class="priceStyle">{{ number_format($total_loan_paid) }}</td>       <!--  طلبات -->
                <td class="priceStyle">{{ number_format($general_total_balance) }}</td>
                <td class="priceStyle"></td>
            </tr>
        </tfoot>
    </table>
   </div>
</div>
