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
function showMoreRow()
{
    var counter = parseInt($('#counter').val());
    if(counter === 0) {
        $('#counter').val(1);
        $('#row1').fadeIn(1);

        $('#from_amount1').prop('required', true);
        $('#option1').prop('required', true);
        $('#currency_id1').prop('required', true);
        $('#comment1').prop('required', true);
    } 
    else if(counter ===1) {
        $('#counter').val(2);
        $('#row2').fadeIn(1);

        $('#from_amount2').prop('required', true);
        $('#option2').prop('required', true);
        $('#currency_id2').prop('required', true);
        $('#comment2').prop('required', true);
    }
    else 
    {

    }
}
function hideRow(id)
{
    var counter = parseInt($('#counter').val());
    $('#row'+id).fadeOut(1);
    $('#counter').val(counter -1);

    $('#from_amount'+id).removeAttr('required');
    $('#option'+id).removeAttr('required');
    $('#currency_id'+id).removeAttr('required');
    $('#comment'+id).removeAttr('required');

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
                        <h4 class="card-title"> ایجاد کهاته مشتریان
                        <span class="pull-left"><a href="<?php echo base_url(); ?>settings"><button class="btn mybtn bg-default">برگشت </button></a></span></h4>
                    </div>
                    
                        <div class="box-body animated fadeInRight" style="border-top:2px solid #89b4ea;">
                        <?php $attributes = array('role' => 'form', 'autocomplete' => 'off');
                        echo form_open('settings/customer/addOrUpdateCustomer',$attributes); ?>
                          <input type="hidden" name="id" value="<?=$customers[0]['id']?>" />
                         <input type="hidden" name="account_id" value="<?=$customers[0]['account_id']?>" />
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
                                <input type="text" class="form-control input-solid" id="full_name" name="full_name"  required   value="<?=$customers[0]['account_name']?>">  
                                 <label for="full_name" class="placeholder">نام مکمل </label>
                                 <span style='color:red'><?=form_error('full_name')?></span>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group form-floating-label">
                                <input type="text" id="phone"  class="form-control input-solid" name="phone" value="<?=$customers[0]['phone']?>">
                                <label for="phone" class="placeholder">  شماره تماس</label>
                                <span style='color:red'><?=form_error('phone')?></span>
                                </div> 
                            </div>	

                            <div class="col-md-4 col-sm-6 col-xs-12">
                               <div class="form-group form-floating-label">
                                <input type="text"  class="form-control input-solid" name="address" required value="<?=$customers[0]['address']?>">
                                <label for="address" class="placeholder">آدرس مکمل</label>
                                <span style='color:red'><?=form_error('address')?></span>
                                </div>
                            </div>

                            <input type="hidden" name="customer_type" value="3" />
                            <!-- <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="form-group form-floating-label">
                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="customer_type">  
                                        <option value=""> انتخاب صورت حساب</option>
                                        <option value="0">کارمند</option>
                                        <option value="1">فروشنده</option>
                                        <option value="2">شرکت تهیه کننده</option>
                                        <option value="3">مشتری</option>
                                        <option value="4">سهم دار</option>
                                        <span style='color:red'><?=form_error('customer_type')?></span>
                                    </select>   
                                </div> 
                            </div> -->

                            <div class="col-md-3 col-sm-6 col-xs-12">
                                <!-- <input type="hidden" id="counter" value="0" name="counter">
                                <button type="button" class="btn btn-primary btn-border btn-sm w-75 fw-bold mb-3 mt-2"
                                onclick="showMoreRow()"> + ثبت حسابات سابقه</button> -->
                            </div>
                            <div class="col-md-5 col-sm-6 col-xs-12"> </div>

                            <!-- hidden row -->
                            <div class="accountWrapper col-12 m-t-20" >
                            <?php 
                            $id = 1;
                            for($i=1; $i<=2; $i++)
                            { ?>
                                    <div class="row" id="row<?=$id?>" style="display:none">
                                        <div class="col-md-3 col-sm-4 col-xs-6">
                                        <div class="form-group">
                                            <label for="">مبلغ</label>
                                               <input type="number" class="form-control input-solid" name="from_amount<?=$id?>"  id="from_amount<?=$id?>" placeholder="مبلغ به عدد">  
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-sm-4 col-xs-6">
                                            <div class="form-group form-floating-label">
                                            <label for="">واحد پولی</label>
                                                <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency_id<?=$id?>" id="currency_id<?=$id?>"> 
                                                    <option value=""> واحد پولی</option>
                                                        <?php foreach($currency as $key => $value){ ?>
                                                            <option value="<?=$value['id']?>"><?=$value['name']?></option>
                                                        <?php } ?>
                                                </select> 
                                                <span style='color:red'><?=form_error('currency_id')?></span> 
                                            </div> 
                                        </div>
                                        <div class="col-md-3 col-sm-4 col-xs-6">
                                        <div class="form-group">
                                            <label for=""> انتخاب گزینه </label>
                                                <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="option<?=$id?>" id="option<?=$id?>"> 
                                                    <option value=""> یک گزینه را انتخاب نمایید</option>
                                                    <option value="1">رسیدگی (طلب)</option>
                                                    <option value="2">بردگی (باقی)</option>
                                                </select>   
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-4 col-xs-6">
                                            <div class="form-group">
                                            <label for="">یادداشت </label>
                                            <input type="text" class="form-control input-solid" name="comment<?=$id?>" value="نقل از حساب سابقه" id="comment<?=$id?>" >  
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-sm-4 col-xs-6">
                                            <i class="fas fa-trash" onclick="hideRow(<?=$id?>)" style="margin-top:50px;color:#a40808; cursor:pointer"></i>
                                        </div>
                                    </div>
                           <?php $id++; } ?>
                            </div>

                            
                            <div class="col-md-12 col-sm-12 col-xs-12 m-t-20">
                                <div class="row">
                                    <div class="col-6">
                                    <input type="submit" id="submit_button" name="submit" value=" ثبت در سیستم " class="form-control btn bg-blue pull-left">
                                    </div>
                                    <div class="col-6">
                                    <a href="<?php echo base_url(); ?>settings">
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
        