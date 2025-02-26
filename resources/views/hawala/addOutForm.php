
<?php $bul=base_url(); ?>
    <!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
		       
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card" style="min-height: 400px"> 
                    <div class="card-header" style="padding: 10px;">
                        <h4 class="card-title">فورم  ثبت حواله رفت 
                        <span class="pull-left"><a href="<?php echo base_url(); ?>transactions"><button class="btn mybtn bg-default">
                            برگشت به لیست  </button></a></span></h4>
                    </div>
                    
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                        <?php $attributes = array('role' => 'form', 'autocomplete' => 'off');
                         echo form_open('addOutTransactions',$attributes); ?>
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
						   <div class="form-group form-floating-label">
								<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_account_id"   required> 
									<option value="">حساب افزایشی را انتخاب نمایید  </option>
									<?php foreach($accounts as $key => $value){ ?>
										<option value="<?=$value['id']?>">  <?=$value['name']?></option>
									<?php } ?>
								</select> 
								<span style='color:red'><?=form_error('from_account_id')?></span> 
						    </div> 
						</div>	
						
						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						      <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="customer_id"   required> 
									<option value=""> اجرا کننده حواله   </option>
									<?php foreach($customers as $key => $value){ ?>
										<option value="<?=$value['id']?>">  <?=$value['full_name']?> - <?=$value['address']?></option>
									<?php } ?>
								</select> 
								<span style='color:red'><?=form_error('to_account_id')?></span>
						    </div> 
						</div>

						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="amount" name="amount" type="text" step="0.01"  required>
								<label for="amount" class="placeholder">   مبلغ </label>
							    <span style='color:red'><?=form_error('amount')?></span>
						   </div> 
						</div>	
						
						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						           <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency"   required> 
                                       <option value=""> انتخاب واحد پولی </option>
                                        <?php foreach($currency as $key => $value){ ?>
                                            <option value="<?=$value['id']?>"> <?=$value['name']?></option>
                                        <?php } ?>
                                    </select> 
                                    <span style='color:red'><?=form_error('currency')?></span> 
						   </div> 
						</div>

                        <div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="total_commission" name="total_commission" type="text" step="0.01"  required>
								<label for="total_commission" class="placeholder">   مجموع کمیشن </label>
							    <span style='color:red'><?=form_error('total_commission')?></span>
						   </div> 
						</div>	

                        <div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="doer_commission" name="doer_commission" type="text" step="0.01"  >
								<label for="doer_commission" class="placeholder">    کمیشن اجرا کننده </label>
							    <span style='color:red'><?=form_error('doer_commission')?></span>
						   </div> 
						</div>	

                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <!-- <label>نمبر حواله</label> -->
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid"  id="tr_auto_num"
                                value="<?=get_new_tr_auto_num()?>" name="tr_auto_num" type="text"  required>
                                <label for="tr_auto_num" class="placeholder">  نمبر حواله </label>
							    <span style='color:red'><?=form_error('tr_auto_num')?></span>
						    </div> 
						</div>	

                        <div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="sender_name" name="sender_name" type="text" step="0.01"  >
								<label for="sender_name" class="placeholder">   فرستنده </label>
							    <span style='color:red'><?=form_error('sender_name')?></span>
						   </div> 
						</div>	

                        <div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="reciever_name" name="reciever_name" type="text" step="0.01"  >
								<label for="reciever_name" class="placeholder">  گیرنده </label>
							    <span style='color:red'><?=form_error('reciever_name')?></span>
						   </div> 
						</div>	

                        <!-- <div class="col-md-4 col-sm-6 col-xs-12">
                          <label> تفصیلات</label>
						   <div class="form-group form-floating-label">
						      <input class="form-control input-solid" id="tr_comment" name="tr_comment" type="text" >
								<label for="tr_comment" class="placeholder">    </label>
							    <span style='color:red'><?=form_error('tr_comment')?></span>
						    </div> 
						</div> -->
                            

                            <div class="col-md-4 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                  
                                    <div class="col-6">
                                      <input type="submit" id="submit_button" name="submit" value=" ثبت در سیستم " class="form-control btn bg-blue pull-left">
                                    </div>
                                    <div class="col-6">
                                    <a href="<?=$bul?>transactions">
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
            document.getElementById('amount').addEventListener('input', function() {
                let amount = this.value.replace(/,/g, ''); // Remove existing commas
                amount = amount.replace(/[^\d.,]/g, ''); // Remove non-numeric characters except commas and decimal points
                amount = amount.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas as thousands separator
                this.value = amount;
            });
       </script>