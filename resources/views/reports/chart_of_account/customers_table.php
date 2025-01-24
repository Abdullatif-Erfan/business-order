<table class="table table-bordered"  style="width:100%">
    <tr style="background-color:#edf7ff" >
        <th>شماره</th>
        <th>حساب</th>
        <th>طلبات</th>
        <th>قرضه</th>
        <th>بیلانس</th>
        <th>تشخیص</th>
    </tr>
    <?php 
     $total_talabas = 0;
     $total_loans = 0;
     $total_balance = 0;
     $id =1;
      foreach ($customers as $key => $valu) { 
        $total_talabas += $valu['talabs'];
        $total_loans += $valu['loans'];
        $total_balance += $valu['talabs'] - $valu['loans'];
        ?>
        <tr>
            <td class="priceStyle"><?=$id++?></td>
            <td class="priceStyle"><?=$valu['account_name']?></td>
            <td class="priceStyle"><?= number_format($valu['talabs'], 2)?></td>
            <td class="priceStyle"><?= number_format($valu['loans'], 2)?></td>
            <td class="priceStyle"><?= number_format($valu['talabs']  - $valu['loans'], 2)?></td>
            <td class="priceStyle"><?php  if($valu['talabs'] - $valu['loans'] > 0) 
            { echo "طلب"; } else if($valu['talabs'] - $valu['loans'] < 0) {
                echo "باقی";
            } else {  echo "تصفیه"; } ?></td>
        </tr>
    <?php } ?>
    <tfoot>
        <tr style="background-color:#edf7ff">
            <td colspan="2">مجموع</td>
            <td class="priceStyle"><?=number_format($total_talabas,2)?></td>
            <td class="priceStyle"><?=number_format($total_loans,2)?></td>
            <td class="priceStyle"><?=number_format($total_balance,2)?></td>
            <td></td>
        </tr>
    </tfoot>
 </table>
