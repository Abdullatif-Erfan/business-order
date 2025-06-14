						
     <div class="table-responsive table_responsive" style="padding:5px;">
        <table id="example8" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>{{__('common.number')}}</th>
                        <th> نام پکیج </th>										
                        <th>  نوع پکیج  </th>	
                        <th>فعال توسط </th>
                        <th> تاریخ فعال سازی </th>
                        <th> وضعیت </th>
                        <th>ویرایش </th>
                        <th>حذف </th>
                        </tr>
                </thead>
                <tbody>
                    <?php $id=1; $id2=800; 
                     foreach($package as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?> </td>
                            <td><?php echo $value['name']; ?></td>
                            <td>
                                <?=$value['type'] == 1 ? "لایت": ($value['type'] == 2 ? "بزنیس": "بزنیس پلس") ?>
                            </td>
                            <td><?php echo $value['activated_by']; ?></td>
                            <td><?php echo $value['activated_date']; ?></td>
                            <td><?=$value['status'] == 0 ? "غیرفعال": "فعال" ?></td>

                             <td> 
                                <?php 
                                  if(doesHaveAccessTo('settings','edit_records')){ ?>
                                  <a href="#">
							         <i class="fas fa-pen-square" data-toggle="modal" data-target="#package_modal<?php echo $id2; ?>" style="font-size:20px;" alt="ویرایش"></i>
                                   </a> 
                                   <!-- modal -->
                                     <div id="package_modal<?php echo $id2; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
									  <div class="modal-dialog2">
										<div class="modal-content">
                                            <div class="modal-header bg-blue3">
                                                
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                <h5 class="modal-title"> ویرایش </h5>
                                            </div>
										<div class="modal-body">
                                        <?php echo form_open('settings/setting/udatePackage', 'class="full-width-form"'); ?>
                                          <input type="hidden" name="id" value="<?php echo $value['id']; ?>"> 
                                          <input type="hidden" name="table" value="package">
                                          <input type="hidden" name="activated_by" value="<?php echo $this->session->userdata('name'); ?>">
                                          <input type="hidden" name="activated_date" value="<?php echo Date('Y-m-d'); ?>">
                                        

                                            <div class="row">

                                            <div class="col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group form-floating-label">
                                                    <input class="form-control input-solid" id="name" name="name" type="text" readonly value="<?=$value['name']?>" required>
                                                        <!-- <label for="name" class="placeholder">    نام پکیج را بنویسید</label> -->
                                                        <span style='color:red'><?=form_error('name')?></span>
                                                    </div> 
                                                </div>	
                                                
                                                <div class="col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group form-floating-label">
                                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="type" required> 
                                                            <option value="<?=$value['type']?>"> <?=$value['type'] == 1 ? "لایت": ($value['type'] == 2 ? "بزنیس": "بزنیس پلس") ?></option>
                                                            <!-- <option value=""> انتخاب پکیج </option> -->
                                                            <!-- <option value="1"> لایت </option>
                                                            <option value="2"> بزنیس </option>
                                                            <option value="3"> بزنیس پلس </option> -->
                                                            
                                                            </select> 
                                                        <span style='color:red'><?=form_error('type')?></span> 
                                                    </div> 
                                                </div>	
                                                                    
                                                <div class="col-md-4 col-sm-6 col-xs-12">
                                                <div class="form-group form-floating-label">
                                                    <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="status" required> 
                                                           <option value="<?=$value['status']?>">  <?=$value['status'] == 0 ? "غیرفعال": "فعال" ?> </option>
                                                            <option value="0"> غیر فعال </option>
                                                            <option value="1"> فعال </option>
                                                            </select> 
                                                        <span style='color:red'><?=form_error('status')?></span> 
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
                              <?php } ?>
                             </td>
                             <td>
                             <?php if(doesHaveAccessTo('settings','delete_records')) { 
                                 ?>
                                <!-- <a href="<?php echo base_url(); ?>settings/setting/delete_account/<?php echo $this->my_encryption->do_encode($value['id']); ?>">
                                    <i class="fas fa-trash-alt" onClick='return doConfirm();' style="font-size:20px;color:red;" alt="حذف"></i>
                                </a> -->
                             <?php }  ?>
                             </td>
                        </tr>
                   <?php $id++; $id2++; } ?>
                    
                </tbody>
            </table>
        </div> <!-- /table responsive -->  