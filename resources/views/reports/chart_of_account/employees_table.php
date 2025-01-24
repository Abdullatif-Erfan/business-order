<table class="table table-bordered"  style="width:100%">
    <tr style="background-color:#edf7ff">
        <th>شماره</th>
        <th>حساب</th>
        <th>معاشات پرداخت شده</th>
        <th>طلبات معاشات</th>
    </tr>
    <?php $cache_paied = 0; $talabat=0; $id=1; 
        foreach ($employees as $key => $val) {
        $cache_paied += $val['cache_paied'];  
        $talabat += $val['talabs'];  
        ?>
        <tr>
            <td class="priceStyle"><?=$id++?></td>
            <td class="priceStyle"><?=$val['account_name']?></td>
            <td class="priceStyle"><?= number_format($val['cache_paied'], 2)?></td>
            <td class="priceStyle"><?= number_format($val['talabs'], 2)?></td>
        </tr>
    <?php } ?>
    <tfoot>
        <tr style="background-color:#edf7ff">
            <td colspan="2">مجموع</td>
            <td class="priceStyle"><?=number_format($cache_paied,2)?></td>
            <td class="priceStyle"><?=number_format($talabat,2)?></td>
        </tr>
    </tfoot>
 </table>
