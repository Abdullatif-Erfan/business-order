
<?php $bul=base_url(); ?>
<script>

function getPayerBalance(currency_id)
    {
        var account_id = $('#from_account_id').val();
        if(parseInt(account_id) > 0 )
        {
            $.ajax({
                type:'POST',
                data:{account_id: account_id,currency_id:currency_id },
                url:"<?php echo base_url() . 'getCurrentBalance'; ?>",
                success: function(result)
                {
                    $('#show_current_balance').html(" مبلغ موجود   :  " + result);
                    // $('#cur_balance').val(result);
                    // $('#from_currency_id').val(currency_id);
                }
            });

        } else {
            $('#show_current_balance').html("");
        }
    }

	function calculateRateAndToAmountBasedOnThisCurrency(to_currency){
		var from_currency = $('#from_currency').val();
		var from_amount = $('#from_amount').val();
		var cleanedAmount = parseFloat(from_amount.replace(/,/g, ''));
		var rate = $('#rate').val();
		var to_amount = $('#to_amount').val();
		// check if to_currency is selected
		if(parseInt(to_currency) > 0)
		{
			// check if customer currency is the same as selected currency
			if(parseInt(to_currency) === parseInt(from_currency)) 
			{
				$('#rate').val(1);
				$('#to_amount').val(from_amount);
				$('#profit').val(0);
			} 
			else  // currency is different and find out the rate
			{
				$.ajax({
					type:'POST',
					data:{from_currency: from_currency,to_currency:to_currency },
					url:"<?php echo base_url() . 'getCurrentRate'; ?>",
					success: function(result)
					{
						$('#rate').val(result);
						let convert = cleanedAmount * parseFloat(result);
						$('#to_amount').val(convert.toFixed(2));
						$('#cur_amount').val(convert.toFixed(2));
			        	$('#profit').val(0);
					}
                });
			}
		} 
		else 
		{
			$('#rate').val();
			$('#to_amount').val();
		}
	}

	function CalculateCusterAmountBasedOnUpdatedRate()
	{
		var updatedRate = parseFloat($('#rate').val());
		var base_customer_amount = parseFloat($('#cur_amount').val());

		var from_amount = $('#from_amount').val();
		var cleanedAmount = parseFloat(from_amount.replace(/,/g, ''));

		var cur_customer_amount = updatedRate * cleanedAmount;
		$('#to_amount').val(cur_customer_amount.toFixed(2));
		// calculate profit with updated rate
		var profit = cur_customer_amount - base_customer_amount;
		$('#profit').val(profit.toFixed(2));
	}
</script>
    <!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
		       
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card" style="min-height: 400px"> 
                    <div class="card-header" style="padding: 10px;">
                        <h4 class="card-title">  صفحه ویرایش تبادله اسعار 
                        <span class="pull-left"><a href="<?php echo base_url(); ?>exchangeRates"><button class="btn mybtn bg-default">
                              <i class="fas fa-arrow-left"></i>  </button></a></span></h4>
                       </div>
                    
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                        <?php $attributes = array('role' => 'form', 'autocomplete' => 'off');
                         echo form_open('addExchangeRates',$attributes); ?>
						 <input  value="<?=get_new_journal_code()?>" type="hidden"  name="code" >
						 <input type="hidden" id="cur_amount" >
                         <div class="form-body">
                          <div class="row">

                          
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
						  <label>حساب فروشنده</label>
						   <div class="form-group form-floating-label">
								<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true"id="from_account_id" name="from_account_id"   required> 
									<?php foreach($accounts as $key => $value){ ?>
										<option value="<?=$value['id']?>">  <?=$value['name']?></option>
									<?php } ?>
								</select> 
						    </div> 
						</div>	

						<div class="col-md-4 col-sm-6 col-xs-12">
						 <label>مبلغ فروش</label>
						   <div class="form-group form-floating-label">
							<input class="form-control " id="from_amount" name="from_amount" type="text" step="0.01" placeholder="به  عدد" required >
						   </div> 
						</div>	
						
						<div class="col-md-4 col-sm-6 col-xs-12">
						 <label>واحد پول فروش</label>
						   <div class="form-group form-floating-label">
								<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_currency" id="from_currency"  onchange="getPayerBalance(this.value)" required>
								   <option value=""> انتخاب واحد پولی </option> 
									<?php foreach($currency as $key => $value){ ?>
										<option value="<?=$value['id']?>"> <?=$value['name']?></option>
									<?php } ?>
								</select> 
								<div class="col-12" style="color: green" id="show_current_balance"></div>
						   </div> 
						</div>

						<div class="col-12">
						   <hr style="border-top: 2px dotted rgb(43 36 36 / 34%) !important;" />
						</div>

						<div class="col-md-4 col-sm-6 col-xs-12">
					      <label>واحد پول مشتری</label>
						   <div class="form-group form-floating-label">
							<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="to_currency" name="to_currency" onchange="calculateRateAndToAmountBasedOnThisCurrency(this.value)"  required> 
							  <option value=""> انتخاب واحد پولی </option> 
								<?php foreach($currency as $key => $value){ ?>
									<option value="<?=$value['id']?>"> <?=$value['name']?></option>
								<?php } ?>
							</select> 
						   </div> 
						</div>

                        <div class="col-md-4 col-sm-6 col-xs-12">
						  <label>نرخ روز</label>
						   <div class="form-group form-floating-label">
							<input class="form-control" id="rate" name="rate" type="text" step="0.01"
							placeholder="به عدد" onkeyup="CalculateCusterAmountBasedOnUpdatedRate()"  required>
						   </div> 
						</div>	

                        <div class="col-md-4 col-sm-6 col-xs-12">
						  <label>مبلغ قابل پرداخت مشتری</label>
						   <div class="form-group">
							<input class="form-control" id="to_amount"  name="to_amount" type="text" step="0.01" placeholder="به عدد"  >
						   </div> 
						</div>	

						

						<div class="col-md-4 col-sm-6 col-xs-12">
						<label>حساب گیرنده / مشتری</label>
						   <div class="form-group form-floating-label">
								<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_account_id"   required> 
								    <option value=""> ? </option>
									<?php foreach($accounts as $key => $value){ ?>
										<option value="<?=$value['id']?>">  <?=$value['name']?></option>
									<?php } ?>
								</select> 
								<span style='color:red'><?=form_error('to_account_id')?></span> 
						    </div> 
						</div>	


                        <div class="col-md-4 col-sm-6 col-xs-12">
                             <label>مفاد</label>
						     <div class="form-group">
							   <input class="form-control"  id="profit"  name="profit" type="number" step="0.0001" >
						    </div> 
						</div>	

                        
                            

                            <div class="col-md-4 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                  
                                    <div class="col-6">
                                      <input type="submit" id="submit_button" name="submit" value=" ثبت در سیستم " class="form-control btn bg-blue pull-left">
                                    </div>
                                    <div class="col-6">
                                    <a href="<?=$bul?>exchangeRates">
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
            document.getElementById('from_amount').addEventListener('input', function() {
                let from_amount = this.value.replace(/,/g, ''); // Remove existing commas
                from_amount = from_amount.replace(/[^\d.,]/g, ''); // Remove non-numeric characters except commas and decimal points
                from_amount = from_amount.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas as thousands separator
                this.value = from_amount;
            });
       </script>