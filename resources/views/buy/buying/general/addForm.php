
<?php $bul=base_url(); ?>
<script>



function showNextRow(id)
{
    var prevId = id - 1;
    var elem = document.getElementById("row" + id);
        elem.style.display = "table-row";
    
    // var counter = parseInt($('#counter').val());
    // $('#counter').val(counter + parseInt(1));
    $('#counter').val(id);
   
    // var rowTotal    = parseFloat($('#total'+prevId).val());
    // var total_price = parseFloat($('#total_price').val());
    // $('#total_price').val(total_price + rowTotal);

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
    
    $('#amount'+id).val(0);

    var rowTotal = parseFloat($('#total'+id).val());
    var total_price = parseFloat($('#total_price').val());
    $('#total_price').val(total_price - rowTotal);

    // SHOW PREV ADDBTN
    $('#addBtn'+prevId).fadeIn(10);

	
}
function setItemValue(id)
{  
	$("#amount").focus();	   
}


// var typingTimer;
// var doneTypingInterval = 1500; // 2 second

// function updateTotal(buy_unit_price,id)
// {
//     var amount = parseFloat($('#amount'+id).val());
//     var weigth = $('#weigth'+id).val();
//     var result = amount * parseFloat(weigth) * parseFloat(buy_unit_price);
//     $('#total'+id).val(result);

//     clearTimeout(typingTimer);
//     typingTimer = setTimeout(function() {
//         calculateFinalResult(result);
//     }, doneTypingInterval);  
// }
// function calculateFinalResult(result) {
//     var tempResult = parseFloat($('#total_price').val()) || 0;
//     var finalResult = tempResult + result;
//     $('#total_price').val(finalResult.toFixed(2));
// }

function updateWhileEnteringDiscount(discount) 
{
    var total_price = $('#total_price').val();
    var result = total_price - discount;
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
function setWeigthAndChickenId(idAndWeigth,ids)
{
    var both = idAndWeigth.split('/');
    var id = both[0];
    var weigth =  both[1];
    $('#med_pre_id'+ids).val(id);
    $('#weigth'+ids).val(weigth);
}

function recalculate_total() {
    var sum = 0;
    for (var i = 0; i <= 10; i++) {
        var total = parseFloat($('#total' + i).val()) || 0; // Ensure 0 is used if the value is not a number
        sum += total;
    }
    $('#total_price').val(sum.toFixed(2));
}

var typingTimer;
var doneTypingInterval = 1500; // 1.5 seconds
function updateFinalChickenAmount(curTotal=0, id) 
{
    clearTimeout(typingTimer);
    typingTimer = setTimeout(function() {
        calculateFinalResult(parseFloat(curTotal));
    }, doneTypingInterval);
}

function calculateFinalResult(result) {
    var tempResult = parseFloat($('#total_price').val()) || 0;
    var finalResult = tempResult + result;
    $('#total_price').val(finalResult.toFixed(2));
}


function selectedBuyingForm(category_id)
{
    var bul = $('#bul').val();
    window.location.href= bul + 'showBuyingForm/'+category_id;
}

let typingTimer2;
const doneTypingInterval2 = 1000; // 1 second
function checkBillNoUniqueness(newBillNo) {
    // Clear the previous timer to restart the countdown
    clearTimeout(typingTimer2);
    
    // Start a new timer
    typingTimer2 = setTimeout(function() {
        // Call the function after the user has finished typing
        $.ajax({
            type: 'POST',
            data: { newBillNo: newBillNo, table: 'bought_items' },
            url: "<?php echo base_url() . 'buy/buying/checkThisCode'; ?>",
            success: function (result) {
                // alert(result);
                if(result == 1) 
                { // show failur message and hide the submit button
                    $('#successMsg').fadeOut(1);
                    $('#submit_button').fadeOut(1);
                    $('#failurMsg').fadeIn(1);
                } 
                else 
                {
                    $('#successMsg').fadeIn(1);
                    $('#submit_button').fadeIn(1);
                    $('#failurMsg').fadeOut(1);
                }
            }
        });
    }, doneTypingInterval2);
}

</script>

<style>

table.new thead tr th{background-color:#fff !important; color:#000 !important;text-align:center;}
table.my_table thead tr th{background-color:#3f7cc7  !important; color:#fff !important;text-align:center;}
.new tbody tr td{padding: 10px 5px;}
select.select2{text-align:right !important;direction:rtl !important;}


@keyframes blink {
  0% { opacity: 1; }
  50% { opacity: 0; }
  100% { opacity: 1; }
}

.blink {
  animation: blink 1s linear infinite;
  color: red;
  font-size: 18px;
}
.blink {
  color: red;
  font-size: 18px;
}

</style>


    <!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
		       
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card" style="min-height: 400px"> 
                    <div class="card-header" style="padding: 10px;">
                        <h4 class="card-title">فورم  خریداری  
                        <span class="pull-left"><a href="<?php echo base_url(); ?>boughtList"><button class="btn mybtn bg-default"> برگشت به لیست  </button></a></span></h4>

                        
                     </div>
                    
                     <?php
                            $attributes = array('role' => 'form', 'autocomplete' => 'off');
                            echo form_open('addBuyingItems',$attributes); 
                            ?>
                     
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                         <div class="form-body" style="padding: 0px 0px 15px !important;">
                         <div class="row" style="padding: 10px 20px;">
                         <input type="hidden" id="bul" value="<?=$bul?>" >

                          
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
                                    <label for="">انتخاب فروشنده</label>
                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="customer_account_id" id="customer_account_id" required> 
                                        <option value=""> انتخاب فروشنده </option>
                                        <?php foreach($customers as $key => $value){ ?>
                                            <option value="<?=$value['id']?>">  <?=$value['name']?> </option>
                                        <?php } ?>
                                    </select> 
                                    <span style='color:red'><?=form_error('customer_account_id')?></span>
                                </div> 
                            </div>

                            <div class="col-md-4 col-sm-4 col-xs-6">
                                <label for="">تاریخ</label>
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
                                   <label for="">نمبر بل</label>
                                   <div class="form-group form-floating-label">
                                        <!-- <input type="number" class="form-control" name="billno" placeholder="نمبر بل" required
                                        onkeyup="checkBillNoUniqueness(this.value)" > -->
                                        <input type="number" class="form-control" name="billno" placeholder="نمبر بل" required>
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

                            <?php  $this->load->view('buy/buying/general/form'); ?>
                        
                            </div>
                            </div>
                        

                            <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    <div class="col-3 col-xs-6">
                                      <input type="submit" id="submit_button" name="submit" value="ثبت" class="form-control btn bg-blue pull-left" >
                                    </div>
                                    <div class="col-3 col-xs-6">
                                    <a href="<?=$bul?>boughtList">
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
