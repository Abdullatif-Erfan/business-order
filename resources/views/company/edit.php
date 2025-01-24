<script>
	$(document).on('click','#edit',function(e){
        e.preventDefault();
        var id=$(this).data('id');
        $.ajax({
        type:'POST',
        data:{id:id},
        url:"<?php echo base_url() . 'company/company/editItemModalData'; ?>",
        success: function(result)
        {
            $("#EditData").html(result);
            jQuery("#edit_modal").modal('show');
        }
        });
	});
</script>
<style>
.dt-button{ display:none !important;}
</style>
<!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				
			  <!-- breadcrum -->
				<div class="page-header m-t--10">
					<ul class="breadcrumbs">
						<li class="nav-home">
							<a href="<?php echo base_url(); ?>home">
								<i class="fas fa-home"></i>
							</a>
						</li>
						<li class="separator">
							<i class="flaticon-right-arrow"></i>
						</li>
						<li class="nav-item">
						 <a href="<?php echo base_url(); ?>company">لیست  شرکت ها</a>
						</li>
					</ul>
				</div>
				<!-- /breadcrum -->
					
				<div class="row">
		        <!-- insertion -->
			          <div class="col-md-12 col-sm-12 col-xs-12">
						
                        <div class="box-body">
					
                        <?php   echo form_open_multipart('company/company/update'); ?>
                        <input  value="<?=$journal[1]['id']?>" type="hidden"  name="first_id" >
                         <input  value="<?=$journal[0]['id']?>" type="hidden"  name="second_id" >
							<div class="form-body">
								<div class="row">
												
								<div class="col-md-4 col-sm-6 col-xs-12">
								  <div class="form-group form-floating-label">
                                    <label for="">شرکت تهیه کننده</label>
									<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_account_id" required> 
                                        <option value="<?=$journal[0]['account_id']?>"> <?=$journal[0]['account_name']?> 
										<option value=""> شرکت تهیه کننده را انتخاب نمایید</option>
										<?php foreach($customers as $key => $value)
										{ ?>
										<option value="<?php echo $value['account_id']; ?>"><?php echo $value['full_name']; ?></option>
										<?php } ?>
									</select>  
									</div>
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
                  <label for=""> مبلغ مجموعی خرید </label>
									<div class="form-group form-floating-label">
									   <!-- <input class="form-control input-solid" id="base_amount" name="base_amount" type="number" step="0.01" value="<?=$journal[0]['base_amount']?>" required> -->
									 </div>
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
                                    <label for=""> مبلغ پرداخت </label>
									 <div class="form-group form-floating-label">
									   <input class="form-control input-solid" id="amount" name="amount" type="number" step="0.01" required value="<?=$journal[0]['amount']?>">
									 </div>
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
                                    <label for="">واحد پولی   </label>
									<div class="form-group form-floating-label">
									<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency" required> 
                                    <option value="<?=$journal[0]['currency']?>"> <?=$journal[0]['currency_name']?> </option> 
										<option value="">   واحد پولی را انتخاب نمایید</option>
										<?php foreach($currency as $key => $val)
										{ ?>
										<option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
										<?php } ?>
									</select>  
									</div>
									</div>


								<div class="col-md-4 col-sm-6 col-xs-12">
                                <label for=""> حساب پرداخت کننده </label>
								  <div class="form-group form-floating-label">
									<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_account_id" required> 
                                       <option value="<?=$journal[1]['account_id']?>"> <?=$journal[1]['account_name']?>  
										<option value="">   حساب پرداخت کننده را انتخاب نمایید</option>
										<?php foreach($account as $key => $value)
										{ ?>
										<option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
										<?php } ?>
									</select>  
									</div>
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
                                    <label for=""> نبر بل </label>
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="bill_no" name="bill_no" type="text" value="<?=$journal[1]['bill_no']?>">
									  </div> 
									</div>


									<div class="col-md-4 col-sm-6 col-xs-12">
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="doc" name="doc" type="file"><label for="doc" class="placeholder"> عکس بل</label>
									  </div> 
									</div>

									<div class="col-md-8 col-sm-6 col-xs-12">
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="details" name="details" type="text" value="<?=$journal[0]['details']?>"><label for="details" class="placeholder" > تفصیلات </label>
									  </div> 
									</div>
				
				
							
									<div class="col-md-12 col-sm-12 col-xs-12">
									    <div class="col-md-4 col-sm-4 col-xs-12 pull-left">
										   
                                           <a href="<?=base_url()."company"?>">
                                           <button type="button" class="btn btn-warning btn-sm pull-left m-r-10" >
											<span class="btn-label"> <i class="fa fa-times" style="color:white"></i> </span>
											  لغو
									    	</button>
                                           </a>
                                            <button type="submit" name="submit" class="btn btn-primary btn-sm pull-left" >
											<span class="btn-label"> <i class="fa fa-save"></i> </span>
											  ثبت
									    	</button>
										</div>
										<div class="col-md-8 col-sm-8 hidden-xs"></div>
									</div>

								</div>
								</div>  <!-- /form-body -->
							</form>
						</div> <!-- box-body -->
					   </div>  <!-- /id="add_form" -->		
			         </div>
			    <!-- /insertion -->
			
		    	
		       </div>
		    </div>

				<!-- footer -->
				<?php $this->load->view('component/footer-text.php'); ?>
				<!-- /footer -->
			</div>
        <!-- /main content -->
        
