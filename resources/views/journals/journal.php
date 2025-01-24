<style>
.dt-button{ display:none !important;}
#table_filter{display:none !important;}
/* table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
	display:none !important;
} */
</style>
<?php $this->load->view('journals/scripts.php'); ?>
<!--  main content -->
		<div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
				<div class="row">
					
		    	<div class="col-md-12 col-sm-12 col-xs-12 m-t--10">
				  <div class="card">
					<div class="card-header" style="padding: 11px 20px !important;">
			
						<?php if(doesHaveAccessTo('journal','create_records')) { ?>
							<a href="<?php echo base_url(); ?>journalAddForm">
								<button type="button" class="btn btn-sm mybtn">
									<i class="fas fa-plus"></i> ثبت ژورنال جدید
								</button>
							</a>

							<a href="<?php echo base_url(); ?>oldJournalList">
								<button type="button" class="btn btn-sm mybtn m-r-10">
									<i class="fas fa-plus"></i> بیلانس افتتاحیه
								</button>
							</a>

						<?php } else { ?>
								<button type="button" onclick="alert('صلاحیت ندارید')" class="btn btn-sm mybtn">
									<i class="fas fa-plus"></i> ثبت جدید
								</button>
						<?php } ?>
						
						<button type="button" class="btn btn-sm mybtn visible-xs" onclick="show_search_form(1)" >
							<i class="fas fa-filter"></i>
						</button>
						
					
						
					</div>


					<div  class="filterForm " id="searchWrapper1">  
					   <div class="col-md-12 col-sm-12 col-xs-12">
							<div class="row">
								<div class="col-md-2 col-sm-6 col-xs-6">
									<select  class="form-control select2 " style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" id="account_id"> 
										<option value=""> حساب </option>
										<?php foreach($accounts as $key => $value){ ?>
											<option value="<?=$value['id']?>">  <?=$value['name']?></option>
										<?php } ?>
									</select> 
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
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#start_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="start_date" id="start_date"  
                                            data-targetselector="#start_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ شروع"  data-placement="right" data-englishnumber="true"  >
                                        </div>
								</div>


								<div class="col-md-3 col-sm-6 col-xs-6">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#end_date" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="pay_date" id="end_date"  
                                            data-targetselector="#end_date" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ ختم / الی  امروز"  data-placement="right" data-englishnumber="true"  >
                                        </div>
								</div>

								<div class="col-md-1 col-sm-6 col-xs-6">
								       <input class="form-control"  id="code_number"  
									    placeholder="کد "  >
								</div>

								<div class="col-md-1 col-sm-6 col-xs-6">
								       <input class="form-control"  id="bill_number"  
									    placeholder="بل "  >
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
								        	روزنامچه حسابات  
									     </center>
								     </td>
								</tr>
								<tr>
									<th> شماره &nbsp; </th>
									<th> کد </th>
									<th> حساب </th>
									<th> جزییات </th>
									<th>  بردگی<br/>  نقد </th>
									<th>  رسیدگی<br/>  نقد  </th>
									<th>  بردگی  <br/>قرض  </th>
									<th>  رسیدگی <br/> قرض  / طلب  </th>
									<th>واحد </th>
									<th>تاریخ</th>
									<th class="hidden-print">جزییات</th>
								</tr>
							</thead>
						    <tfoot>
						     	<tr style="background:#eefcff">
									<td colspan="4">مجموع</td>
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
				<?php
				//  $this->load->view('component/footer-text.php');
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


		