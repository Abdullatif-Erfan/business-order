						
    <div class="table-responsive table_responsive" style="padding:5px;"><!-- table -->
        <table id="example4" class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>شماره </th>
                        <th> واحد پولی </th>										
                        <th>  سمبول / نماد </th>										
                        <th>  نمایش رنگ </th>		
                        <th>ویرایش </th>
                        <th>حذف </th>
                        </tr>
                </thead>
                <tbody>
                    <?php $id=1; $id2=20; 
                    foreach($currency as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td>
                                <?php if($value['is_base'] == 1) { ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php } ?>
                               <?php echo " ".$value['name']; ?> 
                             </td>
                            <td style="color: <?=$value['color']?>"><?php echo $value['symbol']; ?></td>
                            <td>
                                <div style="width:20px;height:20px;border-radius:50%;background-color:<?=$value['color']?>"></div>
                            </td>

                             <td> 
                                <?php if(doesHaveAccessTo('settings','edit_records')) { ?>
                                    <a href="#">
							         <i class="fas fa-pen-square" data-toggle="modal" data-target="#branch_modal<?php echo $id2; ?>" style="font-size:20px;" alt="ویرایش"></i>
                                   </a> 
                                 <?php } ?>
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
                                          <input type="hidden" name="table" value="currency">
            
                                            <div class="row">


                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                  <div class="form-group form-floating-label">
                                                    <input class="form-control  input-solid" id="name" name="name" value="<?php echo $value['name']; ?>" type="text" required>
                                                    <label for="name" class="placeholder">نام واحد پولی </label>
                                                        <span style='color:red'><?=form_error('name')?></span>
                                                    </div> 
                                                </div>


                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group form-floating-label">
                                                    <input class="form-control  input-solid" id="symbol" name="symbol" value="<?php echo $value['symbol']; ?>" type="text" required>
                                                    <label for="symbol" class="placeholder">  علامت مختصر </label>
                                                        <span style='color:red'><?=form_error('symbol')?></span>
                                                    </div> 
                                                </div>


                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group form-floating-label">
                                                    <input class="form-control  input-solid" id="color" name="color" value="<?php echo $value['color']; ?>" type="color" style="height: 40px !important;">
                                                    <label for="color" class="placeholder">نمایش رنگ واحد پولی </label>
                                                        <span style='color:red'><?=form_error('color')?></span>
                                                    </div> 
                                                </div>


                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <div class="form-group form-floating-label">
                                                    <select class="form-control"  name="is_base">
                                                        <option value="<?=$value['is_base']?>"><?=$value['is_base'] == 1 ? 'بلی' : 'نخیر'?></option>
                                                        <option value="0">انتخاب پول پایه</option>
                                                        <option value="1">بلی</option>
                                                        <option value="0">نخیر</option>
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
                                <?php if(doesHaveAccessTo('settings','delete_records')) { ?>
                                    <!-- <a href="<?php echo base_url(); ?>ommit/currency_<?php echo $this->my_encryption->do_encode($value['id']); ?>">
                                    <i class="fas fa-trash-alt" onClick='return doConfirm();' style="font-size:20px;color:red;" alt="حذف"></i>
                                </a> -->
                             <?php } ?>
                             </td>
                        </tr>
                   <?php $id++; $id2++; } ?>
                    
                </tbody>
            </table>
        </div> <!-- /table responsive -->  