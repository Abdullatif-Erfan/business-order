<?php  $cur_year = cur_year(); ?>
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
						<h4 class="card-title">   راپور 12 ماه سال  ( <?=$cur_year?> )
						<span class="pull-left"><i class="fa fa-print" onclick="print_page();"></i></span>
						</h4>
				
                    <!-- filter area -->
                    <div class="col-md-12 col-sm-12 col-xs-12 filter_cover m-t-10 m-b-5" id="filterArea">
                    <?php  echo form_open('yearly'); ?>
                        <div class="row">

                           <div class="col-md-10 col-sm-10 col-xs-8">
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
					    <center><h4 class="card-title"> راپور 12 ماه  سال ( <?= $cur_year ?> ) </h4></center>
					</div>	
					<!-- / end of print header -->
				    
                      <div class="table_responsive" style="padding:5px; min-width: 800px">
                        <table class="table table-bordered table-striped dataTable my_table" style="width:100%">
                        <thead>
                                <tr>
                                    <th rowspan="3"><center> سال</center></th>
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
                                // Aggregate stock and item values by year  
                                $stock_total = [];  
                                $sales_total = [];  
                                $sales_cur_pay = [];  
                                $sales_remained = [];  
                                $sales_profit = [];  

                                $bought_total_bought = [];  
                                $bought_cur_pay = [];  
                                $bought_remained = [];  
                                $bought_trans_spend = [];  

                                // موجودی گدام  
                                foreach ($warehouse_items as $warehouse_items_item) 
                                {  
                                    if (!isset($stock_total[$warehouse_items_item['stock_year']])) {  
                                        $stock_total[$warehouse_items_item['stock_year']] = 0;  
                                    }  
                                    $stock_total[$warehouse_items_item['stock_year']] += $warehouse_items_item['total'];  
                                }  

                                // فروشات  
                                foreach ($warehouse_sales as $warehouse_sales_item) 
                                {  
                                    $year = $warehouse_sales_item['year'];  
                                
                                    // Initialize totals for the year if not already set  
                                    if (!isset($sales_total[$year])) {  
                                        $sales_total[$year] = 0;  
                                        $sales_cur_pay[$year] = 0;  
                                        $sales_remained[$year] = 0;  
                                        $sales_profit[$year] = 0;  
                                    }  
                                
                                    // Safely add values, ensuring they are numeric  
                                    $sales_total[$year] += isset($warehouse_sales_item['total_sales']) ? (float)$warehouse_sales_item['total_sales'] : 0;  
                                    $sales_cur_pay[$year] += isset($warehouse_sales_item['cur_pay']) ? (float)$warehouse_sales_item['cur_pay'] : 0;  
                                    $sales_remained[$year] += isset($warehouse_sales_item['remained']) ? (float)$warehouse_sales_item['remained'] : 0;  
                                    $sales_profit[$year] += isset($warehouse_sales_item['profit']) ? (float)$warehouse_sales_item['profit'] : 0;  
                                }  

                                // خریدها  
                                foreach ($bought_items as $bought_items_item) {  
                                    if (!isset($bought_total_bought[$bought_items_item['year']])) {  
                                        $bought_total_bought[$bought_items_item['year']] = 0;  
                                        $bought_cur_pay[$bought_items_item['year']] = 0;  
                                        $bought_remained[$bought_items_item['year']] = 0;  
                                        $bought_trans_spend[$bought_items_item['year']] = 0;  
                                    }  
                                    $bought_total_bought[$bought_items_item['year']] += $bought_items_item['total_bought'];  
                                    $bought_cur_pay[$bought_items_item['year']] += $bought_items_item['cur_pay'];  
                                    $bought_remained[$bought_items_item['year']] += $bought_items_item['remained'];  
                                    $bought_trans_spend[$bought_items_item['year']] += $bought_items_item['trans_spend'];  
                                }  

                                $total_income_yearly = []; // Initialize an empty array to store yearly total incomes  
                                $final_income = 0;  
                                $total_stock = 0;
                                foreach ($sold_years as $key => $value) {  
                                    $stock_totals = isset($stock_total[$value['year']]) ? $stock_total[$value['year']] : 0; 
                                    $sales_totals = isset($sales_total[$value['year']]) ? $sales_total[$value['year']] : 0;  
                                    $sales_cur_pays = isset($sales_cur_pay[$value['year']]) ? $sales_cur_pay[$value['year']] : 0;  
                                    $sales_remaineds = isset($sales_remained[$value['year']]) ? $sales_remained[$value['year']] : 0;  
                                    $sales_profits = isset($sales_profit[$value['year']]) ? $sales_profit[$value['year']] : 0;  
                                    $bought_total_boughts = isset($bought_total_bought[$value['year']]) ? $bought_total_bought[$value['year']] : 0;  
                                    $bought_cur_pays = isset($bought_cur_pay[$value['year']]) ? $bought_cur_pay[$value['year']] : 0;  
                                    $bought_remaineds = isset($bought_remained[$value['year']]) ? $bought_remained[$value['year']] : 0;  
                                    $bought_trans_spends = isset($bought_trans_spend[$value['year']]) ? $bought_trans_spend[$value['year']] : 0;  
                                    $total_stock += $stock_totals;

                                    ?>  
                                    <tr>  

                                        <td><?= $value['year']; ?></td>  
                                        <td><?= sprintf("%.2f", $stock_totals) ?></td>  
                                        <td><?= sprintf("%.2f", $sales_totals) ?></td>  
                                        <td><?= sprintf("%.2f", $sales_cur_pays) ?></td>  
                                        <td><?= sprintf("%.2f", $sales_remaineds) ?></td>  
                                        <td><?= sprintf("%.2f", $sales_profits) ?></td>  
                                        <td><?= sprintf("%.2f", $bought_total_boughts) ?></td>
                                        <td><?= sprintf("%.2f", $bought_cur_pays) ?></td> 
                                        <td><?= sprintf("%.2f", $bought_remaineds) ?></td>    
                                        <td><?= sprintf("%.2f", $bought_trans_spends) ?></td>  
                                    </tr>  
                                    <?php  
                                }  
                                ?>  
                            </tbody> 
                            <tfoot>  
                                <tr style="background:#eefcff">  
                                    <td>مجموع</td>  
                                    <td><?= !empty($total_stock) ? number_format($total_stock,2) : '0.00' ?></td>  
                                    <td><?= !empty($sales_total) ? sprintf("%.2f", array_sum($sales_total)) : '0.00' ?></td> 
                                    <td><?= !empty($sales_cur_pay) ? sprintf("%.2f", array_sum($sales_cur_pay)) : '0.00' ?></td> 
                                    <td><?= !empty($sales_remained) ? sprintf("%.2f", array_sum($sales_remained)) : '0.00' ?></td> 
                                    <td><?= !empty($sales_profit) ? sprintf("%.2f", array_sum($sales_profit)) : '0.00' ?></td> 
                                    <td><?= !empty($bought_total_bought) ? sprintf("%.2f", array_sum($bought_total_bought)) : '0.00' ?></td> 
                                    <td><?= !empty($bought_cur_pay) ? sprintf("%.2f", array_sum($bought_cur_pay)) : '0.00' ?></td> 
                                    <td><?= !empty($bought_remained) ? sprintf("%.2f", array_sum($bought_remained)) : '0.00' ?></td> 
                                    <td><?= !empty($bought_trans_spend) ? sprintf("%.2f", array_sum($bought_trans_spend)) : '0.00' ?></td> 
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
        
