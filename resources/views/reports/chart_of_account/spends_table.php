<table class="table table-bordered"  style="width:100%">
    <tr style="background-color:#edf7ff">
        <th>شماره</th>
        <th>حساب</th>
        <th>مبلغ</th>
    </tr>
    <?php 
     $total = 0; $id=1;
      foreach ($spends as $key => $val) { 
        $total += $val['spends'];
        ?>
        <tr>
            <td class="priceStyle"><?=$id++?></td>
            <td class="priceStyle"><?=$val['account_name']?></td>
            <td class="priceStyle"><?= number_format($val['spends'], 2)?></td>
        </tr>
    <?php } ?>
    <tfoot>
        <tr style="background-color:#edf7ff">
            <td colspan="2">مجموع</td>
            <td class="priceStyle"><?=number_format($total,2)?></td>
        </tr>
    </tfoot>
 </table>
