<div class="col-12">
    <div class="row">
      @php
            $total_recieved = 0;
            $total_paid = 0;
            $total_balance = 0;
      @endphp
      @foreach($thirdTab as $row)
      @php 
         // Ensure that the received and paid amounts are numeric
         $received = is_numeric($row['total_recieved']) ? floatval($row['total_recieved']) : 0;
         $paid = is_numeric($row['total_paid']) ? floatval($row['total_paid']) : 0;

         // Calculate the balance
         $balance = $received - $paid;
         
         // Format the received, paid, and balance values
         $formatted_received = number_format($received,2);
         $formatted_paid = number_format($paid,2);
         $formatted_balance = number_format($balance,2);
      @endphp
        <div class="col-sm-6 col-lg-5">
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md ml-3" style="background-color:{{ $row['color'] ?? '' }}; height: 90px; padding:0px 10px">
                        {{ $row['currency_name'] ?? 0 }} <br />
                        {{ $row['symbol'] ?? 0 }} 
                    </span>
                    <div>
                        <h5 class="mb-2"><b>
                            <small style="border:1px solid #ddd; padding: 1px 12px;margin-left:10px;border-radius:5px;color:#999">   آمد نقد </small>
                            {{ $formatted_received }}
                        </b>
                        </h5>

                        <h5 class="mb-2"><b>
                            <small style="border:1px solid #ddd; padding: 1px 10px;margin-left:10px;border-radius:5px;color:#999">   رفت نقد </small>
                            {{ $formatted_paid }}
                        </b>
                        </h5>

                        <h5 class="mb-1"><b>
                            <small style="border:1px solid #ddd; padding: 1px 14px;margin-left:10px;border-radius:5px;color:#999;margin-top:3px">   بیلانس </small>
                            {{ $formatted_balance }}
                        </b>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
      @endforeach
    </div>
</div>
