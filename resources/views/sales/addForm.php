
<?php $bul=base_url(); ?>
<script>
function showNextRow(id)
{
    var prevId = id - 1;
    var elem = document.getElementById("row" + id);
        elem.style.display = "table-row";
       $('#counter').val(id);
   
        // $('#med_pre_id'+id).attr('required', 'required');
        $('#med_pre_id'+id).prop('required', true);
        $('#company_id'+id).prop('required', true);
        $('#amount'+id).prop('required', true);
        $('#bought_up'+id).prop('required', true);
        $('#sell_up'+id).prop('required', true);
        $('#unit_id'+id).prop('required', true);
        $('#total'+id).prop('required', true);

        // $('#select_branch_id').removeAttr('required');

    // REMOVE ADDBTN
    $('#addBtn'+prevId).fadeOut(10);
	
}

function removeCurrentRow(id)
{
    var prevId = id - 1;
    var elem = document.getElementById("row" + id);
        elem.style.display = "none";

    var counter = parseInt($('#counter').val());
    $('#counter').val(counter - parseInt(1));
    

    var rowTotal = parseFloat($('#total'+id).val()); 
    $('#total'+id).val(0);

    var total_price = parseFloat($('#total_price').val());
    $('#total_price').val(total_price - rowTotal);

    // SHOW PREV ADDBTN
    $('#addBtn'+prevId).fadeIn(10);

    $('#med_pre_id'+id).removeAttr('required');
    $('#company_id'+id).removeAttr('required');
    $('#amount'+id).removeAttr('required');
    $('#bought_up'+id).removeAttr('required');
    $('#sell_up'+id).removeAttr('required');
    $('#unit_id'+id).removeAttr('required');
    $('#total'+id).removeAttr('required');

}
function setItemValue(id)
{  
	$("#amount").focus();	   
}



function updateWhileEnteringDiscount(discount) 
{
    var total_price = $('#total_price').val();
    var result = total_price - discount
    $('#payable').val(result.toFixed(2));
}
function updateWhileEnteringCurPay(curpay)
{
    var payable = $('#payable').val();
    if(parseInt(curpay) > parseInt(payable) )
    {
        alert('مقدار پرداخت نادرست میباشد');
        $('#submit_button').fadeOut(10);
        $('#remained').val(payable - curpay);
    } 
    else 
    {
        $('#submit_button').fadeIn(10);
        $('#remained').val(payable - curpay);
    }
}

function setThisRowTotal(warehouse_id,rowId)
{
    $('#loader').fadeIn(100);
    $('#warehouse_id'+rowId).val(warehouse_id);

	$.ajax({
        type:'POST',
        data:{warehouse_id:warehouse_id},
        url:"<?php echo base_url().'sales/sales/selectItemDetails'; ?>",
        success: function(result)
        { 
			// {"selected_item":[{"id":"3","selling_up":"200","unit_name":"\u06a9\u06cc\u0644\u0648"}]}
			var jsonObj = JSON.parse(decodeURIComponent(result));
			// alert(jsonObj.selected_item[0]['iname']); die();
			// $('#amount'+rowId).val(jsonObj.selected_item[0]['amount']);
			$('#unit_id'+rowId).val(jsonObj.selected_item[0]['unit_id']);
			$('#unit_name'+rowId).val(jsonObj.selected_item[0]['unit_name']);
			$('#bought_up'+rowId).val(jsonObj.selected_item[0]['bought_up']);
			$('#sell_up'+rowId).val(jsonObj.selected_item[0]['sell_up']);
			$('#available'+rowId).val(jsonObj.selected_item[0]['available']);
			$('#item_name'+rowId).val(jsonObj.selected_item[0]['item_name']);
			$('#warehouse_item_id'+rowId).val(jsonObj.selected_item[0]['wid']);

            
            $('#loader').fadeOut(100);
            if(jsonObj.selected_item[0]['weigth'] > 0) // Heen is selected
            {
                $("#weigths"+rowId).fadeIn(1);
                $("#weigths"+rowId).focus();
                $('#weigths'+rowId).prop('required', true);
                $('#isHeen').val(1);
                // $('#weigth'+rowId).val(jsonObj.selected_item[0]['weigth']);
            }
            else 
            {
                $("#amount"+rowId).focus();
                $("#weigths"+rowId).fadeOut(1);
                $('#weigths'+rowId).val('');
                $('#weigths'+rowId).removeAttr('required');
                $('#isHeen').val(0);
            }
        }
	});	 

}

function recalculate_total() {
    var sum = 0;
    for (var i = 0; i <= 10; i++) {
        var total = parseFloat($('#total' + i).val()) || 0; // Ensure 0 is used if the value is not a number
        sum += total;
    }
    $('#total_price').val(sum.toFixed(2));
}

// ------------- Check Bill Number Duplication ------------------
let typingTimer2;
const doneTypingInterval2 = 1000; // 1 second
function checkBillNoUniqueness(newBillNo) {
    if(parseInt(newBillNo) > 0) {
        // Clear the previous timer to restart the countdown
        clearTimeout(typingTimer2);

        // Start a new timer
        typingTimer2 = setTimeout(function() {
            // Call the function after the user has finished typing
            $.ajax({
                type: 'POST',
                data: { newBillNo: newBillNo, table: 'warehouse_sales' },
                url: "<?php echo base_url() . 'buy/buying/checkThisCode'; ?>",
                success: function (result) {
                    // alert(result);
                    if(result == 1) { // show failur message and hide the submit button
                        $('#successMsg').fadeOut(1);
                        $('#submit_button').fadeOut(1);
                        $('#failurMsg').fadeIn(1);
                    } else {
                        $('#successMsg').fadeIn(1);
                        $('#submit_button').fadeIn(1);
                        $('#failurMsg').fadeOut(1);
                    }
                }
            });
        }, doneTypingInterval2);
    }
}

// ---------- Update profit, total and final_total -----------------
var typingTimer;
var doneTypingInterval = 1500; // 2 second
function calculateProfitAndTotal(id)
{   
    var amount = parseInt($('#amount'+id).val());
    var bought_up = parseFloat($('#bought_up'+id).val());
    var sell_up = parseFloat($('#sell_up'+id).val());
    var discount = parseFloat($('#discount'+id).val());
    var available = parseInt($('#available'+id).val());
    var isHeen = parseInt($('#isHeen').val());

    /**
    *  check if Heen is selected calculate weight
    *  check if current amount is selected more than available items, do not allow 
    */
    if(isHeen === 1)
    {
        if(parseInt(amount) > parseInt(available)) {
            alert('بیشتر از مقدار موجود فروش نمیتوانید');
            $('#amount'+id).val('');
        }
        
        var weigths = parseFloat($('#weigths'+id).val());
        // alert(weigths);
        var profit = ((sell_up - bought_up) * weigths);
        var total = weigths * sell_up;
        $('#profit'+id).val(profit-discount);
        $('#total'+id).val(total);

        // update total_price
        // clearTimeout(typingTimer);
        // typingTimer = setTimeout(function() {
        //     calculateFinalResult(total);
        // }, doneTypingInterval);    
    }
    else
    {
        if(amount > 0)
        {
            if(parseInt(amount) > parseInt(available)) {
                alert('بیشتر از مقدار موجود فروش نمیتوانید');
                $('#amount'+id).val('');
            }
            else 
            {
                var profit = ((sell_up - bought_up) * parseFloat(amount));
                var total = parseFloat(amount) * sell_up;
                $('#profit'+id).val(profit-discount);
                $('#total'+id).val(total);

                // update total_price
                // clearTimeout(typingTimer);
                // typingTimer = setTimeout(function() {
                //     calculateFinalResult(total);
                // }, doneTypingInterval); 
            }
        } 
        else 
        {
            alert('لطفا نمبر درست و بالاتر از صفر انتخاب نمایید');
            $('#amount'+id).val('');
        }
    }
}

function calculateFinalResult(result) {
    var tempResult = parseFloat($('#total_price').val()) || 0;
    var finalResult = tempResult + result;
    $('#total_price').val(finalResult.toFixed(2));
}

</script>
<style>

table.new thead tr th{background-color:#fff !important; color:#000 !important;text-align:center;}
table.my_table thead tr th{background-color:#3f7cc7  !important; color:#fff !important;text-align:center;}
.new tbody tr td{padding: 10px 5px;}
select.select2{text-align:right !important;direction:rtl !important;}

</style>


    <!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
		        <input type="hidden" id="bul" value="<?=$bul?>" >
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card" style="min-height: 400px"> 
                    <div class="card-header" style="padding: 10px;">
                        <h4 class="card-title">فورم فروشات 
                        <span class="pull-left"><a href="<?php echo base_url(); ?>salesList"><button class="btn mybtn bg-default"> برگشت به لیست  </button></a></span></h4>
                     </div>
                    
                         <?php $attributes = array('role' => 'form', 'autocomplete' => 'off');
                         echo form_open('addNewSales',$attributes); ?>
                        <div class="box-body" style="border-top:2px solid #89b4ea;">
                         <div class="form-body" style="padding: 0px 0px 15px !important;">
                         <div class="row" style="padding: 10px 20px;">
                         <input type="hidden" id="isHeen" value="0" />
                           
                          <?php
                           //   Show Form Validation Error
                            if(!empty($this->session->flashdata('validationError'))) 
                            {
                                $err = $this->session->flashdata('validationError');
                                echo ' <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="alert alert-danger m-r-10 m-l-10 error">'.$err.'</div>
                                 </div>';
                            }
                            ?>

                        
                        <!------------------------ first row --------------------------->
                          <div class="col-md-12 col-sm-12 col-xs-12">
                           <div class="row">

                            
                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="form-group form-floating-label">
                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="customer_account_id" id="customer_account_id" required> 
                                        <option value=""> انتخاب مشتری </option>
                                        <?php foreach($customers as $key => $value){ ?>
                                            <option value="<?=$value['id']?>">  <?=$value['name']?> </option>
                                        <?php } ?>
                                    </select> 
                                    <span style='color:red'><?=form_error('customer_account_id')?></span>
                                </div> 
                            </div>

                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <div class="input-group mb-3" style="margin-top:10px" data-provide="datepicker">&nbsp;&nbsp;
                                    <div class="input-group-append">
                                    <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                        data-targetselector="#exampleInput00" data-englishnumber="true">
                                        <span class="fa fa-calendar"></span> 
                                    </span>
                                    </div>
                                        <input class="form-control"  name="todays_date" id="exampleInput00"  
                                        data-targetselector="#exampleInput00" value="<?=todays_date()?>" required
                                        data-mddatetimepicker="true"  placeholder="تاریخ ثبت"  data-placement="right" data-englishnumber="true"  >
                                </div>
                             </div>

                              <div class="col-md-4 col-sm-4 col-xs-12">
                                   <div class="form-group form-floating-label">
                                       <input type="number" class="form-control" name="billno" placeholder="نمبر بل"  required 
                                       onkeyup="checkBillNoUniqueness(this.value)">
                                    </div>
                                        <span id="successMsg" style="display:none"><div style="color:green">تایید است</div></span>
                                        <span id="failurMsg" style="display:none"><div style="color:red"> بل نمبر تکراری است</div></span>
                                    </div>        
                              </div>

                          <!-------------------------  second row (item selection) ------------------------------>
                          <hr />

                            <div class="col-md-12 col-sm-12 col-xs-12 m-t-20">
                            <div class="row">
                            <input type="hidden" id="counter" name="counter" value="1"  class="form-control">
                            <div class="table-responsive">
                            
                            <table class="table table-bordered new">
                                <thead>
                                <tr>
                                    <th style="width:25%">انتخاب جنس
                                       <span id="loader" style="display:none;">
                                         <img src="<?php echo base_url(); ?>assets/img/loader.gif" style="width:35px;margin:10px;" alt="Loading"/>
                                      </span>
                                   </th>
                                    <th style="width:10%">تعداد  </th>
                                    <th style="width:10%"> واحد  </th>
                                    <th style="width:10%;">فیات خرید</th>
                                    <th style="width:10%"> فیات فروش </th>
                                    <th style="width:10%"> تخفیف</th>
                                    <th style="width:10%"> مفاد</th>
                                    <th style="width:10%">مجموع</th>
                                    <th style="width:10%">علاوه / حذف</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php 
                                $id = 1;
                                for($i =1; $i<=10;$i++)
                                { ?>
                                  <tr style="<?= intval($id) > 1 ? "display:none":"table-row"; ?>" id="row<?=$id?>">
                                    <td>
                                        
                                       <input type="hidden" id="warehouse_item_id<?=$id?>" name="warehouse_item_id<?=$id?>">
                                       <input type="hidden" id="warehouse_id<?=$id?>" name="warehouse_id<?=$id?>">
                                       <input type="hidden" id="available<?=$id?>" name="available<?=$id?>">
                                       <input type="hidden" id="unit_id<?=$id?>"    name="unit_id<?=$id?>">
                                       <input type="hidden" id="item_name<?=$id?>"    name="item_name<?=$id?>">

                                        <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" onchange="setThisRowTotal(this.value,<?=$id?>)" 
                                            <?= intval($id) === 1 ? "required": "" ?>> 
                                            <option value="">  انتخاب جنس</option>
                                            <?php foreach($warehouseItemList as $key => $value)
                                             { ?>
                                             <option value="<?php echo $value['id']; ?>">
                                             <?php echo $value['name']; ?> ( 
                                                <?php if(!empty($value['weigth'])) { echo $value['weigth']. 'کیلو / '; } ?>
                                               <?=$value['available'] .' '.$value['unit_name'] ?> ) 
                                                <badge class="badge badge-info"> - <?=$value['warehouse_name']?></badge>
                                              </option>
                                             <?php } ?>
                                        </select>  
                                        </td>
                                        

                                        <td style="width:10%">
                                            <input class="form-control" id="weigths<?=$id?>" name="weigth<?=$id?>" type="number" placeholder="وزن"  oninput="calculateProfitAndTotal(<?=$id?>)" style="display:none" step="0.01">

                                            <input class="form-control" id="amount<?=$id?>" name="amount<?=$id?>" type="number" step="0.01" 
                                             oninput="calculateProfitAndTotal(<?=$id?>)"  <?= intval($id) === 1 ? "required": "" ?>
                                             placeholder="تعداد"
                                             >
                                        </td>

                                        <td>
                                            <input class="form-control" type="text" id="unit_name<?=$id?>" readonly  <?= intval($id) === 1 ? "required": "" ?>  >
                                        </td>


                                        <td>
                                           <input class="form-control"  id="bought_up<?=$id?>" name="bought_up<?=$id?>" type="number" step="0.01"  oninput="calculateProfitAndTotal(<?=$id?>)"  readonly <?= intval($id) === 1 ? "required": "" ?> >
                                        </td>

                                        <td>
                                           <input class="form-control"  id="sell_up<?=$id?>" name="sell_up<?=$id?>" type="number" step="0.01" 
                                           oninput="calculateProfitAndTotal(<?=$id?>)" <?= intval($id) === 1 ? "required": "" ?> >
                                        </td>

                                        <td>
                                           <input class="form-control"  id="discount<?=$id?>" name="discount<?=$id?>" type="number"  step="0.01" value="0" oninput="calculateProfitAndTotal(<?=$id?>)">
                                        </td>

                                        <td>
                                           <input class="form-control"  id="profit<?=$id?>" name="profit<?=$id?>" type="number" step="0.01" readonly >
                                        </td>
                                        
                                        <td>
                                            <input class="form-control"  id="total<?=$id?>" name="total<?=$id?>" value="0" type="number" step="0.01"  <?= intval($id) === 1 ? "required": "" ?>  readonly>
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

                             <table class="table table-bordered new" style="background-color:#f6f6f6; margin-top:10px">
                                <tr>
                                    <td>مجموع پول </td>
                                    <td><input type="number" name="total_price" id="total_price" value="0" class="form-control" step="0.01" required></td>
                                    <td> تخفیف عمومی </td>
                                    <td><input type="number" name="general_discount" id="general_discount"  
                                    step="0.01" onkeyup="updateWhileEnteringDiscount(this.value);" class="form-control"></td>
                                    <td> قابل پرداخت</td>
                                    <td><input type="number" name="payable" id="payable" class="form-control" step="0.01" required></td>
                                </tr>
                                <tr>
                                    <td> پرداخت فعلی</td>
                                    <td><input type="number" name="cur_pay" id="cur_pay" onkeyup="updateWhileEnteringCurPay(this.value);" class="form-control" step="0.01"></td>
                                    <td> باقی </td>
                                    <td><input type="number" name="remained" id="remained" class="form-control" step="0.01"ca></td>
                                    <td> حساب دریافت کننده</td>
                                    <td>
                                         <select  class="form-control select2"  style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_account_id" required> 
                                            <option value=""> حساب دریافت کننده  </option>
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
                                    <td>کمنت</td>
                                    <td colspan="3"> <input type="text" name="note" id="note" class="form-control"> </td>
                                </tr>
                             </table>

                            </div>
                            </div>
                        

                            <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    <div class="col-3 col-xs-6">
                                      <input type="submit" id="submit_button" name="submit" value="ثبت" class="form-control btn bg-blue pull-left" >
                                    </div>
                                    <div class="col-3 col-xs-6">
                                    <a href="<?=$bul?>salesList">
                                      <button type="button"  class="form-control btn bg-danger">لغو</button>
                                    </a>
                                    </div>
                                </div>
                            </div>


                        </div>
                        </div>  <!-- /form-body -->

						</div> <!-- box-body -->
                        <?php echo form_close(); ?>
						
                       
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>
		</div>
        <!-- /main content -->
        
     