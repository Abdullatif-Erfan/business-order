						
     <div class="table-responsive table_responsive" style="padding:5px;">
        <table id="example6" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>شماره </th>
                        <th> حساب اصلی  </th>										
                        <th> حساب فرعی  </th>	
                        <th>تفصیلات </th>
                        <th>ویرایش </th>
                        <th>حذف </th>
                        </tr>
                </thead>
                <tbody>
                    <?php $id=1; $id2=100; 
                     foreach($account as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?> </td>
                            <td><?php echo $value['account_type_name']; ?></td>
                            <td>
                                <?php echo $value['name']; ?>
                            </td>
                            <td><?php echo $value['description']; ?></td>
                             <td> 
                                <?php if($value['is_need_details'] == 1) { 
                                      if(doesHaveAccessTo('settings','edit_records')) { ?>
                                    <a href="<?php echo base_url(); ?>settings/customer/editForm/<?php echo $this->my_encryption->do_encode($value['id']); ?>">
                                       <i class="fas fa-user-plus"  style="font-size:20px;color:green"></i> 
                                    </a>
                                <?php } } else {
                                     if(doesHaveAccessTo('settings','edit_records')){ ?>
                                  <a href="#">
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
                                        <?php echo form_open('settings/setting/edit', 'class="full-width-form"'); ?>
                                          <input type="hidden" name="id" value="<?php echo $value['id']; ?>"> 
                                          <input type="hidden" name="table" value="account">
                                          <input type="hidden" name="parent_code" value="<?=$value['parent_code']?>">
                                          

                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                   <div class="form-group form-floating-label">
                                                    <input class="form-control input-solid" id="name" name="name" value="<?php echo $value['name']; ?>" type="text" required>
                                                    <label for="name" class="placeholder">  صورت حساب  </label>
                                                        <span style='color:red'><?=form_error('name')?></span>
                                                    </div>
                                                </div>


                                                <!-- <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group form-floating-label">
                                                        <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="branch_id" > 
                                                            <option value="<?=$value['branch_id']?>"><?=$value['branch_name']?></option>
                                                            <option value=""> انتخاب شعبه</option>
                                                            <?php foreach($branch as $key => $v){ ?>
                                                                <option value="<?=$v['id']?>"><?=$v['name']?></option>
                                                            <?php } ?>
                                                        </select> 
                                                        <span style='color:red'><?=form_error('branch_id')?></span> 
                                                    </div> 
                                                </div>	 -->

                                                <div class="col-md-6 col-sm-12 col-xs-12 m-t-10">
                                                    <div class="form-group form-floating-label">
                                                        <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="account_type_id" required> 
                                                               <option value="<?=$value['account_type_id']?>">
                                                               <?=$value['account_type_name']?></option>
                                                                   <option value=""> انتخاب نوع صورت حساب</option>
                                                                   <?php
                                                                    foreach($account_type as $k2 => $val2)
                                                                   { ?>
                                                                       <option value="<?=$val2['id']?>"><?=$val2['name']?></option>
                                                                <?php } ?>
                                                            </select> 
                                                        </div> 
                                                 </div>	

                                                <div class="col-md-12 col-sm-12 col-xs-12 m-t-10">
                                                    <div class="form-group form-floating-label">
                                                        <input class="form-control input-solid" id="description" name="description" type="text" value="<?=$value['description']?>" >
                                                            <label for="description" class="placeholder"> تفصیلات</label>
                                                            <span style='color:red'><?=form_error('description')?></span>
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
                              <?php } } ?>
                             </td>
                             <td>
                             <?php if(doesHaveAccessTo('settings','delete_records')) { 
                                 if($value['id'] > 8) { ?>
                                <a href="<?php echo base_url(); ?>settings/setting/delete_account/<?php echo $this->my_encryption->do_encode($value['id']); ?>">
                                    <i class="fas fa-trash-alt" onClick='return doConfirm();' style="font-size:20px;color:red;" alt="حذف"></i>
                                </a>
                             <?php } } ?>
                             </td>
                        </tr>
                   <?php $id++; $id2++; } ?>
                    
                </tbody>
            </table>
        </div> <!-- /table responsive -->  