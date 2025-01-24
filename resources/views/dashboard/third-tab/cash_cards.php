<div class="col-12">
<div class="row">
<?php 
    $balance =0;
    foreach($cache_in_hand as $key => $value){

    // if(intval($value['total_debit']) > intval($value['total_credit']))
    // {
    //     $balance = floatval($value['total_debit'] - intval($value['transaction_debit'] + intval($value['transaction_credit'])));
    //     // $balance = floatval($value['total_debit'] + floatval($value['transaction_debit'] - floatval($value['transaction_credit'])));
    // } else {
    //     // $balance = -intval($value['total_credit'] - intval($value['transaction_credit'] + intval($value['transaction_debit'])));
    //     $balance = -floatval($value['total_credit'] + floatval($value['transaction_credit'] - floatval($value['transaction_debit'])));
    // }

    ?>
        <div class="col-sm-6 col-lg-5">
        <div class="card p-3">
            <div class="d-flex align-items-center">
            <span class="stamp stamp-md ml-3" style="background-color:<?=$value['color']?>; height: 90px;padding:0px 10px">
                <?=$value['currency_name']?> <br />
                <?=$value['symbol']?>
            </span>
            <div>
                <h5 class="mb-2"><b>
                <small style="border:1px solid #ddd; padding: 1px 12px;margin-left:10px;border-radius:5px;color:#999">   آمد نقد </small>
                <?= number_format($value['total_recieved'],2); ?>
                </b>
                </h5>

                <h5 class="mb-2"><b>
                 <small style="border:1px solid #ddd; padding: 1px 10px;margin-left:10px;border-radius:5px;color:#999">   رفت نقد </small>
                 <?= number_format($value['total_payed'],2); ?>
                 </b>
                </h5>

                <h5 class="mb-1"><b>
                <small style="border:1px solid #ddd; padding: 1px 14px;margin-left:10px;border-radius:5px;color:#999;margin-top:3px">   بیلانس </small>
                <?php 
                 $result =  floatval($value['total_recieved']) - floatval($value['total_payed']); 
                 echo number_format($result,2);
                 ?>
                </b>
                </h5>
            </div>
            </div>
        </div>
      </div>
  <?php } ?>
      </div>
</div>