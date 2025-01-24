						
     <div class="table-responsive table_responsive" style="padding:5px;">
        <table id="example7" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>شماره </th>
                        <th>نام شرکت </th>
                        <th>شماره تماس</th>
                        <th>آدرس</th>
                        <th>لوگو</th>
                        <th>هیدر</th>											
                        <th>ویرایش </th>
                        </tr>
                </thead>
                <tbody>
                    <?php $id=1; $id2=300; foreach($records as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td>
                            <?php if($value['is_active'] == 1) { ?>
                                <i class="fas fa-check-circle"></i>
                             <?php } ?>   
                             <?php echo $value['name']; ?></td>
                            <td><?php echo $value['phone']; ?></td>
                            <td><?php echo $value['address']; ?></td>
                            <td><img src="<?php echo base_url().$value['logos']; ?>" width="80"  alt=""></td>
                            <td><img src="<?php echo base_url().$value['header']; ?>" width="200" alt=""></td>
                             <td>  
                                  <?php if(doesHaveAccessTo('settings','edit_records')) { ?>
                                         <a href="#">
                                            <i class="fas fa-pen-square" data-toggle="modal" data-target="#company_modal<?php echo $id2; ?>" style="font-size:20px;" alt="ویرایش"></i>
                                        </a> 
                                 <?php } ?>   
                                   <!-- modal -->
                                     <div id="company_modal<?php echo $id2; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
									  <div class="modal-dialog">
										<div class="modal-content">
                                            <div class="modal-header bg-blue3">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                <h5 class="modal-title"> ویرایش </h5>
                                            </div>
										<div class="modal-body">
                                         <?php  echo form_open_multipart('settings/setting/editCompanyInfo', 'class="full-width-form"'); ?>
                                           <input type="hidden" name="id" value="<?php echo $value['id']; ?>"> 
                                           
                                           <div class="form-group form-floating-label">
                                            <input class="form-control col-md-12 col-sm-12 col-xs-12 input-solid" id="name" name="name" value="<?php echo $value['name']; ?>" type="text" required>
                                            <label for="name" class="placeholder">نام شرکت </label>
                                                <span style='color:red'><?=form_error('name')?></span>
                                            </div> 
                                            

                                            <div class="form-group form-floating-label">
                                            <input class="form-control col-md-12 col-sm-12 col-xs-12 input-solid" id="phone" name="phone" value="<?php echo $value['phone']; ?>" type="text" required>
                                            <label for="phone" class="placeholder"> شماره فعال شرکت </label>
                                                <span style='color:red'><?=form_error('phone')?></span>
                                            </div> 


                                            <div class="form-group form-floating-label">
                                            <input class="form-control col-md-12 col-sm-12 col-xs-12 input-solid" id="address" name="address" value="<?php echo $value['address']; ?>" type="text" required>
                                            <label for="address" class="placeholder"> آدرس شرکت </label>
                                                <span style='color:red'><?=form_error('address')?></span>
                                            </div> 

                            
                                            <img src="<?php echo base_url().$value['header']; ?>" width="100%" alt="">
                                            <div class="form-group form-floating-label">
                                                <input class="form-control input-solid col-12" id="header" name="header" type="file">
                                                    <label for="header" class="placeholder">هیدر</label>
                                                <span style='color:red'><?=form_error('header')?></span>
                                            </div> 


                                            <img src="<?php echo base_url().$value['logos']; ?>" width="50" height="50" alt="">
                                            <div class="form-group form-floating-label">
                                                <input class="form-control input-solid col-12" id="logos" name="logos" type="file">
                                                    <label for="logos" class="placeholder">لوگو</label>
                                                <span style='color:red'><?=form_error('logos')?></span>
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
                        </tr>
                   <?php $id++; $id2++; } ?>
                    
                </tbody>
            </table>
        </div> <!-- /table responsive -->  