<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            responsive: true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "همه"]],
            pageLength: 10,
        });
    });
    function clearThisAccount(account_id, currency_id)
    {
       var conf = confirm('');
       {

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
                   <h3>  تصفیه حساب </h3>
                 </div>

                <!-- search and filter area -->
                <?php echo form_open('reports/clearance'); ?>
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-xs-6 m-b-10 m-t-10">
                            <select class="form-control select2 col-md-5 col-sm-6 col-xs-12" style="width: 100%; border:none !important; background-color:#ddd;" aria-hidden="true" name="account_id" id="account_id"> 
                                <?php if(!empty($cur_account[0]['name'])) { ?>
                                    <option value="<?=$cur_account[0]['id']?>"><?=$cur_account[0]['name']?></option>
                                <?php } ?>
                                <option value="">  انتخاب مشتری / فروشنده</option>
                                <?php foreach($account as $key => $value){ ?>
                                <option value="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-6 m-b-10 m-t-10">
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

                        <div class="col-md-2 col-sm-6 col-xs-6 m-b-4 m-t-10">
                            <button class="btn mybtn search_btn form-control" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>

                        <div class="col-md-2 col-sm-6 col-xs-6 m-b-4 m-t-10">
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
                    <table id="example" class="table table-bordered table-striped table-hover" style="width:100%">
                    
                        <thead>
                            <tr>
                                <th>شماره</th>
                                <th>حسابات</th>
                                <th> رسیدگی قرض</th>
                                <th>بردگی قرض</th>
                                <th>بیلانس</th>
                                <th>تشخیص</th>
                                <th>قابل تصفیه</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php $id =1;
                                $balance = 0;
                                $talabat = 0;
                                $loan = 0;
                                $flag = 0;
                                foreach($balancesheet as $key => $value) 
                                {
                                    $talabat += floatval($value['talab']);
                                    $loan += floatval($value['loan']);
                                    $balance = (floatval($value['talab']) - floatval($value['loan']));
                                    if(floatval($value['talab']) > 0 && $balance == 0) { 
                                        $flag = 1;
                                    } else if(floatval($value['talab']) == 0 && floatval($value['loan']) == 0) { // empty record
                                        $flag = 2;
                                    } else {
                                        $flag = 0;
                                    }
                                    ?>
                                    <tr>
                                        <td><?=$id?></td>
                                        <td>  
                                            <a target="_blank" href="<?=base_url().'ledgerDetails/'.$value['accountId'].'/1'?>">
                                             <?=$value['name']?>
                                            </a>
                                        </td>
                                        <td class="priceStyle"><?=number_format($value['loan'],2)?> </td>
                                        <td  class="priceStyle"><?=number_format($value['talab'],2)?></td>
                                        <td  class="priceStyle"><?=number_format($balance,2)?></td>
                                        <td  class="priceStyle">
                                        <?php if($flag == 1) {  echo "قابل تصفیه ";
                                            } else if($flag == 2) { 
                                                echo " ";
                                            } else { ?>
                                                <?= intval($balance) == 0 ? 'تصفیه' : (intval($balance) > 0 ? 'طلب':'باقی') ?>
                                            <?php }  ?>
                                    
                                       </td>
                                        <td>
                                            <?php if($flag == 1) { ?>
                                                <button type="button" class="btn btn-success btn-sm " id="alert_demo_7" data_id="<?=$value['accountId']?>" data_id2="<?=$cur_currency[0]['id']?>" 
                                                data_id3="<?=base_url()?>">  قابل تصفیه </button>	
                                           <?php } else { ?>
                                               <center> <i class="fas fa-times text-danger"></i></center>
                                           <?php }  ?>
                                        </td>

                                    </tr>
                                    <?php $id++; } ?>
                            </tbody>
                            <tfoot>
                                <tr style="background:#eefcff">
                                    <td  class="priceStyle" colspan="2">مجموع</td>
                                    <td  class="priceStyle"><?=number_format($loan,2)?></td>
                                    <td  class="priceStyle"><?=number_format($talabat,2)?></td>
                                    <td  class="priceStyle"><?=number_format($talabat - $loan,2)?></td>
                                    <td ></td>
                                    <td ></td>

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
		

        <script src="<?php echo base_url(); ?>assets/js/plugin/sweetalert/sweetalert.min.js"></script>
	
		<script>
		//== Class definition
		var SweetAlert2Demo = function() {

			//== Demos
			var initDemos = function() {
				$('#alert_demo_7').click(function(e) {
                    var accountId = $(this).attr('data_id');  
                    var currencyId = $(this).attr('data_id2');
                    var baseUrl = $(this).attr('data_id3');  
                    // console.log('Account ID:', accountId);  
                    // console.log('Currency ID:', currencyId);  

					swal({
						title: 'آیا میخواهید تصفیه حساب نمایید ؟',
						text: " قابل برگشت نمی باشد ",
						icon: 'warning',
						buttons:{
							confirm: {
								text : ' بلی تصفیه نمایید ',
								className : 'btn btn-success btn-sm'
							},
							cancel: {
                                text: 'لغو',
								visible: true,
								className: 'btn btn-danger text-white'
							}
						}
					}).then((Delete) => {
						if (Delete) {
							// redirect to the route
                            window.location.href= baseUrl +'clearThisAccount/'+accountId+'/'+currencyId;
						} else {
							swal.close();
						}
					});
				});

				

			};

			return {
				//== Init
				init: function() {
					initDemos();
				},
			};
		}();

		//== Class Initialization
		jQuery(document).ready(function() {
			SweetAlert2Demo.init();
		});
	</script>