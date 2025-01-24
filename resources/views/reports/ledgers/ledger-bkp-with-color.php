<script>
 function submitAccountIdToURL(accountId)
 {
    // var account_id = parseInt($('#account_id').val());
    var account_id = parseInt(accountId) > 0 ? parseInt(accountId) : parseInt($('#account_id').val());
    var base_url = $('#base_url').val();
    var date = $('#datepicker').val();
    if(date.length > 1 && account_id > 0) 
    {
      window.location.href= base_url + "reports/ledger/" + account_id + "/" + date;
    } 
    else if(date.length < 1 && account_id > 0) 
    {
      window.location.href= base_url + "reports/ledger/" + account_id;
    }
    else 
    {
      alert("حساب را انتخاب نمایید");
    }
 }
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
                <?php echo form_open('reports/ledgers'); ?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-md-5 col-sm-6 col-xs-6 m-b-10 m-t-10">
                            <select class="form-control select2 col-md-5 col-sm-6 col-xs-12" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="branch_id" id="branch_id"> 
                                <?php if(!empty($cur_branch[0]['name'])) { ?>
                                    <option value="<?=$cur_branch[0]['id']?>"><?=$cur_branch[0]['name']?></option>
                                <?php } ?>
                                <option value=""> انتخاب شعبه</option>
                                <?php foreach($branch as $key => $value){ ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-5  col-sm-6 col-xs-6 m-t-10">
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
                        
                        <div class="col-md-1 col-sm-6 col-xs-6 m-b-4 m-t-10">
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
					    <center><h4 class="card-title">لیجر حسابات  </h4></center>
					</div>	
					<!-- / end of print header -->
                   <div class="table_responsive" style="padding:5px; min-width: 800px; max-width: 130%">
                        <table class="table table-bordered dataTable my_table" style="width:100%">
                            <thead>
                                <tr>
                                <th><center> شماره</center></th>
                                <th><center> کتگوری</center></th>
                                <th><center> حساب</center></th>
                                <th><center> افغانی</center></th>
                                <?php foreach($currency as $k => $v) { ?>
                                <th><center><?=$v['name']?></center></th>
                               <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                 <!-- 
                                    1000: سرمایه
                                    2000: دارایی جاریی - نقد و بانکی
                                    3000: دارایی جاری ذخایر
                                    4000: دارایی جاری طلبات
                                    5000: قرضه
                                    6000: عواید
                                    7000: مصارفات
                                 -->
                                <?php
                                $id = 0;
                                $prevCode = null;
                                $groupColor = '#f1f1f1';
                                $total=0;
                                $updateFlag = 0;
                                $soldUpdateFlag = 0;


                                $belongs_code = $sold_income['belongs_code']; 
                                $cur_total_sold = $sold_income['cur_total_sold']; 
                                $prev_total_sold = $sold_income['prev_total_sold']; 
                                $total_sold_income = $sold_income['cur_total_sold'] + $sold_income['prev_total_sold'];
                                intval($total_sold_income) > 0 ? $soldUpdateFlag = 1 : $soldUpdateFlag = 0;

                                foreach ($accounts as $key => $value) 
                                {
                                    if(floatval($value['cur_debit']) > 0 || floatval($value['cur_debit']) > 0) { $updateFlag = 1; } else {
                                        $updateFlag = 0;
                                    }
                                    if (trim($prevCode) !== trim($value['accode'])) {
                                        $groupColor = ($groupColor === '#f1f1f1') ? 'transparent' : '#f1f1f1';
                                    }
                                    ?>
                                    <tr style="background-color: <?= $groupColor ?>;border:1px solid #ddd">
                                        <td><?= ++$id ?></td>
                                        <td>
                                            <?php
                                            // گروب به اساس یک مجموعه
                                            if (trim($prevCode) !== trim($value['accode'])) {
                                                echo $value['account_type_name'];
                                            }
                                             $prevCode = $value['accode'];
                                            ?>
                                        </td>
                                        <td><?= $value['name']?> </td>

                                        <!-- تغیر رنگ به اساس تغیرات سال فعلی -->
                                        <td>
                                        <?php if(intval($updateFlag) === 1) { ?>
                                        <a target="blank" style="color:blue" href="<?=base_url().'ledgerDetails/'.$value['accountId']."/1"?>">  
                                        <?php } else if(intval($value['accountId']) === intval($belongs_code) && intval($soldUpdateFlag) === 1 ) { ?>
                                        <a target="blank"  style="color:blue" href="<?=base_url().'ledgerDetails/'.$value['accountId']."/1"?>"> 
                                        <?php } else { ?>
                                        <a target="blank"  style="color:red" href="<?=base_url().'ledgerDetails/'.$value['accountId']."/1"?>"> 
                                        <?php } ?> 
                                           <?php 
                                           $total_amount = ($value['cur_debit'] + $value['prev_debit']) - ($value['cur_credit'] + $value['prev_credit']); 
                                          
                                          //  مجموع مبلغ فروشات را در بخش خزانه فروشگاها نشان بدهید 
                                           if(intval($value['accountId']) === intval($belongs_code)) 
                                           {
                                                
                                                if(floatval($total_amount) + floatval($total_sold_income) < 0) 
                                                { 
                                                $total -= $total_amount +  $total_sold_income;
                                                echo number_format(-$total_amount + $total_sold_income); 
                                                } else { 
                                                // $total += $total_amount +  $sold_income;
                                                echo number_format($total_amount +  $total_sold_income); 
                                                }
                                           } 
                                           else 
                                           {
                                                if(floatval($total_amount) < 0) 
                                                { 
                                                $total -= $total_amount;
                                                echo number_format(-$total_amount); 
                                                } else { 
                                                $total += $total_amount;
                                                echo number_format($total_amount); 
                                                }
                                           }
                                          ?>
                                        </a> 
                                        </td>

                                        <!-- (cur_debit + prev_debit) - (cur_credit + prev_credit) -->

                                

                                        <?php foreach ($currency as $k => $v) { ?>
                                            <td>
                                            <a target="blank" href="<?=base_url().'ledgerDetails/'.$value['accountId']."/".$v['id']?>"> 
                                              <?= show_this_currency_amount($v['id'], $value['accountId'],$year) ?>
                                           </a> 
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                 <tr style="background:#eefcff">
                                    <td colspan="3">مجموع</td>
                                    <td><?=number_format($total,2)?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>

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


		