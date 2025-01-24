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
						 <a href="<?php echo base_url(); ?>monthly">راپور 12 ماه  </a>
						</li>
					</ul>
				</div>
				<!-- /breadcrum -->
					
				<div class="row">
		
		    	<div class="col-md-12 col-sm-12 col-xs-12">
				  <div class="card">
					<div class="card-header">
						<h4 class="card-title">   راپور 12 ماه سال  ( <?=$year?> )
						<span class="pull-left"><i class="fa fa-print" onclick="print_page();"></i></span>
						</h4>
				
                    <!-- filter area -->
                    <div class="col-md-12 col-sm-12 col-xs-12 filter_cover m-t-10 m-b-5" id="filterArea">
                    <?php  echo form_open('monthly'); ?>
                        <div class="row">

                            <div class="col-md-5 col-sm-5 col-xs-4">
                                     <select  class="form-control select2" 
                                        style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency_id"> 
                                       <option value="<?=$currency_id?>"><?=$currency_name?></option>
                                        <option value=""> --- انتخاب واحد پولی --- </option>
                                        <?php foreach($currency as $key => $v)
                                        { ?>
                                            <option  value="<?php echo $v['id']; ?>">
                                            <?php echo $v['name']; ?></option>
                                        <?php } ?>
                                    </select>
                               </div>

                            <div class="col-md-5 col-sm-5 col-xs-4">
                                    <select  class="form-control select2" 
                                        style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="year"> 
                                        <option value="<?=$year?>"><?=$year?></option>
                                        <option value=""> انتخاب سال دیگر </option>
                                        <?php for($i=1400; $i<=1440; $i++)
                                        { ?>
                                            <option  value="<?php echo $i; ?>">
                                            <?php echo $i; ?></option>
                                        <?php } ?>
                                    </select>
                            </div>

                            <div class="col-md-2 col-sm-2 col-xs-4">
                                <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-6">
                                    <button type="submit" id="btn-filter" class="btn btn-info2 form-control btn-sm" style="border-left: 4px solid #fca505;">
                                        <i class="fa fa-search" style='font-size:12px;color:#ee70c9 !important;'> </i> </span> 
                                    </button>
                                </div>
                                </div>
                            </div>

                            </div>
                            </form>
                        </div>
                        <!-- / filter area -->
                    </div>


					<div class="card-body" id="print_area" style="width: 100%;overflow-x: scroll;"><!-- card-body -->	
					<!-- print header -->
					<div class="col-md-12 col-sm-12 col-xs-12 hide">
					    <img src="<?php echo base_url().show_where('header','org_bio',['is_active' => 1]); ?>" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
					    <center><h4 class="card-title"> راپور 12 ماهه  سال ( <?=$year?> ) </h4></center>
					</div>	
					<!-- / end of print header -->
                      <div class="table_responsive" style="padding:5px; min-width: 800px">
                        <table class="table table-bordered table-striped dataTable my_table" style="width:100%">
                            <thead>
                                <tr>
                                    <th rowspan="3"><center> ماه  </center></th>
                                </tr>
                                <tr>
                                    <th> <center>  گدام </center> </th>
                                    <th colspan="4"> <center>  فروشات </center> </th>
                                    <th colspan="4"> <center>  خرید </center> </th>
                                </tr>
                                <tr>
                                <th style="border-top: 1px solid #fff !important;"><center>موجودی گدام</center></th>
                                <th style="border-top: 1px solid #fff !important;"><center>فروشات </center></th>
                                <th style="border-top: 1px solid #fff !important;"><center>دریافت فروشات</center></th>
                                <th style="border-top: 1px solid #fff !important;"><center>طلب فروشات</center></th>
                                <th style="border-top: 1px solid #fff !important;"><center>مفاد فروشات</center></th>
                                <th style="border-top: 1px solid #fff !important;"><center>  خرید</center></th>
                                <th style="border-top: 1px solid #fff !important;"><center>  پرداخت خرید </center></th>
                                <th style="border-top: 1px solid #fff !important;"><center>  قرض خرید </center></th>
                                <th style="border-top: 1px solid #fff !important;"><center>   ترانسپورت </center></th>

                                </tr>
                            </thead>
                            <tbody>
                             <?php 
                             $cur_month = cur_month();

                             // موجودی گدام
                             foreach ($warehouse_items as $warehouse_items_item) {
                                $stock_month = intval($warehouse_items_item['stock_month']);
                                if ($warehouse_items_item['stock_year'] == $year && $stock_month >= 1 && $stock_month <=12) {
                                    $stock_total = $warehouse_items_item['total'];
                                    $stock_total_values[$stock_month] = $stock_total;
                                }
                              }

                                // فروشات
                                foreach ($warehouse_sales as $warehouse_sales_item) {
                                    $sales_month = intval($warehouse_sales_item['month']);
                                    if ($warehouse_sales_item['year'] == $year &&  $sales_month >= 1 && $sales_month <= 12) {
                                        $sales_total_sales = $warehouse_sales_item['total_sales'];
                                        $sales_cur_pay  = $warehouse_sales_item['cur_pay'];
                                        $sales_remained = $warehouse_sales_item['remained'];
                                        $sales_profit   = $warehouse_sales_item['profit'];

                                        $sales_total_sales_values[$sales_month] = $sales_total_sales;
                                        $sales_cur_pay_values[$sales_month] = $sales_cur_pay;
                                        $sales_remained_values[$sales_month] = $sales_remained;
                                        $sales_profit_values[$sales_month] = $sales_profit;

                                    }
                                }

                                // خریدها
                                foreach ($bought_items as $bought_items_item) {
                                    $bought_month = intval($bought_items_item['month']);
                                    if ($bought_items_item['year'] == $year &&  $bought_month >= 1 && $bought_month <= 12) {
                                        $bought_total_bought = $bought_items_item['total_bought'];
                                        $bought_cur_pay  = $bought_items_item['cur_pay'];
                                        $bought_remained = $bought_items_item['remained'];
                                        $bought_trans_spend   = $bought_items_item['trans_spend'];

                                        $bought_total_bought_values[$bought_month] = $bought_total_bought;
                                        $bought_cur_pay_values[$bought_month] = $bought_cur_pay;
                                        $bought_remained_values[$bought_month] = $bought_remained;
                                        $bought_trans_spend_values[$bought_month] = $bought_trans_spend;
                                    }
                                }
                                
                                  
                                $total_income_monthly = array(); // Initialize an empty array to store monthly total incomes
                                $final_income = 0;
                                $total_stock = 0;

                                for ($i = 1; $i <= 12; $i++) {
                                    // $stock_total = isset($stock_total_values[$i]) ? $stock_total_values[$i] : '';                                
                                    // $final_sales_total = isset($sales_total_sales_values[$i]) ? $sales_total_sales_values[$i] : '';

                                    $stock_total = isset($stock_total_values[$i]) && is_numeric($stock_total_values[$i]) ? floatval($stock_total_values[$i]) : 0.0;  
                                    $final_sales_total = isset($sales_total_sales_values[$i]) && is_numeric($sales_total_sales_values[$i]) ? floatval($sales_total_sales_values[$i]) : 0.0;  

                                
                                    $final_sales_cur_pay = isset($sales_cur_pay_values[$i]) ? $sales_cur_pay_values[$i] : '';
                                    $final_sales_remained = isset($sales_remained_values[$i]) ? $sales_remained_values[$i] : '';
                                    $final_sales_profit = isset($sales_profit_values[$i]) ? $sales_profit_values[$i] : '';
    
                                    $final_total_bought = isset($bought_total_bought_values[$i]) ? $bought_total_bought_values[$i] : '';
                                    $final_bought_cur_pay = isset($bought_cur_pay_values[$i]) ? $bought_cur_pay_values[$i] : '';
                                    $final_bought_remained = isset($bought_remained_values[$i]) ? $bought_remained_values[$i] : '';
                                    $final_bought_trans_spend = isset($bought_trans_spend_values[$i]) ? $bought_trans_spend_values[$i] : '';
                                    $total_stock +=  $stock_total - $final_sales_total;
                                
                                ?>
                                <tr style="<?=$cur_month==$i ? "background-color:#daf2f8": "" ?>">
                                    <td><?=$i.' : '?> <?=show_this_month($i,$year)?> </td>
                                    <td><?= sprintf("%.2f",$stock_total - $final_sales_total) ?> </td>
                                    <td><?= sprintf("%.2f",$final_sales_total) ?></td>                   
                                    <td><?= sprintf("%.2f",$final_sales_cur_pay) ?></td> 
                                    <td><?= sprintf("%.2f",$final_sales_remained) ?></td>   
                                    <td><?= sprintf("%.2f",$final_sales_profit) ?></td>                                       
                                    <td><?= sprintf("%.2f",$final_total_bought) ?></td>                                                                           
                                    <td><?= sprintf("%.2f",$final_bought_cur_pay) ?></td> 
                                    <td><?= sprintf("%.2f",$final_bought_remained) ?></td> 
                                    <td><?= sprintf("%.2f",$final_bought_trans_spend) ?></td> 
                                </tr>
                             <?php } ?>
                            </tbody>
                            <tfoot>
                               <tr style="background:#eefcff">
                                    <td>مجموع</td>
                                    <td><?= !empty($total_stock)  ? number_format($total_stock,2) : '0.00' ?></td>
                                    <td><?= !empty($sales_total_sales_values) ? sprintf("%.2f",array_sum($sales_total_sales_values)) : '0.00' ?></td>
                                    <td><?= !empty($sales_cur_pay_values) ? sprintf("%.2f",array_sum($sales_cur_pay_values)) : '0.00' ?></td>
                                    <td><?= !empty($sales_remained_values) ? sprintf("%.2f",array_sum($sales_remained_values)) : '0.00' ?></td>
                                    <td><?= !empty($sales_profit_values) ? sprintf("%.2f", array_sum($sales_profit_values)) : '0.00' ?></td>
                                    <td><?= !empty($bought_total_bought_values) ? sprintf("%.2f", array_sum($bought_total_bought_values)) : '0.00' ?></td>
                                    <td><?= !empty($bought_cur_pay_values) ? sprintf("%.2f", array_sum($bought_cur_pay_values)) : '0.00' ?></td>
                                    <td><?= !empty($bought_remained_values) ? sprintf("%.2f", array_sum($bought_remained_values)) : '0.00' ?></td>
                                    <td><?= !empty($bought_trans_spend_values) ? sprintf("%.2f", array_sum($bought_trans_spend_values)) : '0.00' ?></td>
                                </tr>
                            </tfoot>
                           
                            
                        </table>
                    </div>

					
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