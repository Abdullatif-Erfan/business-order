<!-- فورم دانه -->
<script>
    var typingTimer;
var doneTypingInterval = 1500; // 1.5 seconds
 function findCoalTotal(up,id)
 {
    var amount = parseFloat($('#amount'+id).val());
    var result = amount * parseFloat(up);
    $('#total'+id).val(result);

     // update final cur_total after a specific time
     clearTimeout(typingTimer);
    // typingTimer = setTimeout(function() {
    //     calculateFinalResult(parseFloat(result));
    // }, doneTypingInterval);
 }  
 function calculateFinalResult(result) {
    var tempResult = parseFloat($('#total_price').val()) || 0;
    var finalResult = tempResult + result;
    $('#total_price').val(finalResult.toFixed(2));
}
</script>
<div class="table-responsive">
    <table class="table table-bordered new">
        <thead>
            <tr>
                <th style="width:5%"> شماره </th>                                    
                <th style="width:10%">  نوع  خریداری  </th>
                <th style="width:10%">تعداد خرید </th>
                <th style="width:10%">واحد</th>
                <th style="width:10%"> قیمت فی واحد </th>
                <th style="width:10%"> قیمت مجموعی</th>
                <th style="width:10%">تاریخ انقضاه</th>
                <th style="width:10%">علاوه / حذف</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $id = 1;
        for($i =1; $i<=10;$i++)
        { ?>
            <tr style="<?= intval($id) > 1 ? "display:none":"table-row"; ?>" id="row<?=$id?>">
            <td><?=$id?></td>
            <td>
                 <select  class="form-control select2"  style="width: 100%; border:none !important;  background-color:#ddd;"  aria-hidden="true" id="pre_list_id<?=$id?>" name="pre_list_id<?=$id?>" <?= intval($id) === 1 ? "required": "" ?> >  
                <option value="0"> انتخاب مواد خریداری</option>
                 <?php 
                        foreach($pre_list as $ke  => $va) { ?>
                        <option value="<?=$va['id']?>"> <?=$va['name']?> </option>
                   <?php } ?>
                </select> 
                
                </td>
                

                <td>
                    <input class="form-control" id="amount<?=$id?>"   name="amount<?=$id?>" type="number" step="0.01" <?= intval($id) === 1 ? "required": "" ?>>
                </td>
                
                
                <td>
                    <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="unit_id<?=$id?>" <?= intval($id) === 1 ? "required": "" ?>> 
                        <option value=""> واحد </option>
                        <?php foreach($unit as $k => $v)
                        { ?>
                        <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?> </option>
                        <?php } ?>
                    </select>  
                </td>

                <td><input class="form-control"  id="bought_up<?=$id?>" name="bought_up<?=$id?>" type="number" step="0.01" 
                oninput="findCoalTotal(this.value,<?=$id?>)" <?= intval($id) === 1 ? "required": "" ?>> </td>

                <td>
                    <input class="form-control"  id="total<?=$id?>" name="total<?=$id?>" type="number" step="0.01" <?= intval($id) === 1 ? "required": "" ?>>
                </td>

                <td style="width:10%">
                    <div class="input-group" style="margin-top:2px" data-provide="datepicker">&nbsp;&nbsp;
                        <input class="form-control"  name="expire_date<?=$id?>" id="exampleInput<?=$id?>"  
                        data-targetselector="#exampleInput<?=$id?>" value="" 
                        data-mddatetimepicker="true"  placeholder="تاریخ ختم"  data-placement="right" data-englishnumber="true"  >
                    </div>
                </td>

                <td>
                    <div style="display:inline">
                        <button type="button" onclick="showNextRow(<?=$id+1?>);" id="addBtn<?=$id?>" class="btn btn-info" style="padding: 0.375rem 0.75rem !important">
                        <i class="fa fa-plus" ></i>
                        </button>

                        <?php if($id > 1) { ?>
                            <button type="button" onclick="removeCurrentRow(<?=$id?>);" id="removeBtn<?=$id?>" class=" btn btn-warning" style="padding: 0.375rem 0.75rem !important;">
                            <i class="fa fa-minus"></i>
                            </button>
                    <?php } ?>
                    </div>
                </td>
            </tr>
            <?php $id++; } ?>
        
        </tbody>
    </table>
    </div>

    <hr />
    <div class="col-12">
        <button type="button" class="btn btn-info btn-sm mb-20" onclick="recalculate_total()">محاسبه قیمت مجموعی</button>
    </div>
    <hr />


<table class="table table-bordered new" style="background-color:#f6f6f6; margin-top:10px;">
   <tr>
       <td>مجموع پول &nbsp; </td>
       <td><input type="number" name="total_price" id="total_price" value="0" step="0.01" class="form-control"></td>
       <td> تخفیف </td>
       <td><input type="number" name="discount" id="discount" step="0.01" onkeyup="updateWhileEnteringDiscount(this.value);" class="form-control"></td>
       <td> قابل پرداخت</td>
       <td><input type="number" name="payable" id="payable" step="0.01" class="form-control"></td>
   </tr>
   <tr>
       <td> پرداخت فعلی</td>
       <td><input type="number" name="cur_pay" id="cur_pay" step="0.01" onkeyup="updateWhileEnteringCurPay(this.value);" class="form-control"></td>
       <td> باقی </td>
       <td><input type="number" name="remained" id="remained" step="0.01" class="form-control"></td>
       <td> پرداخت کننده</td>
       <td>
            <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_account_id"> 
               <option value=""> حساب پرداخت کننده  </option>
               <?php foreach($account as $k => $v)
               { ?>
               <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?> </option>
               <?php } ?>
           </select> 
       </td>
   </tr>
   <tr>
       <td> واحد پولی</td>
       <td>
           <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency_id" required> 
               <?php foreach($currency as $k => $v)
               { ?>
               <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?> </option>
               <?php } ?>
           </select> 
       </td>
       <td> مصارف ترانسپورت </td>
       <td><input type="number" name="trans_spend" step="0.01" id="trans_spend" class="form-control"></td>
       <td>  پرداخت کننده</td>
       <td>
           <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="trans_account_id"> 
               <option value=""> حساب پرداخت کننده ترانسپورت  </option>
               <?php foreach($account as $k => $v)
               { ?>
               <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?> </option>
               <?php } ?>
           </select> 
       </td>
   </tr>
   <tr>
       <td>  کمنت </td>
       <td colspan="5">
       <input type="text" name="note" id="note" class="form-control">
       </td>
   </tr>
</table>