						
     <div class="table-responsive table_responsive" style="padding:5px;">
        <table id="example" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>شماره </th>
                        <th>نام  </th>										
                        <!-- <th> استفاده در بخش  </th>										 -->
                        <th>ویرایش </th>
                        <th>حذف </th>
                        </tr>
                </thead>
                <tbody>
                    <?php $id=1; foreach($unit as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><?php echo $value['name']; ?></td>
                             <td>  
                                  <?php if(doesHaveAccessTo('settings','edit_records')) { ?>
                                    <a href="#">
                                        <i class="fas fa-pen-square" data-toggle="modal" data-target="#unit_modal<?php echo $id; ?>" style="font-size:20px;" alt="ویرایش"></i>
                                      </a> 
                                 <?php } ?>
                                   <!-- modal -->
                                     <div id="unit_modal<?php echo $id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
									  <div class="modal-dialog">
										<div class="modal-content">
                                            <div class="modal-header bg-blue3">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                <h5 class="modal-title"> ویرایش </h5>
                                            </div>
										<div class="modal-body">
                                        <?php echo form_open('settings/setting/edit', 'class="full-width-form"'); ?>
                                          <input type="hidden" name="id" value="<?php echo $value['id']; ?>"> 
                                          <input type="hidden" name="table" value="unit">
            
                                            <div class="row">
                                            <div class="col-12" style="padding: 0px 30px;">
                                                <div class="form-group form-floating-label">
                                                    <input class="form-control input-solid" id="name" name="name" value="<?php echo $value['name']; ?>" type="text" required>
                                                    <label for="name" class="placeholder"> نام  </label>
                                                        <span style='color:red'><?=form_error('name')?></span>
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
                             <?php if(doesHaveAccessTo('settings','delete_records')) {
                                 if($value['id'] >=4){ ?>
                                <a href="<?php echo base_url(); ?>ommit/unit_<?php echo $this->my_encryption->do_encode($value['id']); ?>">
                                    <i class="fas fa-trash-alt" onClick='return doConfirm();' style="font-size:20px;color:red;" alt="حذف"></i>
                                </a>
                             <?php } } ?>
                             </td>
                        </tr>
                   <?php $id++; } ?>
                    
                </tbody>
            </table>
        </div> <!-- /table responsive -->  