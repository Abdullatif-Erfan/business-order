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
<!--  main content -->
		<div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				 <div class="row">
                     
		    	<div class="col-md-12 col-sm-12 col-xs-12 m-t--10">
				  <div class="card">
					<div class="card-header" style="padding: 11px 20px !important;">
					    <span id="reportTitle">
                           پرداخت معاشات 
                         </span> 

                         <span class="pull-left m-t--5"><a href="<?=base_url().'customer/0'?>">
                              <button class="printBtn pull-left m-t--5"><i class="fas fa-arrow-left"></i></button></a>
                         </span>
					</div>

             
                     <!-- / search -->	
				    <div class="card-body"><!-- card-body -->	
                    <div class="col-md-12">
                        <div class="row">
                           
                            <div class="col-md-12 col-sm-12 col-xs-12 border">
                             <br/>
                             <?php if(!empty($customer_id)) { $this->load->view('salary/addForm.php'); } ?>
                              <br />
                            
                                <!-- <button class="printBtn" onclick="print_page()"><i class="fas fa-print"></i></button>    -->
                           <div class="table-responsive table_responsive" style="padding:5px;"><!-- table -->
					       <table id="myTable" class="table table-striped table-bordered my_table">
							<thead>
							 <tr>
                    	        <th> شماره </th>								
								<th> کد</th>
								<th>نام کارمند </th>
								<th> مبلغ </th>
								<th style="width: 100px"> پرداخت کننده </th>
								<th>سال</th>
								<th>ماه</th>
								<!-- <th>تاریخ ثبت</th> -->
	                            <th  class="hidden-print">ویرایش</th> 
	                            <th  class="hidden-print">حذف</th> 
							</tr>
								</thead>
								<tbody>
									<?php $id=1; $total = 0;
									 foreach($record as $key=>$value)
									 { $total += $value['total_debit']; ?>
										<tr>
											<td><?=$id?></td>
											<td><?=$value['jcode']?></td>
											<td><a target="_blank" href="<?=base_url().'ledgerDetails/'.$value['accountId'].'/'.$value['currencys']?>"><?=$value['account_name']?></a></td>
											<td><?=number_format($value['total_debit'])?></td>
											<td><?=$value['to_account_name']?></td>
											<td><?=$value['year']?></td>
											<td><?=show_this_month($value['month'])?></td>
											<!-- <td><?=$value['inserted_short_date']?></td> -->
											
											<td class="hidden-print"><center>
											<?php if(is_admin() || is_manager() && has_priviledge('8','can_edit')){ ?>
											<a href="<?=base_url()."salary/salary/editForm/".$value['jcode']?>/<?=$customer_id?>">
											 <i class="fas fa-pen-square info font-18"></i>
										    </a><?php } ?>
											</center>
											</td>

                                            <td class="hidden-print">
                                            <center>
											<?php if(is_admin() || is_manager() && has_priviledge('8','can_delete')){ ?>
											<a href="<?=base_url();?>salary/salary/deleteSalary/<?=$this->my_encryption->do_encode($value['jcode']);?>/<?=$this->my_encryption->do_encode($value['customer_id']);?>" class="hidden-print">
										    <i class="fas fa-trash-alt danger font-18" onClick="return doConfirm();"></i></a><?php } ?>
											</center>
											</td>

										</tr>
									 <?php $id++; }
									?>
								</tbody>
								<tfoot>
									<tr  style="background:#eefcff">
										<td colspan="3"><center>مجموع</center></td>
										<td><?=number_format($total)?></td>
										<td colspan="5"></td>
									</tr>
								</tfoot>
						</table>
                                  
                                </div> <!-- /table responsive -->  

                            </div>
                        </div>
                    </div>			
					
					   </div> <!-- / card-body -->
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>

				<!-- footer -->
				<?php
				//  $this->load->view('component/footer-text.php');
				 ?>
				<!-- /footer -->
			</div>
		<!-- /main content -->
		


	


		