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
    <?php $talabat =0; $qarza=0; $fianl_balance=0; $total_cache = 0;
    $cache_balance = 0;
    $general_balance = 0;
    $id=1;
     foreach ($caches as $key => $val) { 
         $talabat += $val['talabs'];
         $qarza += $val['loans'];
         $cache_balance = $val['incomes'] - $val['outcomes'];
         $total_cache += $val['incomes'] - $val['outcomes'];
         $general_balance = $cache_balance + $val['talabs'] - $val['loans'];
         $fianl_balance += $general_balance;
         //  $balance = $total_cache;
        ?>
        <tr >
            <td class="priceStyle"><?=$id++?></td>
            <td class="priceStyle"><?=$val['account_name']?></td>
            <td class="priceStyle"><?=number_format($val['incomes'], 2)?></td>
            <td class="priceStyle"><?=number_format($val['outcomes'], 2)?></td>
            <td class="priceStyle"><?=number_format($cache_balance, 2) ?></td>
            <td class="priceStyle"><?=number_format($val['talabs'], 2)?></td>
            <td class="priceStyle"><?=number_format($val['loans'], 2)?> </td>
            <td class="priceStyle"><?=number_format($general_balance, 2)?> </td>
        </tr>
    <?php } ?>
    <tfoot>
        <tr style="background-color:#edf7ff">
            <td class="priceStyle" colspan="4">مجموع</td>
            <td class="priceStyle"><?=number_format($total_cache,2)?></td>
            <td class="priceStyle"><?=number_format($talabat,2)?></td>
            <td class="priceStyle"><?=number_format($qarza,2)?></td>
            <td class="priceStyle"><?=number_format($fianl_balance,2)?></td>
            
        </tr>
    </tfoot>
 </table>
