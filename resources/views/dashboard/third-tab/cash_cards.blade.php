<div class="col-12">
    <div class="row">
      @foreach($thirdTab as $row)
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
                            {{ number_format($row['total_recieved']) ?? 0 }}
                        </b>
                        </h5>

                        <h5 class="mb-2"><b>
                            <small style="border:1px solid #ddd; padding: 1px 10px;margin-left:10px;border-radius:5px;color:#999">   رفت نقد </small>
                            {{ number_format($row['total_payed']) ?? 0 }}
                        </b>
                        </h5>

                        <h5 class="mb-1"><b>
                            <small style="border:1px solid #ddd; padding: 1px 14px;margin-left:10px;border-radius:5px;color:#999;margin-top:3px">   بیلانس </small>
                            {{ (floatval(number_format($row['total_recieved']))) - (floatval(number_format($row['total_payed']))) }}
                        </b>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    </div>
</div>
