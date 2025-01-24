<style>
.dt-button{ display:none !important;}
#table_filter, #table2_filter{display:none !important;}
/* table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
	display:none !important;
} */
/* table.dataTable.row-border tbody th, table.dataTable.row-border tbody td.details, table.dataTable.display tbody th,
 table.dataTable.display tbody td.details{
	font-size:12px !important;
} */
.badge{
	font-size: 14px !important;
}
</style>
<?php 
$cur_account_id = $this->uri->segment(2);
$this->load->view('reports/cashflow/scripts.php');
 ?>
<!--  main content -->
		<div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
			 
		    	<div class="col-md-12 col-sm-12 col-xs-12 m-t-10">
				  <div class="card">
					<div class="card-header" style="padding: 11px 20px !important;">
					    جریان حساب نقده مشتریان
					</div>


					<?php $this->load->view('reports/cashflow/search.php'); ?>	

					<div class="card-body"><!-- card-body -->				
					<button class="printBtn" onclick="print_page()"><i class="fas fa-print"></i></button>
					<div class="table-responsive table_responsive" id="print_area" style="padding:5px;">
					<span class="pull-left visible-print">تاریخ چاپ : <?=show_full_date2()?></span>
					<table id="table2" class="display responsive nowrap table table-bordered my_table"  width="100%">
							<thead>
							  <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
									<td colspan="11">
									<img src="<?php echo base_url().show_where('header','org_bio',['is_active' => 1]); ?>" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
								     </td>
								</tr>
								<tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
									<td colspan="11">
										<center>
								        <h3 style="margin-top:10px">جریان حساب نقده</h3>
									     </center>
								     </td>
								</tr>
								<tr>
							     	<th> شماره &nbsp; </th>
									 <th> تاریخ </th>
									 <th> حساب </th>
									 <th> تفصیلات </th>
									 <th> بردگی <br/> نقد </th>
									 <th> رسیدگی  <br/> نقد  </th>
									 <th> بردگی <br/>  قرض  </th>
									 <th> رسیدگی <br/> قرض (طلب) </th>
									 <th>  بیلانس  </th>
									 <th>واحد پولی</th>
									 <th> کد </th>
								</tr>
							</thead>
							<tfoot>
								<tr style="background:#eefcff">
									<td colspan='2'></td>
									<td colspan='2'></td>
									<td colspan='2'></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tfoot>
						</table>
						</div> <!-- /table responsive -->  
					   </div> <!-- / card-body -->
				     </div>
				   </div>	
				  </div>
		       </div>
		    </div>

				<!-- footer -->
				<?php
				 $this->load->view('component/footer-text.php');
				 ?>
				<!-- /footer -->
			</div>
		<!-- /main content -->
		

		
		 <!-- detailsModal -->
		     <div class="modal fade in" id="detailsModal" role="dialog">
                  <div class="modal-dialog2">
                    <div class="modal-content">
                      <div class="modal-header">
                         <h4 class="modal-title pull-left" style="color: #3b6c08 !important;margin-top:-5px;">جزییات</h4>
						 <button type="button" class="pull-left close" style="color:red" data-dismiss="modal">&times;</button>
                      </div>
                      <div class="modal-body">
                      <div class="row w100" id="memberDetailsData">
                      </div>
                      </div>
                      <div class="modal-footer">
                          <button type="button" class="btn btn-danger" data-dismiss="modal">بسته کردن </button>
                      </div>
                    </div>
                  </div>
                </div>
        <!-- /detailsModal -->


		