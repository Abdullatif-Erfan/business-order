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
    //  clearTimeout(typingTimer);
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
                <th style="width:10%">  نوع فورم خریداری  </th>
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
        $id = 0;
        foreach($details as $key => $value)
        { ?>
        <tr  id="row<?=$id?>">
            <td>
                 <input type="hidden" id="id<?=$id?>" name="id<?=$id?>" value="<?=$details[$id]['id']?>">   
                 <select  class="form-control select2"  style="width: 100%; border:none !important;  background-color:#ddd;"  aria-hidden="true" id="pre_list_id<?=$id?>" name="pre_list_id<?=$id?>" required >
                 <option value="<?=$details[$id]['pre_list_id']?>"> <?=$details[$id]['pre_list_name']?> </option>  
                <option value="0"> انتخاب مواد خریداری</option>
                 <?php 
                        foreach($pre_list as $ke  => $va) { ?>
                        <option value="<?=$va['id']?>"> <?=$va['name']?> </option>
                   <?php } ?>
                </select> 
                
                </td>
                

                <td>
                    <input class="form-control" id="amount<?=$id?>"  name="amount<?=$id?>" type="number" required
                    value="<?=$details[$id]['amount']?>">
                </td>
                
                
                <td>
                    <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="unit_id<?=$id?>" required> 
                    <option value="<?=$details[$id]['unit_id']?>"> <?=$details[$id]['unit_name']?> </option>
                        <option value=""> واحد </option>
                        <?php foreach($unit as $k => $v)
                        { ?>
                        <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?> </option>
                        <?php } ?>
                    </select>  
                </td>

                <td><input class="form-control"  id="bought_up<?=$id?>" name="bought_up<?=$id?>" type="number" step="0.01" 
                oninput="findCoalTotal(this.value,<?=$id?>)" required value="<?=$details[$id]['bought_up']?>"> </td>

                <td>
                    <input class="form-control"  id="total<?=$id?>" name="total<?=$id?>" type="number" value="<?=$details[$id]['total']?>" required>
                </td>

                <td style="width:10%">
                    <div class="input-group" style="margin-top:2px" data-provide="datepicker">&nbsp;&nbsp;
                        <input class="form-control"  name="expire_date<?=$id?>" id="exampleInput<?=$id?>"  
                        data-targetselector="#exampleInput<?=$id?>" value="<?=$details[$id]['expire_date']?>" 
                        data-mddatetimepicker="true"  placeholder="تاریخ ختم"  data-placement="right" data-englishnumber="true"  >
                    </div>
                </td>

                <td>
                    <div style="display:inline">
                        <button type="button" onclick="showNextRow(<?=$id+2?>);" id="addBtn<?=$id?>" class="btn btn-info" style="padding: 0.375rem 0.75rem !important">
                        <i class="fa fa-plus" ></i>
                        </button>
                        <?php if($id > 0) { ?>
                            <button type="button" onclick="removeCurrentRow(<?=$id?>,<?=$details[$id]['id']?>);" id="removeBtn<?=$id?>" class=" btn btn-warning" style="padding: 0.375rem 0.75rem !important;">
                            <i class="fa fa-minus"></i>
                            </button>
                    <?php } ?>
                    </div>
                </td>
            </tr>
            <?php $id++; } ?>
        <?php 
         $totalPrevData = count($details);
         $id2 = $totalPrevData;
         $limit = $id2 + 5; // show 5 more rows for new insertion 
         for($i=$id2; $i<=$limit;$i++)  
        { ?>
            <tr style="display:none" id="row<?=$id2?>">
            <td>
                 <select  class="form-control select2"  style="width: 100%; border:none !important;  background-color:#ddd;"  aria-hidden="true" id="pre_list_id<?=$id2?>" name="pre_list_id<?=$id2?>" >  
                <option value="0"> انتخاب مواد خریداری</option>
                 <?php 
                        foreach($pre_list as $ke  => $va) { ?>
                        <option value="<?=$va['id']?>"> <?=$va['name']?> </option>
                   <?php } ?>
                </select>     
            </td>
                

                <td>
                    <input class="form-control" id="amount<?=$id2?>"  name="amount<?=$id2?>" type="number">
                </td>
                
                
                <td>
                    <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="unit_id<?=$id2?>"> 
                        <option value=""> واحد </option>
                        <?php foreach($unit as $k => $v)
                        { ?>
                        <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?> </option>
                        <?php } ?>
                    </select>  
                </td>

                <td><input class="form-control"  id="bought_up<?=$id2?>" name="bought_up<?=$id2?>" type="number" step="0.01" 
                oninput="findCoalTotal(this.value,<?=$id2?>)"> </td>

                <td>
                    <input class="form-control"  id="total<?=$id2?>" name="total<?=$id2?>" type="number">
                </td>

                <td style="width:10%">
                    <div class="input-group" style="margin-top:2px" data-provide="datepicker">&nbsp;&nbsp;
                        <input class="form-control"  name="expire_date<?=$id2?>" id="exampleInput<?=$id2?>"  
                        data-targetselector="#exampleInput<?=$id2?>"  
                        data-mddatetimepicker="true"  placeholder="تاریخ ختم"  data-placement="right" data-englishnumber="true"  >
                    </div>
                </td>

                <td>
                    <div style="display:inline">
                        <button type="button" onclick="showNextRow(<?=$id2+1?>);" id="addBtn<?=$id2?>" class="btn btn-info" style="padding: 0.375rem 0.75rem !important">
                        <i class="fa fa-plus" ></i>
                        </button>

                        <?php if($id2 > 1) { ?>
                            <button type="button" onclick="removeCurrentRow(<?=$id2?>);" id="removeBtn<?=$id2?>" class=" btn btn-warning" style="padding: 0.375rem 0.75rem !important;">
                            <i class="fa fa-minus"></i>
                            </button>
                    <?php } ?>
                    </div>
                </td>
            </tr>
            <?php $id2++; } ?>
        
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
       <td>مجموع پول </td>
       <td><input type="number" name="total_price" id="total_price"  class="form-control" required 
       value="<?=$med_bought[0]['total_price']?>"></td>
       <td> تخفیف </td>
       <td><input type="number" name="discount" id="discount" onkeyup="updateWhileEnteringDiscount(this.value);" class="form-control" value="<?=$med_bought[0]['discount']?>"></td>
       <td> قابل پرداخت</td>
       <td><input type="number" name="payable" id="payable" class="form-control" required value="<?=$med_bought[0]['payable']?>"></td>
   </tr>
   <tr>
       <td> پرداخت فعلی</td>
       <td><input type="number" name="cur_pay" id="cur_pay" onkeyup="updateWhileEnteringCurPay(this.value);" class="form-control" value="<?=$med_bought[0]['cur_pay']?>"></td>
       <td> باقی </td>
       <td><input type="number" name="remained" id="remained" class="form-control" value="<?=$med_bought[0]['remained']?>"></td>
       <td> پرداخت کننده</td>
       <td>
            <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_account_id" required> 
            <option value="<?=$med_bought[0]['account_id']?>"><?=$med_bought[0]['account_name']?>  </option>
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
           <option value="<?=$med_bought[0]['currency_id']?>"><?=$med_bought[0]['currency_name']?>  </option>
               <?php foreach($currency as $k => $v)
               { ?>
               <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?> </option>
               <?php } ?>
           </select> 
       </td>
       <td> مصارف ترانسپورت </td>
       <td><input type="number" name="trans_spend" id="trans_spend" class="form-control" value="<?=$med_bought[0]['trans_spend']?>"></td>
       <td>  پرداخت کننده</td>
       <td>
           <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="trans_account_id"> 
               <option value="<?=$med_bought[0]['trans_account_id']?>"> <?=show_account_name_by_id($med_bought[0]['trans_account_id'])?> </option>
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
       <input type="text" name="note" id="note" class="form-control" value="<?=$med_bought[0]['note']?>">
       </td>
   </tr>
</table>