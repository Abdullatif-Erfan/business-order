<script>
	$(document).on('click','#edit',function(e){
        e.preventDefault();
        var id=$(this).data('id');
        $.ajax({
        type:'POST',
        data:{id:id},
        url:"<?php echo base_url() . 'company/company/editModalData'; ?>",
        success: function(result)
        {
            $("#EditData").html(result);
            jQuery("#edit_modal").modal('show');
        }
        });
	});
	function updateAmount(base_amount, payed_amount, codes) 
	{
        jQuery("#updateModal").modal('show');
		$('#limit_label').html(base_amount - payed_amount);
		$('#payed_amount').val(payed_amount);
		$('#codes').val(codes);
		$('#base_amount').val(base_amount);
	}
	function noGreater()
	{
		var current_amount = parseFloat($('#current_amount').val());
        var payed_amount = parseFloat($('#payed_amount').val());
		var base_amount = parseFloat($('#base_amount').val());
		if(current_amount >=0 && current_amount + payed_amount <= base_amount)
        { 
            // var total = (return_amount * buy_unit_price).toFixed(2);
            // $('#price_return').val(total);
			$('#total_amount').val(current_amount + payed_amount);
            $('#subBtn').fadeIn(1); 
        }
        else 
        {
            alert('مبلغ پرداخت نادرست میباشد'); 
			$('#subBtn').fadeOut(100); 
        }
	}
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
						 <a href="<?php echo base_url(); ?>company">لیست  شرکت ها</a>
						</li>
					</ul>
				</div>
				<!-- /breadcrum -->
					
				<div class="row">
		        <!-- insertion -->
			          <div class="col-md-12 col-sm-12 col-xs-12">
					  <div class="box-tools"> 	
						<?php if(is_admin() || is_manager() && has_priviledge('3','can_add')){ ?>
							<a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
							<button type="button" class="btn btn-sm btn-primary" style="border-radius:0px;"> 
							<span class="fas fa-plus-square"></span>  &nbsp; ثبت  جدید </button>
							</a> 
						<?php } ?> 
						</div>
						<div id="add_form" class="add-form animated fadeInRight collapse" data-parent="#accordion" style="height: 0px;border-top:2px solid #89b4ea;" aria-expanded="false">
                        <div class="box-body">
						<?php   echo form_open_multipart('company/company/add'); ?>
						<input class="form-control"  value="<?=get_new_journal_code()?>" type="hidden"  name="code" required>
							<div class="form-body">
								<div class="row">
												
								<div class="col-md-4 col-sm-6 col-xs-12">
								  <div class="form-group form-floating-label">
									<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="from_account_id" required> 
										<option value=""> شرکت تهیه کننده را انتخاب نمایید</option>
										<?php foreach($customers as $key => $value)
										{ ?>
										<option value="<?php echo $value['account_id']; ?>"><?php echo $value['full_name']; ?></option>
										<?php } ?>
									</select>  
									</div>
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
									<div class="form-group form-floating-label">
									   <!-- <input class="form-control input-solid" id="base_amount" name="base_amount" type="number" step="0.01" required> -->
                                       <label for="amount" class="placeholder">مبلغ مجموعی خرید</label> 
									 </div>
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
									 <div class="form-group form-floating-label">
									   <input class="form-control input-solid" id="amount" name="amount" type="number" step="0.01" required>
                                       <label for="amount" class="placeholder">مبلغ پرداخت</label> 
									 </div>
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
									<div class="form-group form-floating-label">
									<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency" required> 
										<option value="">   واحد پولی را انتخاب نمایید</option>
										<?php foreach($currency as $key => $val)
										{ ?>
										<option value="<?php echo $val['id']; ?>"><?php echo $val['name']; ?></option>
										<?php } ?>
									</select>  
									</div>
									</div>


								<div class="col-md-4 col-sm-6 col-xs-12">
								  <div class="form-group form-floating-label">
									<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="to_account_id" required> 
										<option value="">   حساب پرداخت کننده را انتخاب نمایید</option>
										<?php foreach($account as $key => $value)
										{ ?>
										<option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
										<?php } ?>
									</select>  
									</div>
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12">
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="bill_no" name="bill_no" type="text"><label for="bill_no" class="placeholder"> نمبر بل</label>
									  </div> 
									</div>


									<div class="col-md-4 col-sm-6 col-xs-12">
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="doc" name="doc" type="file"><label for="doc" class="placeholder"> عکس بل</label>
									  </div> 
									</div>

									<div class="col-md-4 col-sm-6 col-xs-12 m-t-10">
                                        <div class="input-group mb-3" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#exampleInput00" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="todays_date" id="exampleInput00"  
                                            data-targetselector="#exampleInput00" value="<?=todays_date()?>" required
                                            data-mddatetimepicker="true"  placeholder="تاریخ ثبت"  data-placement="right" data-englishnumber="true"  >
                                        </div>
                                  </div>

									<div class="col-md-4 col-sm-4 col-xs-12">
									  <div class="form-group form-floating-label">
										<input class="form-control input-solid" id="details" name="details" type="text" required><label for="details" class="placeholder"> تفصیلات </label>
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
						<h4 class="card-title">  لیست شرکت ها  
						<!-- <span class="pull-left"><i class="fa fa-print" onclick="print_page();"></i></span> -->
						</h4>
					</div>

					<div class="card-body" id="print_area"><!-- card-body -->	
					<!-- print header -->
					<div class="col-md-12 col-sm-12 col-xs-12 hide">
					    <img src="<?php echo base_url().show('header','org_bio'); ?>" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
					    <center><h4 class="card-title"> لیست شرکت ها </h4></center>
					</div>	
					<!-- / end of print header -->
					<div class="table-responsive table_responsive" style="padding:5px;"><!-- table -->
					<table id="myTable" class="table table-striped table-bordered my_table">
							<thead>
							 <tr>
                    	        <th>#</th>								
								<th style="width:20%"> نام شرکت </th>
								<th>مبلغ خرید</th>
								<th>مجموع رسید</th>
								<th>باقی</th>
								<th>واحد پولی</th>
								<th>تاریخ</th>
								<th>بل نمبر</th>
								<th>جزییات</th>
								<th>بل</th>
								<th>ویرایش</th>
	                            <th>عملیات</th> 
							</tr>
								</thead>
								<tbody>
									<?php $id=1; $total = 0;
									 foreach($record as $key=>$value)
									 { ?>
										<tr>
											<td><?=$id?></td>
											<td><?=$value['account_name']?></td>
											<td>
											     <?=  number_format($value['base_amount']); ?>
											 </td>

											<td>
											 <?php if(intval($value['base_amount']) == $value['total_debit']) {
												 echo number_format($value['total_debit']);
											 } else { ?>
											    <button class="btn btn-info btn-sm paymentBtn" onclick="updateAmount(<?=$value['base_amount']?>,<?=$value['total_debit']?>,<?=$value['codes']?>);">
												   <?= number_format($value['total_debit']); ?>
												</button>
											<?php  } ?>
										    </td>


											<td>
											    <?= number_format($value['base_amount'] - $value['total_debit']); ?>
											</td>
											
											<td><?=$value['currency_name']?></td>
											<td><?=$value['inserted_short_date']?></td>
											<td><?=$value['bill_no']?></td>
											<td><?=$value['details']?></td>
											<td>
											<?php if(!empty($value['doc'])) { ?>
											<a target="_blank" href="<?=base_url().$value['doc']?>">
										   	 <i class="fas fa-file-pdf"></i>
											</a>
											<?php } ?>
											</td>



											<td>
											<center>
											<?php if(is_admin()){ ?>
											  <a href="<?=base_url();?>comPayEdit/<?=$this->my_encryption->do_encode($value['codes']);?>" class="hidden-print">
											      <i class="fas fa-pen-square info font-16"></i>
										       </a><?php } ?>
											</center>
											</td>

											<td>
											<center>
											<?php if(is_admin()){ ?>
										    <a href="<?=base_url();?>comOmmit/<?=$this->my_encryption->do_encode($value['codes']);?>" class="hidden-print">
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
		<div id="updateModal" class="modal fade in"  role="dialog" aria-labelledby="updateModal" aria-hidden="true">
            <div class="modal-dialog2">
               <div class="modal-content">
                <div class="modal-header bg-blue3">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h5 class="modal-title">  انتقال اجناس </h5>
                </div>
				<?php echo form_open('company/company/update_amount'); ?>
                <div class="modal-body">
				        <input type="hidden" id="total_amount" name="total_amount">
						<input type="hidden" id="codes" name="codes">
						<input type="hidden" id="base_amount">
						<input type="hidden" id="payed_amount" name="payed_amount">
						<h3> مبلغ قابل پرداخت <span id="limit_label"></span></h3>

						<input type="number" step="0.01"  onkeyup="noGreater()"  id="current_amount" name="current_amount" class="form-control" placeholder="مبلغ پرداخت جدید" required>
						<hr>

					</div> 
					<div class="modal-footer" style="background:#dcf5ff">
					<button type="submit" id="subBtn" name="submit" class="btn btn-info btn-sm m-l-10" >
                    <span class="btn-label"> <i class="fa fa-save"></i> </span>
                        ثبت 
					</button>
                <button type="button" class="btn btn-warning btn-sm m-l-10 pull-left" data-dismiss="modal">لغو </button>
				</div>  
				<?php echo form_close(); ?>
               </div>
            </div>
        </div>
	<!-- /Edit modal -->  
