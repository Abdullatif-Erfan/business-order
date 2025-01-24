<style>
.dt-button{ display:none !important;}
#table_filter{display:none !important;}
/* table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
	display:none !important;
} */
</style>
<?php $this->load->view('warehouseitem/scripts.php'); ?>
<!--  main content -->
<div class="main-panel">
	<div class="content">
		<div class="page-inner">
		<div class="row">
        <input type="hidden" name="warehouse_id" id="warehouse_id" value="<?=$warehouse_id?>" >

		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="card">
			<div class="card-header" style="padding: 10px;">
				<?php if(doesHaveAccessTo('gudam','create_records')) { ?>
						<a href="<?php echo base_url(); ?>oldJournalList">
							<button type="button" class="btn btn-sm mybtn m-r-10">
								<i class="fas fa-plus"></i> ثبت اجناس موجود
							</button>
						</a>

						<a href="<?php echo base_url(); ?>oldJournalList">
							<button type="button" class="btn btn-sm mybtn m-r-10">
								<i class="fas fa-plus"></i> انتقال اجناس 
							</button>
						</a>

						<a href="<?php echo base_url(); ?>oldJournalList">
							<button type="button" class="btn btn-sm mybtn m-r-10">
								<i class="fas fa-plus"></i>  معیار قلم 
							</button>
						</a>

						<a href="<?php echo base_url(); ?>oldJournalList">
							<button type="button" class="btn btn-sm mybtn m-r-10">
								<i class="fas fa-plus"></i>  ایجاد قلم
							</button>
						</a>

				<?php } ?>
                <span class="card-title">  لیست اجناس موجود در (<?=$selected_warehouse[0]['name'] ?? ''?>) </span>
			</div>
            
            <div  class="filterForm " id="searchWrapper1">  
					   <div class="col-md-12 col-sm-12 col-xs-12">
							<div class="row">
								<div class="col-md-4 col-sm-6 col-xs-6">
									<input type="text" id="item_name" placeholder="نام جنس"  class="form-control"> 
								</div>

								<div class="col-md-4 col-sm-6 col-xs-6">
                                        <div class="input-group" data-provide="datepicker">&nbsp;&nbsp;
                                        <div class="input-group-append">
                                        <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                                            data-targetselector="#idate" data-englishnumber="true">
                                            <span class="fa fa-calendar"></span> 
                                        </span>
                                        </div>
                                            <input class="form-control"  name="pay_date" id="idate"  
                                            data-targetselector="#idate" value="" 
                                            data-mddatetimepicker="true"  placeholder="تاریخ ثبت"  data-placement="right" data-englishnumber="true"  >
                                        </div>
								</div>


								<div class="col-md-3 col-sm-6 col-xs-6 m-b-4">
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
									<img  src="<?php echo base_url().'assets/img/header.jpg';?>" class="img-responsive visible-print" style="width:100%" alt="header Image">
								     </td>
								</tr>
								<tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
									<td colspan="10">
										<center>
                                           لیست اجناس موجود در (<?=$selected_warehouse[0]['name'] ?? ''?>)
									     </center>
								     </td>
								</tr>
                                <tr>
                                <th> شماره &nbsp; </th>		
                                <th> نام  </th>		
                                <th> آمد</th>	
                                <th> موجود</th>	
                                <th>واحد</th>									
                                <th>فیات خرید</th>	
                                <th>فیات فروش</th>
                                <th>مجموع خرید</th>
                                <th>مجموع موجود</th>	
                                <th>نوتفکیشن</th>
                                <th class="hidden-print">حذف</th>	
                              </tr>
							</thead>
						    <tfoot>
						     	<tr style="background:#eefcff">
									<td colspan="5">مجموع</td>
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
	
