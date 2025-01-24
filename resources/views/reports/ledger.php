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

        <!-- breadcrumbs -->
        <div class="page-header">
						<ul class="breadcrumbs">
							<li class="nav-home">
								<a href="<?=base_url()?>">
									<i class="flaticon-home"></i>
								</a>
							</li>
							<li class="separator">
								<i class="flaticon-left-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="<?=base_url()."journals"?>">روزنامچه</a>
							</li>
							<li class="separator">
								<i class="flaticon-left-arrow"></i>
							</li>
							<li class="nav-item">
								<a href="#">لیجر</a>
							</li>
						</ul>
					</div>
          <!-- / breadcrumbs -->


				<div class="row">
			  
		    	<div class="col-md-12 col-sm-12 col-xs-12 m-t--10">
				  <div class="card">
				
					    
					   

          <!-- card-body -->				
					<div class="card-body">


           <!-- search and filter area -->
           <div class="col-md-12 col-sm-12 col-xs-12">
              <div class="row">
                  <div class="col-md-5 col-sm-6 col-xs-6 m-b-10 m-t-10">
                      <select onchange="submitAccountIdToURL(this.value)"
                       class="form-control select2 col-md-5 col-sm-6 col-xs-12" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="account_id" id="account_id" required> 
                          <?php if(!empty($selectedAccount[0]['name'])) { ?>
                            <option value="<?=$selectedAccount[0]['id']?>"><?=$selectedAccount[0]['name']?></option>
                          <?php } ?>
                          <option value=""> انتخاب حساب</option>
                          <?php foreach($accounts as $key => $value){ ?>
                          <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                          <?php } ?>
                      </select>
                  </div>

                  <div class="col-md-5  col-sm-6 col-xs-6">
                    <div class="form-group">
                      <div class="input-group " data-provide="datepicker">&nbsp;&nbsp;
                      <div class="input-group-append">
                      <span class="input-group-text" style="width:40px !important;" data-mddatetimepicker="true" data-trigger="click"
                          data-targetselector="#datepicker" data-englishnumber="true">
                          <span class="fa fa-calendar"></span> 
                      </span>
                      </div>
                          <input class="form-control"  name="recieve_date" id="datepicker"  
                          data-targetselector="#datepicker"
                           value="<?php if(!empty($selectedDate)) { echo $selectedDate; } ?>" 
                          data-mddatetimepicker="true"  placeholder=" فلتر نظر به تاریخ"  data-placement="right" data-englishnumber="true"  >
                      </div>
                    </div>	
                  </div>
                  
                  <div class="col-md-1 col-sm-6 col-xs-6 m-b-4 m-t-10">
                      <button class="btn  mybtn search_btn form-control" onclick="submitAccountIdToURL()" >
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
         <!-- / search and filter area -->


         <?php 
         if(intval($selectedAccount[0]['id']) === 1) { ?>
          <!-- fieldset with todays start balance -->
            <fieldset class="scheduler-border" style="border:2px solid #dbdad3 !important;background:#f9f9f9 !important;margin:10px 0px 10px 0px !important;">
                <legend class="scheduler-border"> &nbsp;  شـــروع بیلانـــس   (<?php echo todays_date(); ?>) &nbsp; </legend>
                <div class="row">


                <?php 
                  $balance =0;
                  foreach($start_balance as $key => $value){

                    if(intval($value['total_debit']) > intval($value['total_credit']))
                    {
                      $balance = floatval($value['total_debit'] - intval($value['transaction_debit'] + intval($value['transaction_credit'])));
                      // $balance = floatval($value['total_debit'] + floatval($value['transaction_debit'] - floatval($value['transaction_credit'])));
                    } else {
                      // $balance = -intval($value['total_credit'] - intval($value['transaction_credit'] + intval($value['transaction_debit'])));
                      $balance = -floatval($value['total_credit'] + floatval($value['transaction_credit'] - floatval($value['transaction_debit'])));
                    }

                    ?>
                      <div class="col-sm-6 col-lg-3">
                        <div class="card p-3">
                          <div class="d-flex align-items-center">
                            <span class="stamp stamp-md ml-3" style="background-color:<?=$value['color']?>">
                              <?=$value['symbol']?>
                            </span>
                            <div>
                              <h5 class="mb-1"><b><a href="#"> 
                                <?php if(floatval($balance) > 0) {
                                  echo number_format($balance);
                                } else {
                                  echo $balance;
                                } ?>
                                <small><?=$value['inserted_short_date']?></small></a>
                                </b></h5>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php } ?>
              
                  
                </div>
            </fieldset>
          <!-- /fieldset with todays stat balance -->
        <?php }  ?>




        <fieldset class="scheduler-border" style="border:2px solid #dbdad3 !important;background:#f9f9f9 !important;margin:10px 0px 10px 0px !important;">
              <legend class="scheduler-border"> &nbsp;    بـــیلانس مــوجـود  (<?=$selectedAccount[0]['name']?>) &nbsp; </legend>
              <div class="row">
      
                        <div class="table-responsive table_responsive" id="print_area"  style="padding:5px;"><!-- table -->
                         <table class="table table-bordered" style="width:100%">
                          <!-- <table id="example" class="display nowrap" style="width:100%"> -->
                        <thead>
                              <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                <td colspan="8">
                                <img  src="<?php echo base_url().'assets/img/header.jpg';?>" class="img-responsive visible-print" style="width:100%" alt="header Image">
                                  </td>
                              </tr>
                              <tr class="d-none" style="width:100%; background-color:#fff !important;color:#000 !important;">
                                <td colspan="8">
                                  <center>
                                      <h3 style="margin-top:10px">  بـــیلانس مــوجـود  (<?=$selectedAccount[0]['name']?>)  </h3>
                                    </center>
                                  </td>
                              </tr>

                              <tr>
                              <th> شماره &nbsp; </th>
                              <th> حساب </th>
                              <th> دبت (رسیدگی) </th>
                              <th> کریدت (بردگی / باقی)</th>
                              <th>بیلانس </th>
                              <th>واحد پولی</th>
                              <th>معادل به <?=show_base_currency_name()?></th>
                              <!-- <th> تشخیص عمومی</th> -->
                              </tr>
                      </thead>
                      <tbody>
                          <?php $id=1; $balance =0; $total = 0;
                          foreach($ledger as $key => $value){
                              $balance = floatval($value['total_debit'] - floatval($value['total_credit']));
                              ?>
                              <tr>
                                  <td><?php echo $id; ?> </td>
                                  <td> <?=$value['account_name'];?> </td>
                                  <td> <?php  echo number_format($value['total_debit']); ?> </td>
                                  <td> <?php  echo number_format($value['total_credit']); ?> </td>
                                  
                                  <td>
                                     <center>
                                     <a href="<?php echo base_url(); ?>ledgerDetails/<?=$value['accountId']?>/<?=$value['currencys']?>">
                                      <button type="button" id="projectImages"  class="btn btn-default btn-sm"><?php echo number_format($balance);  ?></button>
                                      </a>
                                    </center>
                                  </td>
                                  
                                  <td style="color:<?=$value['color']?>"> 
                                    <?php  echo $value['currency_name'] ;  ?>
                                  </td>

                                   
                                  <td>
                                    <?php 
                                    $calculated = show_converted_rate($value['currencys'], $balance);
                                    $total += floatval($calculated);
                                     echo (!empty($calculated)) ? number_format($calculated,2) : "  "; 
                                     ?>
                                  </td>

                                  <!-- <td> -->
                                    <!-- if(intval($value['accountId']) === 1) {
                                       echo intval($balance) > 0 ? " طلب " : (intval($balance) === 0 ? "تصفیه" :  " باقی "); 
                                     } else {
                                       echo intval($balance) > 0 ? " باقی " : (intval($balance) === 0 ? "تصفیه" :  " طلب "); 
                                     } -->
                                  <!-- </td> -->
                                  
                              </tr>
                              <?php $id++; } ?>
                          
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="6"> بیلانس حقیقی معادل به   <?=show_base_currency_name()?></td>
                              <td colspan="2"><?=number_format($total,2)?></td>
                            </tr>
                            <tr>
                              <td colspan="8">مجموع به حروف : <?=convertNumber(intval($total))?></td>
                            </tr>
                          </tfoot>
                     </table>
                   </div> <!-- /table responsive -->  
            </div>
        </fieldset>

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


		