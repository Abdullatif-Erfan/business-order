<table class="table table-bordered"  style="width:100%">
    <tr style="background-color:#edf7ff">
        <th>شماره</th>
        <th>حساب</th>
        <th>مبلغ</th>
    </tr>
    <?php $total = 0; $id=1; foreach ($incomes as $key => $val) {
        $total += intval($val['accountId']) == 78 ? $val['sales_incomes'] :  $val['incomes'];  ?>
        <tr>
            <td class="priceStyle"><?=$id++?></td>
            <td class="priceStyle"><?=$val['account_name']?></td>
            <td class="priceStyle"><?= intval($val['accountId']) == 78 ? $val['sales_incomes'] :  number_format($val['incomes'], 2)?></td>
        </tr>
    <?php } ?>
    <tfoot>
        <tr style="background-color:#edf7ff">
            <td colspan="2">مجموع</td>
            <td class="priceStyle"><?=number_format($total,2)?></td>
        </tr>
    </tfoot>
 </table>
