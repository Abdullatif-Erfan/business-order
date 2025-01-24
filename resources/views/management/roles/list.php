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
							<a href="<?php echo base_url().'management/roles'; ?>">مدیریت رول ها</a>
						</li>
					</ul>
				</div>
				<!-- /breadcrum -->
					
				<div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-header" style="padding:10px;">
                        <a class="text-dark" href="<?=base_url().'management/roles/add'?>" aria-expanded="false">
							    <button type="button" class="btn btn-sm " style="border-radius:0px;"> 
								<span class="fas fa-plus-square"></span>  &nbsp; ثبت جدید </button>
						</a> 
					</div>
					<div class="card-body"><!-- card-body -->
										
					<div class="table-responsive table_responsive" style="padding:5px;"><!-- table -->
                       <table id='myTable' class="table table-bordered table-striped table-hover my_table">
                                <thead>
                                <tr>
                                    <th>شماره</th>
                                    <th>رول</th>
                                    <th>وضعیت</th>
                                    <th>تاریخ </th>
                                    <th class="text-center">تعین صلاحیت</th>
                                    <th class="text-center"> حذف</th>

                                </tr>
                                </thead>
                                <tbody>
                                    <?php $id=1; 
                                    foreach($roleRecords as $key => $value) 
									{ ?>
									<tr>
										<td><?php echo $id; ?></td>
										<td><?php echo $value['role']; ?></td>
										<td><?php if($value['status']==1){?>
											<label for="label" class="label label-info">فعال</label> <?php } else { ?>
											<label for="label" class="label label-danger">غیر فعال</label><?php } ?>
                                        </td>
										<td><?php echo $value['createdDtm']; ?></td>

										<td>
											<a href="<?php echo base_url(); ?>management/roles/edit/<?php echo $value['roleId']; ?>">
											   <i class="fas fa-pen-square"  style="font-size:20px;color:red;" alt="ویرایش"></i>
											</a>
										</td>
                                        <td>
											<a href="<?php echo base_url(); ?>management/roles/delete/<?php echo $value['roleId']; ?>">
											   <i class="fas fa-trash-alt" onClick='return doConfirm();'   style="font-size:20px;color:red;" alt="حذف"></i>
											</a>
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
        