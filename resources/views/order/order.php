<script>
	$(document).on('click','#edit',function(e){
        e.preventDefault();
        var id=$(this).data('id');
        $.ajax({
        type:'POST',
        data:{id:id},
        url:"<?php echo base_url() . 'order/order/editModalData'; ?>",
        success: function(result)
        {
            $("#EditData").html(result);
            jQuery("#edit_modal").modal('show');
        }
        });
	});
	$(document).on('click','#confirmOrder',function(e){
        e.preventDefault();
        var id=$(this).data('id');
		var conf = confirm('آیا میخواهید تایید نمایید ؟');
		if(conf)
		{
			$.ajax({
			type:'POST',
			data:{id:id},
			url:"<?php echo base_url() . 'order/order/confirmation'; ?>",
			success: function(result)
			{
				window.location.reload();
			}
			});
		}
	});
</script>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            responsive: true,
			lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "همه"]],
            pageLength: 10,
			columnDefs: [
                { width: '150px', targets: 6 } // Adjust the index (6) to the correct column index
            ]
        });
    });
</script>
<style>
.dt-button{ display:none !important;}
</style>
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
						 <a href="<?php echo base_url(); ?>order">لیست  فرمایشات</a>
						</li>
					</ul>
				</div>
				<!-- /breadcrum -->
					
				<div class="row">
		        <!-- insertion -->
			          <div class="col-md-12 col-sm-12 col-xs-12">
					  <div class="box-tools"> 
						<?php if(is_admin() ||  is_simple() || is_manager() && has_priviledge('4','can_add')){ ?>
							<a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
							<button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
							<span class="fas fa-plus-square"></span>  &nbsp; ثبت  جدید </button>
							</a> 
						<?php } ?>  
						</div>
						<div id="add_form" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
                        <div class="box-body">
						<?php   echo form_open_multipart('order/order/add'); ?>
							<div class="form-body">
								<div class="row">
												
									<div class="col-md-4 col-sm-6 col-xs-12">
									<div class="form-group form-floating-label">
									 <input class="form-control input-solid" id="name" name="name" type="text" required><label for="name" class="placeholder">نام جنس </label> 
									 </div>
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="amount" name="amount" type="number" step="0.01"  required ><label for="amount" class="placeholder"> مقدار </label>
									  </div> 
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
									  <div class="form-group form-floating-label">
										 <select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="unit_id" required> 
											<option value=""> واحد را انتخاب نمایید</option>
											<?php foreach($unit as $key => $value)
											{ ?>
											<option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
											<?php } ?>
										  </select>  
									    </div>
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
										<div class="form-group form-floating-label">
										<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="branch_id" required> 
										<option value=""> شعبه را انتخاب نمایید</option>
											<?php $branches = show_all('branch');
											foreach($branches as $key => $value)
											{    ?>
											<option value="<?=$value['id']?>"><?=$value['name']?></option>
											<?php  } ?>
										</select>
 
									  </div>
									</div>
				

								<div class="col-md-4 col-sm-6 col-xs-6 m-t-10">
									<div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
									<div class="input-group-append">
									<span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
										data-targetselector="#action_date" data-englishnumber="true">
										<span class="fa fa-calendar"></span> 
									</span>
									</div>
										<input class="form-control"   id="action_date" name="action_date"  
										data-targetselector="#action_date" value="" 
										data-mddatetimepicker="true"  placeholder="تاریخ اجرا"  data-placement="right" data-englishnumber="true"  >
									</div>
								  </div>

									<div class="col-md-4 col-sm-6 col-xs-12">
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="payed" name="payed" type="number" step="0.01" value="0"><label for="payed" class="placeholder"> مقدار پرداخت</label>
									  </div> 
									</div>
									<div class="col-md-8 col-sm-6 col-xs-12">
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="details" name="details" type="text"><label for="details" class="placeholder"> جزییات</label>
									  </div> 
									</div>
				
							
									<div class="col-md-12 col-sm-12 col-xs-12">
									    <div class="col-md-2 col-sm-2 col-xs-12 pull-left">
										   <button type="submit" name="submit" class="btn btn-primary btn-sm pull-left" >
											<span class="btn-label"> <i class="fa fa-save"></i> </span>
											  ثبت
									    	</button>
										</div>
										<div class="col-md-10 col-sm-10 hidden-xs"></div>
									</div>

								</div>
								</div>  <!-- /form-body -->
							</form>
						</div> <!-- box-body -->
					   </div>  <!-- /id="add_form" -->		
			         </div>
			    <!-- /insertion -->
			
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-header">
						<h4 class="card-title">  لیست  فرمایشات / سفارشات  
						<!-- <span class="pull-left"><i class="fa fa-print" onclick="print_page();"></i></span> -->
						</h4>
					</div>

					<div class="card-body" id="print_area"><!-- card-body -->	
					<!-- print header -->
					<div class="col-md-12 col-sm-12 col-xs-12 hide">
					    <img src="<?php echo base_url().show('header','org_bio'); ?>" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
					    <center><h4 class="card-title"> لیست  فرمایشات </h4></center>
					</div>	
					<!-- / end of print header -->
					<div class="table-responsive table_responsive" style="padding:5px;">
                     <table id="myTable" class="table table-bordered table-striped table-hover">
					 <thead>
							<tr>
							    <th>&nbsp; شماره</th>	
								<th> شعبه  </th>							
								<th> نام  </th>
								<th>مقدار</th>
								<th>تاریخ موعد</th>
								<th>رسید پول</th>
								<th>تاریخ ثبت</th>
								<th>وضعیت</th>
								<th>جزییات</th>
						    	<th style="width:100px;">عملیات</th> 
							</tr>
								</thead>
								<tbody>
									<?php $id=1; $total = 0;
									 foreach($record as $key=>$value)
									 { ?>
										<tr>
											<td><?=$id?></td>
											<td><?=$value['branch_name']?></td>
											<td><?=$value['name']?></td>
											<td><?=$value['amount'].' '.$value['uname']?></td>
											<td><?=$value['action_date']?></td>
											<td><?=$value['payed']?></td>
											<td><?=$value['idate']?></td>
											<td><?php if($value['state']==0){ ?>
												  <label id="confirmOrder" data-id="<?php echo $value['id']; ?>" class="label label-warning">جدید</label>
											<?php } else { ?>
												  <label class="label label-info">انجام شد</label>
											<?php } ?>
											</td>
											<td><?=$value['details']?></td>
											<td><center>
											<?php if(is_admin() || is_manager() && has_priviledge('4','can_edit')){ ?>
											<a href="#"class="hidden-print" id="edit" data-id="<?php echo $value['id']; ?>">
											<i class="fas fa-pen-square info font-16"></i>
										    </a>
											&nbsp;&nbsp;&nbsp; 
											<?php } ?>
											<?php if(is_admin() || is_manager() && has_priviledge('4','can_delete')){ ?>
										    <a href="<?=base_url();?>orderOmmit/<?=$this->my_encryption->do_encode($value['id']);?>" class="hidden-print">
											<i class="fas fa-trash-alt danger font-16" onClick="return doConfirm();"></i></a><?php } ?>
											</center>
											</td>
										</tr>
									 <?php $id++; }
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
        

 	<!-- Edit modal -->
	       <div id="edit_modal" class="modal fade in"  role="dialog" aria-labelledby="edit_modal" aria-hidden="true">
            <div class="modal-dialog2">
               <div class="modal-content">
                <div class="modal-header bg-blue3">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h5 class="modal-title"> ویرایش </h5>
                </div>
                <div class="modal-body" id="EditData"></div>   
               </div>
            </div>
        </div>
	<!-- /Edit modal -->   