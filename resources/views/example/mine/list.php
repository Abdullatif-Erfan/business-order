<style>
.dt-button{ display:none !important;}
#table_filter{display:none !important;}
/* table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
	display:none !important;
} */
</style>
<?php $this->load->view('example/mine/scripts.php'); ?>
<!--  main content -->
		<div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
		    	<div class="col-md-12 col-sm-12 col-xs-12 m-t--10">
				  <div class="card">
					<div class="card-header" style="padding: 11px 20px !important;">
					  لیست کشور ها
						<?php if(is_admin()) { ?>
							<!-- <a href="<?php echo base_url(); ?>example/mine/add">
								<button type="button" class="btn btn-sm mybtn">
									<i class="fas fa-plus"></i> ثبت  جدید
								</button>
							</a> -->
						<?php } else { ?>
								<!-- <button type="button" class="btn btn-sm mybtn">
									<i class="fas fa-plus"></i> ثبت جدید
								</button> -->
						<?php } ?>
						
					</div>


					<div  class="filterForm " id="searchWrapper1">  
					   <div class="col-md-12 col-sm-12 col-xs-12">
							<div class="row">
								<div class="col-md-10 col-sm-10 col-xs-10">
									<input class="form-control"  id="name_search"  placeholder="جستجو به اساس نام  "  >
								</div>
								<div class="col-md-2 col-sm-2 col-xs-2">
									<button class="btn  mybtn search_btn form-control"   id="btn-filter">
									   <i class="fa fa-search"></i>
									</button>
								</div>
							</div>
						</div> 
					   </div>  <!-- /id="filter_form" -->	

					<div class="card-body"><!-- card-body -->				
					<div class="table-responsive table_responsive" id="print_area" style="padding:5px;">
					<table id="table" class="display responsive nowrap table table-bordered my_table"  width="100%">
							<thead>
								<tr>
									<th> شماره &nbsp; </th>
									<th> نام کشور </th>
									<th> ویرایش  </th>
									<th> حذف </th>
								</tr>
							</thead>
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
				//  $this->load->view('component/footer-text.php');
				 ?>
				<!-- /footer -->
			</div>
		<!-- /main content -->
		

		