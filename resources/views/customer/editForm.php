<script>
    function findRelatedVillages(burl, id ) {
        var csrf_token = $('input[name=pashiCsrf]').val();
        if(csrf_token  === ''){
            csrf_token = '<?php echo $this->security->get_csrf_hash(); ?>';
        }
	$.ajax({
        type: "POST",
        dataType: "JSON",
        data: { id: id, pashiCsrf: csrf_token },
		url: burl + "createAccount/CreateAccount/showVillagesByNation_id",
		success: function(result) {
            $("#dynamic_village").html(result.resp);
             $('input[name=pashiCsrf]').val(result.pashiCsrf);
        },
		error: function(xhr, status) {
			$("#dynamic_village").html("Error, انترنت ضعیف است باردیگر کوشش نمایید ");
		}
	});
}
function showHidePercentage(customer_type)
{
    if(parseInt(customer_type) === 4) {
        $('#percentage').fadeIn(100);
    } else {
        $('#percentage').fadeOut(100);
    }
}
</script>
<?php $bul=base_url(); ?>
    <!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
		       
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card"> 
                    <div class="card-header" style="padding: 10px;">
                        <h4 class="card-title">فورم ویرایش مشتریان / کارمندان / اعضا / ...
                        <span class="pull-left"><a href="<?php echo base_url(); ?>customer/<?=$customer_type?>"><button class="btn mybtn bg-default">برگشت </button></a></span></h4>
                    </div>
                    
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                        <?php $attributes = array('role' => 'form', 'autocomplete' => 'off');
                        echo form_open_multipart('customer/customer/updateCustomer',$attributes); ?>
                        <input type="hidden" name="id" value="<?=$customer_id?>" />
                        <input type="hidden" name="cur_customer_type" value="<?=$customer_type?>" />

                        
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
                        
                            <div class="col-md-6 col-sm-6 col-xs-12">
                            <div class="form-group form-floating-label">
                                <input type="text" class="form-control input-solid" name="full_name" required
                                 value="<?=$customers[0]['full_name']?>">  
                                 <label for="full_name" class="placeholder">نام مکمل </label>
                                 <span style='color:red'><?=form_error('full_name')?></span>
                                </div>
                            </div>


                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group form-floating-label">
                                <input type="text" id="phone"  class="form-control input-solid" name="phone" value="<?=$customers[0]['phone']?>" >
                                <label for="phone" class="placeholder">  شماره تماس</label>
                                <span style='color:red'><?=form_error('phone')?></span>
                                </div> 
                            </div>	
                            


                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group form-floating-label">
                                <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="customer_type"> 
                                      <option value="<?=$customers[0]['customer_type']?>">
                                      <?php echo show_customer_type($customers[0]['customer_type']); ?>
                                       </option>
                                       <option value=""> انتخاب نوع اشخاص</option>
                                            <option value="0">کارمند</option>
                                            <option value="1">فروشنده</option>
                                            <option value="2">شرکت تهیه کننده</option>
                                            <option value="3">مشتری</option>
                                            <option value="4">سهم دار</option>
                                        <span style='color:red'><?=form_error('customer_type')?></span>
                                </select>   
                            </div> 
                            </div>


                            <div class="col-md-6 col-sm-6 col-xs-12">
                               <div class="form-group form-floating-label">
                                <input type="text"  class="form-control input-solid" name="address" required value="<?=$customers[0]['address']?>"><label for="address" class="placeholder"
                                >آدرس مکمل</label>
                                <span style='color:red'><?=form_error('address')?></span>
                                </div>
                            </div>


                            <div class="col-md-6 col-sm-6 col-xs-12 m-t-20">
                                <div class="row">
                                    <div class="col-6">
                                    <input type="submit" id="submit_button" name="submit" value=" ثبت در سیستم " class="form-control btn bg-blue pull-left">
                                    </div>
                                    <div class="col-6">
                                    <a href="<?php echo base_url(); ?>customer/<?=$customer_type?>">
                                      <button type="button"  name="submit"  class="form-control btn bg-danger">لغو</button>
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
        