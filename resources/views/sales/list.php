<style>
.dt-button{ display:none !important;}
#table_filter{display:none !important;}
/* table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
	display:none !important;
} */
</style>
<?php $this->load->view('sales/scripts.php'); ?>
<!--  main content -->
<div class="main-panel">
	<div class="content">
		<div class="page-inner">
		<div class="row ">

		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="card">
			<div class="card-header" style="padding: 10px;">
                <span class="card-title">  لیست فروشات </span>
			</div>
            
            <div  class="filterForm " id="searchWrapper1">  
					   <div class="col-md-12 col-sm-12 col-xs-12">
							<div class="row">
                                <div class="col-md-3 col-sm-6 col-xs-6">
									<input type="text" id="customer_name" placeholder="نام مشتری"  class="form-control"> 
								</div>
								<div class="col-md-3 col-sm-6 col-xs-6">
									<input type="text" id="item_name" placeholder="نام جنس"  class="form-control"> 
								</div>

								<div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#idate" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="pay_date" id="idate"  
                                            data-targetselector="#idate" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ فروش"  data-placement="right" data-englishnumber="true"  >
                                        </div>
								</div>


								<div class="col-md-2 col-sm-6 col-xs-6 m-b-4">
							    	<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="currency_id"  > 
                                       <option value="">  واحد پولی </option>
                                        <?php foreach($currency as $key => $value){ ?>
                                            <option value="<?=$value['id']?>"> <?=$value['name']?></option>
                                        <?php } ?>
                                    </select> 
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
									<td colspan="10">
									<img src="<?php echo base_url().show_where('header','org_bio',['is_active' => 1]); ?>" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
								     </td>
								</tr>
								<tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
									<td colspan="10">
										<center>
                                          لیست فروشات
									     </center>
								     </td>
								</tr>
                                <tr>
                                <th>  شماره &nbsp; </th>	
                                <th>  بل نمبر </th>	
                                <th>مشتری </th>	
                                <th>  جنس  </th>	
                                <th>تعداد</th>	
                                <th>واحد</th>									
                                <th>فیات فروش</th>	
                                <th> مفاد</th>
                                <th>مجموع </th>	
                                <th>تاریخ </th>	
                                <th>جزییات </th>
                              </tr>
							</thead>
						    <tfoot>
						     	<tr style="background:#eefcff">
									<td colspan="7">مجموع</td>
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
	
