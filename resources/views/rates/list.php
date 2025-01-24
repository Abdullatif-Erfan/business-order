<script>
function calculate_reverse_amount_edit(id) {
    var to_currency_amount = parseFloat($('#to_currency_amount'+id).val()); 
	var result =  (1 / to_currency_amount).toFixed(10);
    $('#reverse_amount'+id).val(result);
}
function change_points_edit(value,id) {
	var to_currency_amount = parseFloat($('#to_currency_amount'+id).val()); 
	var result =  (1 / to_currency_amount).toFixed(parseFloat(value));
    $('#reverse_amount'+id).val(result);

}
</script>					
    <div class="table-responsive table_responsive" style="padding:5px;"><!-- table -->
        <table id="example2" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>شماره </th>
                        <th> از  </th>										
                        <th>  معادل </th>										
                        <th width="30%">   عکس آن </th>		
                        <th>ویرایش </th>
                        <th>حذف </th>
                        </tr>
                </thead>
                <tbody>
                    <?php $id=1; $id2=20; 
                    foreach($rates as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?> </td>
                            <td>
                              <label class="label success-label"> 1</label> <?=$value['from_currency_name'];?> 
                             </td>
                             <td>
                                <label class="label success-label">
                                   <?=$value['to_currency_amount']?>
                                </label>
                                <?=$value['to_currency_name']?> 
                             </td>

                            <td>
                            <label class="label success-label"> 
                               1
                            </label>
                            <?php echo $value['to_currency_name']; ?>
                            <label class="label success-label"> 
                                <?php echo $value['reverse_amount']; ?>
                            </label>
                            <?php echo $value['from_currency_name']; ?>

                            </td>


                             <td>  <a href="#">
							         <i class="fas fa-pen-square" data-toggle="modal" data-target="#branch_modal<?php echo $id2; ?>" style="font-size:20px;" alt="ویرایش"></i>
                                   </a> 
                                   <!-- modal -->
                                     <div id="branch_modal<?php echo $id2; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
									  <div class="modal-dialog">
										<div class="modal-content">
                                            <div class="modal-header bg-blue3">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                <h5 class="modal-title"> ویرایش </h5>
                                            </div>
										<div class="modal-body">
                                        <?php echo form_open('updateRates', 'class="full-width-form"'); ?>
                                          <input type="hidden" name="id" value="<?php echo $value['id']; ?>"> 
            
                                            <div class="row">


                                            <div class="col-md-6 col-sm-12 col-xs-12">
                                                <div class="form-group form-floating-label">
                                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_currency_id"   required> 
                                                           <option value="<?=$value['from_currency_id']?>"><?=$value['from_currency_name']?></option>
                                                            <option value=""> انتخاب واحد پولی بزرگتر</option>
                                                                <?php foreach($currency as $k => $v){ ?>
                                                                    <option value="<?=$v['id']?>"> یک  <?=$v['name']?></option>
                                                                <?php } ?>
                                                            </select> 
                                                            <span style='color:red'><?=form_error('from_currency_id')?></span> 
                                                    </div> 
                                                </div>	
                                                
                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                <div class="form-group form-floating-label">
                                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_currency_id"   required> 
                                                    <option value="<?=$value['to_currency_id']?>"><?=$value['to_currency_name']?></option>
                                                            <option value=""> انتخاب واحد پولی کوچکتر</option>
                                                                <?php foreach($currency as $key2 => $value2){ ?>
                                                                    <option value="<?=$value2['id']?>"> معادل به  <?=$value2['name']?></option>
                                                                <?php } ?>
                                                            </select> 
                                                            <span style='color:red'><?=form_error('to_currency_id')?></span> 
                                                    </div> 
                                                </div>


                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                <div class="form-group form-floating-label">
                                                    <input class="form-control input-solid" id="to_currency_amount<?=$id?>" name="to_currency_amount" type="number" step="0.00001" onkeyup="calculate_reverse_amount_edit(<?=$id?>)" required value="<?=$value['to_currency_amount']?>">
                                                        <label for="to_currency_amount" class="placeholder">   مبلغ </label>
                                                        <span style='color:red'><?=form_error('to_currency_amount')?></span>
                                                </div> 
                                                </div>	
                                                

                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                <div class="form-group form-floating-label">
                                                    <input class="form-control input-solid" id="reverse_amount<?=$id?>" placeholder="مبلغ عکس تبادله" name="reverse_amount" type="text" required value="<?=$value['reverse_amount']?>">
                                                        <!-- <label for="reverse_amount" class="placeholder">  مبلغ عکس تبادله </label> -->
                                                        <span style='color:red'><?=form_error('reverse_amount')?></span>
                                                </div> 
                                                </div>

                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                <div class="form-group form-floating-label">
                                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true"  onchange="change_points_edit(this.value,<?=$id?>)"> 
                                                            <option value=""> تعداد خانه اعشاریه</option>
                                                                <?php for($i=10;$i>=1; $i--){ ?>
                                                                    <option value="<?=$i?>"> <?=$i?> خانه</option>
                                                                <?php } ?>
                                                            </select> 
                                                    </div> 
                                                </div>

                                            </div>
                                           </div>


                                               <div class="modal-footer bg-blue4">
                                                    <button type="button" class="btn btn-warning btn-sm m-l-10" data-dismiss="modal">لغو </button>
                                                    <button type="submit" name="submit" class="btn btn-info btn-sm m-l-10" >
                                                    <span class="btn-label"> <i class="fa fa-save"></i> </span>
                                                    ثبت
                                                    </button>
                                                </div>

                                               </form>
											  </div>
											</div>
                                          </div>
                              <!-- /modal -->

                             </td>
                             <td>
                                <?php if(is_admin()) { ?>
                                    <a href="<?php echo base_url(); ?>deleteRates/<?php echo $this->my_encryption->do_encode($value['id']); ?>">
                                    <i class="fas fa-trash-alt" onClick='return doConfirm();' style="font-size:20px;color:red;" alt="حذف"></i>
                                </a>
                             <?php } ?>
                             </td>
                        </tr>
                   <?php $id++; $id2++; } ?>
                    
                </tbody>
            </table>
        </div> <!-- /table responsive -->  