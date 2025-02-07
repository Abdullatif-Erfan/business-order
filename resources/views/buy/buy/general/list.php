<style>
.dt-button{ display:none !important;}
#table_filter{display:none !important;}
/* table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
	display:none !important;
} */
</style>
<?php $this->load->view('buy/buying/general/scripts.php'); ?>
<!--  main content -->
<div class="main-panel">
	<div class="content">
		<div class="page-inner">
		<div class="row ">

		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="card">
			<div class="card-header" style="padding: 10px; text-align:center;">
                
                <a href="<?php echo base_url(); ?>showBuyingForm" class="pull-right">
                    <button type="button" class="btn btn-sm mybtn">
                        <i class="fas fa-plus"></i>  ثبت خریداری جدید 
                    </button>
                </a>
                <span class="card-title"> لیست خرید </span>
			</div>


            <div  class="filterForm " id="searchWrapper1">  
					   <div class="col-md-12 col-sm-12 col-xs-12">
							<div class="row">
								<div class="col-md-3 col-sm-6 col-xs-6">
									<input type="text" id="customer_name" placeholder="فروشنده"  class="form-control"> 
								</div>

								<div class="col-md-2 col-sm-6 col-xs-6">
									<input type="text" id="pre_list_name" placeholder="نوع خرید"  class="form-control"> 
								</div>

								<div class="col-md-2 col-sm-6 col-xs-6 m-b-4">
							    	<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id"  > 
                                       <option value="">  واحد پولی </option>
                                        <?php foreach($currency as $key => $value){ ?>
                                            <option value="<?=$value['id']?>"> <?=$value['name']?></option>
                                        <?php } ?>
                                    </select> 
								</div>


								


								<div class="col-md-2 col-sm-6 col-xs-6">
								       <input class="form-control"  id="bill_number"  
									    placeholder="بل نمبر"  >
								</div>

								<div class="col-md-1 col-sm-6 col-xs-6">
									<button class="btn  mybtn search_btn form-control"   id="btn-filter">
									   <i class="fa fa-search"></i>
									</button>
								</div>
							</div>
						</div> 
					   </div>  <!-- /id="filter_form" -->	

            
                 <div class="card-body"><!-- card-body -->				
					<button class="printBtn" onclick="print_page()"><i class="fas fa-print"></i></button>
					<div class="table-responsive table_responsive" id="print_area" style="padding:5px;">
					<span class="pull-left visible-print">تاریخ چاپ : <?=show_full_date2()?></span>
					<table id="table" class="display responsive nowrap table table-bordered my_table"  width="100%">
							<thead>
							    <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
									<td colspan="11">
									<img src="<?php echo base_url().show_where('header','org_bio',['is_active' => 1]); ?>" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
								     </td>
								</tr>
								<tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
									<td colspan="11">
										<center>
								        	 لیست خرید  
									     </center>
								     </td>
								</tr>
                                <tr>
                                    <th> شماره &nbsp; </th>
                                    <th> نمبربل </th>		
                                    <th> فروشنده  </th>	
                                    <th>  نوع خرید  </th>	
                                    <th>تعداد </th>
                                    <th>واحد</th>				
                                    <th> فی واحد</th>				
                                    <th>قیمت <br /> مجموعی </th>		
                                    <th> رسید نقد </th>		
                                    <th> قرض </th>		
                                    <th>تاریخ</th>	
                                    <th class="hidden-print"> انتقال</th>	
                                    <th class="hidden-print">جزییات</th>	
                              </tr>
							</thead>
						    <tfoot>
						     	<tr style="background:#eefcff">
									<td colspan="6">مجموع</td>
									<td></td>
									<td></td>
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
			<?php $this->load->view('component/footer-text.php'); ?>
			<!-- /footer -->
		</div>
	<!-- /main content -->
	
