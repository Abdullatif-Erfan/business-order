
<?php $bul=base_url(); ?>
<script>
    function showHideDates()
    {
        var show = document.getElementById("dateWrapper");
        if (show.style.display == "none") {
            show.style.display = "block";
            $('#exampleInput02').attr('required', 'required');
            $('#exampleInput03').attr('required', 'required');
            $('#exampleInput04').attr('required', 'required');
        } else {
            show.style.display = "none";
            $('#exampleInput02').removeAttr('required');
            $('#exampleInput03').removeAttr('required');
            $('#exampleInput04').removeAttr('required');
        }

        // $('#dateWrapper').fadeIn(500);
        // $('#'+dateWrapper).fadeOut(500);
        // var pay_date = $('#exampleInput02').val();
        // $('#exampleInput02').attr('required', 'required');
        // $('#exampleInput02').removeAttr('required');
        // $('#exampleInput02').val(''); 
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
                        <h4 class="card-title">فورم ویرایش ژورنال  ویا روزنامچه
                        <span class="pull-left"><a href="<?php echo base_url(); ?>journals"><button class="btn mybtn bg-default">
                            برگشت به لیست  </button></a></span></h4>
                    </div>
                    
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                        <?php $attributes = array('role' => 'form', 'autocomplete' => 'off');
                         echo form_open('addJournals',$attributes); ?>
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
                            <!-- <label>حساب پرداخت کننده (کریدت)<label> -->
						   <div class="form-group form-floating-label col-md-12">
								<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_account_id"   required> 
								    <option value="<?=$journal[0]['from_account_id']?>"><?=$journal[0]['from_account_name']?></option>
                                    <option value="">حساب افزایشی را انتخاب نمایید</option>
									<?php foreach($accounts as $key => $value){ ?>
										<option value="<?=$value['id']?>">  <?=$value['name']?></option>
									<?php } ?>
								</select> 
								<span style='color:red'><?=form_error('from_account_id')?></span> 
						    </div> 
						</div>	
						
						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						      <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_account_id"   required> 
                                   <option value="<?=$journal[0]['to_account_id']?>"><?=$journal[0]['to_account_name']?></option>
                                   <option value=""> حساب کاهشی را انتخاب نمایید</option>
									<?php foreach($accounts as $key => $value){ ?>
										<option value="<?=$value['id']?>">  <?=$value['name']?></option>
									<?php } ?>
								</select> 
								<span style='color:red'><?=form_error('to_account_id')?></span>
						    </div> 
						</div>


						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
							<input class="form-control input-solid" id="amount" name="amount" value="<?=$journal[0]['from_account_amount']?>" type="text" step="0.01"  required>
								<label for="amount" class="placeholder">   مبلغ </label>
							    <span style='color:red'><?=form_error('amount')?></span>
						   </div> 
						</div>	
						

                    


						<div class="col-md-4 col-sm-6 col-xs-12">
						   <div class="form-group form-floating-label">
						           <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency"   required> 
                                   <option value="<?=$journal[0]['from_account_currency']?>"><?=$journal[0]['currency_name']?></option>
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
						      <input class="form-control input-solid" id="details" value="<?=$journal[0]['details']?>" name="details" type="text" required>
								<label for="details" class="placeholder">   تفصیلات </label>
							    <span style='color:red'><?=form_error('details')?></span>
						    </div> 
						</div>


                        <!-- hidden dates -->
                        <div class="col-md-12 col-sm-12 col-xs-12" id="dateWrapper" style="display:none" >
                            <div class="row">
                                <!-- pay_date -->
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <div class="input-group mb-3" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#exampleInput02" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="pay_date" id="exampleInput02"  
                                            data-targetselector="#exampleInput02" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ پرداخت"  data-placement="right" data-englishnumber="true"  >
                                        </div>
                                     </div>	
                                </div>

                                <!-- recieve_date -->
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <div class="input-group mb-3" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#exampleInput03" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="recieve_date" id="exampleInput03"  
                                            data-targetselector="#exampleInput03" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ دریافت"  data-placement="right" data-englishnumber="true"  >
                                        </div>
                                     </div>	
                                </div>

                                <!-- notification_date -->
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group">
                                        <div class="input-group mb-3" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#exampleInput04" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="notification_date" id="exampleInput04"  
                                            data-targetselector="#exampleInput04" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ آگهی یا نوتفکیشن"  data-placement="right" data-englishnumber="true"  >
                                        </div>
                                     </div>	
                                </div>


                            </div>
                        </div>
                        <!-- / hidden dates -->


                            <div class="col-md-8 col-sm-8 col-xs-12 m-t-20">
                                <div class="row">
                                    <div class="col-6">
                                      <button type="button" onclick="showHideDates()"  class="form-control btn " style="border: 1px solid #2196f3">
                                        <i class="fa fa-calendar"></i> &nbsp;
                                        ثبت تاریخ پرداخت / دریافت / آگهی
                                      </button>
                                    </div>
                                    <div class="col-3">
                                    <a href="<?=$bul?>addJournals">
                                      <input type="submit" id="submit_button" name="submit" value=" ثبت در سیستم " class="form-control btn bg-blue pull-left">
                                    </a>
                                    </div>
                                    <div class="col-3">
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
            document.getElementById('amount').addEventListener('input', function() {
                let amount = this.value.replace(/,/g, ''); // Remove existing commas
                amount = amount.replace(/[^\d.,]/g, ''); // Remove non-numeric characters except commas and decimal points
                amount = amount.replace(/,/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas as thousands separator
                this.value = amount;
            });
       </script>