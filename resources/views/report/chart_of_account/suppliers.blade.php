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
            <th>آمدنقد</th>
            <th>رفت نقد</th>
            <th>بیلانس نقد</th>
            <th>طلبات</th>
            <th>قرضه</th>
            <th>بیلانس عمومی</th>
        </tr>
        @foreach($supplier_accounts as $index => $row)
        @php
                $total_cache_recieved += $row->cache_recieved;
                $total_cache_paid += $row->cache_paid;
                $total_loan_recieved += $row->loan_recieved;
                $total_loan_paid += $row->loan_paid;
                $cache_balance = $row->cache_recieved - $row->cache_paid;
                $general_balance = $cache_balance + $row->loan_recieved - $row->loan_paid;
                $general_total_balance += $general_balance;
        @endphp
            <tr >
                <td class="priceStyle">{{ $loop->iteration }}</td>
                <td class="priceStyle">{{ $row->name }}</td>
                <td class="priceStyle">{{ $row->cache_recieved }}</td>
                <td class="priceStyle">{{ $row->cache_paid }}</td>
                <td class="priceStyle"> {{ $cache_balance  }} </td>
                <td class="priceStyle">{{ $row->loan_recieved }}</td>
                <td class="priceStyle">{{ $row->loan_paid }}</td>
                <td class="priceStyle">{{ $general_balance }}</td>
            </tr>
            @endforeach
        <tfoot>
            <tr style="background-color:#edf7ff">
                <td class="priceStyle" colspan="2">مجموع</td>
                <td class="priceStyle">{{ $total_cache_recieved }}</td>
                <td class="priceStyle">{{ $total_cache_paid }}</td>
                <td class="priceStyle"></td>
                <td class="priceStyle">{{ $total_loan_recieved }}</td>
                <td class="priceStyle">{{ $total_loan_paid }}</td>
                <td class="priceStyle">{{ $general_total_balance }}</td>
            </tr>
        </tfoot>
    </table>
   </div>
</div>
