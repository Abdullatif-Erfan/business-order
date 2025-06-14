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
        $total_loan_recieved = 0;
        $total_loan_paid = 0;
        $cache_balance = 0;
        $loan_balance = 0;
        $general_balance = 0;
        $general_total_balance = 0;
    @endphp
    <table class="table table-bordered"  style="width:100%">
        <tr style="background-color:#edf7ff">
            <th>{{__('common.number')}}</th>
            <th>حساب</th>
            <th>قرضه</th>
            <th>طلبات</th>
            <th>بیلانس </th>
            <th>تشخیص</th>
        </tr>
        @foreach($supplier_accounts as $index => $row)
        @php
                // Ensure values are always numeric (avoid null issues)
                $loan_paid = $row->loan_paid ?? 0;
                $cache_paid = $row->cache_paid ?? 0;

                $loan_recieved = $row->loan_recieved ?? 0; 
                $cache_recieved = $row->cache_recieved ?? 0; 

                $loan_balance = $loan_recieved + $cache_recieved; // بیلانس قرضه
                $talab_balance = $loan_paid + $cache_paid; // بیلانس طلبات

                // مجموع بیلانس قرضه
                $total_loan_recieved += $loan_balance;

                // مجموع بیلانس طلبات
                $total_loan_paid += $talab_balance;

                // بیلانس عمومی 
                $general_balance =  $talab_balance - $loan_balance;

                // مجموع بیلانس عمومی
                $general_total_balance += $general_balance;

        @endphp
        <tr >
                <td class="priceStyle">{{ $loop->iteration }}</td>
                <td class="priceStyle">{{ $row->name }}</td>
                <td class="priceStyle">{{ number_format($loan_balance,2) }}</td>  <!--  قرضه -->
                <td class="priceStyle">{{ number_format($talab_balance,2) }}</td>      <!--  طلبات -->
                <td class="priceStyle">{{ number_format($general_balance,2) }}</td>
                <td class="priceStyle"> {{ $general_balance == 0 ? 'تصفیه' : ($general_balance < 0 ? 'باقی' : 'طلب') }} </td>
            </tr>
            @endforeach
        <tfoot>
            <tr style="background-color:#edf7ff">
                <td class="priceStyle" colspan="2">مجموع</td>
                <td class="priceStyle" style="color:green;font-weight:bolder;">{{ number_format($total_loan_recieved,2) }}</td>   <!--  قرضه -->
                <td class="priceStyle" style="color:red;font-weight:bolder;">{{ number_format($total_loan_paid,2) }}</td>       <!--  طلبات -->
                <td class="priceStyle" style="color:blue;font-weight:bolder;">{{ number_format($general_total_balance,2) }}</td>
                <td class="priceStyle"></td>
            </tr>
        </tfoot>
    </table>
   </div>
</div>
