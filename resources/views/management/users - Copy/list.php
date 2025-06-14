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
							<a href="<?php echo base_url().'management/user'; ?>">مدیریت کاربران</a>
						</li>
					</ul>
				</div>
				<!-- /breadcrum -->
					
				<div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-header" style="padding:10px;">
                        <a class="text-dark" href="<?=base_url().'management/user/addNew'?>" aria-expanded="false">
							    <button type="button" class="btn btn-sm " style="border-radius:0px;"> 
								<span class="fas fa-plus-square"></span>  &nbsp; <th>{{__('common.add')}}</th> </button>
						</a> 
					</div>
					<div class="card-body"><!-- card-body -->
										
					<div class="table-responsive table_responsive" style="padding:5px;"><!-- table -->
                    <table id='myTable' class="table table-bordered table-striped table-hover my_table">
                      <thead>
							<tr>
								<th>{{__('common.number')}}</th>
								<th>نام </th>
								<th>ایمیل</th>
								<th>شماره مبایل</th>
								<th>رول</th>
								<!-- <th>نوعیت کاربر</th> -->
								<th>عکس</th>
								<th>ورود</th>
								<!-- <th>تاریخچه</th> -->
								<th>{{__('common.edit')}}</th>
								<th>{{__('common.delete')}}</th>

							</tr>
                      </thead>
                      <tbody>
							<?php
                            $id = 1;
							if(!empty($userRecords))
							{
								foreach($userRecords as $key => $value)
								{
							?>
							<tr>
                                <td><?=$id++?></td>
								<td><?php echo $value['name']; ?></td>
								<td><?php echo $value['email']; ?></td>
								<td><?php echo $value['mobile']; ?></td>
                                <td><?php echo $value['role']; 
                                    if($value['roleStatus'] == 2) 
                                    {
                                         echo ' <br><span class="label label-warning">غیر فعال</span>'; 
                                    } ?>
                                </td>
								<!-- <td>
                                   <?php if($value['isAdmin'] == 1) 
                                     {
                                        echo 'ادمین'; } 
                                        else if($value['isAdmin'] == 0) 
                                        { echo 'عادی'; } ?>
                                </td> -->
								
                                <td>
                                <?php if(!empty($value['photo'])){?>
                                    <img src="<?php echo base_url().$value['photo']; ?>" alt="image" class="avatar-img rounded"
                                     style="width:30px;margin:2px 0px;">
                                    <?php } ?>
								</td>
								
                                <td class="text-center">
								  <a class="" href="<?= base_url().'auth/Auth/relogin/'.$value['userId']; ?>" title="Re Login">
                                    <i class="fas fa-retweet"></i>
                                   </a>   
								</td>

								<!-- <td class="text-center">
								   <a class="" href="<?= base_url().'login-history/'.$value['userId']; ?>" title="Login history">
                                      <i class="fa fa-history"></i>
                                    </a>
								</td> -->


								<td class="text-center">
								<a class="" href="<?php echo base_url().'management/user/edit/'.$value['userId']; ?>" title="Edit"><i class="fas fa-pen"></i>
                                    </a>
								</td>

                                <td class="text-center">
									<?php if($value['userId'] > 2) { ?>
										<a class="text-danger" href="<?php echo base_url(); ?>management/user/deleteUser/<?=$value['userId']?>" onClick='return doConfirm();'  title="Delete"><i class="fa fa-trash"></i>
                                    </a>
									<?php } ?>
								</td>
							</tr>
							<?php
								}
							}
							?>
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
        