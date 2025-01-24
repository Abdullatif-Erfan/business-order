<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            responsive: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "همه"]],
            pageLength: 10,
        });
    });
</script>
<style>
.dt-button{ display:none !important;}
#table_filter{display:none !important;}
table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>td:first-child:before, table.dataTable.dtr-inline.collapsed>tbody>tr[role="row"]>th:first-child:before{
	display:none !important;
}
</style>
<?php $this->load->view('reports/numToChar.php'); ?>
<!--  main content -->
		<div class="main-panel">
		   <div class="content">
			  <div class="page-inner">
        <input type="hidden" id="base_url" value="<?php echo base_url(); ?>" >



				<div class="row">
			  
		    	<div class="col-md-12 col-sm-12 col-xs-12 m-t--10">
				  <div class="card">
				
                  <div class="card-header" style="padding: 4px 20px !important;">
                   <h3> بیلانس شیت</h3>
                 </div>

                <!-- search and filter area -->
                <?php echo form_open('reports/balancesheet'); ?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-6 m-b-10 m-t-10">
                            <select class="form-control select2 col-md-5 col-sm-6 col-xs-12" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="account_id" id="account_id"> 
                                <?php if(!empty($cur_account[0]['name'])) { ?>
                                    <option value="<?=$cur_account[0]['id']?>"><?=$cur_account[0]['name']?></option>
                                <?php } ?>
                                <option value=""> انتخاب مشتری</option>
                                <?php foreach($account as $key => $value){ ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-3 col-sm-6 col-xs-6 m-b-10 m-t-10">
                            <select class="form-control select2 col-md-5 col-sm-6 col-xs-12" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="currency_id" id="currency_id"> 
                                <?php if(!empty($cur_currency[0]['name'])) { ?>
                                    <option value="<?=$cur_currency[0]['id']?>"><?=$cur_currency[0]['name']?></option>
                                <?php } ?>
                                <option value=""> انتخاب واحد پولی</option>
                                <?php foreach($currency as $key => $value){ ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-3  col-sm-6 col-xs-6 m-t-10">
                            <select  class="form-control select2" 
                                style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="searched_year"> 
                                <option value="<?=$searched_year?>"><?=$searched_year?></option>
                                <option value=""> انتخاب سال دیگر </option>
                                <?php for($i=1400; $i<=1440; $i++)
                                { ?>
                                    <option  value="<?php echo $i; ?>">
                                    <?php echo $i; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2 col-sm-6 col-xs-6 m-b-4 m-t-10">
                            <button class="btn mybtn search_btn form-control" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>

                        <div class="col-md-1 col-sm-6 col-xs-6 m-b-4 m-t-10">
                            <button class="printBtn " onclick="print_page()" style="padding: 6px 12px;" >
                            <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
                <!-- / search and filter area -->

           <div class="card-body" id="print_area" style="width: 100%;overflow-x: scroll;">
           <!-- card-body -->	
					<!-- print header -->
					<div class="col-md-12 col-sm-12 col-xs-12 hide">
					    <img src="<?php echo base_url().show_where('header','org_bio',['is_active' => 1]); ?>" alt="navbar brand" class="navbar-brand" style="width: 100% !important;">
					    <center><h4 class="card-title"> بیلانس شیت  </h4></center>
					</div>	
					<!-- / end of print header -->
                    <div class="table-responsive table_responsive" style="padding:5px;">
                    <table id="myTable" class="table table-bordered table-striped table-hover" style="width:100%">
                    
                        <thead>
                            <tr>
                                <th rowspan="2">شماره</th>
                                <th rowspan="2">حسابات</th>
                                <th colspan="2" ><center>سال های قبل</center></th>
                                <th colspan="2"> <center>سال فعلی  ( <?=$searched_year?>     ) </center></th>
                                <th><center>مجموع حسابات</center></th>
                            </tr>
                            <tr>
                               <th style="border-top: 1px solid #fff !important;"><center>رسیدگی (طلب)</center></th>
                                <th style="border-top: 1px solid #fff !important;"><center>بردگی (باقی)</center></th>

                                <th style="border-top: 1px solid #fff !important;"><center>رسیدگی (طلب)</center></th>
                                <th style="border-top: 1px solid #fff !important;"><center>بردگی (باقی)</center></th>
                                <th style="border-top: 1px solid #fff !important;"><center> بیلانس  </center></th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php $id =1;
                                $final_others = 0;
                                $final_own= 0;
                                $final_total = 0;
                                $prev_years_payed = 0;     
                                $prev_years_recieved = 0;   
                                $cur_years_payed = 0;
                                $cur_years_recieved = 0;
                                $final_result = 0;

                                foreach($balancesheet as $key => $value) 
                                {
                                    $final_out = floatval($value['prev_years_payed']) + floatval($value['cur_year_payed']); 
                                    $final_in = floatval($value['prev_years_recieved']) + floatval($value['cur_year_recieved']);
                                    $final_total = $final_in - $final_out;

                                    // find total
                                    $prev_years_payed += floatval($value['prev_years_payed']);
                                    $prev_years_recieved += floatval($value['prev_years_recieved']);
                                    $cur_years_payed += floatval($value['cur_year_payed']);
                                    $cur_years_recieved += floatval($value['cur_year_recieved']);
                                    $final_result += $final_total;

                                    ?>
                                    <tr>
                                        <td><?=$id?></td>
                                        <td>  
                                            <a target="_blank" href="<?=base_url().'ledgerDetails/'.$value['accountId'].'/1'?>">
                                             <?=$value['name']?>
                                            </a>
                                        </td>
                                        <td><?=$value['prev_years_recieved']?></td>
                                        <td><?=$value['prev_years_payed']?></td>
                                        <td><?=$value['cur_year_recieved']?></td>
                                        <td><?=$value['cur_year_payed']?></td>
                                        <td><?=$final_total?></td>
                                    </tr>
                                    <?php $id++; } ?>
                            </tbody>
                            <tfoot>
                                <tr style="background:#eefcff">
                                    <td colspan="2">مجموع</td>
                                    <td><?=$prev_years_recieved?></td>
                                    <td><?=$prev_years_payed?></td>
                                    <td><?=$cur_years_recieved?></td>
                                    <td><?=$cur_years_payed?></td>
                                    <td><?=$final_result?>
                                        
                                            <?php if(floatval($final_result) === 0) { ?>
                                                <badge class="badge badge-success"> تصفیه </badge>
                                           <?php } else if(floatval($final_result) < 0) { ?>
                                                <badge class="badge badge-info"> درمجموع شرکت طلب است </badge>
                                            <?php } else { ?>
                                                <badge class="badge badge-danger"> درمجموع شرکت باقی است </badge>
                                           <?php }  ?>
                                       
                                     </td>
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
				<?php
				//  $this->load->view('component/footer-text.php');
				 ?>
				<!-- /footer -->
			</div>
		<!-- /main content -->
		

		
	
		