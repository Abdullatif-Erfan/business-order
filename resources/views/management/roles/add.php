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
							<a href="<?php echo base_url(); ?>user">کاربران</a>
						</li>
					</ul>
				</div>
				<!-- /breadcrum -->
					
				<div class="row">
		      
			
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-body"><!-- card-body -->
										
				
                    <?php  $attribute = array('name'=>'add_form');  
                           echo form_open('management/roles/addNewRole', array('id' => 'img')); ?>
							<div class="form-body">
								<div class="row">
										
									<div class="col-md-5 col-sm-6 col-xs-12">
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="role" name="role" type="text" required><label for="role" class="placeholder">نام رول را بنویسید</label>
										<span style='color:red'><?=form_error('role')?></span>
									  </div> 
									</div>

									<div class="col-md-5 col-sm-6 col-xs-12">
									<div class="form-group form-floating-label">
									  <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="status" required> 
										<option value="">وضعیت فعلی</option>
										<option value="1">فعال</option>
										<option value="2">غیرفعال</option>
									 </select>  
									 </div>
									</div>

									<div class="col-md-2 col-sm-6 col-xs-12">
									    <div class="col-md-2 col-sm-2 col-xs-12 pull-left">
										   <button type="submit" name="submit" class="btn btn-primary btn-sm pull-left" >
											<span class="btn-label"> <i class="fa fa-save"></i> </span>
											  ثبت
									    	</button>
										</div>
										<div class="col-md-10 col-sm-10 hidden-xs"></div>
									</div>

								</div>  <!-- /form-body -->
							</form>
						</div> <!-- box-body -->

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
        