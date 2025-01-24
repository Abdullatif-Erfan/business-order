<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            responsive: true,
			lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "همه"]],
            pageLength: 10,
			// columnDefs: [
            //     { width: '150px', targets: 6 } // Adjust the index (6) to the correct column index
            // ]
        });
    });
</script>
<!--  main content -->
    <div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
			
					
				<div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-body" style="padding: 15px 15px 33px 15px;"><!-- card-body -->
                    <h3 style="margin-bottom: 15px">کتگوری خریدها</h3>
    
                    <!-- insertion -->
                    <div class="box-tools m-t-10"> <a class="text-dark collapsed" data-toggle="collapse" href="#add_currency" aria-expanded="false">
                            <button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
                                <span class="fas fa-plus-square"></span>  &nbsp; ثبت جدید </button>
                            </a> 
                        </div>
                        <div id="add_currency" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
                            <div class="box-body">
                            <?php  echo form_open('addNewCategory'); ?>
                            <div class="form-body">
                                <div class="row">
                                
                                    <div class="col-md-10 col-sm-10 col-xs-8">
                                       <div class="form-group form-floating-label">
                                            <input class="form-control input-solid" id="name" name="name" type="text"  required>
                                            <label for="name" class="placeholder">   نام </label>
                                            <span style='color:red'><?=form_error('name')?></span>
                                        </div> 
                                    </div>	
                                    
                                    <div class="col-md-2 col-sm-2 col-xs-4 center m-t-10">
                                        <button type="submit" name="submit" class="btn btn-primary btn-sm m-l-10" >
                                          <span class="btn-label"> <i class="fa fa-save"></i> </span> ثبت
                                        </button>
                                    </div>

                                </div>
                                </div>  <!-- /form-body -->
                            </form>
                        </div> <!-- box-body -->
                    </div>  <!-- /id="add_form" -->	
            <!-- /insertion -->


            <div class="table-responsive table_responsive" style="padding:5px; margin-top:20px"><!-- table -->
            <table id="example2" class="table table-striped table-bordered my_table">
                <thead>
                    <tr>
                        <th>شماره </th>
                        <th> نام  </th>									
                        <th>ویرایش </th>
                        <th>حذف </th>
                        </tr>
                </thead>
                <tbody>
                    <?php $id=1; 
                    foreach($category as $key => $value){ ?>
                        <tr>
                            <td><?php echo $id; ?> </td>
                            <td> <?=$value['name']; ?> </td>
                             <td>  <a href="#">
							         <i class="fas fa-pen-square" data-toggle="modal" data-target="#modal<?php echo $id; ?>" style="font-size:20px;" alt="ویرایش"></i>
                                   </a> 
                                   <!-- modal -->
                                     <div id="modal<?php echo $id; ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
									  <div class="modal-dialog">
										<div class="modal-content">
                                            <div class="modal-header bg-blue3">
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                <h5 class="modal-title"> ویرایش </h5>
                                            </div>
										<div class="modal-body">
                                        <?php echo form_open('updateCategory', 'class="full-width-form"'); ?>
                                          <input type="hidden" name="id" value="<?php echo $value['id']; ?>"> 
            
                                            <div class="row">
                                                <div class="col-md-12 col-sm-12 col-xs-12">	
                                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                                    <div class="form-group form-floating-label">
                                                        <input class="form-control input-solid" id="name" name="name" type="text"  required  value="<?=$value['name']?>">
                                                            <label for="name" class="placeholder">   نام </label>
                                                            <span style='color:red'><?=form_error('name')?></span>
                                                    </div> 
                                                    </div>	
                                                </div>

                                            </div> 
                                            <!-- / row -->


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
                                    <a href="<?php echo base_url(); ?>deleteCategory/<?php echo $this->my_encryption->do_encode($value['id']); ?>">
                                    <i class="fas fa-trash-alt" onClick='return doConfirm();' style="font-size:20px;color:red;" alt="حذف"></i>
                                </a>
                             <?php } ?>
                             </td>
                        </tr>
                   <?php $id++; } ?>
                    
                </tbody>
            </table>
        </div> <!-- /table responsive --> 




					   </div> <!-- / card-body -->
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>

				<!-- footer -->
				<?php $this->load->view('component/footer-text.php'); ?>
				<!-- /footer -->
			</div>
        <!-- /main content -->
        