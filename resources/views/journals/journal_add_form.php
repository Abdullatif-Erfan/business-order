
<?php $bul=base_url(); ?>
<script>
    function updateToAmountWithThisValue(from_amount)
    {
        // $('#to_amount').val(from_amount);
        // Remove commas before setting the value 

        // const formattedAmount = from_amount.replace(/,/g, ''); // Remove commas for correct value  
        // $('#to_amount').val(formattedAmount);  

          // Remove commas before processing to get the raw value  
          const rawAmount = from_amount.replace(/,/g, '');  
        // Format the amount to with commas and set it to the to_amount field  
        $('#to_amount').val(formatNumberWithCommas(rawAmount));  

   }
   function selectAccountsLabel(paymentType)
   {
     if(parseInt(paymentType) === 1) // نقد به نقد
     {
         $('#from_account_label').html('حساب رسیدگی (پرداخت کننده)');
         $('#to_account_label').html('حساب بردگی (دریافت کننده)');
     } 
     else if(parseInt(paymentType) === 2) // نقد به نسیه
     {
        $('#from_account_label').html('حساب رسیدگی (پرداخت کننده نقد)');
        $('#to_account_label').html('حساب بردگی (دریافت کننده قرض)');
     }
     else // نسیه به نسیه
     {
        $('#from_account_label').html('حساب رسیدگی ( حساب که طلب میشود)');
        $('#to_account_label').html('حساب بردگی (حساب که قرضدار میشود)');
     }
   }
</script>
<style>
    @keyframes blink {
  0% { opacity: 1; }
  50% { opacity: 0; }
  100% { opacity: 1; }
}

.blink {
  animation: blink 1s linear infinite;
  color: red;
  font-size: 20px;
}
.blink {
  color: red;
  font-size: 20px;
}

@keyframes bold_normal {  
    0%, 100% {  
    font-weight: bold; /* Start and end with bold */  
  }  
  50% {  
    font-weight: normal; /* Transition to normal */  
  }  
}  

.typing-effect {  
  animation: bold_normal 1s linear infinite; /* Apply the animation */  
  color: green; /* Set the text color */  
  font-size: 18px; /* Set the font size */  
  margin-bottom: 10px;
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
                        <h4 class="card-title">فورم ثبت ژورنال  ویا روزنامچه
                        <span class="pull-left"><a href="<?php echo base_url(); ?>journals"><button class="btn mybtn bg-default"> برگشت به لیست  </button></a></span></h4>
                     </div>
                    
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                         <?php $attributes = array('role' => 'form', 'autocomplete' => 'off');
                         echo form_open_multipart('addJournal',$attributes); ?>
                        
                         <div class="form-body" style="padding: 0px 0px 15px !important;">
                         <div class="row" style="padding: 10px 20px;margin-top:10px;">

                          
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

                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group form-floating-label">
                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="options" required 
                                    onchange="selectAccountsLabel(this.value)" > 
                                        <option value="">  --- انتخاب نوع معامله --- </option>
                                        <option value="1"> معاملات نقد به نقد </option>
                                        <option value="2"> معاملات نسیه به نسیه </option>
                                        <option value="3"> معاملات نقد به نسیه </option>
                                        </select> 
                                    <span style='color:red'><?=form_error('options')?></span> 
                                </div> 
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group form-floating-label mb-3">
                                    <input class="form-control input-solid" id="code"  name="code" type="number" value="<?=get_new_journal_code()?>"
                                    required readonly >
                                    <!-- <label for="code" class="placeholder">   کد نمبر   </label> -->
                                    <span style='color:red'><?=form_error('code')?></span>
                                </div> 
                            </div>


                            <div class="col-md-4 col-sm-6 col-xs-12 m-t-5">
                                <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
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

                           


                        
                            <!-- second row (حساب رسیدگی) -->
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group form-floating-label">
                                        <span class="typing-effect" id="from_account_label"></span>
                                        <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_account_id" id="from_account_id" required > 
                                            <option value="">  حساب پرداخت کننده  </option>
                                            <?php foreach($accounts as $key => $value){ ?>
                                                <option value="<?=$value['id']?>">  <?=$value['name']?> </option>
                                            <?php } ?>
                                        </select> 
                                        <span style='color:red'><?=form_error('from_account_id')?></span>
                                    </div> 
                                </div>

                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group form-floating-label">
                                      <span class="typing-effect" id="to_account_label"></span>
                                        <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_account_id" id="to_account_id" required > 
                                            <option value="">  حساب دریافت کننده </option>
                                            <?php foreach($accounts as $key => $value){ ?>
                                                <option value="<?=$value['id']?>">  <?=$value['name']?> </option>
                                            <?php } ?>
                                        </select> 
                                        <span style='color:red'><?=form_error('to_account_id')?></span>
                                    </div> 
                                </div>
                                
                                <div class="col-6">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group form-floating-label">
                                                <input class="form-control input-solid" id="from_amount" name="from_amount" type="text" step="0.01"   required  oninput="updateToAmountWithThisValue(this.value)" >
                                                    <label for="from_amount" class="placeholder">   مبلغ  </label>
                                                    <span style='color:red'><?=form_error('from_amount')?></span>
                                              </div> 
                                         </div>
                                        
                                         <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group form-floating-label">
                                                <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_currency" required > 
                                                    <!-- <option value="">  واحد پولی  </option> -->
                                                        <?php foreach($currency as $key => $value){ ?>
                                                            <option value="<?=$value['id']?>"> <?=$value['name']?></option>
                                                        <?php } ?>
                                                    </select> 
                                                <span style='color:red'><?=form_error('from_currency')?></span> 
                                            </div> 
                                        </div>

                                    </div>
                                </div>


                            
                                <div class="col-6">
                                    <div class="row">

                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group form-floating-label">
                                                <input class="form-control input-solid" id="to_amount" name="to_amount" type="text" step="0.01"  required >
                                                    <label for="to_amount" class="placeholder">   مبلغ  </label>
                                                    <span style='color:red'><?=form_error('to_amount')?></span>
                                              </div> 
                                         </div>
                                         <div class="col-md-6 col-sm-6 col-xs-12">
                                            <div class="form-group form-floating-label">
                                                <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_currency" required > 
                                                    <!-- <option value="">  واحد پولی  </option> -->
                                                        <?php foreach($currency as $key => $value){ ?>
                                                            <option value="<?=$value['id']?>"> <?=$value['name']?></option>
                                                        <?php } ?>
                                                    </select> 
                                                <span style='color:red'><?=form_error('to_currency')?></span> 
                                            </div> 
                                        </div>


                                    </div>
                                </div>

                                
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group form-floating-label ">
                                        <input class="form-control input-solid" id="from_details" name="from_details" type="text"  placeholder="تفصیلات حساب رسیدگی" required>
                                        <span style='color:red'><?=form_error('from_details')?></span>
                                    </div> 
                               </div>

                               
                               <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group form-floating-label ">
                                        <input class="form-control input-solid" id="to_details" name="to_details" type="text" placeholder="تفصیلات حساب بردگی" required >
                                       <span style='color:red'><?=form_error('to_details')?></span>
                                    </div> 
                               </div>


                               
                               <div class="col-md-6 col-sm-6 col-xs-12">
                                    <div class="form-group form-floating-label"><label> اسناد</label>
                                    <input type="file" class="form-control input-solid" name="doc" accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx" >
                                    <span style='color:red'><?=form_error('doc')?></span>
                                    </div>
                                </div>


                            <div class="col-md-6 col-sm-6 col-xs-12 m-t-20">
                                <div class="row  m-t-20">
                                    <div class="col-6 col-xs-6">
                                      <input type="submit" id="submit_button" name="submit" value=" ثبت   " class="form-control btn bg-blue pull-left">
                                    </div>
                                    <div class="col-6 col-xs-6">
                                    <a href="<?=$bul?>journals">
                                      <button type="button"  class="form-control btn bg-danger">لغو</button>
                                    </a>
                                    </div>
                                </div>
                            </div>


                        </div>
                        </div>  <!-- /form-body -->
                    <?php echo form_close(); ?>

						</div> <!-- box-body -->
						
                       
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>
		</div>
        <!-- /main content -->
           
        <script>
            document.getElementById('to_amount').addEventListener('input', function() {
                let to_amount = this.value.replace(/,/g, ''); // Remove existing commas
                to_amount = to_amount.replace(/[^\d.,]/g, ''); // Remove non-numeric characters except commas and decimal points
                to_amount = to_amount.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas as thousands separator
                this.value = to_amount;
            });
    
            // Function to format a number with commas  
            function formatNumberWithCommas(number) {  
                const parts = number.split('.');  
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas for thousands  
                return parts.join('.'); // Join back the integer and decimal parts  
            }  
       </script>
        <script>
            document.getElementById('from_amount').addEventListener('input', function() {
                let from_amount = this.value.replace(/,/g, ''); // Remove existing commas
                from_amount = from_amount.replace(/[^\d.,]/g, ''); // Remove non-numeric characters except commas and decimal points
                from_amount = from_amount.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas as thousands separator
                this.value = from_amount;
            });
       </script>
